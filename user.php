<?php

use mod_groupproject\local\loaders\entity_loader;

require_once('../../config.php');
require_once('locallib.php');

$id = required_param('id', PARAM_INT);
$groupid = required_param('groupid', PARAM_INT);

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'groupproject');

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/groupproject:view', $context);

$urlparams = array('id' => $id, 'groupid' => $groupid);

$url = new moodle_url('/mod/groupproject/user.php', $urlparams);
$PAGE->set_url($url);

echo $OUTPUT->header();

$groupproject = entity_loader::groupproject_loader($cm->instance);
$groupproject->setModuleViewed();

echo user_view($groupproject,$context,$groupid);

echo $OUTPUT->footer();