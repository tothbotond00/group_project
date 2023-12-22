<?php

/**
 * Group entity.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 */

namespace mod_groupproject\local\entities;
use grade_grade;
use grade_item;
use mod_groupproject\local\factories\entity_factory;
use mod_groupproject\local\loaders\entity_loader;

global $CFG;

require_once ($CFG->libdir . '/gradelib.php');

class group extends entity {

    /** @var string $TABLE DB table of class */
    public static $TABLE = 'groupproject_groups';


    /** @var int $groupprojectid Id of the groupproject instance */
    protected $groupprojectid;
    /** @var string $name Name of the group  */
    protected $name;
    /** @var ?string $idnumber Idnumber of group */
    protected $idnumber;
    /** @var int $size Size of the group */
    protected $size;
    /** @var int $timecreated Group creation unix timestamp */
    protected $timecreated;
    /** @var int $timemodified Group creation unix timestamp */
    protected $timemodified;
    /** @var array $users The users in the current group */
    protected $users = array();

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
        $this->groupprojectid = $groupprojectid;
        $this->name = $name;
        $this->idnumber = $idnumber;
        $this->size = $size;
        $this->timecreated = $timecreated;
        $this->timemodified = $timemodified;
    }

    /**
     * @return int
     */
    public function getGroupprojectid(): int
    {
        return $this->groupprojectid;
    }

    /**
     * @param int $groupprojectid
     * @return void
     */
    public function setGroupprojectid(int $groupprojectid): void
    {
        $this->groupprojectid = $groupprojectid;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return void
     */
    public function setName($name){
        $this->name = $name;
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

    /**
     * @param string|null $idnumber
     */
    public function setIdnumber(?string $idnumber): void
    {
        $this->idnumber = $idnumber;
    }

    /**
     * @param int $size
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * @param int $timecreated
     */
    public function setTimecreated(int $timecreated): void
    {
        $this->timecreated = $timecreated;
    }

    /**
     * @param int $timemodified
     */
    public function setTimemodified(int $timemodified): void
    {
        $this->timemodified = $timemodified;
    }

    /**
     * Deletes the group user assignments, grades, comments and files related to the group.
     * @return void
     * @throws \dml_exception
     */
    public function delete()
    {
        global $DB;

        //Related table data deletion
        $DB->delete_records(user_assign::$TABLE, ['groupid' => $this->id]);
        $DB->delete_records(comment::$TABLE, ['groupid' => $this->id]);
        $DB->delete_records(grade::$TABLE, ['groupid' => $this->id]);

        //Submission deletion
        $groupproject = entity_loader::groupproject_loader($this->groupprojectid);
        $context = $groupproject->get_context();
        $fs = new \file_storage();
        list($in, $inparams) = $DB->get_in_or_equal([$this->id], SQL_PARAMS_NAMED);
        $fs->delete_area_files_select($context->id, 'mod_groupproject','groupproject_submission',$in, $inparams);

        parent::delete();
    }

    /**
     * Returns the userids in an array that belong to the group.
     * @return array
     * @throws \dml_exception
     */
    public function get_user_ids(){
        global $DB;
        $userids = [];
        foreach ($DB->get_records(user_assign::$TABLE, array('groupid' => $this->id)) as $record){
            $userids[] = $record->userid;
        }
        return $userids;
    }


    /**
     * Returns the role of the user in the group. Null if the user has no role.
     * @param $userid
     * @return false|mixed
     * @throws \dml_exception
     */
    public function get_user_role_id($userid){
        global $DB;

        return $DB->get_field(user_assign::$TABLE, 'roleid', ['groupid' => $this->id, 'userid' => $userid]);
    }


    /**
     * Returns the user assings as stdclasses.
     * @return array
     * @throws \dml_exception
     */
    public function get_users(){
        global $DB;
        return $DB->get_records(user_assign::$TABLE, array('groupid' => $this->id));
    }

    /**
     * Returns the comments as comment entites.
     * @return array
     * @throws \dml_exception
     */
    public function get_comments() {
        global $DB;
        $records = $DB->get_records(comment::$TABLE,array('groupid' => $this->id));
        $comments = array();
        foreach ($records as $record) {
            $comments[] = entity_factory::create_comment_from_stdclass($record);
        }
        return $comments;
    }

    /**
     * Gives the grade to every user in the group from the parameter.
     * @param $grade
     * @return void
     * @throws \dml_exception
     */
    public function grade_users($grade)
    {
        global $DB, $USER;
        $groupproject = entity_loader::groupproject_loader($this->groupprojectid);
        $gradeitem = $groupproject->get_gradeitem();
        if($groupproject->getGrade() < 0) $grade += 1;
        if(empty($gradeitem)) return;

        //Updates every gradeitem for the users in the gorup
        foreach ($DB->get_records(user_assign::$TABLE, array('groupid' => $this->id)) as $record){
            $userid = $record->userid;
            $gradegrade = new grade_grade(array('itemid'=>$gradeitem->id, 'userid'=>$userid));
            if(empty($gradegrade)){
                $gradegrade = new grade_grade([
                    'itemid' => $gradeitem->id,
                    'userid' => $userid,
                    'rawgrade' => null,
                    'rawgrademax' => $gradeitem->grademax,
                    'rawgrademin' => $gradeitem->grademin,
                    'rawscaleid' => $gradeitem->scaleid,
                    'usermodified' => $USER->id,
                    'finalgrade' => $grade,
                    'timemodified' => time(),
                    'aggreagtionstatus' => 'unknown']);
                $gradegrade->insert('mod_groupproject');
            }else {
                $gradegrade->itemid = $gradeitem->id;
                $gradegrade->userid = $userid;
                $gradegrade->rawgrade = null;
                $gradegrade->rawgrademax = $gradeitem->grademax;
                $gradegrade->rawgrademin = $gradeitem->grademin;
                $gradegrade->rawscaleid = $gradeitem->scaleid;
                $gradegrade->finalgrade = (float)$grade;
                $gradegrade->usermodified = $USER->id;
                $gradegrade->aggregationstatus = 'unknown';
                $gradegrade->timemodified = time();
                if(empty($gradegrade->id)){
                    $gradegrade->insert('mod_groupproject');
                }
                else $gradegrade->update('mod_groupproject');
            }
        }
        $course_grade = grade_item::fetch_course_item($groupproject->getCourse());
        $course_grade->force_regrading();

        //Saves the grade to the grade table
        if($groupgrade = $DB->get_record(grade::$TABLE, ['groupid' => $this->getId()])){
            $groupgrade = entity_loader::grade_loader($groupgrade->id);
            $groupgrade->setGrade($grade);
            $groupgrade->setTimemodified(time());
            $groupgrade->setGrader($USER->id);
            $groupgrade->update();
        }else {
            $record = new \stdClass();
            $record->groupid = $this->getId();
            $record->timecreated = $record->timemodified = time();
            $record->grader = $USER->id;
            $record->grade = $grade;
            grade::create($record);
        }

    }

}