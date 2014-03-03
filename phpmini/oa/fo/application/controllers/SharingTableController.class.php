<?php 
class  SharingTableController extends ApplicationController {
	
	/**
	 * When updating perrmissions, sharing table should be updated
	 * @param stdClass $permission:  
	 * 			[m] => 36 : Member Id 
	 * 			[o] => 3 : Object Type Id 
	 * 			[d] => 0 //delete
	 * 			[w] => 1 //write
	 * 			[r] => 1 //read 
	 * @throws Exception
	 */
	function afterPermissionChanged($groups, $permissions) {
		if (!is_array($groups)) {
			if (is_numeric($groups)) $groups = array($groups);
			else return;
		}
		foreach ($groups as $group) {
			$this->after_permission_changed($group, $permissions);
		}
	}
	
	
	function after_permission_changed($group = null, $permissions = null) {
		@set_time_limit(0);
		$die = false;
		if ($group == null || $permissions == null) {
			$die = true;
			if ($group == null) {
				$group = array_var($_REQUEST, 'group');
			}
			if ($permissions == null) {
				$permissions = json_decode(array_var($_REQUEST, 'permissions'));
			}
		}
		
		// CHECK PARAMETERS
		if(!count($permissions)){
			return false;
		}
		if (!is_numeric($group) || !$group) {
			throw new Error("Error filling sharing table. Invalid Paramenters for afterPermissionChanged method");
		}

		// INIT LOCAL VARS
		$stManager = SharingTables::instance();
		$affectedObjects = array();
		$members = array();
		$general_condition = '';
		$read_condition = '';
		$read_conditions = array();
		$delete_condition = '';
		$delete_conditions = array();

		$all_read_conditions = array();
		$read_count = 0;
		$all_del_conditions = array();
		$del_count = 0;
		
		// BUILD OBJECT_IDs SUB-QUERIES
		$from = "FROM ".TABLE_PREFIX."object_members om INNER JOIN ".TABLE_PREFIX."objects o ON o.id = om.object_id";
		foreach ($permissions as $permission) {
			$memberId = $permission->m;
			$objectTypeId = $permission->o;
			if (!$memberId || !$objectTypeId) continue;
			$delete_conditions[] = " ( object_type_id = '$objectTypeId' AND om.member_id = '$memberId' ) ";
			$del_count++;
			if ($del_count >= 500) {
				$all_del_conditions[] = $delete_conditions;
				$delete_conditions = array();
				$del_count = 0;
			}
			if ($permission->r) {
				$read_conditions[] = " ( object_type_id = '$objectTypeId' AND om.member_id = '$memberId' ) ";
				
				$read_count++;
				if ($read_count >= 500) {
					$all_read_conditions[] = $read_conditions;
					$read_count = 0;
					$read_conditions = array();
				}
			}
		}
		$all_read_conditions[] = $read_conditions;
		$all_del_conditions[] = $delete_conditions;
		
		// DELETE THE AFFECTED OBJECTS FROM SHARING TABLE
		foreach ($all_del_conditions as $delete_conditions) {
			
			// check if the permission group still can view any of the affected objects (if they are classified in another dimension member)
			$del_objs = DB::executeAll("SELECT object_id $from WHERE ".implode(' OR ' , $delete_conditions ));
			$del_objs = array_flat($del_objs);
			
			$del_objs_can_read = array();
			foreach ($del_objs as $do) {
				$dobj = Objects::findObject($do);
				if ($dobj instanceof ContentDataObject && can_access_pgids(array($group), $dobj->getMembers(), $dobj->getObjectTypeId(), ACCESS_LEVEL_READ)) {
					$del_objs_can_read[] = $do;
				}
			}
			
			// objects that were included to be deleted but still can be read
			$not_to_del_objs_cond = "";
			if (count($del_objs_can_read) > 0) {
				$not_to_del_objs_cond = " AND object_id NOT IN (".implode(',',$del_objs_can_read).")";
			}
			
			// delete registers only for objects that cannot be read anymore for this permission group
			$stManager->delete("object_id IN (SELECT object_id $from WHERE ".implode(' OR ' , $delete_conditions ).") AND group_id = '$group'");
		}
		// 2. POPULATE THE SHARING TABLE AGAIN WITH THE READ-PERMISSIONS (If there are)
		foreach ($all_read_conditions as $read_conditions) {
			if (isset($read_conditions) && count($read_conditions)) {
				$st_new_rows = "
					SELECT $group AS group_id, object_id $from
					WHERE om.is_optimization=0 AND (". implode(' OR ', $read_conditions) . ")";
	
				$st_insert_sql =  "INSERT INTO ".TABLE_PREFIX."sharing_table(group_id, object_id) $st_new_rows ";
				DB::execute($st_insert_sql);
			}
		}
		if ($die) die();
	}
}