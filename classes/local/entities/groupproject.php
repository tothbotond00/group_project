<?php

namespace mod_groupproject\local\entities;

use mod_groupproject\local\factories\entity_factory;

class groupproject extends entity {

    /** @var string $TABLE DB table of class */
    public static $TABLE = 'groupproject';

    /** @var int $course ID of the course  */
    private $course;
    /** @var string $name Name of groupproject */
    private $name;
    /** @var string $intro Intro of the groupproject */
    private $intro;
    /** @var int $intro Intro of the groupproject */
    private $introformat;
    /** @var int $timecreated Group creation unix timestamp */
    private $timecreated;
    /** @var int $timemodified Group creation unix timestamp */
    private $timemodified;
    /** @var array $groups The Groups associated with the project */
    private $groups = array();

    /**
     * @param int $id
     * @param int $course
     * @param string $name
     * @param string $intro
     * @param int $introformat
     * @param int $timecreated
     * @param int $timemodified
     */
    public function __construct(
        int $id,
        int $course,
        string $name,
        string $intro,
        int $introformat,
        int $timecreated,
        int $timemodified
    ) {
        $this->id = $id;
        $this->course = $course;
        $this->name = $name;
        $this->intro = $intro;
        $this->introformat = $introformat;
        $this->timecreated = $timecreated;
        $this->timemodified = $timemodified;
        $this->loadGroups();
    }

    /**
     * @return int
     */
    public function getCourse(): int
    {
        return $this->course;
    }

    /**
     * @param int $course
     */
    public function setCourse(int $course): void
    {
        $this->course = $course;
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
    public function getIntro(): string
    {
        return $this->intro;
    }

    /**
     * @param string $intro
     */
    public function setIntro(string $intro): void
    {
        $this->intro = $intro;
    }

    /**
     * @return int
     */
    public function getIntroformat(): int
    {
        return $this->introformat;
    }

    /**
     * @param int $introformat
     */
    public function setIntroformat(int $introformat): void
    {
        $this->introformat = $introformat;
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

    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @param array $groups
     */
    public function setGroups(array $groups): void
    {
        $this->groups = $groups;
    }

    private function loadGroups()
    {
        global $DB;

        $this->groups = array();
        $groups = $DB->get_records(group::$TABLE, array('groupprojectid' => $this->id));

        foreach ($groups as $group){
            $this->groups[] = entity_factory::create_group_from_stdclass($group);
        }
    }

    public function delete()
    {
        foreach ($this->groups as $group) {
            $group->delete();
        }

        parent::delete();
    }

}