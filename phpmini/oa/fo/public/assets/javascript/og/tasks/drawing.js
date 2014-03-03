/**
 * drawing.js
 *
 * This module holds the rendering logic for groups and tasks
 *
 * @author Carlos Palma <chonwil@gmail.com>
 */

//************************************
//*		<RX : dragging
//************************************

var rx__dd = 1000;
rx__TasksD = Ext.extend(Ext.dd.DDProxy, {
startDrag: function(x, y) {
	var dragEl = Ext.get(this.getDragEl());
	var el = Ext.get(this.getEl());
	
	if (!Ext.isIE) dragEl.applyStyles({'border':'1px solid gray;','border-width':'1px 1px 1px 6px','width':'auto','height':'auto','cursor':'move'});
	else dragEl.setWidth('auto');
	var task = ogTasks.getTask(this.config.dragData.i_t);	
	dragEl.update(task.title);
	dragEl.addClass(el.dom.className + ' RX__tasks_dd-proxy'); 
},
onDragOver: function(e, targetId) {
    var target = Ext.get(targetId);
	if(targetId.indexOf(rx__TasksDrag.idGroup)>=0) /* group */ {
        this.lastTargetId = targetId;		
		this.lastGroupTargetId = targetId;
        target.addClass('RX__tasks_dd-over');
	}else if(targetId.indexOf(rx__TasksDrag.idTask)>=0) /* task */ {
        this.lastTargetId = targetId;				
        target.addClass('RX__tasks_dd-over');
	}else{
		//XXX: mark wrong target, check other options
	}
},
onDragOut: function(e, targetId) {
    var target = Ext.get(targetId);
	if(targetId.indexOf(rx__TasksDrag.idGroup)>=0) /* group */ {
        this.lastTargetId = ''; //targetId;		
        target.removeClass('RX__tasks_dd-over');
	}else if(targetId.indexOf(rx__TasksDrag.idTask)>=0) /* task */ {
        this.lastTargetId = this.lastGroupTargetId;				
        target.removeClass('RX__tasks_dd-over');
	}else{
		//XXX: mark wrong target, check other options
	}
},
endDrag: function() {
    var dragEl = Ext.get(this.getDragEl());
    var el = Ext.get(this.getEl());
	if(this.lastGroupTargetId) 
		Ext.get(this.lastGroupTargetId).removeClass('RX__tasks_dd-over');
	if(this.lastTargetId) 
		Ext.get(this.lastTargetId).removeClass('RX__tasks_dd-over');
		
	var targetId = this.lastTargetId;
	rx__TasksDrag.d = rx__TasksDrag.haveExtDD[this.lastGroupTargetId];
	rx__TasksDrag.p = rx__TasksDrag.haveExtDD[this.lastTargetId];
	rx__TasksDrag.t = this.config.dragData.i_t;
	rx__TasksDrag.g = this.config.dragData.i_g;
	this.lastTargetId = null;
	this.lastGroupTargetId = null;
	
	var doProcess = false;
	if (targetId) {
		if(targetId.indexOf(rx__TasksDrag.idGroup)>=0) /* group */ {
			doProcess = true;
			rx__TasksDrag.p = false;
		}else if(targetId.indexOf(rx__TasksDrag.idTask)>=0) /* task */ {
			doProcess = true;
		}else{
			//XXX: mark wrong target
		}
	}
	
	if(doProcess) {
		rx__TasksDrag.process();
		/*/ alert('From '+rx__TasksDrag.g+'.'+rx__TasksDrag.t+' to '+rx__TasksDrag.d+'.'+rx__TasksDrag.p+' ('+rx__TasksDrag.displayCriteria.group_by+')'); /* */
		//alert(dump(ogTasks.Groups));
	}else{
		//alert(targetId);
	}
}
});

var rx__TasksDrag = {
	t: false,
	g: false,
	d: false,
	p: false,
	// (g::t)-->(d::p)
	displayCriteria: '',
	allowDrag: false,
	state: 'no',
	haveExtDD: {},
	full_redraw: false,

	classGroup: 'ogTasksGroup', //'ogTasksGroupHeader',
	idGroup: 'ogTasksPanelGroupCont', // 'ogTasksPanelGroup',
	classTask: 'ogTasksTaskTable',
	idTask: 'ogTasksPanelTaskTable',
	ddGroup: 'WorkspaceDD', // group
	dzClass: 'rx__hasDZ',
	
	initialize: function() {
		this.haveExtDD = {};
	},
	prepareExt: function(t,g,id) {
		if(this.haveExtDD[id]) return;
		Ext.get(id).dd = new rx__TasksD(id, rx__TasksDrag.ddGroup, { scope: this, dragData: {i_t:t, i_g: g} });
		new Ext.dd.DropZone(id, {ddGroup: rx__TasksDrag.ddGroup});
		this.haveExtDD[id] = t; // true
		this.prepareDrops();
	},
	prepareDrops: function() {
		Ext.select('.'+rx__TasksDrag.classGroup).each( function(el) {
			if(el.hasClass(rx__TasksDrag.dzClass)) return;
			el.addClass(rx__TasksDrag.dzClass);
			id = el.dom.id;
			new Ext.dd.DropZone(id, {ddGroup: rx__TasksDrag.ddGroup});
			d = id.substr(rx__TasksDrag.idGroup.length, 66);
			rx__TasksDrag.haveExtDD[id] = d;
		} );
		Ext.select('.'+rx__TasksDrag.classTask).each( function(el) {
			if(el.hasClass(rx__TasksDrag.dzClass)) return;
			el.addClass(rx__TasksDrag.dzClass);
			id = el.dom.id;
			new Ext.dd.DropZone(id, {ddGroup: rx__TasksDrag.ddGroup});
			d = new String( id.substr(rx__TasksDrag.idTask.length, 66) );
			d = d.substr(1,d.indexOf('G')-1); // format: T{task_id}G{group_id}
			rx__TasksDrag.haveExtDD[id] = d;
		} );
	},
	prepareDrop: function(d,id) {
		if(this.haveExtDD[id]) return;
		/*Ext.get(id).dd =*/ new Ext.dd.DropZone(id, {ddGroup: rx__TasksDrag.ddGroup});
		this.haveExtDD[id] = d;
	},
	parametersFromTask: function(task) {
		var parameters = [];
		
		// mandatory
		parameters["assigned_to_contact_id"] = task.assignedToId;
		parameters["milestone_id"] = task.milestoneId;
		parameters["priority"] = task.priority;
		parameters["title"] = task.title;
		parameters["text"] = task.description;
		
		var ehours = Math.floor(task.TimeEstimate / 60);
		var emins = task.TimeEstimate - ehours*60;
		parameters["hours"] = ehours;
		parameters["minutes"] = emins;
		
		// add dates to parameters
		if (task.dueDate) {
			var d1 = new Date();
			var seconds = task.dueDate + (task.useDueTime ? -og.loggedUser.tz * 3600 : 0);
			d1.setTime(seconds * 1000);
			parameters["task_due_date"] = d1.format(og.preferences['date_format']);
			if (task.useDueTime) {
				parameters["use_due_time"] = true;
				parameters["task_due_time"] = d1.format(og.config.time_format_use_24_duetime);
			}
		}
		if (task.startDate) {
			var d2 = new Date();
			var seconds = task.startDate + (task.useStartTime ? -og.loggedUser.tz * 3600 : 0);
			d2.setTime(seconds * 1000);
			parameters["task_start_date"] = d2.format(og.preferences['date_format']);
			if (task.useStartTime) {
				parameters["use_start_time"] = true;
				parameters["task_start_time"] = d2.format(og.config.time_format_use_24_duetime);
			}
		}
		
		return parameters;
	},
	quickEdit: function(task_id, parameters) {
		// wrap
		var params2 = [];
		for (var i in parameters) {
			if (parameters[i] || parameters[i] === 0) {
				params2["task[" + i + "]"] = parameters[i];
			}
		}
		
		parameters = params2;
		var url = og.getUrl('task', 'quick_edit_task', {id:task_id, dont_mark_as_read:1});
	
		og.openLink(url, {
			method: 'POST',
			post: parameters,
			callback: function(success, data) {
				if (success && ! data.errorCode) {
					var task = ogTasks.getTask(data.task.id);
					if (!task){
						var task = new ogTasksTask();
						task.setFromTdata(data.task);
						if (data.task.s) {
							task.statusOnCreate = data.task.s;
						}
						task.isCreatedClientSide = true;
						ogTasks.Tasks[ogTasks.Tasks.length] = task;
						var parent = ogTasks.getTask(task.parentId);
						if (parent){
							task.parent = parent;
							parent.subtasks[parent.subtasks.length] = task;
						}
					} else {
						task.setFromTdata(data.task);
						var parent = ogTasks.getTask(task.parentId);
						if (parent){
							task.parent = parent;
							parent.subtasks[parent.subtasks.length] = task;
						}
					}
					
					if (data.subtasks && data.subtasks.length > 0)
						ogTasks.setSubtasksFromData(task, data.subtasks);
					
					if(!rx__TasksDrag.full_redraw) ogTasks.redrawGroups = false;
					else rx__TasksDrag.full_redraw = true;
					ogTasks.draw();
					ogTasks.redrawGroups = true;
					rx__TasksDrag.haveExtDD = {};
				} else {
					if (!data.errorMessage || data.errorMessage == '') {
						og.err(lang("error adding task"));
					}
				}
			},
			scope: ogTasks
		});
		
	},
	process: function() {
		var task = ogTasks.getTask(this.t);
		this.p = parseInt(this.p);
		
		// non-edits
		if (this.g == this.d && !this.p) {
			// task is being dragged from group #G to group #G
			if (task.parentId != 0) {
				// however, the intention might be to un-attach the task from its parent (!)
				this.p = 0;
			} else return;
		}
		if (task.parentId == this.d && task.parentId) {// is the task being dragged as a subtask o its own parent?
			return;
		}

		// check for unwanted cycles - #t cannot be a predecessor of #p 
		var ti = this.p;
		var tiQ = {};
		while(ti!=0 && !tiQ[ti]) {
			if(ti == this.t) return;
			var tt = ogTasks.getTask(ti);
			if(!tt) break;
			tiQ[ti] = 1; // loop protection - mark visited vertices
			ti = tt.parentId;
		}
		
		// unattach from current parent
		if(task.parentId) {
			// delete task #t from the list of its parent subtasks 
			var parent = ogTasks.getTask(task.parentId);
			for(var i=parent.subtasks.length; i-->0;) 
				if(parent.subtasks[i].id == this.t)
				{
					parent.subtasks.splice(i,1);
					break;
				}
			// change task #t parent to #0
			for (var i = 0; i < ogTasks.Tasks.length; i++)
				if (ogTasks.Tasks[i].id == this.t) {
					ogTasks.Tasks[i].parentId = 0;
					ogTasks.Tasks[i].parent = null;
					break;
				}

		}
		
		var parameters = this.parametersFromTask(task);
		
		// special edits
		switch(this.displayCriteria.group_by) {
			case 'status': ogTasks.ToggleCompleteStatus(task.id, 1-this.d); return; break;
			default:
		}

		parameters['parent_id'] = this.p?this.p:0;
		parameters['apply_ws_subtasks'] = "checked";
		parameters['apply_milestone_subtasks'] = "checked";
	
		var group = ogTasks.getGroup(this.d);
		var group_not_empty = group && group.group_tasks && group.group_tasks.length > 0;
		
		// change
		switch (this.displayCriteria.group_by){
			case 'milestone':	parameters["milestone_id"] = this.d != 'unclassified' ? ogTasks.getMilestone(this.d).id : 0; break;
			case 'priority':	parameters["priority"] = this.d != 'unclassified' ? parseInt(this.d) : 200; /*100,200,300*/ break;
			case 'assigned_to':	parameters["assigned_to_contact_id"] = this.d; break;
			case 'due_date' : 	if(group_not_empty) parameters["task_due_date"] = group.group_tasks[0].dueDate; break;
			case 'start_date' : if(group_not_empty) parameters["task_start_date"] = group.group_tasks[0].startDate; break;
			case 'created_on' : if(group_not_empty) parameters["created_on"] = group.group_tasks[0].createdOn; break;
			case 'completed_on':if(group_not_empty) parameters["completed_on"] = group.group_tasks[0].completedOn.toString().format(lang('date format')); break;
			case 'created_by' :	parameters["created_by"] = this.d; /* ? */ break;
			case 'status' : 	parameters["status"] = this.d; /* done previously, special request */ break;
			case 'completed_by':parameters["completed_by"] = this.d; /* ? */ break;
			case 'subtype':parameters["object_subtype"] = this.d; /* ? */ break;
			default:
				if (this.displayCriteria.group_by.indexOf('dimension_') == 0) {
					// Group by dimension
					var dim_id = this.displayCriteria.group_by.replace('dimension_', '');
					parameters['member_id'] = this.d;
					parameters['remove_from_dimension'] = dim_id;
				}
				break;
		}
		
		rx__TasksDrag.full_redraw = true;
		task_id = this.t;
		this.quickEdit(task_id, parameters);
		
	},
	onDragStart: function(t,g,id) {
		return false;
		/*if(this.state!='no') return false;
		this.t=t;
		this.g=g;
		this.state = 'md';
		return false;*/
	},
	last_oDO_e: null,
	markCursor: function(e,d) {
		if(this.last_oDO_e)
			this.last_oDO_e.style.cursor = 'auto';
		if(e)
			e.style.cursor = (d==this.g?'not-allowed':'crosshair')+' !important';
		this.last_oDO_e = e;
	},
	onDragOver: function(e,d) {
		if(this.state!='md') return false;
		if(this.last_oDO_e==e) return false;
			else this.markCursor(e,d);
		return false;
	},
	onDrop: function(d) {
		if(this.state!='md') return false;
		this.markCursor(null,d);
		this.d=d;
		this.state = 'no';
		return false;
	},
	showHandle: function(id,v) {
		if(!rx__TasksDrag.allowDrag || og.loggedUser.isGuest) return;
		var o = document.getElementById('RX__ogTasksPanelDrag'+id);
		var ine = Ext.get('ogTasksPanelAT');
		if(ine) if(ine.isVisible()) v = false;
		if(o) o.style.visibility = v?'visible':'hidden';
	}
};


//************************************
//*		Main function
//************************************

ogTasks.draw = function(){
	var start = new Date(); 
	if (this.redrawGroups)
		this.Groups = [];
	for (var i = 0; i < this.Tasks.length; i++)
		this.Tasks[i].divInfo = [];

	var bottomToolbar = Ext.getCmp('tasksPanelBottomToolbarObject');
	var topToolbar = Ext.getCmp('tasksPanelTopToolbarObject');
	var displayCriteria = bottomToolbar.getDisplayCriteria();
	var drawOptions = topToolbar.getDrawOptions();
	this.Groups = this.groupTasks(displayCriteria, this.Tasks);
	for (var i = 0; i < this.Groups.length; i++){
		this.Groups[i].group_tasks = this.orderTasks(displayCriteria, this.Groups[i].group_tasks);
	}
	
	// *** <RX ***
	rx__TasksDrag.displayCriteria = displayCriteria;
	rx__TasksDrag.allowDrag = false;
	if( displayCriteria.group_by=='milestone' || displayCriteria.group_by=='priority' || displayCriteria.group_by=='assigned_to' 
		|| displayCriteria.group_by=='status' || displayCriteria.group_by=='subtype' || displayCriteria.group_by.indexOf('dimension_') == 0) {
		
		rx__TasksDrag.allowDrag = true;
	}
	// *** /RX ***
	
	//Drawing
	var sb = new StringBuffer();
	for (var i = 0; i < this.Groups.length; i++){
		if (i != (this.Groups.length-1) || this.Groups[i].group_tasks.length > 0) { //If there are no unclassified or unassigned tasks, do not show unassigned group
			if (ogTasks.userPreferences.showEmptyMilestones == 0 && displayCriteria.group_by == 'milestone' && this.Groups[i].group_tasks.length == 0) continue;
			sb.append(this.drawGroup(displayCriteria, drawOptions, this.Groups[i]));
		}
	}
	
	// *** <RX ***
	if(this.Groups.length==1 && this.Groups[0].group_tasks.length==0) {
		var context_names = og.contextManager.getActiveContextNames();
		if (context_names.length == 0) context_names.push(lang('all'));
		
		sb.append('<div id="rx__no_tasks_info">' +
		'<button title="' + lang('add task') + '" class="no-tasks-add-task-btn add-first-btn"' + 
			'onClick="document.getElementById(\'rx__no_tasks_info\').style.display=\'none\'; document.getElementById(\'rx__hidden_group\').style.display=\'block\'; ogTasks.drawAddNewTaskForm(\'' + this.Groups[0].group_id + '\')">' + '<img src="public/assets/themes/default/images/16x16/add.png"/>&nbsp;' +
			(lang('add first task')) + '</button>'+
			'<div class="inner-message">'+lang('no tasks to display', '"'+context_names.join('", "')+'"')+ '</div>'+
		'</div>');
		var rx__hidden_group = new String();
		rx__hidden_group = this.drawGroup(displayCriteria, drawOptions, this.Groups[0]);
		rx__hidden_group = '<div id="rx__hidden_group" style="display: none;">'+rx__hidden_group+'</div>';
		sb.append(rx__hidden_group);
	}
	// *** /RX ***
	
	var container = document.getElementById('tasksPanelContainer');
	sb.append("<div style='height:20px'></div>")
	container.innerHTML = sb.toString();
}

ogTasks.toggleSubtasks = function(taskId, groupId){
	var subtasksDiv = document.getElementById('ogTasksPanelSubtasksT' + taskId + 'G' + groupId);
	var expander = document.getElementById('ogTasksPanelFixedExpanderT' + taskId + 'G' + groupId);
	var task = this.getTask(taskId);
	if (subtasksDiv){
		task.isExpanded = !task.isExpanded;
		subtasksDiv.style.display = (task.isExpanded)? 'block':'none';
		expander.className = "og-task-expander " + ((task.isExpanded)?'toggle_expanded':'toggle_collapsed');
	}
}

ogTasks.loadAllDescriptions = function(task_ids) {
	ogTasks.all_descriptions_loaded = false;
	og.openLink(og.getUrl('task', 'get_task_descriptions'), {
		hideLoading: true,
		scope: this,
		method: 'POST',
		post: {ids: task_ids.join(',')},
		callback: function(success, data) {
			for (i=0; i<ogTasks.Tasks.length; i++) {
				var task = ogTasks.Tasks[i];
				if (data.descriptions['t'+task.id]) {
					task.description = data.descriptions['t'+task.id];
				}
			}
			ogTasks.all_descriptions_loaded = true;
		}
	});
}

//************************************
//*		Draw group
//************************************

ogTasks.drawMilestoneCompleteBar = function(group){
	var html = '';
	var milestone = this.getMilestone(group.group_id);
	if (!milestone) return html;
	var complete = 0;
	var completedTasks = parseInt(milestone.completedTasks);
	var totalTasks =  parseInt(milestone.totalTasks);
	var tasks = this.flattenTasks(group.group_tasks);
	for (var i = 0; i < tasks.length; i++){
		var t = tasks[i];
		if (t.milestoneId == group.group_id){
			completedTasks += (t.status == 1 && (t.statusOnCreate == 0))? parseInt(1) : parseInt(0);
			completedTasks -= (t.status == 0 && (t.statusOnCreate == 1))? parseInt(1) : parseInt(0);
			totalTasks = (t.isCreatedClientSide)? totalTasks + parseInt(1) : totalTasks + parseInt(0);
		}
	}
	if (totalTasks > 0)
		complete = ((100 * completedTasks) / totalTasks);
	html += "<table><tr><td style='padding-left:15px;padding-top:5px'>" +
	"<table style='height:7px;width:50px'><tr><td style='height:7px;width:" + (complete) + "%;background-color:#6C2'></td><td style='width:" + (100 - complete) + "%;background-color:#DDD'></td></tr></table>" +
	"</td><td style='padding-left:3px;line-height:12px'><span style='font-size:8px;color:#AAA'>(" + completedTasks + '/' +  totalTasks + ")</span></td></tr></table>";

	return html;			
}

ogTasks.drawGroup = function(displayCriteria, drawOptions, group){
	var sb = new StringBuffer();
	
		// **** <RX : dragging **** //
	//sb.append('<script>rx__TasksDrag.prepareDrop(\"" + group.group_id + "\",this.id);</scr'+'ipt>');
	//rx__TasksDrag.haveExtDD['ogTasksPanelGroupCont'+group.group_id] = group.group_id;
	sb.append("<div id='ogTasksPanelGroupCont" + group.group_id + "' class='ogTasksGroup' style='display:" + ((this.existsSoloGroup() && !group.solo)? 'none':'block') + "'><div id='ogTasksPanelGroup" + group.group_id + "' class='ogTasksGroupHeader' onmouseover='ogTasks.mouseMovement(null,\"" + group.group_id + "\",true)' onmouseout='ogTasks.mouseMovement(null,\"" + group.group_id + "\", false)'>");
	sb.append("<table width='100%'><tr>");
	sb.append('<td style="width:20px"><div onclick="ogTasks.expandCollapseAllTasksGroup(\'' + group.group_id + '\')" class="og-task-expander toggle_expanded" id="ogTasksPanelGroupExpanderG' + group.group_id + '"></div></td>');
	sb.append('<td style="width:20px" title="'+lang('select all tasks')+'"><input style="width:14px;height:14px" type="checkbox" id="ogTasksPanelGroupChk' + group.group_id + '" ' + (group.isChecked?'checked':'') + ' onclick="ogTasks.GroupSelected(this,\'' + group.group_id + '\')"/></td>');
	
	sb.append("<td width='20px'><div class='db-ico " + group.group_icon + "'></div></td>");
	
	sb.append('<td>');
	switch (displayCriteria.group_by){
		case 'milestone':
			var milestone = this.getMilestone(group.group_id);
			if (milestone){
				if (milestone.isUrgent){
					sb.append("</td><td><div class='db-ico ico-urgent-milestone'></div></td><td>");
				}
				sb.append("<table><tr><td><div class='ogTasksGroupHeaderName'>");
				if (milestone.completedById){
					var user = this.getUser(milestone.completedById, true);
					var tooltip = '';
					if (user){
						var time = new Date(milestone.completedOn * 1000);
						var now = new Date();
						var timeFormatted = time.getYear() != now.getYear() ? time.dateFormat('M j, Y'): time.dateFormat('M j');
						tooltip = lang('completed by name on', og.clean(user.name), timeFormatted).replace(/'\''/g, '\\\'');
					}
					sb.append("<a href='#' style='text-decoration:line-through' class='internalLink' onclick='og.openLink(\"" + og.getUrl('milestone', 'view', {id: group.group_id}) + "\")' title='" + tooltip + "'>" + og.clean(group.group_name) + '</a></div></td>');
				}
				else
					sb.append("<a href='#' class='internalLink' onclick='og.openLink(\"" + og.getUrl('milestone', 'view', {id: group.group_id}) + "\")'>" + og.clean(group.group_name) + '</a></div></td>');
				
			} else {
				sb.append("<table><tr><td><div class='ogTasksGroupHeaderName'>" + og.clean(group.group_name) + '</div></td>');
			}
			sb.append("</tr></table>");
			break;
		default:
			sb.append("<div class='ogTasksGroupHeaderName'>" + og.clean(group.group_name) + '</div>');
	}
	sb.append("</td><td align='right'>");
	var transparent_style = "opacity:0.35;filter:alpha(opacity=35);";
	if (displayCriteria.group_by == 'milestone' && this.getMilestone(group.group_id)){
		var milestone = this.getMilestone(group.group_id);
		sb.append("<table><tr>");
		if (drawOptions.show_dates){
			sb.append('<td><span style="padding-left:12px;color:#888;">');
			var date = new Date();
			date.setTime((milestone.dueDate + date.getTimezoneOffset()*60)* 1000);
			var now = new Date();
			var dateFormatted = date.getYear() != now.getYear() ? date.dateFormat('M j, Y'): date.dateFormat('M j');
			if (milestone.completedById > 0){
				sb.append('<span style="text-decoration:line-through">' +  lang('due') + ':&nbsp;' + dateFormatted + '</span>');
			} else {
				if ((date < now))
					sb.append('<span style="font-weight:bold;color:#F00">' + lang('due') + ':&nbsp;' + dateFormatted + '</span>');
				else
					sb.append(lang('due') + ':&nbsp;' + dateFormatted);
			}
			sb.append('</span></td>');
		}
		sb.append("<td><div id='ogTasksPanelCompleteBar" + group.group_id + "'>" + this.drawMilestoneCompleteBar(group) + "</div></td>");
		sb.append("<td><div class='ogTasksGroupHeaderActions' style='"+transparent_style+"padding-left:15px' id='ogTasksPanelGroupActions" + group.group_id + "'>" + this.drawGroupActions(group) + '</div></td></tr></table>');
	} else {
		sb.append("<div class='ogTasksGroupHeaderActions' style='"+transparent_style+"' id='ogTasksPanelGroupActions" + group.group_id + "'>" + this.drawGroupActions(group) + '</div>');
	}
	sb.append('</td></tr></table></div>');
	
	sb.append("<div id='ogTasksPanelTaskRowsContainer" + group.group_id + "'>");
	//draw the group's tasks
	var time_estimated = 0;
	group.isExpanded = ogTasks.expandedGroups.indexOf(group.group_id) > -1;
	for (var i = 0; i < group.group_tasks.length; i++){
		if (i == og.noOfTasks){//Draw expander if group has more than og.noOfTasks tasks
			sb.append("<div class='ogTasksTaskRow' style='display:" + (group.isExpanded? "none" : "inline") + "' id='ogTasksGroupExpandTasksTitle" + group.group_id + "'>");
			sb.append("<a href='#' class='internalLink' onclick='ogTasks.expandGroup(\"" + group.group_id + "\")'>" + lang('show more tasks number', (group.group_tasks.length - i)) + "</a>");
			sb.append("</div>");
			sb.append("<div id='ogTasksGroupExpandTasks" + group.group_id + "'>");
			if (group.isExpanded){
				for (var j = og.noOfTasks; j < group.group_tasks.length; j++){
					sb.append(this.drawTask(group.group_tasks[j], drawOptions, displayCriteria, group.group_id, 1));
				}
			}
			sb.append("</div>");
			break;
		}
		sb.append(this.drawTask(group.group_tasks[i], drawOptions, displayCriteria, group.group_id, 1));
	}

	for (var c = 0; c < group.group_tasks.length; c++){
		if (group.group_tasks[c].subtasks.length > 0){
			time_estimated += this.subtasksTimeEstimate(Number(group.group_tasks[c].TimeEstimate), group.group_tasks[c], displayCriteria);
		}else{
			time_estimated += Number(group.group_tasks[c].TimeEstimate);
		}
	}

	if (drawOptions.show_time_estimates) {
		var total_estimate_split = Math.round(time_estimated * 100 / 60) / 100;
		var total_estimate = (total_estimate_split + '').split(".");
		var hours_estimate = total_estimate[0] + " " + lang('hours');
		var minutes_estimate = "";
		if (total_estimate[1]) {
			if (total_estimate[1].length == 1) {
				minutes_estimate = ", " + Math.round(((total_estimate[1] * 60) / 10)) + " " + lang('minutes');
			} else {
				minutes_estimate = ", " + Math.round(((total_estimate[1] * 60) / 100)) + " " + lang('minutes');
			}
			var format_total_estimate = hours_estimate + minutes_estimate;
		} else {
			var format_total_estimate = hours_estimate;
		}
		sb.append("<div style='float:right;'><span style='font-weight:bold;color:#888'>" + lang('estimated time') + ':&nbsp;' + format_total_estimate + "</span>");
	}
	sb.append("</div></div></div>");
	return sb.toString();
}

ogTasks.drawGroupActions = function(group){
	var html = '<a id="ogTasksPanelGroupSoloOn' + group.group_id + '" style="margin-right:15px;display:' + (group.solo? "none" : "inline") + '" href="#" class="internalLink" onClick="ogTasks.hideShowGroups(\'' + group.group_id + '\')" title="' + lang('hide other groups') + '">' + (lang('hide others')) + '</a>' +
	'<a id="ogTasksPanelGroupSoloOff' + group.group_id + '" style="display:' + (group.solo? "inline" : "none") + ';margin-right:15px;" href="#" class="internalLink" onClick="ogTasks.hideShowGroups(\'' + group.group_id + '\')" title="' + lang('show all groups') + '">' + (lang('show all')) + '</a>' +
	'<a href="#" class="internalLink ogTasksGroupAction ico-print" style="margin-right:15px;" onClick="ogTasks.printGroup(\'' + group.group_id + '\')" title="' + lang('print this group') + '">' + (lang('print')) + '</a>';
	if (ogTasks.userPermissions.can_add) {
		html += '<a href="#" class="internalLink ogTasksGroupAction ico-add" onClick="ogTasks.drawAddNewTaskForm(\'' + group.group_id + '\')" title="' + lang('add a new task to this group') + '">' + (lang('add task')) + '</a>';
	}
	return html;
}


ogTasks.hideShowGroups = function(group_id){
	var group = this.getGroup(group_id);
	if (group){
		var soloOn = document.getElementById('ogTasksPanelGroupSoloOn' + group_id);
		var soloOff = document.getElementById('ogTasksPanelGroupSoloOff' + group_id);
		group.solo = !group.solo;
		
		soloOn.style.display = group.solo ? 'none':'inline';
		soloOff.style.display= group.solo ? 'inline':'none';
		
		for (var i = 0; i < this.Groups.length; i++){
			if (this.Groups[i].group_id != group_id){
				var groupEl = document.getElementById('ogTasksPanelGroupCont' + this.Groups[i].group_id);
				if (groupEl)
					groupEl.style.display = group.solo ? 'none':'block';
			}
		}
		
		if (group.solo)
			this.expandGroup(group_id);
		else
			this.collapseGroup(group_id);
	}
}



ogTasks.expandGroup = function(group_id){
	var div = document.getElementById('ogTasksGroupExpandTasks' + group_id);
	var divLink = document.getElementById('ogTasksGroupExpandTasksTitle' + group_id);
	if (div){
		var group = this.getGroup(group_id);
		group.isExpanded = true;
		var html = '';
		var bottomToolbar = Ext.getCmp('tasksPanelBottomToolbarObject');
		var topToolbar = Ext.getCmp('tasksPanelTopToolbarObject');
		var displayCriteria = bottomToolbar.getDisplayCriteria();
		var drawOptions = topToolbar.getDrawOptions();
		for (var i = og.noOfTasks; i < group.group_tasks.length; i++)
			html += this.drawTask(group.group_tasks[i], drawOptions, displayCriteria, group.group_id, 1);
		div.innerHTML = html;
		divLink.style.display = 'none';
		ogTasks.expandedGroups.push(group.group_id);
/*		if (drawOptions.show_workspaces)
			og.showWsPaths('ogTasksGroupExpandTasks' + group_id);*/
	}
}



ogTasks.collapseGroup = function(group_id){
	var div = document.getElementById('ogTasksGroupExpandTasks' + group_id);
	var divLink = document.getElementById('ogTasksGroupExpandTasksTitle' + group_id);
	if (div){
		var group = this.getGroup(group_id);
		group.isExpanded = false;
		div.innerHTML = '';
		divLink.style.display = 'block';
	}
}

ogTasks.expandCollapseAllTasksGroup = function(group_id) {
	var group = this.getGroup(group_id);
	if (group){
		var expander = document.getElementById('ogTasksPanelGroupExpanderG' + group_id);
		if (group.alltasks_collapsed) {
			group.alltasks_collapsed = false;
			if (expander) expander.className = 'og-task-expander toggle_expanded';
		} else {
			group.alltasks_collapsed = true;
			if (expander) expander.className = 'og-task-expander toggle_collapsed';
		}
		
		$("#ogTasksPanelTaskRowsContainer" +  group.group_id).slideToggle();
	}
}


ogTasks.drawAddTask = function(id_subtask, group_id, level){
	//Draw indentation
	// FIXME: quick add task
	var padding = (15 * (level + 1)) + 10;
	return '<div class="ogTasksTaskRow" style="padding-left:' + padding + 'px">' + 
	'</div>';
	
	/*return '<div class="ogTasksAddTask ico-add">' +
	'<a href="#" class="internalLink"  onClick="ogTasks.drawAddNewTaskForm(\'' + group_id + '\', ' + id_subtask + ', ' + level + ')">' + ((id_subtask > 0)?lang('add subtask') : lang('add task')) + '</a>' +
	'</div></div>'; */
}



//************************************
//*		Draw task
//************************************

ogTasks.drawTask = function(task, drawOptions, displayCriteria, group_id, level){
	//Draw indentation
	var padding = 15 * level;
	var containerName = 'ogTasksPanelTask' + task.id + 'G' + group_id;
	task.divInfo[task.divInfo.length] = {group_id: group_id, drawOptions: drawOptions, displayCriteria: displayCriteria, group_id: group_id, level:level};

	// **** <RX : dragging **** //
	var rx__drag_h = '';
	var tgId = "T" + task.id + 'G' + group_id;
	if(rx__TasksDrag.allowDrag)
		rx__drag_h = "<div id='RX__ogTasksPanelDrag" + tgId + "' class='RX__tasks_og-drag ogTasksIcon' title='"+lang('click to drag task')+"' onmouseover='rx__TasksDrag.prepareExt("+task.id+", \"" + group_id + "\",this.id)' onmousedown='rx__TasksDrag.onDragStart("+task.id+", \"" + group_id + "\",this.id); return false;'></div>";

	var html = '<div style="padding-left:' + padding + 'px" id="' + containerName + '" class="RX__tasks_row level-'+level+'" onmouseover="rx__TasksDrag.showHandle(\''+tgId+'\',1)"  onmouseout="rx__TasksDrag.showHandle(\''+tgId+'\',0)">' + rx__drag_h 
		 + this.drawTaskRow(task, drawOptions, displayCriteria, group_id, level) + '</div>';
	// **** /RX **** //
	
	if (task.subtasks.length > 0)
		html += this.drawSubtasks(task, drawOptions, displayCriteria, group_id, level);
	return html;
}

ogTasks.drawTaskRow = function(task, drawOptions, displayCriteria, group_id, level){
	var sb = new StringBuffer();
	var tgId = "T" + task.id + 'G' + group_id;
	sb.append('<table id="ogTasksPanelTaskTable' + tgId + '" class="ogTasksTaskTable' + (task.isChecked?'Selected':'') + '" onmouseover="ogTasks.mouseMovement(' + task.id + ',\'' + group_id + '\',true)" onmouseout="ogTasks.mouseMovement(' + task.id + ',\'' + group_id + '\',false)"><tr>');

	//Draw checkbox
	var priorityColor = "white";
	switch(task.priority){
		case 200: priorityColor = "#DAE3F0"; break;
		case 300: priorityColor = "#FF9088"; break;
		case 400: priorityColor = "#FF0000"; break;
		default: break;
	}
	sb.append('<td class="ogTasksCheckbox" style="background-color:' + priorityColor + '">');
	sb.append('<input style="width:14px;height:14px" type="checkbox" id="ogTasksPanelChk' + tgId + '" ' + (task.isChecked?'checked':'') + ' onclick="ogTasks.TaskSelected(this,' + task.id + ', \'' + group_id + '\')"/></td>'); 
	
	//Draw subtasks expander
	if (task.subtasks.length > 0){
		sb.append("<td style='padding-top:3px;width:16px;'><div id='ogTasksPanelFixedExpander" + tgId + "' class='og-task-expander " + ((task.isExpanded)?'toggle_expanded':'toggle_collapsed') + "' onclick='ogTasks.toggleSubtasks(" + task.id +", \"" + group_id + "\")'></div></td>");
	} else {
		// FIXME: quick add task
		//sb.append("<td class='add-subtask-link-container'><div class='add-subtask-link'  id='ogTasksPanelExpander" + tgId + "' style='visibility:hidden' class='og-task-expander _____ico-add ogTasksIcon' onClick='ogTasks.drawAddNewTaskForm(\"" + group_id + "\", " + task.id + "," + level +")' title='" + lang('add subtask') + "'>"+lang('add sub task')+"</div></td>");
		sb.append("<td style='width:20px;min-width:20px;'>&nbsp;</td>");
	}
	

	if (task.isRead){
		sb.append("<td style=\"width:16px\" id=\"ogTasksPanelMarkasTd" + task.id + "\"><div title=\"" + lang('mark as unread') + "\" id=\"readunreadtask" + task.id + "\" class=\"db-ico ico-read\" onclick=\"ogTasks.readTask(" + task.id + ","+task.isRead+")\" /></td>");		
	}else {
		sb.append("<td style=\"width:16px\" id=\"ogTasksPanelMarkasTd" + task.id + "\"><div title=\"" + lang('mark as read') + "\" id=\"readunreadtask" + task.id + "\" class=\"db-ico ico-unread\" onclick=\"ogTasks.readTask(" + task.id + ","+task.isRead+")\" /> </td>");
	}
	
	//Center td
	sb.append('<td style="text-align:left;width:'+(drawOptions.show_dates ? '47' : (drawOptions.show_time_estimates ? '47' : '63'))+'%;">');
	
	//Member Path
	mem_path = "";
	var mpath = Ext.util.JSON.decode(task.memPath);
	if (mpath) mem_path = og.getCrumbHtml(mpath);
	sb.append(mem_path);
	
	var taskName = '';
	//Draw the Assigned user
	if (task.assignedToId && (displayCriteria.group_by != 'assigned_to' || task.assignedToId != group_id)){
		taskName += '<span class="bold">' + og.clean(this.getUserCompanyName(task.assignedToId)) + '</span>:&nbsp;';
	}
	//Draw the task name
	taskName += og.clean(task.title);
	if (task.status > 0){
		var user = this.getUser(task.completedById, true);
		var tooltip = '';
		if (user){
			var time = new Date(task.completedOn * 1000);
			var now = new Date();
			var timeFormatted = time.getYear() != now.getYear() ? time.dateFormat('M j, Y'): time.dateFormat('M j');
			tooltip = lang('completed by name on', og.clean(user.name), timeFormatted).replace(/'\''/g, '\\\'');
		}
		taskName = "<span style='text-decoration:line-through' title='" + tooltip + "'>" + taskName + "</span>";
	}
	var viewUrl = og.getUrl('task', 'view', {id: task.id});
	sb.append('<a class="internalLink" href="' + viewUrl + '" onclick="og.openLink(\'' + viewUrl + '\');return false;" id="rx__dd'+(++rx__dd)+'">' + taskName + '</a>');
	
	//Draw repeat icon (if repetitive)
	if (task.repetitive > 0){
		sb.append('<span style="margin: 0px 8px; padding: 0px 0px 2px 12px;" class="ico-recurrent" title="'+ lang('repetitive task') +'">&nbsp;</span>');
	}
	
	// Draw percent completed bar
	sb.append('</td><td style="width:85px;">' + ogTasks.buildTaskPercentCompletedBar(task) + '</td><td>');

	sb.append('</td><td align=right><table style="height:100%"><tr>');
        
	//Draw task actions
	sb.append("<td class='nobr'><div id='ogTasksPanelTaskActions" + tgId + "' class='ogTaskActions'><table><tr>");
	
	// Add Subtask
	if (ogTasks.userPermissions.can_add) {
		sb.append("<td style='padding-left:8px;'><div id='ogTasksPanelExpander" + tgId + "' style='visibility:hidden' class='add-subtask-link ico-add coViewAction' onClick='ogTasks.drawAddNewTaskForm(\"" + group_id + "\", " + task.id + "," + level +")' title='" + lang('add subtask') + "'>"+lang('add sub task')+"</div></td>");
	}
	
	if (ogTasks.userPermissions.can_add) {
		sb.append("<td style='padding-left:8px;'><a href='#' onclick='ogTasks.drawEditTaskForm(" + task.id + ", \"" + group_id + "\")'>");
		// FIXME: remove this function when quick add is enabled
        //sb.append("<td style='padding-left:8px;'><a href='#' onclick='ogTasks.goToCompleteEditForm(" + task.id + ")'>");
		sb.append("<div class='ico-edit coViewAction' title='" + lang('edit') + "' style='cursor:pointer;height:16px;padding-top:0px'>" + lang('edit') + "</div></a></td>");
	}
	sb.append("<td style='padding-left:8px;'><a href='#' onclick='ogTasks.ToggleCompleteStatus(" + task.id + ", " + task.status + ")'>");
	if (task.status > 0){
		sb.append("<div class='ico-reopen coViewAction' title='" + lang('reopen this task') + "' style='cursor:pointer;height:16px;padding-top:0px'>" + lang('reopen') + "</div></a></td>");
	} else {
		sb.append("<div class='ico-complete coViewAction' title='" + lang('complete this task') + "' style='cursor:pointer;height:16px;padding-top:0px'>" + lang('do complete') + "</div></a></td>");
	}
	sb.append("</tr></table></div></td>");
	
        if (drawOptions.show_dates || drawOptions.show_time_estimates){
            sb.append('<td style="color:#888;font-size:9px;padding-left:6px;padding-right:3px;width:150px;text-align:right;">');
            
            //Draw time stimate
            if (drawOptions.show_time_estimates && task.estimatedTime){
                    sb.append('<span class="estimated-time nobr">'+ lang('estimated')+': '+task.estimatedTime +'</span> ');
            }

            //Draw dates
            if (drawOptions.show_dates && (task.startDate || task.dueDate)){
                    sb.append('<span class="nobr"' + (task.status == 1 ? ' style="text-decoration:line-through;"' : '') + '>');

                    if (task.startDate){
                            var date = new Date(task.startDate * 1000);
                            date = new Date(Date.parse(date.toUTCString().slice(0, -4)));
                            var hm_format = task.useStartTime ? (og.preferences['time_format_use_24'] == 1 ? ' - G:i' : ' - g:i A') : '';
                            var now = new Date();
                            var dateFormatted = date.getYear() != now.getYear() ? date.dateFormat('M j, Y' + hm_format): date.dateFormat('M j' + hm_format);
                            sb.append(lang('start') + ':&nbsp;' + dateFormatted);
                    }
                    if (task.startDate && task.dueDate) {
                            sb.append('&nbsp;|&nbsp;');
                    }

                    if (task.dueDate){
                            var date = new Date((task.dueDate) * 1000);
                            date = new Date(Date.parse(date.toUTCString().slice(0, -4)));
                            var hm_format = task.useDueTime ? (og.preferences['time_format_use_24'] == 1 ? ' - G:i' : ' - g:i A') : '';
                            var now = new Date();
                            var dateFormatted = date.getYear() != now.getYear() ? date.dateFormat('M j, Y' + hm_format): date.dateFormat('M j' + hm_format);
                            var dueString = lang('due') + ':&nbsp;' + dateFormatted;
                            if (task.status == 0 && date < now) {
                                    dueString = '<span style="font-weight:bold;color:#F00">' + dueString + '</span>';
                            }
                            sb.append(dueString);
                    }
                    sb.append('</span>');
            }
            sb.append('</td>');
        }
	
	//Draw time tracking
	if (drawOptions.show_time){
		if (task.workingOnIds){
			var ids = (task.workingOnIds + ' ').split(',');
			var userIsWorking = false;
			for (var i = 0; i < ids.length; i++) {
				if (this.currentUser && ids[i] == this.currentUser.id){
					userIsWorking = true;
					var pauses = (task.workingOnPauses + ' ').split(',');
					var userPaused = pauses[i] == 1;
				}
			}
			sb.append("<td class='" + (userIsWorking?(userPaused?"ogTasksPausedTimeTd": "ogTasksActiveTimeTd") : "ogTasksTimeTd") + "'><table><tr>");
			if (userIsWorking){
				if (userPaused) {
					sb.append("<td><a href='#' onclick='ogTasks.executeAction(\"resume_work\",[" + task.id + "])'><div class='ogTasksTimeClock ico-time-play' title='" + lang('resume_work') + "'></div></a></td>");
				} else {
					sb.append("<td><a href='#' onclick='ogTasks.executeAction(\"pause_work\",[" + task.id + "])'><div class='ogTasksTimeClock ico-time-pause' title='" + lang('pause_work') + "'></div></a></td>");
				}
				sb.append("<td><a href='#' onclick='ogTasks.closeTimeslot(\"" + tgId + "\")'><div class='ogTasksTimeClock ico-time-stop' title='" + lang('close_work') + "'></div></a></td>");
			} else {
				sb.append("<td><a href='#' onclick='ogTasks.executeAction(\"start_work\",[" + task.id + "])'><div class='ogTasksTimeClock ico-time' title='" + lang('start_work') + "'></div></a></td>");
			}
			sb.append("<td style='white-space:nowrap'><b>");
			
			for (var i = 0; i < ids.length; i++){
				var user = this.getUser(ids[i]);
				if (user){
					sb.append("" + og.clean(user.name));
					if (i < ids.length - 1) {
						sb.append(",");
					}
					sb.append("&nbsp;");
				}
			}
			sb.append("</b>");
			if (userIsWorking){
				sb.append("<div id='ogTasksPanelCWD" + tgId + "' style='display:none'><table><tr><td>" + lang('description') + ":<br/><textarea tabIndex=10100 style='height:54px;width:220px;margin-right:8px' id='ogTasksPanelCWDescription" + tgId + "'></textarea></td></tr>");
				sb.append("<tr><td style='padding-bottom:5px'><button type='submit' tabIndex=10101 onclick='ogTasks.executeAction(\"close_work\",[" + task.id + "],document.getElementById(\"ogTasksPanelCWDescription" + tgId + "\").value);return false'>" + lang('close work') + "</button>&nbsp;&nbsp;<button tabIndex=10102 type='submit' onclick='ogTasks.closeTimeslot(\"" + tgId + "\");return false'>" + lang('cancel') + "</button></td></tr></table></div>");
			}
			sb.append("</td></tr></table>");
		}else{
			sb.append("<td class='ogTasksTimeTd'>");
			sb.append("<a href='#' onclick='ogTasks.executeAction(\"start_work\",[" + task.id + "])'><div class='ogTasksTimeClock ico-time' title='" + lang('start_work') + "'></div></a>");
		}
		sb.append("</td>");
	}
	
	if (og.config.use_tasks_dependencies > 0) {
		var dep_cls = "";
		var dep_text = "";
		var dep_title = "";
		if (task.status == 0) {
			var dep = ogTasks.getDependencyCount(task.id);
			if (dep) {
				dep_cls = dep.count > 0 ? "incomplete-task-bck" : "complete-task-bck";
				dep_text = dep.count > 0 ? dep.count : "&nbsp;";
				dep_title = dep.count > 0 ? lang('this task has x pending tasks', dep.count) : lang('this task has no pending dependencies and can be completed');
			}
		}
		sb.append('<td class="'+dep_cls+'" style="width:10px;padding-left:2px;" title="'+dep_title+'">' + dep_text + '</td>');
	}
	
	sb.append('</tr></table></td></tr></table>');
		
	return sb.toString();
}



ogTasks.closeTimeslot = function(tgId){
	var panel = document.getElementById('ogTasksPanelCWD' + tgId);
	if (panel.style.display == 'block')
		panel.style.display = 'none';
	else {
		panel.style.display = 'block';
		document.getElementById('ogTasksPanelCWDescription' + tgId).focus();
	}
}

ogTasks.drawSubtasks = function(task, drawOptions, displayCriteria, group_id, level){
	var html = '<div style="display:' + ((task.isExpanded)?'block':'none') + '" id="ogTasksPanelSubtasksT' + task.id + 'G' + group_id + '">';
	var orderedTasks = this.orderTasks(displayCriteria, task.subtasks);
	for (var i = 0; i < orderedTasks.length; i++){
		html += this.drawTask(orderedTasks[i], drawOptions, displayCriteria, group_id, level + 1);
	}
	html += this.drawAddTask(task.id, group_id, level);
	html += '</div>';
	return html;
}
                    
ogTasks.subtasksTimeEstimate = function(time_estimated, task, displayCriteria){
	var orderedTasks = this.orderTasks(displayCriteria, task.subtasks);
	for (var i = 0; i < orderedTasks.length; i++){
		if (orderedTasks[i].subtasks.length > 0){
			time_estimated += this.subtasksTimeEstimate(Number(orderedTasks[i].TimeEstimate), orderedTasks[i], displayCriteria);
		}else{
			time_estimated = time_estimated + Number(orderedTasks[i].TimeEstimate);
		}
	}
	return time_estimated;
}

ogTasks.ToggleCompleteStatus = function(task_id, status) {
	var related = false;
	if (status == 0) {
		var task = ogTasks.getTask(task_id);
		for ( var j = 0; j < task.subtasks.length; j++) {
			if (task.subtasks[j].status == 0) {
				related = true;
			}
			if (related) {
				break;
			}
		}
	}

	if (related) {
		this.dialog = new og.TaskCompletePopUp(task_id);
		this.dialog.setTitle(lang('do complete'));
		this.dialog.show();
	} else {
		ogTasks.ToggleCompleteStatusOk(task_id, status, '');
	}
}

ogTasks.ToggleCompleteStatusOk = function(task_id, status, opt){
	var action = (status == 0)? 'complete_task' : 'open_task';
	og.openLink(og.getUrl('task', action, {id: task_id, quick: true, options: opt}), {
		callback: function(success, data) {
			if (!success || data.errorCode) {
				
			} else {
				//Set task data
				var task = ogTasks.getTask(task_id);
				prev_status = task.status;
				task.setFromTdata(data.task);
				
				if (data.subtasks) {
					for (i=0; i < data.subtasks.length; i++) {
						var subtask = this.getTask(data.subtasks[i].id);
						if (subtask) {
							subtask.setFromTdata(data.subtasks[i]);
						}
					}
				}
				
				//Redraw task, or redraw whole panel
				var bottomToolbar = Ext.getCmp('tasksPanelBottomToolbarObject');
				var displayCriteria = bottomToolbar.getDisplayCriteria();
				if (og.config.use_tasks_dependencies) {
					var dc = ogTasks.getDependencyCount(task.id);
					this.UpdateDependants(task, status!=1, prev_status);
				}
				if (displayCriteria.group_by != 'status') {
					//this.UpdateTask(task.id);
					this.draw();
				} else {
					this.draw();
				}
			}
		},
		scope: this
	});
}

ogTasks.readTask = function(task_id,isUnRead){
	var task = ogTasks.getTask(task_id);
	if (!isUnRead){
		og.openLink(
			og.getUrl('task','multi_task_action'),
			{ method:'POST' ,	post:{ids:task_id, action:'markasread'},callback:function(success, data){
					if (!success || data.errorCode) {
					} else {
						var td = document.getElementById('ogTasksPanelMarkasTd' + task_id);
						td.innerHTML = "<div title=\"" + lang('mark as unread') + "\" id=\"readunreadtask" + task_id + "\" class=\"db-ico ico-read\" onclick=\"ogTasks.readTask(" + task_id + ",true)\" />";
						task.isRead = true;
					}
				}
			}
		);
	}else{
		og.openLink(
			og.getUrl('task','multi_task_action'),
			{ method:'POST' ,	post:{ids:task_id, action:'markasunread'},callback:function(success, data){
					if (!success || data.errorCode) {
					} else {								
						var td = document.getElementById('ogTasksPanelMarkasTd' + task_id);
						td.innerHTML = "<div title=\"" + lang('mark as read') + "\" id=\"readunreadtask" + task_id + "\" class=\"db-ico ico-unread\" onclick=\"ogTasks.readTask(" + task_id + ",false)\" />";
						task.isRead = false;
					}
				}
			}
		);
	}
}

ogTasks.UpdateTask = function(task_id){
	var task = ogTasks.getTask(task_id);
	for (var i = 0; i < task.divInfo.length; i++){
		var containerName = 'ogTasksPanelTask' + task.id + 'G' + task.divInfo[i].group_id;
		var div = document.getElementById(containerName);
		if (div){
			div.innerHTML = this.drawTaskRow(task, task.divInfo[i].drawOptions, task.divInfo[i].displayCriteria, task.divInfo[i].group_id, task.divInfo[i].level);
			if (task.divInfo[i].displayCriteria.group_by == 'milestone') { //Update milestone complete bar
				var div2 = document.getElementById('ogTasksPanelCompleteBar' + task.divInfo[i].group_id);
				div2.innerHTML = this.drawMilestoneCompleteBar(this.getGroup(task.divInfo[i].group_id));
			}
		}
	}
}

// FIXME: remove this function when wuick add is enabled
//ogTasks.goToCompleteEditForm = function (task_id) {
//	og.openLink(og.getUrl('task', 'edit_task', {id:task_id}));
//}

ogTasks.buildTaskPercentCompletedBar = function(task) {
	var color_cls = 'task-percent-completed-';
	
	if (task.percentCompleted < 25) color_cls += '0';
	else if (task.percentCompleted < 50) color_cls += '25';
	else if (task.percentCompleted < 75) color_cls += '50';
	else if (task.percentCompleted < 100) color_cls += '75';
        else if (task.percentCompleted == 100) color_cls += '100';
	else color_cls += 'more-estimate';
        
        var percent_complete = 100;
        if(task.percentCompleted <= 100){
            percent_complete = task.percentCompleted;
        }
	
	var html = "<span><span class='nobr'><table style='display:inline;'><tr><td style='padding-left:15px;padding-top:5px'>" +
			"<table style='height:7px;width:50px'><tr><td style='height:7px;width:" + percent_complete + "%;' class='"+color_cls+"'></td><td style='width:" + (100 - percent_complete) + "%;background-color:#DDD'></td></tr></table>" +
			"</td><td style='padding-left:3px;line-height:12px'><span style='font-size:8px;color:#777'>" + percent_complete + "%</span></td></tr></table></span></span>";
	
	return html;
}


ogTasks.UpdateDependants = function(task, complete, prev_status) {
	var deps = this.getDependencyCount(task.id);
	if (deps) {
		var dependants = deps.dependants.split(',');
		for (var i = 0; i < dependants.length; i++){
			var dependant_id = dependants[i];
			var dc = this.getDependencyCount(dependant_id);
			if (dc) {
				if (complete) {
					dc.count -= 1;
					this.UpdateTask(dependant_id);
				} else {
					// Reopen: add 1 and reopen parents
					if (prev_status == 1) {
						dc.count += 1;
						var dep = this.getTask(dependant_id);
						dep.status = 0;
						this.UpdateTask(dependant_id);
						this.UpdateDependants(dep, false);
					}
				}
			}
		}
	}
}