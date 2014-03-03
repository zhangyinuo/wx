<?php
chdir($argv[1]);
define("CONSOLE_MODE", true);
define('PUBLIC_FOLDER', 'public');
include "init.php";

session_commit(); // we don't need sessions
@set_time_limit(0); // don't limit execution of cron, if possible
ini_set('memory_limit', '1024M');

try {
	Env::useHelper('permissions');
	DB::beginWork();
	
	$user_id = array_var($argv, 2);
	$token = array_var($argv, 3);
	
	// log user in
	$user = Contacts::findById($user_id);
	if(!($user instanceof Contact) || !$user->isValidToken($token)) {
		die();
	}

	CompanyWebsite::instance()->setLoggedUser($user, false, false, false);
		
	// save permissions
	$pg_id = array_var($argv, 4);
	$is_guest = array_var($argv, 5);
	$permissions_filename = array_var($argv, 6);
	$sys_permissions_filename = array_var($argv, 7);
	$mod_permissions_filename = array_var($argv, 8);
	$root_permissions_filename = array_var($argv, 9);
	$root_permissions_genid = array_var($argv, 10);
	
	$permissions = file_get_contents($permissions_filename);
	$sys_permissions = json_decode(file_get_contents($sys_permissions_filename), true);
	$mod_permissions = json_decode(file_get_contents($mod_permissions_filename), true);
	$root_permissions = json_decode(file_get_contents($root_permissions_filename), true);
	
	$perms = array(
		'permissions' => $permissions,
		'sys_perm' => $sys_permissions,
		'mod_perm' => $mod_permissions,
		'root_perm' => $root_permissions,
		'root_perm_genid' => $root_permissions_genid,
	);
	
	save_permissions($pg_id, $is_guest, $perms, false);
	
	@unlink($permissions_filename);
	@unlink($sys_permissions_filename);
	@unlink($mod_permissions_filename);
	@unlink($root_permissions_filename);
	
	DB::commit();
} catch (Exception $e) {
	DB::rollback();
	Logger::log("Error saving permissions: ".$e->getMessage()."\n".$e->getTraceAsString());
}