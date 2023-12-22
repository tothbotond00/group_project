<?php

/**
 * Role entity.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 */

namespace mod_groupproject\local\entities;

use mod_groupproject\local\factories\entity_factory;
use mod_groupproject\local\loaders\entity_loader;

class role extends entity {

    public static $TABLE = 'groupproject_roles';

    /** @var string $name Name of the role */
    protected $name;
    /** @var string $name Description of the role */
    protected $description;
    /** @var int $timecreated  Role creation unix timestamp */
    protected $timecreated;
    /** @var int $timecreated  Role modification unix timestamp */
    protected $timemodified;

    /**
     * @param string $name
     * @param string $description
     * @param int $timecreated
     * @param int $timemodified
     */
    public function __construct(
        int $id,
        string $name,
        string $description,
        int $timecreated,
        int $timemodified)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->timecreated = $timecreated;
        $this->timemodified = $timemodified;
    }

    /**
     * Returns every available role.
     * @return array|\stdClass[]
     * @throws \dml_exception
     */
    public static function get_all_roles()
    {
        global $DB;
        $empty = new \stdClass(); $empty->id = '0'; $empty->name = ' - ';
        return [$empty] + $DB->get_records(static::$TABLE);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return stdClass
     */
    public function getDescription(): \stdClass
    {
        return json_decode($this->description);
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getTimecreated(): int
    {
        return $this->timecreated;
    }

    /**
     * @param int $timecreated
     */
    public function setTimecreated(int $timecreated): void
    {
        $this->timecreated = $timecreated;
    }

    /**
     * @return int
     */
    public function getTimemodified(): int
    {
        return $this->timemodified;
    }

    /**
     * @param int $timemodified
     */
    public function setTimemodified(int $timemodified): void
    {
        $this->timemodified = $timemodified;
    }

    /**
     * Deletes every role by setting every user_role to null in the given tables.
     * @return void
     * @throws \dml_exception
     */
    public function delete()
    {
        global $DB;
        $user_assigns = $DB->get_records(user_assign::$TABLE, array('roleid' => $this->id));
        foreach ($user_assigns as $user_assign) {
            $user_assign = entity_factory::create_user_assign_from_stdclass($user_assign);
            $user_assign->setRoleid(null);
            $user_assign->update();
        }

        parent::delete();
    }

    /**
     * Updates the capibilities that a role can have.
     * @param $capabilities
     * @return void
     * @throws \dml_exception
     */
    public function update_capabilities($capabilities)
    {
        global $DB;
        $DB->delete_records(capability::$TABLE, ['roleid' => $this->id]);

        foreach ($capabilities as $capability){
            $record = new \stdClass();
            $record->roleid = $this->id;
            $record->capabilityid = $capability;
            capability::create($record);
        }
    }

    /**
     * Checks if the role has the given capibility
     * @param $capability
     * @return bool
     * @throws \dml_exception
     */
    public function has_capability($capability): bool
    {
        global $DB;

        $paramteres = [];
        $sql = "SELECT gc.id
                  FROM {groupproject_capabilities} gc 
                  JOIN {capabilities} c ON c.id = gc.capabilityid
                 WHERE gc.roleid = {$this->id} AND c.name = :name ";
        $paramteres['name'] = $capability;
        $returns = $DB->get_records_sql($sql, $paramteres);
        return !empty($returns);
    }

}