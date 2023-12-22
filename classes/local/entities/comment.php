<?php

/**
 * Comment entity.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 */

namespace mod_groupproject\local\entities;

use mod_groupproject\local\factories\entity_factory;
use mod_groupproject\local\loaders\entity_loader;

class comment extends entity{

    public static $TABLE = 'groupproject_comments';

    /** @var int $groupid The ID of the group */
    protected $groupid;
    /** @var int $userid The ID of the user*/
    protected $userid;
    /** @var string $comment The comment */
    protected $comment;
    /** @var int $timecreated Comment creation timestamp */
    protected $timecreated;

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

    /**
     * Returns the new comments for an external service.
     * @param int $count
     * @param $groupid
     * @param $userid
     * @return array
     * @throws \dml_exception
     */
    public static function get_group_comments(int $count, $groupid, $userid){
        global $DB;
        $records = $DB->get_records(self::$TABLE, ['groupid' => $groupid], 'timecreated DESC');

        $difference = count($records) - $count;

        $comments = [];
        foreach($records as $record){
            if($difference === 0) break;
            if($userid == $record->userid) continue;
            $comments[] = entity_factory::create_comment_from_stdclass($record);
            $difference--;
        }

        return $comments;
    }

}