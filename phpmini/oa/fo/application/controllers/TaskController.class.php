<?php

/**
 * Controller for handling task list and task related requests
 *
 * @version 1.0
 * @author Ilija Studen <ilija.studen@gmail.com>
 */
class TaskController extends ApplicationController {

	/**
	 * Construct the MilestoneController
	 *
	 * @access public
	 * @param void
	 * @return MilestoneController
	 */
	function __construct() {
		parent::__construct();
		prepare_company_website_controller($this, 'website');
	} // __construct

	private function task_item(ProjectTask $task) {
		return array(
			"id" => $task->getId(),
			"title" => clean($task->getObjectName()),
			"parent" => $task->getParentId(),
			"milestone" => $task->getMilestoneId(),
			"assignedTo" => $task->getAssignedTo()? $task->getAssignedToName():'',
			"completed" => $task->isCompleted(),
			"completedBy" => $task->getCompletedByName(),
			"isLate" => $task->isLate(),
			"daysLate" => $task->getLateInDays(),
			"priority" => $task->getPriority(),
			"percentCompleted" => $task->getPercentCompleted(),
			"duedate" => ($task->getDueDate() ? $task->getDueDate()->getTimestamp() : '0'),
			"order" => $task->getOrder()
		);
	}

	function quick_add_task() {
		if (logged_user()->isGuest()) {
			flash_error(lang('no access permissions'));
			ajx_current("empty");
			return;
		}
		
		$notAllowedMember = '' ;
		if(!ProjectTask::canAdd(logged_user(), active_context(), $notAllowedMember)) {
			if (str_starts_with($notAllowedMember, '-- req dim --')) flash_error(lang('must choose at least one member of', str_replace_first('-- req dim --', '', $notAllowedMember, $in)));
			else trim($notAllowedMember) == "" ? flash_error(lang('you must select where to keep', lang('the task'))) : flash_error(lang('no context permissions to add',lang("tasks"), $notAllowedMember));
			ajx_current("empty");
			return;
		}
		
		ajx_current("empty");
		$task = new ProjectTask();
		$task_data = array_var($_POST, 'task');
		$parent_id = array_var($task_data, 'parent_id', 0);
		$parent = ProjectTasks::findById($parent_id);
		
		if (is_array($task_data)) {
			$task_data['due_date'] = getDateValue(array_var($task_data, 'task_due_date'));
			$task_data['start_date'] = getDateValue(array_var($task_data, 'task_start_date'));
			
			if ($task_data['due_date'] instanceof DateTimeValue) {
				$duetime = getTimeValue(array_var($task_data, 'task_due_time'));
				if (is_array($duetime)) {
					$task_data['due_date']->setHour(array_var($duetime, 'hours'));
					$task_data['due_date']->setMinute(array_var($duetime, 'mins'));
				}
				$task_data['due_date']->advance(logged_user()->getTimezone() * -3600);
				$task_data['use_due_time'] = is_array($duetime);
			}
			if ($task_data['start_date'] instanceof DateTimeValue) {
				$starttime = getTimeValue(array_var($task_data, 'task_start_time'));
				if (is_array($starttime)) {
					$task_data['start_date']->setHour(array_var($starttime, 'hours'));
					$task_data['start_date']->setMinute(array_var($starttime, 'mins'));
				}
				$task_data['start_date']->advance(logged_user()->getTimezone() * -3600);
				$task_data['use_start_time'] = is_array($starttime);
			}

			if(config_option("wysiwyg_tasks")){
				$task_data['type_content'] = "html";
				$task_data['text'] = preg_replace("/[\n|\r|\n\r]/", '', array_var($task_data, 'text'));
			}else{
				$task_data['type_content'] = "text";
			}
			
			$task_data['object_type_id'] = $task->getObjectTypeId();
			
			$task->setFromAttributes($task_data);
				
			if (array_var($task_data,'is_completed',false) == 'true'){
				$task->setCompletedOn(DateTimeValueLib::now());
				$task->setCompletedById(logged_user()->getId());
			}
				
			try {
				DB::beginWork();
				$task->save();
				$totalMinutes = (array_var($task_data, 'hours') * 60) + (array_var($task_data, 'minutes'));
				$task->setTimeEstimate($totalMinutes);
				$task->save();
				
				$gb_member_id = array_var($task_data, 'member_id');				
				$member_ids = array();
				$persons_dim = Dimensions::findByCode('feng_persons');
				$persons_dim_id = $persons_dim instanceof Dimension ? $persons_dim->getId() : 0;
				if($parent){
					if(count($parent->getMembers()) > 0){
						foreach ($parent->getMembers() as $member){
							if($member->getDimensionId() != $persons_dim_id){
								$member_ids[] = $member->getId();
							}
						}
					}
					$task->setMilestoneId($parent->getMilestoneId());
					$task->save();
				}

				if(count($member_ids) == 0){
					$member_ids = active_context_members(false);
				}
                                
				if ($gb_member_id && is_numeric($gb_member_id)) {
					$member_ids[] = $gb_member_id;
				}
                                
				$object_controller = new ObjectController();
				$object_controller->add_to_members($task, $member_ids);
				
				//Add new work timeslot for this task
//				if (array_var($task_data,'hours') != '' && array_var($task_data,'hours') > 0){
//					$hours = array_var($task_data, 'hours');
//					$hours = - $hours;
//						
//					$timeslot = new Timeslot();
//					$dt = DateTimeValueLib::now();
//					$dt2 = DateTimeValueLib::now();
//					$timeslot->setEndTime($dt);
//					$dt2 = $dt2->add('h', $hours);
//					$timeslot->setStartTime($dt2);
//					$timeslot->setContactId(logged_user()->getId());
//					$timeslot->setObjectId($task->getId());
//					$timeslot->save();
//				}

				
				$assignee = $task->getAssignedToContact();
				if ($assignee instanceof Contact) {
					$task->subscribeUser($assignee);
				}
				
				// create default reminder by user config option
				if ($task->getDueDate()!= null && user_config_option("add_task_default_reminder")){			
					$reminder = new ObjectReminder();
					$def = explode(",",user_config_option("reminders_tasks"));          			
					$minutes = $def[2] * $def[1];
          			$reminder->setMinutesBefore($minutes);
                    $reminder->setType($def[0]);
                    $reminder->setContext("due_date");
                    $reminder->setObject($task);
                    $reminder->setUserId(0);
                    $date = $task->getDueDate();
                                        
					if ($date instanceof DateTimeValue) {
						$rdate = new DateTimeValue($date->getTimestamp() - $minutes * 60);
						$reminder->setDate($rdate);
					}
					$reminder->save();
				}
				
				$subs = array();
				if(config_option('multi_assignment') && Plugins::instance()->isActivePlugin('crpm')){
					$json_subtasks = json_decode(array_var($_POST, 'multi_assignment'), true);
					$subtasks = array();
					$line = 0;
					if (is_array($json_subtasks)) {
						foreach ($json_subtasks as $json_subtask){
							$subtasks[$line]['assigned_to_contact_id'] = $json_subtask['assigned_to_contact_id'];
							$subtasks[$line]['name'] = $json_subtask['name'];
							$subtasks[$line]['time_estimate_hours'] = $json_subtask['time_estimate_hours'];
							$subtasks[$line]['time_estimate_minutes'] = $json_subtask['time_estimate_minutes'];
							$line++;
						}
					}
					Hook::fire('save_subtasks', $task, $subtasks);

					$subtasks = ProjectTasks::findAll(array(
						'conditions' => '`parent_id` = ' . DB::escape($task->getId())
					)); // findAll
					foreach ($subtasks as $sub){
						$subs[] = $sub->getArrayInfo();
					}
				}
				
				// subscribe
				$task->subscribeUser(logged_user());
                                
				DB::commit();
				ApplicationLogs::createLog($task, ApplicationLogs::ACTION_ADD);
				// notify asignee
				if(array_var($task_data, 'notify') == 'true') {
					try {
						Notifier::taskAssigned($task);
					} catch(Exception $e) {
						Logger::log($e->getMessage());
						Logger::log($e->getTraceAsString());
					} // try
				}
				ajx_extra_data(array("task" => $task->getArrayInfo(), 'subtasks' => $subs));
				flash_success(lang('success add task', $task->getObjectName()));
			} catch(Exception $e) {
				DB::rollback();
				flash_error($e->getMessage());
			} // try
		} // if
	}

	function quick_edit_task() {
		if (logged_user()->isGuest()) {
			flash_error(lang('no access permissions'));
			ajx_current("empty");
			return;
		}
		ajx_current("empty");

		$task = ProjectTasks::findById(get_id());
		if(!($task instanceof ProjectTask)) {
			flash_error(lang('task list dnx'));
			return;
		}

		if(!$task->canEdit(logged_user())) {
			flash_error(lang('no access permissions'));
			return;
		}

		$task_data = array_var($_POST, 'task');

		// set task dates
		if (is_array($task_data)) {
			$send_edit = false;
			if($task->getAssignedToContactId() == array_var($task_data, 'assigned_to_contact_id')){
				$send_edit = true;
			}
			$task_data['due_date'] = getDateValue(array_var($task_data, 'task_due_date'));
			$task_data['start_date'] = getDateValue(array_var($task_data, 'task_start_date'));
			
			if ($task_data['due_date'] instanceof DateTimeValue) {
				$duetime = getTimeValue(array_var($task_data, 'task_due_time'));
				if (is_array($duetime)) {
					$task_data['due_date']->setHour(array_var($duetime, 'hours'));
					$task_data['due_date']->setMinute(array_var($duetime, 'mins'));
				}
				$task_data['due_date']->advance(logged_user()->getTimezone() * -3600);
				$task_data['use_due_time'] = is_array($duetime);
			}
			if ($task_data['start_date'] instanceof DateTimeValue) {
				$starttime = getTimeValue(array_var($task_data, 'task_start_time'));
				if (is_array($starttime)) {
					$task_data['start_date']->setHour(array_var($starttime, 'hours'));
					$task_data['start_date']->setMinute(array_var($starttime, 'mins'));
				}
				$task_data['start_date']->advance(logged_user()->getTimezone() * -3600);
				$task_data['use_start_time'] = is_array($starttime);
			}
				
			//control date subtask with parent
			if(array_var($task_data, 'control_dates') == "child"){
				$parent = $task->getParent();
				if ($parent->getStartDate() instanceof DateTimeValue && $task_data['start_date'] instanceof DateTimeValue) {
					if($task_data['start_date']->getTimestamp() < $parent->getStartDate()->getTimestamp()){
						$parent->setStartDate($task_data['start_date']);
						$parent->setUseStartTime($task_data['use_start_time']);
					}
				}else{
					$parent->setStartDate($task_data['start_date']);
					$parent->setUseStartTime(array_var($task_data, 'use_start_time',0));
				}
				if ($parent->getDueDate() instanceof DateTimeValue && $task_data['due_date'] instanceof DateTimeValue) {
					if($task_data['due_date']->getTimestamp() > $parent->getDueDate()->getTimestamp()){
						$parent->setDueDate($task_data['due_date']);
						$parent->setUseDueTime($task_data['use_due_time']);
					}
				}else{
					$parent->setDueDate($task_data['due_date']);
					$parent->setUseDueTime(array_var($task_data, 'use_due_time',0));
				}
				// calculate and set estimated time
				if (array_var($task_data, 'hours') !== null || array_var($task_data, 'minutes') !== null) {
					$totalMinutes = (array_var($task_data, 'hours') * 60) + (array_var($task_data, 'minutes'));
					$parent->setTimeEstimate($totalMinutes);
				}
				$parent->save();
			}
				
			if(config_option("wysiwyg_tasks")){
				$task_data['type_content'] = "html";
				if (array_var($task_data, 'text') !== null) {
					$task_data['text'] = preg_replace("/[\n|\r|\n\r]/", '', array_var($task_data, 'text'));
				}
			}else{
				$task_data['type_content'] = "text";
			}
			$task->setFromAttributes($task_data);
				
			if (array_var($_GET, 'dont_mark_as_read')) {
				$is_read = $task->getIsRead(logged_user()->getId());
			}

			try {
				DB::beginWork();

				if(config_option('multi_assignment') && Plugins::instance()->isActivePlugin('crpm')){
					if(array_var($task_data, 'multi_assignment_aplly_change') == 'subtask') {
						$null = null;
						Hook::fire('edit_subtasks', $task, $null);
					}
				}

				// calculate and set estimated time
				if (array_var($task_data, 'hours') !== null || array_var($task_data, 'minutes') !== null) {
					$totalMinutes = (array_var($task_data, 'hours') * 60) + (array_var($task_data, 'minutes'));
					$task->setTimeEstimate($totalMinutes);
				}
				$task->save();

				$task->calculatePercentComplete();
				
				// get member ids
				$member_ids = array();
				if (array_var($task_data, 'members')) {
					$member_ids = json_decode(array_var($task_data, 'members'));
				}
				
				$remove_members_from_dim = array_var($task_data, 'remove_from_dimension');
				$old_members = $task->getMembers();
				foreach ($old_members as $old_mem) {
					if ($old_mem->getDimensionId() != $remove_members_from_dim) $member_ids[] = $old_mem->getId();
				}

				// get member id when changing member via drag & drop
				if (array_var($task_data, 'member_id')) {
					$member_ids[] = array_var($task_data, 'member_id');
				}

				// drag & drop - also apply changes to subtasks
				$tasks_to_update = $task->getAllSubTasks();
				$tasks_to_update[] = $task;

				$assignee = $task->getAssignedToContact();
				if ($assignee instanceof Contact) {
					$task->subscribeUser($assignee);
				}

				// add to members, subscribers, etc
				$object_controller = new ObjectController();
				if (count($member_ids) > 0) {
					foreach ($tasks_to_update as $task_to_update) {
						$object_controller->add_to_members($task_to_update, $member_ids);
					}
				}

				$task->resetIsRead();

				$log_info = '';
				if($send_edit == true){
					$log_info = $task->getAssignedToContactId();
				}else if($send_edit == false){
					$task->setAssignedBy(logged_user());
					$task->save();
				}
				

				//edit reminders accordingly
				if ($task->getDueDate()!= null && !$task->isCompleted() && $task->getSubscriberIds() != null){ //to make sure the task has a due date and it is not completed yet, and that it has subscribed people						
					$old_reminders = ObjectReminders::getByObject($task);	    
					if($old_reminders != null){		
						$object_controller = new ObjectController();												
						$object_controller->update_reminders($task, $old_reminders); //updating the old ones						
					}else if ( $task->getAssignedTo() == null //if there is no asignee, but it still has subscribers
					 		  || (user_config_option("add_self_task_autoreminder") && logged_user()->getId() == $task->getAssignedToContactId()) //if the user is going to set up reminders for his own tasks
					 		  || (user_config_option("add_task_autoreminder") && logged_user()->getId() != $task->getAssignedToContactId())){ //if the user is going to set up reminders for tasks assigned to its colleagues			
						$reminder = new ObjectReminder();
						$def = explode(",",user_config_option("reminders_tasks"));          			
						$minutes = $def[2] * $def[1];
	          			$reminder->setMinutesBefore($minutes);
	                    $reminder->setType($def[0]);
	                    $reminder->setContext("due_date");
	                    $reminder->setObject($task);
	                    $reminder->setUserId(0);
	                    $date = $task->getDueDate();	                                        
						if ($date instanceof DateTimeValue) {
							$rdate = new DateTimeValue($date->getTimestamp() - $minutes * 60);
							$reminder->setDate($rdate);
						}
						$reminder->save();			
						
					}
				}
				
				// subscribe
				$task->subscribeUser(logged_user());
				if(isset($_POST['type_related'])){
					if($_POST['type_related'] == "all" || $_POST['type_related'] == "news"){
						$task_data['members'] = $member_ids;
						unset($task_data['due_date']);
						unset($task_data['use_due_time']);
						unset($task_data['start_date']);
						unset($task_data['use_start_time']);
						$this->repetitive_tasks_related($task,"edit",$_POST['type_related'],$task_data);
					}
				}

				DB::commit();
				ApplicationLogs::createLog($task, ApplicationLogs::ACTION_EDIT, false, false, true, $log_info);

				// notify asignee
				if(array_var($task_data, 'notify') == 'true' && $send_edit == false) {
					try {
						Notifier::taskAssigned($task);
					} catch(Exception $e) {
					} // try
				}

				$subs = array();
				$subtasks = $task->getAllSubTasks(); // findAll

				foreach ($subtasks as $sub){
					//control date parent whit subtask
					if(array_var($task_data, 'control_dates') == "father"){
						if ($sub->getStartDate() instanceof DateTimeValue) {
							if ($task->getStartDate() instanceof DateTimeValue) {
								if($task->getStartDate()->getTimestamp() > $sub->getStartDate()->getTimestamp()) {
									$sub->setStartDate($task->getStartDate());
								}
							}
						}else{
							if ($task->getStartDate() instanceof DateTimeValue)
							$sub->setStartDate($task->getStartDate());
						}
						$sub->setUseStartTime($task->getUseStartTime());
						if ($sub->getDueDate() instanceof DateTimeValue) {
							if ($task->getDueDate() instanceof DateTimeValue) {
								if($task->getDueDate()->getTimestamp() < $sub->getDueDate()->getTimestamp()) {
									$sub->setDueDate($task->getDueDate());
								}
							}
						}else{
							if($task->getDueDate() instanceof DateTimeValue) {
								$sub->setDueDate($task->getDueDate());
							}
						}
						$sub->setUseDueTime($task->getUseDueTime());
						$sub->save();
					}
					$subs[] = $sub->getArrayInfo();
				}
				$parent = '';
				if($task->getParent()){
					$parent = $task->getParent()->getArrayInfo();
				}
				ajx_extra_data(array("task" => $task->getArrayInfo(), 'subtasks' => $subs, 'parent' => $parent));
				flash_success(lang('success edit task', $task->getObjectName()));
			} catch(Exception $e) {
				DB::rollback();
				flash_error($e->getMessage());
			} // try
		} // if
	}
	
	
	function get_task_data() {
		ajx_current("empty");
		$id = get_id();
		$task = ProjectTasks::findById($id);
		if ($task instanceof ProjectTask && $task->canView(logged_user())) {
			$data = array('id' => $id);
			if (array_var($_REQUEST, 'desc')) {
				$desc = $task->getText();
				$data['desc'] = $desc;
			}
		}
		ajx_extra_data($data);
	}
	
	function get_task_descriptions() {
		ajx_current("empty");
		$ids = explode(',', $_REQUEST['ids']);
		foreach ($ids as $k => &$id) {
			if (!is_numeric($id)) $id=0;
		}
		
		$data = array();
		if (is_array($ids) && count($ids) > 0) {
			$rows = DB::executeAll("SELECT object_id, `text` FROM ".TABLE_PREFIX."project_tasks WHERE object_id IN (".implode(',',$ids).")");
			if(is_array($rows)){
				foreach ($rows as $row) {
					$data['t'.$row['object_id']] = $row['text'];
				}
			}
		}
		ajx_extra_data(array('descriptions' => $data));
	}

	function multi_task_action(){
		ajx_current("empty");
		$ids = explode(',', array_var($_POST, 'ids'));
		$action = array_var($_POST, 'action');
		$options = array_var($_POST, 'options');

		if (!is_array($ids) || trim(array_var($_POST, 'ids')) == '' || count($ids) <= 0){
			flash_error(lang('no items selected'));
			return;
		}

		$count_tasks = ProjectTasks::count('object_id in (' . implode(',',$ids) . ')');
		$tasksToReturn = array();
		$subt_info = array();
		$showSuccessMessage = true;
		
		
		$application_logs = array();
		try{
			DB::beginWork();
			foreach($ids as $id){
				$task = Objects::findObject($id);
				switch ($action){
					case 'complete':
						if ($task->canEdit(logged_user())){
							$log_info = $task->completeTask($options);
							$application_logs[] = array($task,ApplicationLogs::ACTION_CLOSE,false, false, true, substr($log_info,0,-1));
							// if task is repetitive, generate a complete instance of this task and modify repeat values
							if ($task->isRepetitive()) {
								$complete_last_task = false;
								// calculate next repetition date
								$opt_rep_day = array('saturday' => false, 'sunday' => false);
								$new_dates = $this->getNextRepetitionDates($task, $opt_rep_day, $new_st_date, $new_due_date);

								// if this is the last task of the repetetition, complete it, do not generate a new instance
								if ($task->getRepeatNum() > 0) {
									$task->setRepeatNum($task->getRepeatNum() - 1);
									if ($task->getRepeatNum() == 0) {
										$complete_last_task = true;
									}
								}
								if (!$complete_last_task && $task->getRepeatEnd() instanceof DateTimeValue) {
									if ($task->getRepeatBy() == 'start_date' && array_var($new_dates, 'st') > $task->getRepeatEnd() ||
									$task->getRepeatBy() == 'due_date' && array_var($new_dates, 'due') > $task->getRepeatEnd() ) {

										$complete_last_task = true;
									}
								}

								if (!$complete_last_task) {
									// generate new pending task
									$new_task = $task->cloneTask(array_var($new_dates, 'st'), array_var($new_dates, 'due'));
									$reload_view = true;
								}
							}
							foreach($task->getAllSubTasks() as $sub){
								$tasksToReturn[]=$sub->getArrayInfo();
							}
							$tasksToReturn[] = $task->getArrayInfo();
						}
						break;
					case 'delete':
						if ($task->canDelete(logged_user())){
							$tasksToReturn[] = array('id' => $task->getId());
							$task->trash();
							$application_logs[] = array($task, ApplicationLogs::ACTION_TRASH);
							if($options == "news" || $options == "all"){
								$tasksToReturn_related = $this->repetitive_tasks_related($task,"delete",$options);
								foreach ($tasksToReturn_related as $tasksToReturn_rel){
									$tasksToReturn[] = array('id' => $tasksToReturn_rel);
								}
							}
						}
						break;
					case 'archive':
						if ($task->canEdit(logged_user())){
							$tasksToReturn[] = $task->getArrayInfo();
							$task->archive();
							$application_logs[] = array($task, ApplicationLogs::ACTION_ARCHIVE);
							if($options == "news" || $options == "all"){
								$tasksToReturn_related = $this->repetitive_tasks_related($task,"archive",$options);;
								foreach ($tasksToReturn_related as $tasksToReturn_rel){
									$tasksToReturn[] = array('id' => $tasksToReturn_rel);
								}
							}
						}
						break;
					case 'start_work':
						if ($task->canEdit(logged_user())){
							$timeslot = $task->addTimeslot(logged_user());
							$application_logs[] = array($timeslot, ApplicationLogs::ACTION_OPEN,false,true);
							$tasksToReturn[] = $task->getArrayInfo();
							$showSuccessMessage = false;
						}
						break;
					case 'close_work':
						if ($task->canEdit(logged_user())){
							$timeslot = $task->closeTimeslots(logged_user(),array_var($_POST, 'options'));
							$application_logs[] = array($timeslot, ApplicationLogs::ACTION_CLOSE,false,true);
							$tasksToReturn[] = $task->getArrayInfo();
							$showSuccessMessage = false;
						}
						break;
					case 'pause_work':
						if ($task->canEdit(logged_user())){
							$task->pauseTimeslots(logged_user());
							$tasksToReturn[] = $task->getArrayInfo();
							$showSuccessMessage = false;
						}
						break;
					case 'resume_work':
						if ($task->canEdit(logged_user())){
							$task->resumeTimeslots(logged_user());
							$tasksToReturn[] = $task->getArrayInfo();
							$showSuccessMessage = false;
						}
						break;
					case 'markasread':
						$task->setIsRead(logged_user()->getId(),true);
						$tasksToReturn[] = $task->getArrayInfo();
						$showSuccessMessage = false;
						break;
					case 'markasunread':
						$task->setIsRead(logged_user()->getId(),false);
						$tasksToReturn[] = $task->getArrayInfo();
						$showSuccessMessage = false;
						break;
					default:
						//DB::rollback();
						flash_error(lang('invalid action'));
						return;
				} // end switch
			} // end foreach
			DB::commit();
						
			foreach ($application_logs as $log){				
				if(count($log) >= 2 && $log[0] instanceof ApplicationDataObject){
					call_user_func_array( 'ApplicationLogs::createLog', $log );	
				}			
			}			
			
			if (count($tasksToReturn) < $count_tasks) {
				flash_error(lang('tasks updated') . '. ' . lang('some tasks could not be updated due to permission restrictions'));
			} else if ($showSuccessMessage) {
				flash_success(lang('tasks updated'));
			}
			if(count($subt_info) > 0){
				ajx_extra_data(array("tasks" => $tasksToReturn,'subtasks' => $subt_info));
			}else{
				ajx_extra_data(array('tasks' => $tasksToReturn));
			}
		} catch(Exception $e){
			DB::rollback();
			flash_error($e->getMessage());
		}

	}

	function new_list_tasks(){
		//load config options into cache for better performance
		load_user_config_options_by_category_name('task panel');
		 
		// get query parameters, save user preferences if necessary
		$status = array_var($_GET,'status',null);
		if (is_null($status) || $status == '') {
			$status = user_config_option('task panel status',2);
		} else
		if (user_config_option('task panel status') != $status) {
			set_user_config_option('task panel status', $status, logged_user()->getId());
		}

		$previous_filter = user_config_option('task panel filter', 'no_filter');

		$filter_from_date =getDateValue(array_var($_GET, 'from_date'));
		if ($filter_from_date instanceof DateTimeValue) {
			$copFromDate = $filter_from_date;
			$filter_from_date = $filter_from_date->toMySQL();
		}
		
		$tasks_from_date = '';
		
		$filter_to_date =getDateValue(array_var($_GET, 'to_date'));		
		if ($filter_to_date instanceof DateTimeValue) {
			$copToDate = $filter_to_date;
			$filter_to_date = $filter_to_date->toMySQL();
		}		
		$tasks_to_date = '';
		if (user_config_option('tasksDateStart') != $filter_from_date){
			if($filter_from_date != '0000-00-00 00:00:00' || array_var($_GET, 'resetDateStart')){
				set_user_config_option('tasksDateStart', $copFromDate , logged_user()->getId());
			}else{
				$filter_from_date = user_config_option('tasksDateStart');
			}
		}
		
		if (user_config_option('tasksDateEnd') != $filter_to_date) {
			if( $filter_to_date != '0000-00-00 00:00:00'|| array_var($_GET, 'resetDateEnd')){
				set_user_config_option('tasksDateEnd', $copToDate , logged_user()->getId());
			}else{
				$filter_to_date = user_config_option('tasksDateEnd');
			}
		}
				
		if ((($filter_from_date != '0000-00-00 00:00:00')) || (($filter_to_date != '0000-00-00 00:00:00'))){
			if(($filter_from_date != '0000-00-00 00:00:00')){ 
				$dateFrom = DateTimeValueLib::dateFromFormatAndString(DATE_MYSQL, $filter_from_date);
				$dateFrom->advance(logged_user()->getTimezone() * -3600);
				$dateFrom = $dateFrom->toMySQL();
			}
			if(($filter_to_date != '0000-00-00 00:00:00')){
				$dateTo = DateTimeValueLib::dateFromFormatAndString(DATE_MYSQL, $filter_to_date);
				$dateTo->setHour(23);
				$dateTo->setMinute(59);
				$dateTo->setSecond(59);
				$dateTo->advance(logged_user()->getTimezone() * -3600);
				$dateTo = $dateTo->toMySQL();
			}
			if((($filter_from_date != '0000-00-00 00:00:00')) && (($filter_to_date != '0000-00-00 00:00:00'))){
				
				$tasks_from_date = " AND (((`start_date` BETWEEN '" . $dateFrom ."' AND '".$dateTo."') AND `start_date` != ". DB::escape(EMPTY_DATETIME) .") OR ((`due_date` BETWEEN '" . $dateFrom ."' AND '".$dateTo."') AND `due_date` != ". DB::escape(EMPTY_DATETIME) ."))";
			}elseif (($filter_from_date != '0000-00-00 00:00:00')){
				$tasks_from_date = " AND (`start_date` > '" . $dateFrom ."' OR `due_date` > '" . $dateFrom."') ";
			}else{
			
				$tasks_from_date = "AND ((`start_date` < '".$dateTo."' AND `start_date` != ". DB::escape(EMPTY_DATETIME) .") OR (`due_date` < '".$dateTo."' AND `due_date` != ".DB::escape(EMPTY_DATETIME)."))";
		
			}
		}else{
				$tasks_from_date = "";
		}
		$filter = array_var($_GET, 'filter');
		if (is_null($filter) || $filter == '') {
			$filter = $previous_filter;
		} else if ($previous_filter != $filter) {
			set_user_config_option('task panel filter', $filter, logged_user()->getId());
		}

		if ($filter != 'no_filter'){
			$filter_value = array_var($_GET,'fval');
			if (is_null($filter_value) || $filter_value == '') {
				$filter_value = user_config_option('task panel filter value', null, logged_user()->getId());
				set_user_config_option('task panel filter value', $filter_value, logged_user()->getId());
				$filter = $previous_filter;
				set_user_config_option('task panel filter', $filter, logged_user()->getId());
			} else
			if (user_config_option('task panel filter value') != $filter_value) {
				set_user_config_option('task panel filter value', $filter_value, logged_user()->getId());
			}
		}
		$isJson = array_var($_GET,'isJson',false);
		if ($isJson) ajx_current("empty");

		$template_condition = "`is_template` = 0 ";

		//Get the task query conditions
		$task_filter_condition = "";
                
		switch($filter){
			case 'assigned_to':
				$assigned_to = $filter_value;
				if ($assigned_to > 0) {
					$task_filter_condition = " AND (`assigned_to_contact_id` = " . $assigned_to . ") ";
				} else {
					if ($assigned_to == -1) 
						$task_filter_condition = " AND `assigned_to_contact_id` = 0";
				}
				break;
			case 'assigned_by':
				if ($filter_value != 0) {
					$task_filter_condition = " AND  `assigned_by_id` = " . $filter_value . " ";
				}
				break;
			case 'created_by':
				if ($filter_value != 0) {
					$task_filter_condition = " AND  `created_by_id` = " . $filter_value . " ";
				}
				break;
			case 'completed_by':
				if ($filter_value != 0) {
					$task_filter_condition = " AND  `completed_by_id` = " . $filter_value . " ";
				}
				break;
			case 'milestone':
				$task_filter_condition = " AND  `milestone_id` = " . $filter_value . " ";
				break;
			case 'priority':
				$task_filter_condition = " AND  `priority` = " . $filter_value . " ";
				break;
			case 'subtype':
				if ($filter_value != 0) {
					$task_filter_condition = " AND  `object_subtype` = " . $filter_value . " ";
				}
				break;
			case 'subscribed_to':
				if ($filter_value > 0) {
					$res20 = DB::execute("SELECT object_id FROM ". TABLE_PREFIX . "object_subscriptions WHERE `contact_id` = " . $filter_value);
					$subs_rows = $res20->fetchAll($res20);
					$subs = array();
					if(count($subs_rows) > 0){
						foreach($subs_rows as $row) $subs[] = $row['object_id'];
						unset($res20, $subs_rows, $row);
						if(count($subs) > 0){
							$task_filter_condition = " AND `completed_on` = " . DB::escape(EMPTY_DATETIME) . " AND `id` IN(" . implode(',', $subs) . ")";
						}
					}else{
						$task_filter_condition = " AND `completed_on` = " . DB::escape(EMPTY_DATETIME) . " AND `id` = -1";
					}
				}
				break;
			case 'no_filter':
				$task_filter_condition = "";
				break;
			default:
				flash_error(lang('task filter criteria not recognised', $filter));
		}

		$task_status_condition = "";
		$now_date = DateTimeValueLib::now();
		$now_date->advance(logged_user()->getTimezone() * 3600);
		$now = $now_date->format('Y-m-d 00:00:00');
		$now_end = $now_date->format('Y-m-d 23:59:59');
		switch($status){
			case 0: // Incomplete tasks
				$task_status_condition = " AND `completed_on` = " . DB::escape(EMPTY_DATETIME);
				break;
			case 1: // Complete tasks
				$task_status_condition = " AND `completed_on` > " . DB::escape(EMPTY_DATETIME);
				break;
			case 10: // Active tasks
				$task_status_condition = " AND (SELECT COUNT(ts.object_id) FROM ".TABLE_PREFIX."timeslots ts WHERE ts.rel_object_id=o.id AND ts.end_time = '".EMPTY_DATETIME."') > 0";
				break;
			case 11: // Overdue tasks
				$task_status_condition = " AND `completed_on` = " . DB::escape(EMPTY_DATETIME) . " AND `due_date` < '$now'";
				break;
			case 12: // Today tasks
				$task_status_condition = " AND `completed_on` = " . DB::escape(EMPTY_DATETIME) . " AND `due_date` >= '$now' AND `due_date` <= '$now_end'";
				break;
			case 13: // Today + Overdue tasks
				$task_status_condition = " AND `completed_on` = " . DB::escape(EMPTY_DATETIME) . " AND `due_date` <= '$now_end'";
				break;
			case 20: // Actives task by current user
				$task_status_condition = " AND `completed_on` = " . DB::escape(EMPTY_DATETIME) . " AND `start_date` <= '$now' AND `assigned_to_contact_id` = " . logged_user()->getId();
				break;
			case 21: // Subscribed tasks by current user
				$res20 = DB::execute("SELECT object_id FROM ". TABLE_PREFIX . "object_subscriptions WHERE `contact_id` = " . logged_user()->getId());
				$subs_rows = $res20->fetchAll($res20);
				foreach($subs_rows as $row) $subs[] = $row['object_id'];
				unset($res20, $subs_rows, $row);
				$task_status_condition = " AND `completed_on` = " . DB::escape(EMPTY_DATETIME) . " AND `id` IN(" . implode(',', $subs) . ")";
				break;				
			case 2: // All tasks
				break;
			default:
				throw new Exception('Task status "' . $status . '" not recognised');
		}

		$task_assignment_conditions = "";
		if (!SystemPermissions::userHasSystemPermission(logged_user(), 'can_see_assigned_to_other_tasks')) {
			$task_assignment_conditions = " AND assigned_to_contact_id = ".logged_user()->getId();
		}
		
		$conditions = "AND $template_condition $task_filter_condition $task_status_condition $task_assignment_conditions $tasks_from_date";
		//Now get the tasks
		$tasks = ProjectTasks::instance()->listing(array(
			"extra_conditions" => $conditions,
			"start" => 0,
			"limit" => user_config_option('task_display_limit', 999),
			"count_results" => false,
			"raw_data" => true,
		))->objects;
		
		$pendingstr = $status == 0 ? " AND `completed_on` = " . DB::escape(EMPTY_DATETIME) . " " : "";
		$milestone_conditions = " AND `is_template` = false " . $pendingstr;
		
		//Find all internal milestones for these tasks
		$internalMilestones = ProjectMilestones::instance()->listing(array("extra_conditions" => $milestone_conditions))->objects;
		
		//Find all external milestones for these tasks, external milestones are the ones that belong to a parent member and have tasks in the current member
		$milestone_ids = array();
		if($tasks){
			$task_ids = array();
			foreach ($tasks as $task){
				$task_ids[] = $task['id'];
				if ($task['milestone_id'] != 0) {
					$milestone_ids[$task['milestone_id']] = $task['milestone_id'];
				}
			}
			
			// generate request cache
			ObjectMembers::instance()->getCachedObjectMembers(0, $task_ids);
			ProjectTasks::instance()->findByRelatedCached(0, $task_ids);
		}
		
		$int_milestone_ids = array();
		foreach($internalMilestones as $milestone) {
			$int_milestone_ids[] = $milestone->getId();
		}
		
		$milestone_ids = array_diff($milestone_ids, $int_milestone_ids);
		
		if (count($milestone_ids) == 0) $milestone_ids[] = 0;
		$ext_milestone_conditions = " `is_template` = false " . $pendingstr . ' AND `object_id` IN (' . implode(',',$milestone_ids) . ')';

		$externalMilestones = ProjectMilestones::findAll(array('conditions' => $ext_milestone_conditions));
		
		// Get Users Info
		$users = allowed_users_in_context(ProjectTasks::instance()->getObjectTypeId(), active_context(), ACCESS_LEVEL_READ, '', true);
		$allUsers = Contacts::getAllUsers();
		
		$user_ids = array(-1);
		foreach ($allUsers as $user) {
			$user_ids[] = $user->getId();
		}
		
		// only companies with users
		$companies = Contacts::findAll(array(
			"conditions" => "e.is_company = 1",
			"join" => array(
				"table" => Contacts::instance()->getTableName(),
				"jt_field" => "object_id",
				"j_sub_q" => "SELECT xx.object_id FROM ".Contacts::instance()->getTableName(true)." xx WHERE 
					xx.is_company=0 AND xx.company_id = e.object_id AND xx.object_id IN (".implode(",", $user_ids).") LIMIT 1"
			)
		));
        tpl_assign('tasks', $tasks);
        
        if (config_option('use tasks dependencies')) {
        	$dependency_count = array();
        	if (is_array($tasks)) {
		        foreach ($tasks as $task) {
					$previous = 0;
					$ptasks = ProjectTaskDependencies::getDependenciesForTask($task['id']);
					foreach ($ptasks as $pdep) {
						$ptask = ProjectTasks::findById($pdep->getPreviousTaskId());
						if ($ptask instanceof ProjectTask && !$ptask->isCompleted()) $previous++;
					}
					$dependants = ProjectTaskDependencies::getDependantsForTask($task['id']);
					$dep_csv = "";
					foreach ($dependants as $dep) $dep_csv .= ($dep_csv==""?"":",") . $dep->getTaskId();
					$dependency_count[] = array('id' => $task['id'], 'count' => $previous, 'dependants' => $dep_csv);
				}
        	}
			tpl_assign('dependency_count', $dependency_count);
        }
        
		if (!$isJson){
			
			$all_templates = COTemplates::findAll(array('conditions' => '`trashed_by_id` = 0 AND `archived_by_id` = 0'));
			
			tpl_assign('all_templates', $all_templates);

			if (user_config_option('task_display_limit') > 0 && count($tasks) > user_config_option('task_display_limit')) {
				tpl_assign('displayTooManyTasks', true);
				array_pop($tasks);
			}
				
			tpl_assign('object_subtypes',array());
			tpl_assign('internalMilestones', $internalMilestones);
			tpl_assign('externalMilestones', $externalMilestones);
			tpl_assign('users', $users);
			tpl_assign('allUsers', $allUsers);
			tpl_assign('companies', $companies);
			if (strtotime(user_config_option('tasksDateStart'))){//this return null if date is 0000-00-00 00:00:00
				$dateStart = new DateTime('@'.strtotime(user_config_option('tasksDateStart')));
				$dateStart = $dateStart->format(user_config_option('date_format'));
			}else{
				$dateStart = '';
			}
			if (strtotime(user_config_option('tasksDateEnd'))){//this return null if date is 0000-00-00 00:00:00
				$dateEnd = new DateTime('@'.strtotime(user_config_option('tasksDateEnd')));
				$dateEnd = $dateEnd->format(user_config_option('date_format'));
			}else{
				$dateEnd = '';
				}
			$userPref = array();
			$userPref = array(
				'filterValue' => isset($filter_value) ? $filter_value : '',
				'filter' => $filter,
				'dateStart' => $dateStart,
				'dateEnd' => $dateEnd,
				'status' => $status,
				'showTime' => user_config_option('tasksShowTime'),
				'showDates' => user_config_option('tasksShowDates'),
				'showTags' => user_config_option('tasksShowTags',0),
				'showEmptyMilestones' => user_config_option('tasksShowEmptyMilestones',1),
				'showTimeEstimates' => user_config_option('tasksShowTimeEstimates',1),
				'groupBy' => user_config_option('tasksGroupBy'),
				'orderBy' => user_config_option('tasksOrderBy'),
				'defaultNotifyValue' => user_config_option('can notify from quick add'),
			);
			hook::fire('tasks_user_preferences', null, $userPref);
			
			tpl_assign('userPreferences', $userPref);
			
			tpl_assign('userPermissions', array('can_add' => ProjectTask::canAdd(logged_user(), active_context()) ? 1 : 0));
			
			ajx_set_no_toolbar(true);
		}
	}

	/**
	 * View task page
	 *
	 * @access public
	 * @param void
	 * @return null
	 */
	function view() {
		if(array_var($_REQUEST, "template_task")){
			$task_list = TemplateTasks::findById(get_id());
			if(!($task_list instanceof TemplateTask)) {
				flash_error(lang('task list dnx'));
				ajx_current("empty");
				return;
			} // if
			$this->setTemplate(get_template_path('view', 'template_task'));
		}else{
			$task_list = ProjectTasks::findById(get_id());
			
			$this->addHelper('textile');
			
			if(!($task_list instanceof ProjectTask)) {
				flash_error(lang('task list dnx'));
				ajx_current("empty");
				return;
			} // if
			
			if(!$task_list->canView(logged_user())) {
				flash_error(lang('no access permissions'));
				ajx_current("empty");
				return;
			} // if
		}	
	
		//read object for this user
		$task_list->setIsRead(logged_user()->getId(),true);
		
		tpl_assign('task_list', $task_list);

		$this->addHelper('textile');
		ajx_extra_data(array("title" => $task_list->getObjectName(), 'icon'=>'ico-task'));
		ajx_set_no_toolbar(true);
		
		ApplicationReadLogs::createLog($task_list, ApplicationReadLogs::ACTION_READ);
	} // view

	function print_task() {
		$this->setLayout("html");
		$task = ProjectTasks::findById(get_id());

		if(!($task instanceof ProjectTask)) {
			flash_error(lang('task list dnx'));
			ajx_current("empty");
			return;
		} // if

		if(!$task->canView(logged_user())) {
			flash_error(lang('no access permissions'));
			ajx_current("empty");
			return;
		} // if

		tpl_assign('task', $task);
		$this->setTemplate('print_task');
	} // print_task

	/**
	 * Add new task
	 *
	 * @access public
	 * @param void
	 * @return null
	 */
	function add_task() {
		//is template task?
		$isTemplateTask = false;
		if(array_var($_REQUEST, 'template_task') == true){
			$isTemplateTask = true;
			if(array_var($_REQUEST, 'template_id')){
				$template_id = array_var($_REQUEST, 'template_id');
			}else{
				$template_id = 0;
			}			
			tpl_assign('template_id', $template_id);
		}
		
		if (logged_user()->isGuest()) {
			flash_error(lang('no access permissions'));
			ajx_current("empty");
			return;
		}

		$notAllowedMember = '' ;
		if(!ProjectTask::canAdd(logged_user(), active_context(), $notAllowedMember) && !$isTemplateTask) {
			if (str_starts_with($notAllowedMember, '-- req dim --')) flash_error(lang('must choose at least one member of', str_replace_first('-- req dim --', '', $notAllowedMember, $in)));
			else trim($notAllowedMember) == "" ? flash_error(lang('you must select where to keep', lang('the task'))) : flash_error(lang('no context permissions to add',lang("tasks"), $notAllowedMember));
			ajx_current("empty");
			return;
		} // if

		//is template task?
		if(array_var($_REQUEST, 'template_task') == true){
			$task = new TemplateTask();
			$this->setTemplate(get_template_path('add_template_task', 'template_task'));
						
		}else{
			$task = new ProjectTask();
		}
		
		$task_data = array_var($_POST, 'task');
		if(!is_array($task_data)) {
			$dd = getDateValue(array_var($_POST, 'task_due_date', ''));
			if ($dd instanceof DateTimeValue) {
				$duetime = getTimeValue(array_var($_POST, 'task_due_time'));
				if (is_array($duetime)) {
					$dd->setHour(array_var($duetime, 'hours'));
					$dd->setMinute(array_var($duetime, 'mins'));
				}
				$task->setUseDueTime(is_array($duetime));
			}
			$sd = getDateValue(array_var($_POST, 'task_start_date', ''));
			if ($sd instanceof DateTimeValue) {
				$starttime = getTimeValue(array_var($_POST, 'task_start_time'));
				if (is_array($starttime)) {
					$sd->setHour(array_var($starttime, 'hours'));
					$sd->setMinute(array_var($starttime, 'mins'));
				}
				$task->setUseStartTime(is_array($starttime));
			}
			$time_estimate = (array_var($_POST, 'hours', 0) * 60) + array_var($_POST, 'minutes', 0);
			if(config_option("wysiwyg_tasks")){
				$text_post = preg_replace("/[\n|\r|\n\r]/", '', array_var($_POST, 'text', ''));
			}else{
				$text_post = array_var($_POST, 'text', '');
			}
			
			$task_data = array(
				'milestone_id' => array_var($_REQUEST, 'milestone_id',0),
				'project_id' => 1 ,
				'name' => array_var($_POST, 'name', ''),
				'assigned_to_contact_id' => array_var($_POST, 'assigned_to_contact_id', '0'),
				'parent_id' => array_var($_REQUEST, 'parent_id', 0),
				'priority' => array_var($_POST, 'priority', ProjectTasks::PRIORITY_NORMAL),
				'text' => $text_post,
				'start_date' => $sd,
				'due_date' => $dd,
				'time_estimate' => $time_estimate,
				'is_template' => array_var($_POST, "is_template", array_var($_GET, "is_template", false)),
				'percent_completed' => array_var($_POST, "percent_completed", ''),
				'object_subtype' => array_var($_POST, "object_subtype", config_option('default task co type')),
				'send_notification' => array_var($_POST, 'notify') && array_var($_POST, 'notify') == 'true'
			); // array
			
			if (Plugins::instance()->isActivePlugin('mail')) {
				$from_email = array_var($_GET, 'from_email');
				$email = MailContents::findById($from_email);
				if ($email instanceof MailContent) {
					$task_data['name'] = $email->getSubject();
					$task_data['text'] = lang('create task from email description', $email->getSubject(), $email->getFrom(), $email->getTextBody());
					tpl_assign('from_email', $email);
				}
			}
			
		} // if
		
		if (array_var($_GET, 'replace')) {
			ajx_replace(true);
		}

		tpl_assign('task_data', $task_data);
		tpl_assign('task', $task);
		tpl_assign('pending_task_id', 0);

		$subtasks = array();
		if(array_var($_POST, 'multi_assignment')){
			$subtasks = json_decode(array_var($_POST, 'multi_assignment'), true);
		}
		tpl_assign('multi_assignment', $subtasks);

		if (is_array(array_var($_POST, 'task'))) {
			try {
				// order
				$task->setOrder(ProjectTasks::maxOrder(array_var($task_data, "parent_id", 0), array_var($task_data, "milestone_id", 0)));
				
				try {
					$task_data['due_date'] = getDateValue(array_var($_POST, 'task_due_date'));
					$task_data['start_date'] = getDateValue(array_var($_POST, 'task_start_date'));
				} catch (Exception $e) {
					throw new Exception(lang('date format error', date_format_tip(user_config_option('date_format'))));
				}
				
				if ($task_data['due_date'] instanceof DateTimeValue) {
					$duetime = getTimeValue(array_var($_POST, 'task_due_time'));
					if (is_array($duetime)) {
						$task_data['due_date']->setHour(array_var($duetime, 'hours'));
						$task_data['due_date']->setMinute(array_var($duetime, 'mins'));
					}
					$task_data['due_date']->advance(logged_user()->getTimezone() * -3600);
					$task_data['use_due_time'] = is_array($duetime);
				}
				if ($task_data['start_date'] instanceof DateTimeValue) {
					$starttime = getTimeValue(array_var($_POST, 'task_start_time'));
					if (is_array($starttime)) {
						$task_data['start_date']->setHour(array_var($starttime, 'hours'));
						$task_data['start_date']->setMinute(array_var($starttime, 'mins'));
					}
					$task_data['start_date']->advance(logged_user()->getTimezone() * -3600);
					$task_data['use_start_time'] = is_array($starttime);
				}
				
			
				$err_msg = $this->setRepeatOptions($task_data);
				if ($err_msg) {
					flash_error($err_msg);
					ajx_current("empty");
					return;
				}

				if(config_option("wysiwyg_tasks")){
					$task_data['type_content'] = "html";
					$task_data['text'] = preg_replace("/[\n|\r|\n\r]/", '', array_var($task_data, 'text'));
				}else{
					$task_data['type_content'] = "text";
				}
				$task_data['object_type_id'] = $task->getObjectTypeId();
				$member_ids = json_decode(array_var($_POST, 'members'));
				$task->setFromAttributes($task_data);
				if(!can_task_assignee(logged_user())){
					flash_error(lang('no access permissions'));
					ajx_current("empty");
					return;
				}
				$totalMinutes = (array_var($task_data, 'time_estimate_hours',0) * 60) + (array_var($task_data, 'time_estimate_minutes',0));
				$task->setTimeEstimate($totalMinutes);

				$id = array_var($_GET, 'id', 0);
				if($task instanceof TemplateTask){
					//evt_add("template task added", array("id_template_task" => $file->getId()));
					
					$task->setIsTemplate(true);
					$parent = TemplateTasks::findById($id);
					if ($parent instanceof TemplateTask) {
						$task->setParentId($id);
						$member_ids = $parent->getMemberIds();
						if ($parent->getIsTemplate()) {
							$task->setIsTemplate(true);
						}
					}
					
				}else{
					$parent = ProjectTasks::findById($id);
					if ($parent instanceof ProjectTask) {
						$task->setParentId($id);
						$member_ids = $parent->getMemberIds();
						if ($parent->getIsTemplate()) {
							$task->setIsTemplate(true);
						}
					}
				}

				if ($task->getParentId() > 0 && $task->hasChild($task->getParentId())) {
					flash_error(lang('task child of child error'));
					ajx_current("empty");
					return;
				}
				
				if($task instanceof TemplateTask){
					$task->setSessionId(logged_user()->getId());
				}
				
				DB::beginWork();
				$task->save();
				
				
				// dependencies
				if (config_option('use tasks dependencies')) {
					$previous_tasks = array_var($task_data, 'previous');
					if (is_array($previous_tasks)) {
						foreach ($previous_tasks as $ptask) {
							if ($ptask == $task->getId()) continue;
							$dep = ProjectTaskDependencies::findById(array('previous_task_id' => $ptask, 'task_id' => $task->getId()));
							if (!$dep instanceof ProjectTaskDependency) {
								$dep = new ProjectTaskDependency();
								$dep->setPreviousTaskId($ptask);
								$dep->setTaskId($task->getId());
								$dep->save();
							}
						}
					}
				}
				

				if (array_var($_GET, 'copyId', 0) > 0) {
					// copy remaining stuff from the task with id copyId
					$toCopy = ProjectTasks::findById(array_var($_GET, 'copyId'));
					if ($toCopy instanceof ProjectTask) {
						ProjectTasks::copySubTasks($toCopy, $task, array_var($task_data, 'is_template', false));
					}
				}
				
				// if task is added from task view -> add subscribers
				if (array_var($task_data, 'inputtype') == 'taskview') {
					if (!isset($_POST['subscribers'])) $_POST['subscribers'] = array();
					$_POST['subscribers']['user_'.logged_user()->getId()] = 'checked';
					if ($task->getAssignedToContactId() > 0 && Contacts::instance()->findById( $task->getAssignedToContactId())->getUserType() ) {
						$_POST['subscribers']['user_'.$task->getAssignedToContactId()] = 'checked';
						
					}
				}
				
				// Add assigned user to the subscibers list
				if (isset($_POST['subscribers']) && $task->getAssignedToContactId() > 0  && Contacts::instance()->findById( $task->getAssignedToContactId()) ) {
					$_POST['subscribers']['user_'.$task->getAssignedToContactId()] = 'checked';
				}
				
				//Link objects
				$object_controller = new ObjectController();
				
				if($task instanceof TemplateTask ){
					//if(!empty($member_ids)){
						$object_controller->add_to_members($task, $member_ids, null, false);
					//}
				}else{
					$object_controller->add_to_members($task, $member_ids);
				}				
				$object_controller->add_subscribers($task);
				$object_controller->link_to_new_object($task);
				$object_controller->add_custom_properties($task);
				if ($task->getDueDate()!= null && user_config_option("add_task_default_reminder")){
					$object_controller->add_reminders($task);
				}
				
				
				if(config_option('repeating_task') == 1){
					$opt_rep_day['saturday'] = false;
					$opt_rep_day['sunday'] = false;
					if(array_var($task_data, 'repeat_saturdays',false)){
						$opt_rep_day['saturday'] = true;
					}
					if(array_var($task_data, 'repeat_sundays',false)){
						$opt_rep_day['sunday'] = true;
					}

					$this->repetitive_task($task, $opt_rep_day);
				}

				if(config_option('multi_assignment') && Plugins::instance()->isActivePlugin('crpm')){
					$subtasks = array_var($_POST, 'multi_assignment');
					Hook::fire('save_subtasks', $task, $subtasks);
				}
				
				DB::commit();
				ApplicationLogs::createLog($task, ApplicationLogs::ACTION_ADD);
				
				//Send Template task to view
				if($task instanceof TemplateTask){
					$objectId = $task->getObjectId();
					$id = $task->getId();
					$objectTypeName = $task->getObjectTypeName();
					$objectName = $task->getObjectName();
					$manager = get_class($task->manager());
					$milestoneId = $task instanceof TemplateTask ? $task->getMilestoneId() : '0';
					$subTasks = array();
					$parentId = $task->getParentId();
					$ico = "ico-task";
					$action = "add";
					$object = TemplateController::prepareObject($objectId, $id, $objectName, $objectTypeName, $manager, $action,$milestoneId, $subTasks, $parentId, $ico);
															
					evt_add("template object added", $object);
				}

				// notify asignee
				if(array_var($task_data, 'send_notification') == 'checked') {
					try {
						Notifier::taskAssigned($task);
					} catch(Exception $e) {
						evt_add("debug", $e->getMessage());
					} // try
				}

				if ($task->getIsTemplate()) {
					flash_success(lang('success add template', $task->getObjectName()));
				} else {
					flash_success(lang('success add task list', $task->getObjectName()));
				}
				if (array_var($task_data, 'inputtype') != 'taskview') {
					ajx_current("back");
				} else {
					ajx_current("reload");
				}

			} catch(Exception $e) {
				DB::rollback();
				flash_error($e->getMessage());
				ajx_current("empty");
			} // try
		} // if
	} // add_task
	
	/**
	 * Copy task
	 *
	 * @access public
	 * @param void
	 * @return null
	 */
	function copy_task() {
		if (logged_user()->isGuest()) {
			flash_error(lang('no access permissions'));
			ajx_current("empty");
			return;
		}
		
		$notAllowedMember = '';
		if(!ProjectTask::canAdd(logged_user(), active_context(),$notAllowedMember)) {
			if (str_starts_with($notAllowedMember, '-- req dim --')) flash_error(lang('must choose at least one member of', str_replace_first('-- req dim --', '', $notAllowedMember, $in)));
			else trim($notAllowedMember) == "" ? flash_error(lang('you must select where to keep', lang('the task'))) : flash_error(lang('no context permissions to add',lang("tasks"), $notAllowedMember));
			ajx_current("empty");
			return;
		} // if

		$id = get_id();
		$task = ProjectTasks::findById($id);
		if (!$task instanceof ProjectTask) {
			flash_error(lang('no access permissions'));
			ajx_current("empty");
			return;
		}
		$title = $task->getIsTemplate() ? $task->getObjectName() : lang("copy of", $task->getObjectName());
		$dd = $task->getDueDate() instanceof DateTimeValue ? $task->getDueDate()->advance(logged_user()->getTimezone() * 3600, false) : null;
		$sd = $task->getStartDate() instanceof DateTimeValue ? $task->getStartDate()->advance(logged_user()->getTimezone() * 3600, false) : null;
		
		$task_data = array(
			'milestone_id' => $task->getMilestoneId(),
			'title' => $title,
			'name' => $title, //Alias for title
			'due_date' => getDateValue($dd),
			'start_date' => getDateValue($sd),
			'assigned_to_contact_id' => $task->getAssignedToContactId(),
			'parent_id' => $task->getParentId(),
			'priority' => $task->getPriority(),
			'time_estimate' => $task->getTimeEstimate(),
			'text' => $task->getText(),
			'copyId' => $task->getId(),
			'percent_completed' => $task->getPercentCompleted(),
		); // array
		$newtask = new ProjectTask();
		if($task->getUseStartTime()){
			$newtask->setUseStartTime($task->getUseStartTime());
		}
		if($task->getUseDueTime()){
			$newtask->setUseDueTime($task->getUseDueTime());
		}
		tpl_assign('task_data', $task_data);
		tpl_assign('task', $newtask);
		tpl_assign('base_task', $task);
		tpl_assign('pending_task_id', 0);
		tpl_assign('multi_assignment', array());
		$this->setTemplate("add_task");
	} // copy_task


	/**
	 * Edit task
	 *
	 * @access public
	 * @param void
	 * @return null
	 */
	function edit_task() {
		$isTemplateTask = false;
		if(array_var($_REQUEST, 'template_task') == true){
			$isTemplateTask = true;
		}
		if (logged_user()->isGuest()) {
			flash_error(lang('no access permissions'));
			ajx_current("empty");
			return;
		}
		$this->setTemplate('add_task');
		
		if(array_var($_REQUEST, "template_task")){
			$task = TemplateTasks::findById(array_var($_REQUEST, "template_task_id",get_id()));
			$this->setTemplate(get_template_path('add_template_task', 'template_task'));
			if(!($task instanceof TemplateTask)) {
				flash_error(lang('task list dnx'));
				ajx_current("empty");
				return;
			} // if
		}else{				
			$task = ProjectTasks::findById(get_id());
			if(!($task instanceof ProjectTask)) {
				flash_error(lang('task list dnx'));
				ajx_current("empty");
				return;
			} // if
	
			if(!$task->canEdit(logged_user())) {
				flash_error(lang('no access permissions'));
				ajx_current("empty");
				return;
			} // if		
		}
		
		
		if (array_var($_GET, 'replace')) {
			ajx_replace(true);
		}

		$task_data = array_var($_POST, 'task');
		$time_estimate = (array_var($_POST, 'hours', 0) * 60) + array_var($_POST, 'minutes', 0);
		if($time_estimate > 0){
			$estimatedTime = $time_estimate;
		}else{
			$estimatedTime = $task->getTimeEstimate();
		}
		if(!is_array($task_data)) {
			$this->getRepeatOptions($task, $occ, $rsel1, $rsel2, $rsel3, $rnum, $rend, $rjump);
			$dd = $task->getDueDate() instanceof DateTimeValue ? $task->getDueDate()->advance(logged_user()->getTimezone() * 3600, false) : null;
			$sd = $task->getStartDate() instanceof DateTimeValue ? $task->getStartDate()->advance(logged_user()->getTimezone() * 3600, false) : null;

			$post_dd = null;
			if(array_var($_POST, 'task_due_date')){
				$post_dd = getDateValue(array_var($_POST, 'task_due_date'));
				if ($post_dd instanceof DateTimeValue) {
					$duetime = getTimeValue(array_var($_POST, 'task_due_time'));
					if (is_array($duetime)) {
						$post_dd->setHour(array_var($duetime, 'hours'));
						$post_dd->setMinute(array_var($duetime, 'mins'));
						$post_dd->advance(logged_user()->getTimezone() * 3600, false);
					}
				}
			}

			$post_st = null;
			if(array_var($_POST, 'task_start_date')){
				$post_st = getDateValue(array_var($_POST, 'task_start_date'));
				if ($post_st instanceof DateTimeValue) {
					$starttime = getTimeValue(array_var($_POST, 'task_start_time'));
					if (is_array($starttime)) {
						$post_st->setHour(array_var($starttime, 'hours'));
						$post_st->setMinute(array_var($starttime, 'mins'));
						$post_st->advance(logged_user()->getTimezone() * 3600, false);
					}
				}
			}
			if(config_option("wysiwyg_tasks")){
				$text_post = preg_replace("/[\n|\r|\n\r]/", '', array_var($_POST, 'text', $task->getText()));
			}else{
				$text_post = array_var($_POST, 'text', $task->getText());
			}
			$task_data = array(
				'name' => array_var($_POST, 'name', $task->getObjectName()),
				'text' => $text_post,
				'milestone_id' => array_var($_POST, 'milestone_id',$task->getMilestoneId()),
				'due_date' => getDateValue($post_dd, $dd),
				'start_date' => getDateValue($post_st, $sd),
				'parent_id' => $task->getParentId(),
				'assigned_to_contact_id' => array_var($_POST, 'assigned_to_contact_id', $task->getAssignedToContactId()),
				'priority' => array_var($_POST, 'priority', $task->getPriority()),
				'send_notification' => array_var($_POST, 'notify') == 'true',
				'time_estimate' => $estimatedTime,
				'percent_completed' => $task->getPercentCompleted(),
				'forever' => $task->getRepeatForever(),
				'rend' => $rend,
				'rnum' => $rnum,
				'rjump' => $rjump,
				'rsel1' => $rsel1,
				'rsel2' => $rsel2,
				'rsel3' => $rsel3,
				'occ' => $occ,
				'repeat_by' => $task->getRepeatBy(),
				'object_subtype' => array_var($_POST, "object_subtype", ($task->getObjectSubtype() != 0 ? $task->getObjectSubtype() : config_option('default task co type'))),
				'type_content' => $task->getTypeContent(),
				'multi_assignment' => $task->getColumnValue('multi_assignment',0)
			); // array

			//control dates of parent and subtasks
			$task_data ['type_control'] = "";
			$parent_data = $task->getParent();
			if($parent_data){
				$task_data ['type_control'] = "child";
				$task_data ['control_title'] = $parent_data->getObjectName();
				$task_data ['control_due_date'] = $parent_data->getDueDate() instanceof DateTimeValue ? $parent_data->getDueDate()->getTimestamp() + logged_user()->getTimezone() * 3600 : null;
				$task_data ['control_start_date'] = $parent_data->getStartDate() instanceof DateTimeValue ? $parent_data->getStartDate()->getTimestamp() + logged_user()->getTimezone() * 3600 : null;
			}
			$subtask_data = $task->getAllSubTasks();
			if($subtask_data){
				$task_data ['type_control'] = "father";
				$task_data ['control_title'] = $task->getObjectName();
				$task_data ['control_due_date'] = getDateValue($post_dd, $dd) instanceof DateTimeValue ? getDateValue($post_dd, $dd)->getTimestamp() : null;
				$task_data ['control_start_date'] = getDateValue($post_st, $sd)instanceof DateTimeValue ? getDateValue($post_st, $sd)->getTimestamp() : null;
			}

		} // if

		//I find all those related to the task to find out if the original
		$task_related = ProjectTasks::findByRelated($task->getObjectId());
		if(!$task_related){
			//is not the original as the original look plus other related
			if($task->getOriginalTaskId() != "0"){
				$task_related = ProjectTasks::findByTaskAndRelated($task->getObjectId(),$task->getOriginalTaskId());
			}
		}
		if($task_related){
			$pending_id = 0;
			foreach($task_related as $t_rel){
				if($task->getStartDate() <= $t_rel->getStartDate() && $task->getDueDate() <= $t_rel->getDueDate() && !$t_rel->isCompleted()){
					$pending_id = $t_rel->getId();
					break;
				}
			}
			tpl_assign('pending_task_id', $pending_id);
			tpl_assign('task_related', true);
		}else{
			tpl_assign('pending_task_id', 0);
			tpl_assign('task_related', false);
		}
		tpl_assign('task', $task);
		tpl_assign('task_data', $task_data);

		if(is_array(array_var($_POST, 'task'))) {
			$send_edit = false;
			if($task->getAssignedToContactId() == array_var($task_data, 'assigned_to_contact_id')){
				$send_edit = true;
			}
				
			$old_owner = $task->getAssignedTo();
			if (array_var($task_data, 'parent_id') == $task->getId()) {
				flash_error(lang("task own parent error"));
				ajx_current("empty");
				return;
			}

			try {
				try {
					$task_data['due_date'] = getDateValue(array_var($_POST, 'task_due_date'));
					$task_data['start_date'] = getDateValue(array_var($_POST, 'task_start_date'));
				} catch (Exception $e) {
					throw new Exception(lang('date format error', date_format_tip(user_config_option('date_format'))));
				}
					
				if ($task_data['due_date'] instanceof DateTimeValue) {
					$duetime = getTimeValue(array_var($_POST, 'task_due_time'));
					if (is_array($duetime)) {
						$task_data['due_date']->setHour(array_var($duetime, 'hours'));
						$task_data['due_date']->setMinute(array_var($duetime, 'mins'));
					}
					$task_data['due_date']->advance(logged_user()->getTimezone() * -3600);
					$task_data['use_due_time'] = is_array($duetime);
				}
				if ($task_data['start_date'] instanceof DateTimeValue) {
					$starttime = getTimeValue(array_var($_POST, 'task_start_time'));
					if (is_array($starttime)) {
						$task_data['start_date']->setHour(array_var($starttime, 'hours'));
						$task_data['start_date']->setMinute(array_var($starttime, 'mins'));
					}
					$task_data['start_date']->advance(logged_user()->getTimezone() * -3600);
					$task_data['use_start_time'] = is_array($starttime);
				}
	
				//control date subtask whit parent
				if(array_var($_POST, 'control_dates') == "child"){
					$parent = $task->getParent();
					if ($parent->getStartDate() instanceof DateTimeValue && $task_data['start_date'] instanceof DateTimeValue) {
						if($task_data['start_date']->getTimestamp() < $parent->getStartDate()->getTimestamp()){
							$parent->setStartDate($task_data['start_date']);
							$parent->setUseStartTime($task_data['use_start_time']);
						}
					}else{
						$parent->setStartDate($task_data['start_date']);
						$parent->setUseStartTime(array_var($task_data, 'use_start_time',0));
					}
					if ($parent->getDueDate() instanceof DateTimeValue && $task_data['due_date'] instanceof DateTimeValue) {
						if($task_data['due_date']->getTimestamp() > $parent->getDueDate()->getTimestamp()){
							$parent->setDueDate($task_data['due_date']);
							$parent->setUseDueTime($task_data['use_due_time']);
						}
					}else{
						$parent->setDueDate($task_data['due_date']);
						$parent->setUseDueTime(array_var($task_data, 'use_due_time',0));
					}
					// calculate and set estimated time
					$totalMinutes = (array_var($task_data, 'time_estimate_hours') * 60) + (array_var($task_data, 'time_estimate_minutes'));
					$parent->setTimeEstimate($totalMinutes);
					$parent->save();
				}
				$err_msg = $this->setRepeatOptions($task_data);
				if ($err_msg) {
					flash_error($err_msg);
					ajx_current("empty");
					return;
				}

				if (!isset($task_data['parent_id'])) {
					$task_data['parent_id'] = 0;
				}

				$member_ids = json_decode(array_var($_POST, 'members'));

				$was_template = $task->getIsTemplate();

				if(config_option("wysiwyg_tasks")){
					$task_data['type_content'] = "html";
					$task_data['text'] = preg_replace("/[\n|\r|\n\r]/", '', array_var($task_data, 'text'));
				}else{
					$task_data['type_content'] = "text";
				}
				$task->setFromAttributes($task_data);
				$task->setIsTemplate($was_template); // is_template value must not be changed from ui

				$totalMinutes = (array_var($task_data, 'time_estimate_hours') * 60) + (array_var($task_data, 'time_estimate_minutes'));
				$task->setTimeEstimate($totalMinutes);

				if ($task->getParentId() > 0 && $task->hasChild($task->getParentId())) {
					flash_error(lang('task child of child error'));
					ajx_current("empty");
					return;
				}
				
				if (isset($task_data['percent_completed']) && $task_data['percent_completed'] >= 0 && $task_data['percent_completed'] <= 100) {
					$task->setPercentCompleted($task_data['percent_completed']);
				}

				DB::beginWork();
				$task->save();

				if (!isset($task_data['percent_completed'])) {
					$task->calculatePercentComplete();
				}

				// dependencies
				if (config_option('use tasks dependencies')) {
					$previous_tasks = array_var($task_data, 'previous');
					if (is_array($previous_tasks)) {
						foreach ($previous_tasks as $ptask) {
							if ($ptask == $task->getId()) continue;
							$dep = ProjectTaskDependencies::findById(array('previous_task_id' => $ptask, 'task_id' => $task->getId()));
							if (!$dep instanceof ProjectTaskDependency) {
								$dep = new ProjectTaskDependency();
								$dep->setPreviousTaskId($ptask);
								$dep->setTaskId($task->getId());
								$dep->save();
							}
						}

						$saved_ptasks = ProjectTaskDependencies::findAll(array('conditions' => 'task_id = '. $task->getId()));
						foreach ($saved_ptasks as $pdep) {
							if (!in_array($pdep->getPreviousTaskId(), $previous_tasks)) $pdep->delete();
						}
					} else {
						ProjectTaskDependencies::delete('task_id = '. $task->getId());
					}
				}

				// Add assigned user to the subscibers list
				if ($task->getAssignedToContactId() > 0  && Contacts::instance()->findById( $task->getAssignedToContactId()) ) {
					if (!isset($_POST['subscribers'])) $_POST['subscribers'] = array();
					$_POST['subscribers']['user_'.$task->getAssignedToContactId()] = 'checked';
				}

				$object_controller = new ObjectController();
				if($isTemplateTask){
					$object_controller->add_to_members($task, $member_ids, null, false);
				}else{
					$object_controller->add_to_members($task, $member_ids);
				}				
				$object_controller->add_subscribers($task);
				$object_controller->link_to_new_object($task);
				$object_controller->add_custom_properties($task);
				
				if ($task->getDueDate()!= null && !$task->isCompleted() && $task->getSubscriberIds() != null){ //to make sure the task has a due date and it is not completed yet, and that it has subscribed people											
					$old_reminders = ObjectReminders::getByObject($task);	    
					if($old_reminders != null){		
						$object_controller = new ObjectController();
						$object_controller->add_reminders($task); //adding the new reminders, if any						
						$object_controller->update_reminders($task, $old_reminders); //updating the old ones	
					}else if (logged_user() instanceof Contact 
							  && (user_config_option("add_task_autoreminder") && logged_user()->getId() != $task->getAssignedToContactId() //if the user is going to set up reminders for tasks assigned to its colleagues
					 		  || user_config_option("add_self_task_autoreminder") && logged_user()->getId() == $task->getAssignedToContactId() //if the user is going to set up reminders for his own tasks
					 		  || $task->getAssignedTo() == null )){ //if there is no asignee, but it still has subscribers
						$reminder = new ObjectReminder();
						$def = explode(",",user_config_option("reminders_tasks"));          			
						$minutes = $def[2] * $def[1];
	          			$reminder->setMinutesBefore($minutes);
	                    $reminder->setType($def[0]);
	                    $reminder->setContext("due_date");
	                    $reminder->setObject($task);
	                    $reminder->setUserId(0);
	                    $date = $task->getDueDate();	                                        
						if ($date instanceof DateTimeValue) {
							$rdate = new DateTimeValue($date->getTimestamp() - $minutes * 60);
							$reminder->setDate($rdate);
						}
						$reminder->save();			
						
					}
				}
				

				$task->apply_members_to_subtasks($member_ids, true);

				// apply values to subtasks
				$assigned_to = $task->getAssignedToContactId();
				$subtasks = $task->getAllSubTasks();
				$milestone_id = $task->getMilestoneId();
				$apply_ms = array_var($task_data, 'apply_milestone_subtasks') == "checked";
				$apply_at = array_var($task_data, 'apply_assignee_subtasks', '') == "checked";
				foreach ($subtasks as $sub) {
					$modified = false;
					//if ($apply_at || !($sub->getAssignedToContactId() > 0)) {
					if ($apply_at) {
						$sub->setAssignedToContactId($assigned_to);
						$modified = true;
					}
					if ($apply_ms) {
						$sub->setMilestoneId($milestone_id);
						$modified = true;
					}
					if ($modified) {
						$sub->save();
					}

					//control date parent whit subtask
					if($_POST['control_dates'] == "father"){
						if ($sub->getStartDate() instanceof DateTimeValue) {
							if ($task->getStartDate() instanceof DateTimeValue) {
								if($task->getStartDate()->getTimestamp() > $sub->getStartDate()->getTimestamp())
								$sub->setStartDate($task->getStartDate());
							}
						}else{
							if ($task->getStartDate() instanceof DateTimeValue)
							$sub->setStartDate($task->getStartDate());
						}
						$sub->setUseStartTime($task->getUseStartTime());
						if ($sub->getDueDate() instanceof DateTimeValue) {
							if ($task->getDueDate() instanceof DateTimeValue) {
								if($task->getDueDate()->getTimestamp() < $sub->getDueDate()->getTimestamp())
								$sub->setDueDate($task->getDueDate());
							}
						}else{
							if($task->getDueDate() instanceof DateTimeValue)
							$sub->setDueDate($task->getDueDate());
						}
						$sub->setUseDueTime($task->getUseDueTime());
						$sub->save();
					}
				}

				$task->resetIsRead();

				$log_info = '';
				if($send_edit == true){
					$log_info = $task->getAssignedToContactId();
				}else if($send_edit == false){
					$task->setAssignedBy(logged_user());
					$task->save();
				}
				

				if(config_option('repeating_task') == 1){
					$opt_rep_day['saturday'] = false;
					$opt_rep_day['sunday'] = false;
					if(array_var($task_data, 'repeat_saturdays',false)){
						$opt_rep_day['saturday'] = true;
					}
					if(array_var($task_data, 'repeat_sundays',false)){
						$opt_rep_day['sunday'] = true;
					}

					$this->repetitive_task($task, $opt_rep_day);
				}

				if(isset($_POST['type_related'])){
					if($_POST['type_related'] == "all" || $_POST['type_related'] == "news"){
						$task_data['members'] = json_decode(array_var($_POST, 'members'));
						unset($task_data['due_date']);
						unset($task_data['use_due_time']);
						unset($task_data['start_date']);
						unset($task_data['use_start_time']);
						$this->repetitive_tasks_related($task,"edit",$_POST['type_related'],$task_data);
					}
				}

				if(config_option('multi_assignment') && Plugins::instance()->isActivePlugin('crpm')){
					if(array_var($task_data, 'multi_assignment_aplly_change') == 'subtask') {
						$null = null;
						Hook::fire('edit_subtasks', $task, $null);
					}
				}

				DB::commit();
				ApplicationLogs::createLog($task, ApplicationLogs::ACTION_EDIT, false, false, true, $log_info);
				
				//Send Template task to view
				if($task instanceof TemplateTask){
					$objectId = $task->getObjectId();
					$id = $task->getId();
					$objectTypeName = $task->getObjectTypeName();
					$objectName = $task->getObjectName();
					$manager = get_class($task->manager());
					$milestoneId = $task instanceof TemplateTask ? $task->getMilestoneId() : '0';
					$subTasks = $task->getSubTasks();
					$parentId = $task->getParentId();
					$ico = "ico-task";
					$action = "edit";
					$object = TemplateController::prepareObject($objectId, $id, $objectName, $objectTypeName, $manager, $action,$milestoneId, $subTasks, $parentId, $ico);
						
					evt_add("template object added", $object);
				}

				try {
					if(array_var($task_data, 'send_notification') == 'checked' && $send_edit == false) {
						$new_owner = $task->getAssignedTo();
						if($new_owner instanceof Contact) {
							Notifier::taskAssigned($task);
						} // if
					} // if
				} catch(Exception $e) {

				} // try

				flash_success(lang('success edit task list', $task->getObjectName()));
				ajx_current("back");

			} catch(Exception $e) {
				DB::rollback();
				flash_error($e->getMessage());
				ajx_current("empty");
			} // try
		} // if
	} // edit_task

	/**
	 * Delete task
	 *
	 * @access public
	 * @param void
	 * @return null
	 */
	function delete_task() {
		if (logged_user()->isGuest()) {
			flash_error(lang('no access permissions'));
			ajx_current("empty");
			return;
		}
		ajx_current("empty");
		$project = active_or_personal_project();
		$task = ProjectTasks::findById(get_id());
		if (!($task instanceof ProjectTask)) {
			flash_error(lang('task dnx'));
			return;
		} // if

		if (!$task->canDelete(logged_user())) {
			flash_error(lang('no access permissions'));
			return;
		} // if

		try {
			DB::beginWork();
			$is_template = $task->getIsTemplate();
			$task->trash();
			DB::commit();
			ApplicationLogs::createLog($task, ApplicationLogs::ACTION_TRASH);

			if ($is_template) {
				flash_success(lang('success delete template', $task->getObjectName()));
			} else {
				flash_success(lang('success delete task list', $task->getObjectName()));
			}
			if (array_var($_GET, 'quick', false)) {
				ajx_current('empty');
			} else if (array_var($_GET, 'taskview', false)){
				ajx_current('reload');
			} else {
				ajx_current('back');
			}
		} catch(Exception $e) {
			DB::rollback();
			flash_error(lang('error delete task list'));
		} // try
	} // delete_task


	// ---------------------------------------------------
	//  Tasks
	// ---------------------------------------------------

	private function getNextRepetitionDates($task, $opt_rep_day, &$new_st_date, &$new_due_date) {
		$new_due_date = null;
		$new_st_date = null;

		if ($task->getStartDate() instanceof DateTimeValue ) {
			$new_st_date = new DateTimeValue($task->getStartDate()->getTimestamp());
		}
		if ($task->getDueDate() instanceof DateTimeValue ) {
			$new_due_date = new DateTimeValue($task->getDueDate()->getTimestamp());
		}
		if ($task->getRepeatD() > 0) {
			if ($new_st_date instanceof DateTimeValue) {
				$new_st_date = $new_st_date->add('d', $task->getRepeatD());
			}                        
			if ($new_due_date instanceof DateTimeValue) {
				$new_due_date = $new_due_date->add('d', $task->getRepeatD());
			}
		} else if ($task->getRepeatM() > 0) {
			if ($new_st_date instanceof DateTimeValue) {
				$new_st_date = $new_st_date->add('M', $task->getRepeatM());
			}
			if ($new_due_date instanceof DateTimeValue) {
				$new_due_date = $new_due_date->add('M', $task->getRepeatM());
			}
		} else if ($task->getRepeatY() > 0) {
			if ($new_st_date instanceof DateTimeValue) {
				$new_st_date = $new_st_date->add('y', $task->getRepeatY());
			}
			if ($new_due_date instanceof DateTimeValue) {
				$new_due_date = $new_due_date->add('y', $task->getRepeatY());
			}
		}

		$new_st_date = $this->correct_days_task_repetitive($new_st_date, $opt_rep_day['saturday'], $opt_rep_day['sunday']);
		$new_due_date = $this->correct_days_task_repetitive($new_due_date, $opt_rep_day['saturday'], $opt_rep_day['sunday']);
		
		return array('st' => $new_st_date, 'due' => $new_due_date);
	}

	function generate_new_repetitive_instance() {
		ajx_current("empty");
		$task = ProjectTasks::findById(get_id());
		if (!($task instanceof ProjectTask)) {
			flash_error(lang('task dnx'));
			return;
		} // if

		if (!$task->isRepetitive()) {
			flash_error(lang('task not repetitive'));
			return;
		}

		$opt_rep_day = array('saturday' => false, 'sunday' => false);
		
		$this->getNextRepetitionDates($task, $opt_rep_day, $new_st_date, $new_due_date);
		
		// if this is the last task of the repetetition, do not generate a new instance
		if ($task->getRepeatNum() > 0) {
			$task->setRepeatNum($task->getRepeatNum() - 1);
			if ($task->getRepeatNum() == 0) {
				flash_error(lang('task cannot be instantiated more times'));
				return;
			}
		}
		if ($task->getRepeatEnd() instanceof DateTimeValue) {
			if ($task->getRepeatBy() == 'start_date' && $new_st_date > $task->getRepeatEnd() ||
			$task->getRepeatBy() == 'due_date' && $new_due_date > $task->getRepeatEnd() ) {
				flash_error(lang('task cannot be instantiated more times'));
				return;
			}
		}
		try {
			
			// generate new pending task
			$new_task = $task->cloneTask($new_st_date, $new_due_date);
			$task->clearRepeatOptions();
			foreach ($new_task->getAllSubTasks() as $subt) {
				$subt->setCompletedById(0);
				$subt->setCompletedOn(EMPTY_DATETIME);
				$subt->save();
			}
			
			DB::beginWork();
			
			$new_task->save();
			$task->save();
			
			DB::commit();
			flash_success(lang("new task repetition generated"));
			
			ajx_current("back");
		} catch (Exception $e) {
			DB::rollback();
			flash_error($e->getMessage());
		}
	}

	/**
	 * Complete task
	 *
	 * @access public
	 * @param void
	 * @return null
	 */
	function complete_task() {
		$options = array_var($_GET, 'options');            
		if (logged_user()->isGuest()) {
			flash_error(lang('no access permissions'));
			ajx_current("empty");
			return;
		}
	
		ajx_current("empty");
		$task = ProjectTasks::findById(get_id());
		if(!($task instanceof ProjectTask)) {
			flash_error(lang('task dnx'));
			return;
		} // if
		
		
		// don't complete the same task twice
		if ($task->getCompletedById() > 0) {
			ajx_current("empty");
			return;
		}
		
		if(!$task->canEdit(logged_user())&&$task->getAssignedTo()!=logged_user()) {
			flash_error(lang('no access permissions'));
			ajx_current("empty");
			return;
		} 
		//	
		if(!$task->canChangeStatus(logged_user())) {
			flash_error(lang('no access permissions'));
			return;
		} // if

		try {
			$reload_view = false;
			DB::beginWork();
			
			$log_info = $task->completeTask($options);
			
			// if task is repetitive, generate a complete instance of this task and modify repeat values
			if ($task->isRepetitive()) {
				$complete_last_task = false;
				// calculate next repetition date
				$opt_rep_day = array('saturday' => false, 'sunday' => false);
				$new_dates = $this->getNextRepetitionDates($task, $opt_rep_day, $new_st_date, $new_due_date);
				
				// if this is the last task of the repetetition, complete it, do not generate a new instance
				if ($task->getRepeatNum() > 0) {
					$task->setRepeatNum($task->getRepeatNum() - 1);
					if ($task->getRepeatNum() == 0) {
						$complete_last_task = true;
					}
				}
				if (!$complete_last_task && $task->getRepeatEnd() instanceof DateTimeValue) {
					if ($task->getRepeatBy() == 'start_date' && array_var($new_dates, 'st') > $task->getRepeatEnd() ||
						$task->getRepeatBy() == 'due_date' && array_var($new_dates, 'due') > $task->getRepeatEnd() ) {
						
							$complete_last_task = true;
					}
				}
				
				if (!$complete_last_task) {
					// generate new pending task
					$new_task = $task->cloneTask(array_var($new_dates, 'st'), array_var($new_dates, 'due'));
					$reload_view = true;
				}
			}
			
			DB::commit();
			ApplicationLogs::createLog($task, ApplicationLogs::ACTION_CLOSE, false, false, true, substr($log_info,0,-1));
			flash_success(lang('success complete task'));

			$subt_info = array();
			foreach($task->getAllSubTasks() as $sub){
				$subt_info[]=$sub->getArrayInfo();
			}
			
			ajx_extra_data(array("task" => $task->getArrayInfo(),'subtasks' => $subt_info));
			
			if (array_var($_GET, 'quick', false) && !$reload_view) {
				ajx_current("empty");
			} else {
				ajx_current("reload");
			}
		} catch(Exception $e) {
			DB::rollback();
			flash_error($e->getMessage());
		} // try
	} // complete_task

	/**
	 * Reopen completed task
	 *
	 * @access public
	 * @param void
	 * @return null
	 */
	function open_task() {
		if (logged_user()->isGuest()) {
			flash_error(lang('no access permissions'));
			ajx_current("empty");
			return;
		}
		ajx_current("empty");
		$task = ProjectTasks::findById(get_id());
		if(!($task instanceof ProjectTask)) {
			flash_error(lang('task dnx'));
			return;
		} // if
		
		// don't open the same task twice
		if ($task->getCompletedById() == 0) {
			ajx_current("empty");
			return;
		}

		if(!$task->canChangeStatus(logged_user())) {
			flash_error(lang('no access permissions'));
			ajx_current("empty");
			return;
		} // if

		try {
			DB::beginWork();
			$log_info = $task->openTask();
				
			/*FIXME $opened_tasks = array();
			 $parent = $task->getParent();
			 while ($parent instanceof ProjectTask && $parent->isCompleted()) {
				$parent->openTask();
				$opened_tasks[] = $parent->getId();
				$milestone = ProjectMilestones::findById($parent->getMilestoneId());
				if ($milestone instanceof ProjectMilestones && $milestone->isCompleted()) {
				$milestone->setCompletedOn(EMPTY_DATETIME);
				ajx_extra_data(array("openedMilestone" => $milestone->getId()));
				}
				$parent = $parent->getParent();
				}
				ajx_extra_data(array("openedTasks" => $opened_tasks));*/
				
			//Already called in openTask
			//ApplicationLogs::createLog($task, ApplicationLogs::ACTION_OPEN);
			DB::commit();
			ApplicationLogs::createLog($task, ApplicationLogs::ACTION_OPEN, false, false, true, substr($log_info,0,-1));
			flash_success(lang('success open task'));
				
			//$redirect_to = array_var($_GET, 'redirect_to', false);
			if (array_var($_GET, 'quick', false)) {
				ajx_current("empty");
				ajx_extra_data(array("task" => $task->getArrayInfo()));
			} else {
				ajx_current("reload");
			}
		} catch(Exception $e) {
			DB::rollback();
			flash_error(lang('error open task'));
		} // try
	} // open_task

	/**
	 * Create a new template
	 *
	 */
	function new_template() {
		if (logged_user()->isGuest()) {
			flash_error(lang('no access permissions'));
			ajx_current("empty");
			return;
		}
		
		$notAllowedMember = '';
		if(!ProjectTask::canAdd(logged_user(), active_context(), $notAllowedMember)) {
			if (str_starts_with($notAllowedMember, '-- req dim --')) flash_error(lang('must choose at least one member of', str_replace_first('-- req dim --', '', $notAllowedMember, $in)));
			else trim($notAllowedMember) == "" ? flash_error(lang('you must select where to keep', lang('the task'))) : flash_error(lang('no context permissions to add',lang("tasks"), $notAllowedMember));
			ajx_current("empty");
			return;
		} // if


		$id = get_id();
		$task = ProjectTasks::findById($id);
		if (!$task instanceof ProjectTask) {
			$task_data = array('is_template' => true);
		} else {
			$task_data = array(
				'milestone_id' => $task->getMilestoneId(),
				'title' => $task->getObjectName(),
				'assigned_to' => $task->getAssignedToContactId(),
				'parent_id' => $task->getParentId(),
				'priority' => $task->getPriority(),
				'time_estimate' => $task->getTimeEstimate(),
				'text' => $task->getText(),
				'is_template' => true,
				'copyId' => $task->getId(),
			); // array
			if ($task->getStartDate() instanceof DateTimeValue) {
				$task_data['start_date'] = $task->getStartDate()->getTimestamp();
			}
			if ($task->getDueDate() instanceof DateTimeValue) {
				$task_data['due_date'] = $task->getDueDate()->getTimestamp();
			}
		}

		$task = new ProjectTask();
		tpl_assign('task_data', $task_data);
		tpl_assign('task', $task);
		$this->setTemplate("add_task");
	} // new_template

	

	function allowed_users_to_assign() {
		
		$members = array();
		$member_ids = explode(',', array_var($_GET, 'member_ids'));
		if (count($member_ids) > 0) {
			$tmp_members = Members::findAll(array('conditions' => 'id IN ('.implode(',',$member_ids).')'));
			foreach ($tmp_members as $m) {
				if ($m->getDimension()->getIsManageable()) $members[] = $m;
			}
		}
		
		if (count($members) == 0) {
			$context_plain = array_var($_GET, 'context');
			$context = null;
			if (!is_null($context_plain)) $context = build_context_array($context_plain);
		} else {
			$context = $members;
		}
		
		$comp_array = allowed_users_to_assign($context);
		$object = array(
			"companies" => $comp_array
		);
		
		Hook::fire('modify_allowed_users_to_assign', null, $object);
		
		if(!can_manage_tasks(logged_user()) && can_task_assignee(logged_user())) $object['only_me'] = "1";
		
		ajx_extra_data($object);
		ajx_current("empty");
	} // allowed_users_to_assign

	function change_start_due_date() {
		$task = ProjectTasks::findById(get_id());
		if(!$task->canEdit(logged_user())){
			flash_error(lang('no access permissions'));
			ajx_current("empty");
			return ;
		}
	  
		$tochange = array_var($_GET, 'tochange', '');
	  
		if (($tochange == 'both' || $tochange == 'due') && $task->getDueDate() instanceof DateTimeValue ) {
			$year = array_var($_GET, 'year', $task->getDueDate()->getYear());
			$month = array_var($_GET, 'month', $task->getDueDate()->getMonth());
			$day = array_var($_GET, 'day', $task->getDueDate()->getDay());
			$hour = array_var($_GET, 'hour', $task->getDueDate()->getHour());
			$minute = array_var($_GET, 'min', $task->getDueDate()->getMinute());
			
			$new_date = new DateTimeValue(mktime($hour, $minute, 0, $month, $day, $year));
			if (isset($_GET['hour']) && isset($_GET['min'])) {
				$new_date->advance(logged_user()->getTimezone() * -3600);
			}
			$task->setDueDate($new_date);
		}
		if (($tochange == 'both' || $tochange == 'start') && $task->getStartDate() instanceof DateTimeValue ) {
			$year = array_var($_GET, 'year', $task->getStartDate()->getYear());
			$month = array_var($_GET, 'month', $task->getStartDate()->getMonth());
			$day = array_var($_GET, 'day', $task->getStartDate()->getDay());
			$hour = array_var($_GET, 'hour', $task->getStartDate()->getHour());
			$minute = array_var($_GET, 'min', $task->getStartDate()->getMinute());
			
			$new_date = new DateTimeValue(mktime($hour, $minute, 0, $month, $day, $year));
			if (isset($_GET['hour']) && isset($_GET['min'])) {
				$new_date->advance(logged_user()->getTimezone() * -3600);
			}
			$task->setStartDate($new_date);
		}
		
		try {
			DB::beginWork();
			$task->save();
			DB::commit();
	  	} catch(Exception $e) {
			DB::rollback();
			flash_error(lang('error change date'));
		} // try
		ajx_current("empty");
	}

	private function getRepeatOptions($task, &$occ, &$rsel1, &$rsel2, &$rsel3, &$rnum, &$rend, &$rjump) {
		//Repeating options
		$rsel1 = false;
		$rsel2 = false;
		$rsel3 = false;
		$rend = null;
		$rnum = null;
		$occ = 1;
		if($task->getRepeatD() > 0) {
			$occ = 2;
			$rjump = $task->getRepeatD();
		}
		if($task->getRepeatD() > 0 AND $task->getRepeatD()%7 == 0) {
			$occ = 3;
			$rjump = $task->getRepeatD() / 7;
		}
		if($task->getRepeatM() > 0) {
			$occ = 4;
			$rjump = $task->getRepeatM();
		}
		if($task->getRepeatY() > 0) {
			$occ = 5;
			$rjump = $task->getRepeatY();
		}
		if($task->getRepeatEnd()) $rend = $task->getRepeatEnd();
		if($task->getRepeatNum() > 0) $rnum = $task->getRepeatNum();
		if(!isset($rjump) || !is_numeric($rjump)) $rjump = 1;
		// decide which repeat type it is
		if($task->getRepeatForever()) $rsel1 = true; //forever
		else if(isset($rnum) AND $rnum > 0) $rsel2 = true; //repeat n-times
		else if(isset($rend) AND $rend instanceof DateTimeValue) $rsel3 = true; //repeat until
		//else $rsel1 = true; // default
	}

	private function setRepeatOptions(&$task_data) {
		// repeat options
		$repeat_d = 0;
		$repeat_m = 0;
		$repeat_y = 0;
		$repeat_h = 0;
		$rend = '';
		$forever = 0;
		$jump = array_var($task_data, 'occurance_jump');

		if(array_var($task_data, 'repeat_option') == 1) $forever = 1;
		elseif(array_var($task_data, 'repeat_option') == 2) $rnum = array_var($task_data, 'repeat_num');
		elseif(array_var($task_data, 'repeat_option') == 3) $rend = getDateValue(array_var($task_data, 'repeat_end'));
		// verify the options above are valid
		if (isset($rnum) && $rnum) {
			if(!is_numeric($rnum) || $rnum < 1 || $rnum > 1000) throw new Exception(lang('repeat x times must be a valid number between 1 and 1000'));
		} else $rnum = 0;

		if (isset($jump) && $jump) {
			if(!is_numeric($jump) || $jump < 1 || $jump > 1000) throw new Exception(lang('repeat period must be a valid number between 1 and 1000'));
		} else {
			$occurrance = array_var($task_data, 'occurance');
			if ($occurrance && $occurrance != 1)
				return lang('repeat period must be a valid number between 1 and 1000');
		}

		// check for repeating options
		// 1=repeat once, 2=repeat daily, 3=weekly, 4=monthy, 5=yearly, 6=holiday repeating
		$oend = null;
		switch(array_var($task_data, 'occurance')){
			case "1":
				$forever = 0;
				$task_data['repeat_d'] = 0;
				$task_data['repeat_m'] = 0;
				$task_data['repeat_y'] = 0;
				$task_data['repeat_by'] = '';
				break;
			case "2":
				$task_data['repeat_d'] = $jump;
				$task_data['repeat_m'] = 0;
				$task_data['repeat_y'] = 0;
				if(isset($forever) && $forever == 1) $oend = null;
				else $oend = $rend;
				break;
			case "3":
				$task_data['repeat_d'] = 7 * $jump;
				$task_data['repeat_m'] = 0;
				$task_data['repeat_y'] = 0;
				if(isset($forever) && $forever == 1) $oend = null;
				else $oend = $rend;
				break;
			case "4":
				$task_data['repeat_m'] = $jump;
				$task_data['repeat_d'] = 0;
				$task_data['repeat_y'] = 0;
				if(isset($forever) && $forever == 1) $oend = null;
				else $oend = $rend;
				break;
			case "5":
				$task_data['repeat_y'] = $jump;
				$task_data['repeat_d'] = 0;
				$task_data['repeat_m'] = 0;
				if(isset($forever) && $forever == 1) $oend = null;
				else $oend = $rend;
				break;
			default: break;
		}
		$task_data['repeat_num'] = $rnum;
		$task_data['repeat_forever'] = $forever;
		$task_data['repeat_end'] =  $oend;

		if ($task_data['repeat_num'] || $task_data['repeat_forever'] || $task_data['repeat_end']) {
			if ($task_data['repeat_by'] == 'start_date' && !$task_data['start_date'] instanceof DateTimeValue ) {
				return lang('to repeat by start date you must specify task start date');
			}
			if ($task_data['repeat_by'] == 'due_date' && !$task_data['due_date'] instanceof DateTimeValue ) {
				return lang('to repeat by due date you must specify task due date');
			}
		}
		return null;
	}

	function repetitive_task($task, $opt_rep_day){
		if($task->isRepetitive() && !$task->getRepeatForever()) {
			if ($task->getRepeatNum() > 0) {
				
				if ($task->getStartDate() instanceof DateTimeValue ) $original_st_date = $task->getStartDate();
				if ($task->getDueDate() instanceof DateTimeValue ) $original_due_date = $task->getDueDate();
					
				$task->setRepeatNum($task->getRepeatNum() - 1);
				while($task->getRepeatNum() > 0){
					$this->getNextRepetitionDates($task, $opt_rep_day, $new_st_date, $new_due_date);
					$task->setRepeatNum($task->getRepeatNum() - 1);
					// generate completed task
					$task->cloneTask($new_st_date,$new_due_date,true, false);
					// set next values for repetetive task
					if ($task->getStartDate() instanceof DateTimeValue ) $task->setStartDate($new_st_date);
					if ($task->getDueDate() instanceof DateTimeValue ) $task->setDueDate($new_due_date);
					foreach ($task->getAllSubTasks() as $subt) {
						$subt->setCompletedById(0);
						$subt->setCompletedOn(EMPTY_DATETIME);
						$subt->save();
					}
					$task->save();
				}
				if (isset($original_due_date)) $task->setDueDate($original_due_date);
				if (isset($original_st_date)) $task->setStartDate($original_st_date);
				$task->save();

			}elseif ($task->getRepeatForever() == 0) {
				$task_end = $task->getRepeatEnd();
				$new_st_date = "";
				$new_due_date = "";
				while($task->getRepeatBy() == 'start_date' && $new_st_date <= $task_end ||
				$task->getRepeatBy() == 'due_date' && $new_due_date <= $task_end){
					 
					$this->getNextRepetitionDates($task, $opt_rep_day, $new_st_date, $new_due_date);
					// generate completed task
					$task->cloneTask($new_st_date,$new_due_date,true, false);
					// set next values for repetetive task
					if ($task->getStartDate() instanceof DateTimeValue ) $task->setStartDate($new_st_date);
					if ($task->getDueDate() instanceof DateTimeValue ) $task->setDueDate($new_due_date);
					foreach ($task->getAllSubTasks() as $subt) {
						$subt->setCompletedById(0);
						$subt->setCompletedOn(EMPTY_DATETIME);
						$subt->save();
					}
				}
			}
			$task->setRepeatEnd(EMPTY_DATETIME);
			$task->setRepeatNum(0);
			$task->setRepeatD(0);
			$task->setRepeatM(0);
			$task->setRepeatY(0);
			$task->setRepeatBy("");
			$task->save();
		}
	}
        
        function repetitive_tasks_related($task,$action,$type_related = "",$task_data = array()){
            //I find all those related to the task to find out if the original
            $task_related = ProjectTasks::findByRelated($task->getObjectId());
            if(!$task_related){
                //is not the original as the original look plus other related
                if($task->getOriginalTaskId() != "0"){
                    $task_related = ProjectTasks::findByTaskAndRelated($task->getObjectId(),$task->getOriginalTaskId());
                }
            }            
            if($task_related){
                switch($action){
                        case "edit":
                                foreach ($task_related as $t_rel){
                                    if($type_related == "news"){
                                        if($task->getStartDate() <= $t_rel->getStartDate() && $task->getDueDate() <= $t_rel->getDueDate()){
                                            $this->repetitive_task_related_edit($t_rel,$task_data);
                                        }
                                    }else{
                                        $this->repetitive_task_related_edit($t_rel,$task_data);
                                    }                                    
                                }
                        break;
                        case "delete":
                                $delete_task = array();
                                foreach ($task_related as $t_rel){
                                    $task_rel = Objects::findObject($t_rel->getId());   
                                    if($type_related == "news"){
                                        if($task->getStartDate() <= $t_rel->getStartDate() && $task->getDueDate() <= $t_rel->getDueDate()){
                                            $delete_task[] = $t_rel->getId();                                                                             
                                            $task_rel->trash(); 
                                        }
                                    }else{
                                        $delete_task[] = $t_rel->getId();                                                                             
                                        $task_rel->trash(); 
                                    }                                                                        
                                }
                                return $delete_task;
                        break;
                        case "archive":
                                $archive_task = array();
                                foreach ($task_related as $t_rel){
                                    $task_rel = Objects::findObject($t_rel->getId());                                    
                                    if($type_related == "news"){
                                        if($task->getStartDate() <= $t_rel->getStartDate() && $task->getDueDate() <= $t_rel->getDueDate()){
                                            $archive_task[] = $t_rel->getId();                                                                            
                                            $t_rel->archive();  
                                        }
                                    }else{
                                        $archive_task[] = $t_rel->getId();                                                                            
                                        $t_rel->archive();
                                    }
                                }
                                return $archive_task;
                        break;
                }
            }
            
        }
        
        function repetitive_task_related_edit($task,$task_data){
            $was_template = $task->getIsTemplate();
            $task->setFromAttributes($task_data);
            $task->setIsTemplate($was_template); // is_template value must not be changed from ui

            $totalMinutes = (array_var($task_data, 'time_estimate_hours') * 60) + (array_var($task_data, 'time_estimate_minutes'));
            $task->setTimeEstimate($totalMinutes);

            if ($task->getParentId() > 0 && $task->hasChild($task->getParentId())) {
                    flash_error(lang('task child of child error'));
                    ajx_current("empty");
                    return;
            }

            DB::beginWork();
            $task->save();            
            
            $task->setObjectName(array_var($task_data, 'name'));
            $task->save();   

            // dependencies
            if (config_option('use tasks dependencies')) {
                    $previous_tasks = array_var($task_data, 'previous');
                    if (is_array($previous_tasks)) {
                            foreach ($previous_tasks as $ptask) {
                                    if ($ptask == $task->getId()) continue;
                                    $dep = ProjectTaskDependencies::findById(array('previous_task_id' => $ptask, 'task_id' => $task->getId()));
                                    if (!$dep instanceof ProjectTaskDependency) {
                                            $dep = new ProjectTaskDependency();
                                            $dep->setPreviousTaskId($ptask);
                                            $dep->setTaskId($task->getId());
                                            $dep->save();
                                    }
                            }

                            $saved_ptasks = ProjectTaskDependencies::findAll(array('conditions' => 'task_id = '. $task->getId()));
                            foreach ($saved_ptasks as $pdep) {
                                    if (!in_array($pdep->getPreviousTaskId(), $previous_tasks)) $pdep->delete();
                            }
                    } else {
                            ProjectTaskDependencies::delete('task_id = '. $task->getId());
                    }
            }

            // Add assigned user to the subscibers list
            if ($task->getAssignedToContactId() > 0  && Contacts::instance()->findById( $task->getAssignedToContactId()) ) {
                    if (!isset($_POST['subscribers'])) $_POST['subscribers'] = array();
                    $_POST['subscribers']['user_'.$task->getAssignedToContactId()] = 'checked';
            }

            $object_controller = new ObjectController();
            $object_controller->add_to_members($task, array_var($task_data, 'members'));
            $object_controller->add_subscribers($task);
            $object_controller->link_to_new_object($task);
            $object_controller->add_custom_properties($task);
            $object_controller->add_reminders($task);

            // apply values to subtasks
            $assigned_to = $task->getAssignedToContactId();
            $subtasks = $task->getAllSubTasks();
            $milestone_id = $task->getMilestoneId();
            $apply_ms = array_var($task_data, 'apply_milestone_subtasks') == "checked";
            $apply_at = array_var($task_data, 'apply_assignee_subtasks', '') == "checked";
            foreach ($subtasks as $sub) {
                    $modified = false;
                    if ($apply_at || !($sub->getAssignedToContactId() > 0)) {
                            $sub->setAssignedToContactId($assigned_to);
                            $modified = true;
                    }
                    if ($apply_ms) {
                            $sub->setMilestoneId($milestone_id);
                            $modified = true;
                    }
                    if ($modified) {
                            $sub->save();
                    }
            }

            $task->resetIsRead();

            DB::commit();
            ApplicationLogs::createLog($task, ApplicationLogs::ACTION_EDIT);
        }
        
        function check_related_task(){
            ajx_current("empty");
            //I find all those related to the task to find out if the original
            $task_related = ProjectTasks::findByRelated(array_var($_REQUEST, 'related_id'));
            if(!$task_related){
                $task_related = ProjectTasks::findById(array_var($_REQUEST, 'related_id'));
                //is not the original as the original look plus other related
                if($task_related->getOriginalTaskId() != "0"){
                    ajx_extra_data(array("status" => true));
                }else{
                    ajx_extra_data(array("status" => false));
                }                
            }else{
                ajx_extra_data(array("status" => true));
            }
        }

        function correct_days_task_repetitive($date, $repeat_saturday = false, $repeat_sunday = false){
            if($date != ""){
                $working_days = explode(",",config_option("working_days"));
                if($repeat_saturday) $working_days[] = 6;
                if($repeat_sunday) $working_days[] = 0;
                if(!in_array(date("w",  $date->getTimestamp()), $working_days)){
                    $date = $date->add('d', 1);
                    $this->correct_days_task_repetitive($date);
                }
            }
            return $date;
        }

} // TaskController

?>
