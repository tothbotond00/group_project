<?php

global $PAGE, $OUTPUT, $CFG;

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('locallib.php');

admin_externalpage_setup('groupproject_roles');

require_login();

if(!is_siteadmin()){
    throw new moodle_exception('noacceess');
}

$context = context_system::instance();

$url = new moodle_url('/mod/groupproject/manage_roles.php');
$PAGE->set_url($url);

echo $OUTPUT->header();

echo manage_roles();

echo $OUTPUT->footer();