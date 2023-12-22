<?php

/**
 * Grade entity.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 */

namespace mod_groupproject\local\entities;

use mod_groupproject\local\loaders\entity_loader;

class grade extends entity {
    public static $TABLE = 'groupproject_grades';

    /** @var int $groupid Id of the group who got the grade */
    protected $groupid;
    /** @var int $grader Id of the person who graded */
    protected $grader;
    /** @var float $timecreated  The grade itself */
    protected $grade;
    /** @var int $timemodified Grade modification unix timestamp  */
    protected $timemodified;
    /** @var int $timecreated Grade creation unix timestamp  */
    protected $timecreated;

    /**
     * @param int $id
     * @param int $groupid
     * @param int $grader
     * @param float $grade
     * @param int $timemodified
     * @param int $timecreated
     */
    public function __construct(
        int $id,
        int $groupid,
        int $grader,
        float $grade,
        int $timemodified,
        int $timecreated)
    {
        $this->id = $id;
        $this->groupid = $groupid;
        $this->grader = $grader;
        $this->grade = $grade;
        $this->timemodified = $timemodified;
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
    public function getGrader(): int
    {
        return $this->grader;
    }

    /**
     * @param int $grader
     */
    public function setGrader(int $grader): void
    {
        $this->grader = $grader;
    }

    /**
     * @return float
     */
    public function getGrade(): float
    {
        return $this->grade;
    }

    /**
     * @param float $grade
     */
    public function setGrade(float $grade): void
    {
        $this->grade = $grade;
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
     * Converts the current grade to a string that can be read in the system.
     * @return float|string
     * @throws \dml_exception
     */
    public function convert_grade()
    {
        global $DB;

        $group = entity_loader::group_loader($this->groupid);
        $groupproject = entity_loader::groupproject_loader($group->getGroupprojectid());
        $gradeitem = $groupproject->get_gradeitem();
        if(!empty($gradeitem->scaleid)){
            $scale = $DB->get_record('scale', ['id' => $gradeitem->scaleid]);
            $scales = explode(',',$scale->scale);
            return $scales[($this->grade) - 1];
        }
        return $this->grade;
    }
}