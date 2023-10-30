<?php

use mod_groupproject\local\loaders\entity_loader;

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('locallib.php');

admin_externalpage_setup('groupproject_roles');

require_login();

if(!is_siteadmin()){
    throw new moodle_exception('noacceess');
}

$roleid = required_param('roleid', PARAM_INT);
$action = required_param('action', PARAM_TEXT);

$context = context_system::instance();

$url = new moodle_url('/mod/groupproject/manage_roles.php', array('action' => $action));

$PAGE->set_url($url);

echo $OUTPUT->header();

if($action === 'add'){
    echo add_role();
}else if($action === 'modify'){
    echo modify_role($roleid);
}else if ($action === 'delete'){
    delete_role_gp($roleid);
}

echo $OUTPUT->footer();
