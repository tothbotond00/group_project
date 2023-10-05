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

use mod_groupproject\local\entities\groupproject;
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

function groupproject_update_instance($groupproject){

}

function groupproject_delete_instance($id){
    global $DB;

    $groupproject = entity_loader::groupproject_loader($id);
    $groupproject->delete();

    if ($DB->get_record('groupproject', array('id'=>$id))) {
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