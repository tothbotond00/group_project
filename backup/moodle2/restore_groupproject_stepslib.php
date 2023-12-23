<?php

/**
 * @package mod_groupproject
 * @subpackage backup-moodle2
 */

/**
 * Define all the restore steps that will be used by the restore_groupproject_activity_task
 */

/**
 * Structure step to restore one groupproject activity
 */
class restore_groupproject_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('groupproject', '/activity/groupproject');
        $paths[] = new restore_path_element('groupproject_groups', '/activity/groupproject/groups/group');
        if ($userinfo) {
            $paths[] = new restore_path_element('groupproject_grades', '/activity/groupproject/groups/group/grades/grade');
            $paths[] = new restore_path_element('groupproject_user_assign', '/activity/groupproject/groups/group/user_assignments/user_assignment');
            $paths[] = new restore_path_element('groupproject_comments', '/activity/groupproject/groups/group/comments/comment');
        }

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

        protected function process_groupproject($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // insert the groupproject record
        $newitemid = $DB->insert_record('groupproject', $data);
        $this->apply_activity_instance($newitemid);
    }

    protected function process_groupproject_groups($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->groupprojectid = $this->get_new_parentid('groupproject');
        $newitemid = $DB->insert_record('groupproject_groups', $data);
        $this->set_mapping('groupproject_groups', $oldid, $newitemid, true);
    }

    protected function process_groupproject_grades($data) {
        global $DB;

        $data = (object)$data;

        $data->groupid = $this->get_new_parentid('groupproject_groups');
        $newitemid = $DB->insert_record('groupproject_grades', $data);
    }

        protected function process_groupproject_user_assign($data) {
        global $DB;

        $data = (object)$data;
        $result = $DB->get_records_sql("SELECT * 
                                    FROM {course} c 
                                    JOIN {enrol} en ON en.courseid = c.id
                                    JOIN {user_enrolments} ue ON ue.enrolid = en.id
                                   WHERE c.id = {$this->get_courseid()} AND ue.userid = {$data->userid}");
        if(empty($result)) return;

        $data->groupid = $this->get_new_parentid('groupproject_groups');
        $newitemid = $DB->insert_record('groupproject_user_assign', $data);
    }

    protected function process_groupproject_comments($data) {
        global $DB;

        $data = (object)$data;
        $data->groupid = $this->get_new_parentid('groupproject_groups');
        $newitemid = $DB->insert_record('groupproject_comments', $data);
    }

    protected function after_execute() {
        // Add groupproject related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_groupproject', 'intro', null);
        // Add entries related files, matching by itemname (groupproject_group)
        $userinfo = $this->get_setting_value('userinfo');
        if($userinfo){
            $this->add_related_files('mod_groupproject', 'groupproject_submission', 'groupproject_groups');
        }
    }
}
