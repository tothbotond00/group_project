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

/**
 * Define all the backup steps that will be used by the backup_groupproject_activity_task
 */

/**
 * Define the complete groupproject structure for backup, with file and id annotations
 */
class backup_groupproject_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $groupproject = new backup_nested_element('groupproject', array('id'), array(
            'name', 'intro', 'introformat', 'duedate', 'grade', 'timecreated', 'timemodified'
        ));

        $groups = new backup_nested_element('groups');

        $group = new backup_nested_element('group', array('id'), array(
            'groupprojectid', 'name', 'idnumber', 'size','timecreated', 'timemodified'
        ));

        $grades = new backup_nested_element('grades');

        $grade = new backup_nested_element('grade', array('id'), array(
            'groupid', 'timecreated', 'timemodified', 'grader', 'grade'
        ));

        $user_assignments = new backup_nested_element('user_assignments');

        $user_assignment = new backup_nested_element('user_assignment', array('id'), array(
            'groupid', 'userid', 'roleid'
        ));

        $comments = new backup_nested_element('comments');

        $comment = new backup_nested_element('comment', array('id'), array(
            'groupid', 'userid', 'comment', 'timecreated'
        ));

        // Build the tree
        $groupproject->add_child($groups);
        $groups->add_child($group);

        $group->add_child($grades);
        $grades->add_child($grade);

        $group->add_child($user_assignments);
        $user_assignments->add_child($user_assignment);

        $group->add_child($comments);
        $comments->add_child($comment);

        // Define sources
        $groupproject->set_source_table('groupproject', array('id' => backup::VAR_ACTIVITYID));

        $group->set_source_table('groupproject_groups', array('groupprojectid' => backup::VAR_PARENTID));

        // All the rest of elements only happen if we are including user info
        if ($userinfo) {
            $grade->set_source_table('groupproject_grades', array('groupid' => backup::VAR_PARENTID));
            $user_assignment->set_source_table('groupproject_user_assign', array('groupid' => backup::VAR_PARENTID));
            $comment->set_source_table('groupproject_comments', array('groupid' => backup::VAR_PARENTID));
        }

        // Define file annotations
        $groupproject->annotate_files('mod_groupproject', 'intro', null); // This file area hasn't itemid

        $group->annotate_files('mod_groupproject', 'groupproject_submission', 'id');

        // Return the root element (groupproject), wrapped into standard activity structure
        return $this->prepare_activity_structure($groupproject);
    }
}
