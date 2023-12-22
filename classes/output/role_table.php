<?php

/**
 * Role table.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 */

namespace mod_groupproject\output;

use mod_groupproject\local\entities\capability;

class role_table extends \table_sql {
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
            'name' => get_string('rolename', 'mod_groupproject'),
            'description' => get_string('roledescription', 'mod_groupproject'),
            'capabilities' => get_string('capabilities', 'mod_groupproject'),
            'actions' => get_string('actions', 'mod_groupproject'),
        );

        $this->define_columns(array_keys($cols));
        $this->define_headers(array_values($cols));
    }

    private function define_table_configs()
    {
        $this->collapsible(false);
        $this->sortable(true, 'rolename', SORT_ASC);
        $this->pageable(true);
        $this->no_sorting('actions');
        $this->no_sorting('capabilities');
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
                  FROM {groupproject_roles} r";

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

    public function col_description($role){
        return json_decode($role->description)->text;
    }

    public function col_capabilities($role){
        $capabilities = capability::get_role_assignments($role->id, 'name');
        $o = '';
        foreach ($capabilities as $capability){
            $o .= get_string($capability, 'mod_groupproject') . '<br>';
        }
        return $o;
    }

    public function col_actions($role){
        global $OUTPUT;

        $links = '';

        // Modify
        $modifyparams = ['roleid' => $role->id, 'action' => 'modify'];
        $modifyurl = new \moodle_url('/mod/groupproject/role.php', $modifyparams);

        $links .= $OUTPUT->action_icon(
            $modifyurl,
            new \pix_icon('t/edit', get_string('modify', 'mod_groupproject'))
        );

        // Delete.
        $deleteparams = ['roleid' => $role->id, 'action' => 'delete'];
        $deleteurl = new \moodle_url('/mod/groupproject/role.php', $deleteparams);
        $deleteconfirm = new \confirm_action(get_string('confirmdelete', 'mod_groupproject'));
        $links .= $OUTPUT->action_icon(
            $deleteurl,
            new \pix_icon('t/delete', get_string('delete')),
            $deleteconfirm
        );

        return $links;
    }

}