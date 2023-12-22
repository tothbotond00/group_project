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
 * Defines backup_groupproject_activity_task class
 *
 * @package     mod_groupproject
 * @category    backup
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/groupproject/backup/moodle2/backup_groupproject_stepslib.php');

class backup_groupproject_activity_task extends backup_activity_task {

    /**
     * No specific settings for this activity
     */
    protected function define_my_settings() {
    }

    /**
     * Defines a backup step to store the instance data in the groupproject.xml file
     */
    protected function define_my_steps() {
        $this->add_step(new backup_groupproject_activity_structure_step('groupproject_structure', 'groupproject.xml'));
    }

    /**
     * Encodes URLs to the index.php and view.php scripts
     *
     * @param string $content some HTML text that eventually contains URLs to the activity instance scripts
     * @return string the content with the URLs encoded
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        $search="/(".$base."\/mod\/groupproject\/index.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@GROUPPROJECTINDEX*$2@$', $content);

        $search="/(".$base."\/mod\/groupproject\/view.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@GROUPPROJECTVIEWBYID*$2@$', $content);

        return $content;
    }
}
