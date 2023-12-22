<?php

/**
 * Capability entity.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 */

namespace mod_groupproject\local\entities;

use mod_groupproject\local\loaders\entity_loader;

class capability extends entity
{
    public static $TABLE = 'groupproject_capabilities';

    /** @var int $roleid The id of the role */
    private $roleid;

    /** @var int $capabilityid The id of the capability */
    private $capabilityid;

    /**
     * @param int $roleid
     * @param int $capabilityid
     */
    public function __construct(int $id, int $roleid, int $capabilityid)
    {
        $this->id = $id;
        $this->roleid = $roleid;
        $this->capabilityid = $capabilityid;
    }

    /**
     * @return int
     */
    public function getRoleid(): int
    {
        return $this->roleid;
    }

    /**
     * @param int $roleid
     */
    public function setRoleid(int $roleid): void
    {
        $this->roleid = $roleid;
    }

    /**
     * @return int
     */
    public function getCapibilityid(): int
    {
        return $this->capabilityid;
    }

    /**
     * @param int $capabilityid
     */
    public function setCapibilityid(int $capabilityid): void
    {
        $this->capabilityid = $capabilityid;
    }


    /**
     * Returns the valid capibilities that a role can have.
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function get_valid_capabilities(){
        global $CFG, $DB;

        require_once($CFG->dirroot . '/mod/groupproject/locallib.php');

        $sql_like = $DB->sql_like('c.name','?');
        $params[] = '%mod/groupproject%';
        $sql = "SELECT *
                  FROM {capabilities} c
                 WHERE {$sql_like}";
        $records = $DB->get_records_sql($sql,$params);
        $capabilities = [];
        foreach ($records as $record){
            if(in_array($record->name, GROUPPROJECT_FELXIBLE_CAPIBILITIES)){
                $capabilities[$record->id] = get_string($record->name, 'mod_groupproject');
            }
        }
        return $capabilities;
    }

    /**
     *
     * @param $roleid
     * @param $param
     * @return array
     * @throws \dml_exception
     */
    public static function get_role_assignments($roleid, $param = 'id'){
        global $DB;

        $records = $DB->get_records(self::$TABLE, ['roleid' => $roleid]);
        $capabilities = [];
        foreach ($records as $record){
            if($capability = $DB->get_record('capabilities', ['id' => $record->capabilityid])){
                $capabilities[$capability->id] = $capability->$param;
            }
        }
        return $capabilities;
    }

    /**
     * Checks in the given groupproject if a user has capbility.
     * @param groupproject $groupproject
     * @param $capability
     * @param \context $context
     * @return bool
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function has_capability(groupproject $groupproject, $capability,\context $context,?int $groupid = null): bool{
        global $USER;

        if(has_capability($capability, $context)) {
            return true;
        }
        $group = $groupproject->user_has_group();
        if($group){
            if(!empty($groupid) && $group->getId() != $groupid) return false;
            $roleid = $group->get_user_role_id($USER->id);
            if(!empty($roleid)){
                $role = entity_loader::role_loader($roleid);
                return $role->has_capability($capability);
            }
        }
        return false;
    }
}