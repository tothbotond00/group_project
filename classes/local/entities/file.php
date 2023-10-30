<?php

namespace mod_groupproject\local\entities;

class file extends entity {

    public static $TABLE = 'groupproject_files';

    /** @var int $groupid Id of the group uploaded the file  */
    protected $groupid;
    /** @var int $fielid Id of the file stored in teh files table */
    protected $fielid;
    /** @var int $timecreated  File creation unix timestamp */
    protected $timecreated;
    /** @var int $timemodified File modification unix timestamp  */
    protected $timemodified;

    /**
     * @param int $groupid
     * @param int $fielid
     * @param int $timecreated
     * @param int $timemodified
     */
    public function __construct(
        int $id,
        int $groupid,
        int $fielid,
        int $timecreated,
        int $timemodified)
    {
        $this->id = $id;
        $this->groupid = $groupid;
        $this->fielid = $fielid;
        $this->timecreated = $timecreated;
        $this->timemodified = $timemodified;
    }

    /**
     * @return int
     */
    public function getGroupid(): int
    {
        return $this->groupid;
    }

    /**
     * @param int $groupid
     */
    public function setGroupid(int $groupid): void
    {
        $this->groupid = $groupid;
    }

    /**
     * @return int
     */
    public function getFielid(): int
    {
        return $this->fielid;
    }

    /**
     * @param int $fielid
     */
    public function setFielid(int $fielid): void
    {
        $this->fielid = $fielid;
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
        //TODO Delete file itself

        parent::delete();
    }

}