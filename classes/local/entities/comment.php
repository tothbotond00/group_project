<?php

namespace mod_groupproject\local\entities;

class comment extends entity{

    public static $TABLE = 'groupproject_comments';

    /** @var int $groupid The ID of the group */
    private $groupid;
    /** @var int $userid The ID of the user*/
    private $userid;
    /** @var string $comment The comment */
    private $comment;
    /** @var int $timecreated Comment creation timestamp */
    private $timecreated;

    /**
     * @param int $groupid
     * @param int $userid
     * @param string $comment
     * @param int $timecreated
     */
    public function __construct(
        int $id,
        int $groupid,
        int $userid,
        string $comment,
        int $timecreated)
    {
        $this->id = $id;
        $this->groupid = $groupid;
        $this->userid = $userid;
        $this->comment = $comment;
        $this->timecreated = $timecreated;
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
    public function getUserid(): int
    {
        return $this->userid;
    }

    /**
     * @param int $userid
     */
    public function setUserid(int $userid): void
    {
        $this->userid = $userid;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
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

}