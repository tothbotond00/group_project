<?php

global $PAGE, $OUTPUT;

use mod_groupproject\local\loaders\entity_loader;

require_once('../../config.php');
require_once('locallib.php');

$id = required_param('id', PARAM_INT);
$success = optional_param('successs', false,PARAM_BOOL);

list ($course, $cm) = get_course_and_cm_from_cmid($id, 'groupproject');

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/groupproject:view', $context);

$urlparams = array('id' => $id,);

$url = new moodle_url('/mod/groupproject/manage_groups.php', $urlparams);
$PAGE->set_url($url);

echo $OUTPUT->header();

$groupproject = entity_loader::groupproject_loader($cm->instance);
$groupproject->set_module_viewed();

echo view($groupproject, $context, $success);

echo $OUTPUT->footer();