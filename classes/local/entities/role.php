<?php

namespace mod_groupproject\local\entities;

use mod_groupproject\local\factories\entity_factory;
use mod_groupproject\local\loaders\entity_loader;

class role extends entity {

    public static $TABLE = 'groupproject_roles';

    /** @var string $name Name of the role */
    private $name;
    /** @var string $name Description of the role */
    private $description;
    /** @var int $timecreated  Role creation unix timestamp */
    private $timecreated;
    /** @var int $timecreated  Role modification unix timestamp */
    private $timemodified;

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
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
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

}