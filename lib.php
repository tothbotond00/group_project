<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Library of functions and constants for module groupproject
 *
 * @package   mod_groupproject
 * @copyright 2023 Tóth Botond
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_groupproject\local\entities\capability;
use mod_groupproject\local\entities\groupproject;
use mod_groupproject\local\factories\entity_factory;
use mod_groupproject\local\loaders\entity_loader;

require_once($CFG->libdir . '/completionlib.php');

/// STANDARD FUNCTIONS ///////////////////////////////////////////////////////////
function groupproject_add_instance($groupproject) {
    global $DB;

    $groupproject->timecreated  = time();
    $groupproject->timemodified = $groupproject->timecreated;

    $returnid = groupproject::create($groupproject);
    $groupproject->id = $returnid;

    return $returnid;
}

function groupproject_update_instance($module){
    global $DB;

    $groupproject = entity_loader::groupproject_loader($module->instance);
    $groupproject->setTimemodified(time());
    $groupproject->setName($module->name);
    $groupproject->setIntro($module->intro);
    $groupproject->setIntroformat($module->introformat);
    $groupproject->setDuedate(0);
    $groupproject->setGrade($module->grade);
    return $groupproject->update();
}

function groupproject_delete_instance($id){
    global $DB;

    $groupproject = entity_loader::groupproject_loader($id);
    $groupproject->delete();

    if ($DB->get_record('groupproject', array('id' => $id))) {
        return false;
    }

    if (!$cm = get_coursemodule_from_instance('groupproject', $id)) {
        return false;
    }

    if (!$context = context_module::instance($cm->id, IGNORE_MISSING)) {
        return false;
    }

    return true;
}

function groupproject_reset_userdata($data){
    global $DB;

    $status = array();
    $courseid = $data->courseid;
    $groupprojects = $DB->get_records(groupproject::$TABLE, array('course' => $courseid));
    foreach ($groupprojects as $groupproject){
        $groupproject = entity_factory::create_groupproject_from_stdclass($groupproject);
        $status = $groupproject->resetUserdata();
    }
    return $status;
}

function groupproject_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPINGS:
        case FEATURE_BACKUP_MOODLE2:
        case FEATURE_PLAGIARISM:
        case FEATURE_GROUPS:
            return false;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
        case FEATURE_COMPLETION_HAS_RULES:
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

    $group = $groupproject->userHasGroup($USER->id);
    if($group){
        $node = navigation_node::create(get_string('group_chat', 'groupproject'),
            new moodle_url('/mod/groupproject/group_chat.php', ['id' => $settings->get_page()->cm->id]),
            navigation_node::TYPE_ACTIVITY, null, 'mod_groupproject_groupchat', new pix_icon('t/edit', ''));
        $modnode->add_node($node, $beforekey);

        $node = navigation_node::create(get_string('group_submssion', 'groupproject'),
            new moodle_url('/mod/groupproject/group_submission.php', ['id' => $settings->get_page()->cm->id]),
            navigation_node::TYPE_ACTIVITY, null, 'mod_groupproject_groupsubmssion', new pix_icon('t/edit', ''));
        $modnode->add_node($node, $beforekey);
    }
}

function groupproject_get_file_areas($course, $cm, $context) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/groupproject/locallib.php');

    $areas = array(
        GROUPPROJECT_SUBMISSION_FILEAREA => get_string('groupproject_submission', 'mod_groupproject'),
    );
    return $areas;
}

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

