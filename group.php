<?php

use mod_groupproject\local\loaders\entity_loader;

global $PAGE, $OUTPUT;

require_once('../../config.php');
require_once('locallib.php');

$id = required_param('id', PARAM_INT);
$groupid = required_param('groupid', PARAM_INT);
$action = required_param('action', PARAM_TEXT);

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'groupproject');

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/groupproject:view', $context);

$urlparams = array('id' => $id, 'groupid' => $groupid, 'action' => $action);

$url = new moodle_url('/mod/groupproject/group.php', $urlparams);
$PAGE->set_url($url);

echo $OUTPUT->header();

$groupproject = entity_loader::groupproject_loader($cm->instance);
$groupproject->set_module_viewed();

switch ($action){
    case 'add':
        echo add_group($groupproject,$context);
        break;
    case 'modify':
        echo modify_group($groupproject, $context, $groupid);
        break;
    case 'grade':
        echo grade_group($groupproject, $context, $groupid);
        break;
    case 'delete':
        delete_group($groupproject, $context, $groupid);
        break;
    default:
}

echo $OUTPUT->footer();
