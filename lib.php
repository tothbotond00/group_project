<?php

/**
 * Library of functions and constants for module groupproject required by Moodle.
 *
 * @package   mod_groupproject
 * @copyright 2023 Tóth Botond
 */

use mod_groupproject\local\entities\capability;
use mod_groupproject\local\entities\groupproject;
use mod_groupproject\local\factories\entity_factory;
use mod_groupproject\local\loaders\entity_loader;

global $CFG;

require_once($CFG->libdir . '/completionlib.php');

/// STANDARD FUNCTIONS ///////////////////////////////////////////////////////////

/**
 * Called when a new course module is created for a groupproject instance.
 * @param $groupproject
 * @return bool|int
 * @throws dml_exception
 */
function groupproject_add_instance($groupproject) {
    global $DB;

    $groupproject->timecreated  = time();
    $groupproject->timemodified = $groupproject->timecreated;

    $returnid = groupproject::create($groupproject);
    $groupproject->id = $returnid;
    groupproject_grade_item_update($groupproject);

    return $returnid;
}

/**
 * Used when coursemodule data gets updated. Updates the instance too.
 * @param $module
 * @return bool
 * @throws dml_exception
 */
function groupproject_update_instance($module){
    global $DB;

    $groupproject = entity_loader::groupproject_loader($module->instance);
    $groupproject->setTimemodified(time());
    $groupproject->setName($module->name);
    $groupproject->setIntro($module->intro);
    $groupproject->setIntroformat($module->introformat);
    $groupproject->setDuedate(0);
    $groupproject->setGrade($module->grade);
    $groupproject->setDuedate($module->duedate);
    $module->id = $module->instance;
    groupproject_grade_item_update($module);
    return $groupproject->update();
}

/**
 * Deletes the instance with id. Only called in adhoc task, so this can create phantom data if you don't run cron often.
 * @param $id
 * @return bool
 * @throws coding_exception
 * @throws dml_exception
 */
function groupproject_delete_instance($id){
    global $DB;

    $groupproject = entity_loader::groupproject_loader($id);
    $data = new \stdClass();
    $data->courseid = $groupproject->getCourse();
    $data->id = $id;
    $groupproject->delete();

    groupproject_grade_item_delete($data);

    return true;
}

/**
 * Resets userdata for this instance. Deletes all groups, chat history, files and grades associated with this activity.
 * @param $data
 * @return array|array[]
 * @throws coding_exception
 * @throws dml_exception
 */
function groupproject_reset_userdata($data){
    global $DB;

    $status = array();
    $courseid = $data->courseid;
    $groupprojects = $DB->get_records(groupproject::$TABLE, array('course' => $courseid));
    foreach ($groupprojects as $groupproject){
        $groupproject = entity_factory::create_groupproject_from_stdclass($groupproject);
        $status = $groupproject->reset_user_data();
    }
    return $status;
}

/**
 * Standard supports function required by Moodle.
 * @param $feature
 * @return bool|string|null
 */
function groupproject_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPINGS:
        case FEATURE_PLAGIARISM:
        case FEATURE_GROUPS:
        case FEATURE_COMPLETION_HAS_RULES:
            return false;
        case FEATURE_BACKUP_MOODLE2:
        case FEATURE_COMPLETION_TRACKS_VIEWS:
        case FEATURE_GRADE_HAS_GRADE:
        case FEATURE_GRADE_OUTCOMES:
        case FEATURE_SHOW_DESCRIPTION:
        case FEATURE_ADVANCED_GRADING:
        case FEATURE_COMMENT:
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_ASSESSMENT;
        default:
            return null;
    }
}

/**
 * Navigation node extension inside the activity module. Mostly depends on user capabilities.
 * @param settings_navigation $settings
 * @param navigation_node $modnode
 * @return void
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function groupproject_extend_settings_navigation(settings_navigation $settings, navigation_node $modnode) {
    global $CFG, $USER;

    // We want to add these new nodes after the Edit settings node, and before the
    // Locally assigned roles node. Of course, both of those are controlled by capabilities.
    $keys = $modnode->get_children_key_list();
    $beforekey = null;
    $i = array_search('modedit', $keys);
    if ($i === false and array_key_exists(0, $keys)) {
        $beforekey = $keys[0];
    } else if (array_key_exists($i + 1, $keys)) {
        $beforekey = $keys[$i + 1];
    }

    $groupproject = entity_loader::groupproject_loader($settings->get_page()->cm->instance);

    if(capability::has_capability($groupproject,'mod/groupproject:managegroup', $settings->get_page()->cm->context)){
        $node = navigation_node::create(get_string('manage_groups', 'groupproject'),
            new moodle_url('/mod/groupproject/manage_groups.php', ['id' => $settings->get_page()->cm->id]),
            navigation_node::TYPE_ACTIVITY, null, 'mod_groupproject_managegroups', new pix_icon('t/edit', ''));
        $modnode->add_node($node, $beforekey);
    }

    $group = $groupproject->user_has_group($USER->id);
    if($group){
        $node = navigation_node::create(get_string('group_chat', 'groupproject'),
            new moodle_url('/mod/groupproject/group_chat.php', ['id' => $settings->get_page()->cm->id]),
            navigation_node::TYPE_ACTIVITY, null, 'mod_groupproject_groupchat', new pix_icon('t/edit', ''));
        $modnode->add_node($node, $beforekey);

        if(capability::has_capability($groupproject, 'mod/groupproject:submitfile', $settings->get_page()->cm->context)){
            $node = navigation_node::create(get_string('group_submssion', 'groupproject'),
                new moodle_url('/mod/groupproject/group_submission.php', ['id' => $settings->get_page()->cm->id]),
                navigation_node::TYPE_ACTIVITY, null, 'mod_groupproject_groupsubmssion', new pix_icon('t/edit', ''));
            $modnode->add_node($node, $beforekey);
        }

        if(capability::has_capability($groupproject, 'mod/groupproject:adduser', $settings->get_page()->cm->context)){
            $node = navigation_node::create(get_string('add_user', 'groupproject'),
                new moodle_url('/mod/groupproject/user.php', ['id' => $settings->get_page()->cm->id, 'groupid' => $group->getId()]),
                navigation_node::TYPE_ACTIVITY, null, 'mod_groupproject_add_user', new pix_icon('t/edit', ''));
            $modnode->add_node($node, $beforekey);
        }

        if(capability::has_capability($groupproject, 'mod/groupproject:modifygroup', $settings->get_page()->cm->context)){
            $node = navigation_node::create(get_string('modify_group', 'groupproject'),
                new moodle_url('/mod/groupproject/group.php',
                    ['id' => $settings->get_page()->cm->id, 'groupid' => $group->getId(), 'action' => 'modify']),
                navigation_node::TYPE_ACTIVITY, null, 'mod_groupproject_modify_group', new pix_icon('t/edit', ''));
            $modnode->add_node($node, $beforekey);
        }
    }
}

/**
 * Returns every valid file area, that can be handled by this plugin.
 * @param $course
 * @param $cm
 * @param $context
 * @return array
 * @throws coding_exception
 */
function groupproject_get_file_areas($course, $cm, $context) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/groupproject/locallib.php');

    $areas = array(
        GROUPPROJECT_SUBMISSION_FILEAREA => get_string('groupproject_submission', 'mod_groupproject'),
    );
    return $areas;
}

/**
 * Plugin file sending. Checks if the required file exists and if it can be send tot the user.
 * @param $course
 * @param $cm
 * @param $context
 * @param $filearea
 * @param $args
 * @param $forcedownload
 * @param array $options
 * @return void
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function groupproject_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $DB;

    if(key_exists($filearea, groupproject_get_file_areas($course, $cm, $context))){
        $itemid = $args[0];
        $files = $DB->get_records('files', ['itemid' => $itemid, 'component' => 'mod_groupproject', 'filearea' => $filearea]);
        $fs = get_file_storage();

        foreach ($files as $f) {
            if((int)$f->filesize > 0) {
                $file = $fs->get_file_by_id($f->id);
                send_stored_file($file, null, 0, $forcedownload, $options);
            }
        }
    }
    send_file_not_found();
}

/**
 * Generates path from plugin-file. Mostly used by Moodle file picker for other activities.
 * @param string $filearea
 * @param array $args
 * @return array
 */
function mod_groupproject_get_path_from_pluginfile(string $filearea, array $args) : array {
    // Get the filepath.
    if (empty($args)) {
        $filepath = '/';
    } else {
        $filepath = '/' . implode('/', $args) . '/';
    }

    return [
        'itemid' => $args[0],
        'filepath' => $filepath,
    ];
}

/**
 * Updates the gradeitems associated with this activity in the course.
 * @param $groupproject
 * @param $grades
 * @return int
 */
function groupproject_grade_item_update($groupproject, $grades=null) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    if (!isset($groupproject->courseid)) {
        $groupproject->courseid = $groupproject->course;
    }

    $params = array('itemname'=> $groupproject->name, 'idnumber'=> $groupproject->cmidnumber);

    // gradetype = GRADE_TYPE_TEXT else GRADE_TYPE_NONE.
    $gradefeedbackenabled = false;

    if ($groupproject->grade > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $groupproject->grade;
        $params['grademin']  = 0;

    } else if ($groupproject->grade < 0) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid']   = -$groupproject->grade;

    } else {
        // $assign->grade == 0 and no feedback enabled.
        $params['gradetype'] = GRADE_TYPE_NONE;
    }

    if ($grades  === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }

    return grade_update('mod/groupproject',
        $groupproject->courseid,
        'mod',
        'groupproject',
        $groupproject->id,
        0,
        $grades,
        $params);
}

/**
 * Delete grade item for given data
 *
 * @category grade
 * @param object $data object
 * @return int grade_item
 */
function groupproject_grade_item_delete($data) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    return grade_update('mod/groupproject', $data->course, 'mod', 'groupproject', $data->id, 0, NULL, array('deleted'=>1));
}

