<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Group class.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_groupproject\local\entities;
use core_group\reportbuilder\datasource\groups;
use mod_groupproject\local\factories\entity_factory;
use mod_groupproject\local\loaders\entity_loader;

class group extends entity {

    /** @var string $TABLE DB table of class */
    public static $TABLE = 'groupproject_groups';


    /** @var int $groupprojectid Id of the groupproject instance */
    private $groupprojectid;
    /** @var string $name Name of the group  */
    private $name;
    /** @var ?string $idnumber Idnumber of group */
    private $idnumber;
    /** @var int $size Size of the group */
    private $size;
    /** @var int $timecreated Group creation unix timestamp */
    private $timecreated;
    /** @var int $timemodified Group creation unix timestamp */
    private $timemodified;
    /** @var array $users The users in the current group */
    private $users = array();

    /**
     * @param int $id
     * @param string $name
     * @param ?string $idnumber
     * @param int $size
     * @param int $timecreated
     * @param int $timemodified
     */
    public function __construct(
        int $id,
        int $groupprojectid,
        string $name,
        ?string $idnumber,
        int $size,
        int $timecreated,
        int $timemodified
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->idnumber = $idnumber;
        $this->size = $size;
        $this->timecreated = $timecreated;
        $this->timemodified = $timemodified;
    }

    public function getGroupprojectid(): int
    {
        return $this->groupprojectid;
    }

    public function setGroupprojectid(int $groupprojectid): void
    {
        $this->groupprojectid = $groupprojectid;
    }

    /**
     * @return string|null
     */
    public function getIdnumber(): ?string
    {
        return $this->idnumber;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return int
     */
    public function getTimecreated(): int
    {
        return $this->timecreated;
    }

    /**
     * @return int
     */
    public function getTimemodified(): int
    {
        return $this->timemodified;
    }

    public function delete()
    {
        global $DB;

        $DB->delete_records('groupproject_user_assign', ['groupid' => $this->id]);
        $DB->delete_records('groupproject_comments', ['groupid' => $this->id]);
        $DB->delete_records('groupproject_grades', ['groupid' => $this->id]);
        $files = $DB->get_records('groupproject_files', ['groupid' => $this->id]);
        foreach ($files as $file){
            $file = entity_factory::create_file_from_stdclass($file);
            $file->delete();
        }

        parent::delete();
    }

    public function getUsers(){
        global $DB;
        return $DB->get_records_select('groupproject_user_assign', 'userid',array('groupid' => $this->id));
    }

    public function getComments() {
        global $DB;
        $records = $DB->get_records('groupproject_comments',array('groupid' => $this->id));
        $comments = array();
        foreach ($records as $record) {
            $comments[] = entity_factory::create_comment_from_stdclass($record);
        }
        return $comments;
    }

}