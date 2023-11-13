<?php

namespace mod_groupproject\output;

use mod_groupproject\local\factories\entity_factory;
use mod_groupproject\local\loaders\entity_loader;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

class group_table extends \table_sql
{
    public function __construct($uniqueid, $url) {
        global $CFG;
        parent::__construct($uniqueid);

        // Define columns in the table.
        $this->define_table_columns();

        $this->define_baseurl($url);
        // Define configs.
        $this->define_table_configs();
    }

    private function define_table_columns()
    {
        $cols = array(
            'name' => get_string('groupname', 'mod_groupproject'),
            'groupsize' => get_string('groupsize', 'mod_groupproject'),
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

    public function  col_groupsize($group){
        $group_obj = entity_factory::create_group_from_stdclass($group);
        return   count($group_obj->getUserIds()) . ' / ' . $group->size ;
    }

    public function  col_fileupload($group){
        return 'nothing';
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