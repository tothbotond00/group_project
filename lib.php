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

use mod_groupproject\local\entities\group;
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

    print_r($module);
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
        case FEATURE_GROUPS:
            return false;
        case FEATURE_GROUPINGS:
            return false;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_GRADE_OUTCOMES:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return false;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_ADVANCED_GRADING:
            return true;
        case FEATURE_PLAGIARISM:
            return false;
        case FEATURE_COMMENT:
            return true;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_ASSESSMENT;
        default:
            return null;
    }
}

function groupproject_extend_settings_navigation(settings_navigation $settings, navigation_node $modnode) {
    global $CFG;

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

    $node = navigation_node::create(get_string('manage_groups', 'groupproject'),
        new moodle_url('/mod/groupproject/manage_groups.php', ['id' => $settings->get_page()->cm->id]),
        navigation_node::TYPE_ACTIVITY, null, 'mod_groupproject_managegroups', new pix_icon('t/edit', ''));
    $modnode->add_node($node, $beforekey);

    $node = navigation_node::create(get_string('group_chat', 'groupproject'),
        new moodle_url('/mod/groupproject/group_chat.php', ['id' => $settings->get_page()->cm->id]),
        navigation_node::TYPE_ACTIVITY, null, 'mod_groupproject_groupchat', new pix_icon('t/edit', ''));
    $modnode->add_node($node, $beforekey);

}

