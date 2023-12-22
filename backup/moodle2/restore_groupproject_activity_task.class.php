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
 * @package mod_groupproject
 * @subpackage backup-moodle2
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/groupproject/backup/moodle2/restore_groupproject_stepslib.php');

/**
 * groupproject restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 */
class restore_groupproject_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Choice only has one structure step
        $this->add_step(new restore_groupproject_activity_structure_step('groupproject_structure', 'groupproject.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('groupproject', array('intro'), 'groupproject');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('GROUPPROJECTVIEWBYID',
            '/mod/groupproject/view.php?id=$1',
            'course_module');
        $rules[] = new restore_decode_rule('GROUPPROJECTINDEX',
            '/mod/groupproject/index.php?id=$1',
            'course_module');

        return $rules;

    }

    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('assign', 'add', 'view.php?id={course_module}', '{groupproject}');
        $rules[] = new restore_log_rule('assign', 'update', 'view.php?id={course_module}', '{groupproject}');
        $rules[] = new restore_log_rule('assign', 'view', 'view.php?id={course_module}', '{groupproject}');

        return $rules;
    }


    static public function define_restore_log_rules_for_course() {
        $rules = array();

        return $rules;
    }
}
