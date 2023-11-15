<?php

namespace mod_groupproject\output;

use mod_groupproject\local\entities\groupproject;
use mod_groupproject\local\factories\entity_factory;
use mod_groupproject\local\loaders\entity_loader;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

class group_table extends \table_sql
{
    /** @var groupproject  */
    private $groupproject;

    public function __construct($uniqueid, $url, groupproject $groupproject) {
        global $CFG;
        parent::__construct($uniqueid);

        // Define columns in the table.
        $this->define_table_columns();

        $this->define_baseurl($url);
        // Define configs.
        $this->define_table_configs();
        $this->groupproject = $groupproject;
    }

    private function define_table_columns()
    {
        $cols = array(
            'name' => get_string('groupname', 'mod_groupproject'),
            'size' => get_string('groupsize', 'mod_groupproject'),
            'fileupload' => get_string('fileupload', 'mod_groupproject'),
            'actions' => get_string('actions', 'mod_groupproject'),
        );

        $this->define_columns(array_keys($cols));
        $this->define_headers(array_values($cols));
    }

    private function define_table_configs()
    {
        $this->collapsible(false);
        $this->sortable(true, 'groupname', SORT_ASC);
        $this->pageable(true);
        $this->no_sorting('actions');
        $this->no_sorting('fileupload');
    }

    public function print_nothing_to_display() {
        global $OUTPUT;
        echo $this->render_reset_button();
        $this->print_initials_bar();
        echo $OUTPUT->heading(get_string('nothingtodisplay'), 4);
    }

    public function query_db($pagesize, $useinitialsbar = true) {
        global $DB;

        list($countsql, $countparams) = $this->get_sql_and_params(true);
        list($sql, $params) = $this->get_sql_and_params();
        $total = $DB->count_records_sql($countsql, $countparams);
        $this->pagesize($pagesize, $total);
        $this->rawdata = $DB->get_records_sql($sql, $params, $this->get_page_start(), $this->get_page_size());

        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars($total > $pagesize);
        }
    }

    protected function get_sql_and_params(bool $count = false)
    {
        $fields = "*";

        if ($count) {
            $select = "COUNT(1)";
        } else {
            $select = "$fields";
        }

        $sql = "SELECT $select
                  FROM {groupproject_groups} g";

        // Check if any additional filtering is required.
        [$sqlwhere, $params] = $this->get_sql_where();
        if ($sqlwhere) {
            $sql .= " WHERE {$sqlwhere}";
        }

        if (!$count && $sqlsort = $this->get_sql_sort()) {
            $sql .= " ORDER BY " . $sqlsort;
        }

        return array($sql, $params);
    }

    public function get_sql_where() {
        $groupprojectid = $this->groupproject->getId();
        list($where, $params) = parent::get_sql_where();
        $where .= "groupprojectid = {$groupprojectid}";
        return [$where, $params];
    }

    public function  col_size($group){
        $group_obj = entity_factory::create_group_from_stdclass($group);
        return   count($group_obj->getUserIds()) . ' / ' . $group->size ;
    }

    public function  col_fileupload($group){
        global $DB;
        $file_storage = get_file_storage();
        $filearea = GROUPPROJECT_SUBMISSION_FILEAREA;
        $file = $DB->get_records_sql("SELECT *
                                    FROM {files} f
                                   WHERE f.component = 'mod_groupproject' 
                                     AND f.filearea = '{$filearea}' 
                                     AND itemid = {$group->id}
                                     AND f.filesize <> 0 
                                     HAVING MAX(f.timemodified)");
        if(empty($file)) return get_string('no_file', 'mod_groupproject');
        $uploaded_file = $file_storage->get_file_by_id(array_keys($file)[0]);

        $url = \html_writer::tag('a',$uploaded_file->get_filename(),['href' => moodle_url::make_pluginfile_url(
            $uploaded_file->get_contextid(),
            $uploaded_file->get_component(),
            $uploaded_file->get_filearea(),
            $uploaded_file->get_itemid(),
            $uploaded_file->get_filepath(),
            $uploaded_file->get_filename(),
            true
        )->out()]) . \html_writer::end_tag('a');

        return $url;
    }

    public function col_actions($group){
        global $OUTPUT;

        $links = '';

        $groupproject = entity_loader::groupproject_loader($group->groupprojectid);
        $context = $groupproject->getContext();

        //Add Users
        $addusers = ['id' => $context->instanceid, 'groupid' => $group->id];
        $usersurl = new \moodle_url('/mod/groupproject/user.php', $addusers);

        $links .= $OUTPUT->action_icon(
            $usersurl,
            new \pix_icon('t/move', get_string('add_users', 'mod_groupproject'))
        );

        // Modify
        $modifyparams = ['id' => $context->instanceid, 'groupid' => $group->id, 'action' => 'modify'];
        $modifyurl = new \moodle_url('/mod/groupproject/group.php', $modifyparams);

        $links .= $OUTPUT->action_icon(
            $modifyurl,
            new \pix_icon('t/edit', get_string('modify', 'mod_groupproject'))
        );

        // Delete.
        $deleteparams = ['id' => $context->instanceid, 'groupid' => $group->id, 'action' => 'delete'];
        $deleteurl = new \moodle_url('/mod/groupproject/group.php', $deleteparams);
        $deleteconfirm = new \confirm_action(get_string('confirmdelete', 'mod_groupproject'));
        $links .= $OUTPUT->action_icon(
            $deleteurl,
            new \pix_icon('t/delete', get_string('delete')),
            $deleteconfirm
        );

        return $links;
    }
}