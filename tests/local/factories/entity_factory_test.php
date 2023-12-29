<?php

/**
 * Entity factory tests.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 * @coversDefaultClass mod_groupproject\local\factories\entity_factory
 */

namespace mod_groupproject\local\factories;

use mod_groupproject\local\entities\capability;
use mod_groupproject\local\entities\comment;
use mod_groupproject\local\entities\grade;
use mod_groupproject\local\entities\group;
use mod_groupproject\local\entities\groupproject;
use mod_groupproject\local\entities\role;
use mod_groupproject\local\entities\user_assign;

class entity_factory_test extends \advanced_testcase {

    /**
     * Creates groupproject entity from a stdclass.
     */
    public function test_create_groupproject_from_stdclass(){
        $record = new \stdClass();
        $record->id = 1;
        $record->course = 1;
        $record->name = "Test";
        $record->intro = "";
        $record->introformat = 10;
        $record->duedate = 0;
        $record->grade = 0;
        $record->timecreated = $record->timemodified = time();
        $groupproject = entity_factory::create_groupproject_from_stdclass($record);
        $this->assertEquals($record->id, $groupproject->getId());
        $this->assertEquals($record->course, $groupproject->getCourse());
        $this->assertEquals($record->name, $groupproject->getName());
        $this->assertEquals($record->intro, $groupproject->getIntro());
        $this->assertEquals($record->introformat, $groupproject->getIntroformat());
        $this->assertEquals($record->duedate, $groupproject->getDuedate());
        $this->assertEquals($record->grade, $groupproject->getGrade());
        $this->assertEquals($record->timecreated, $groupproject->getTimecreated());
    }

    /**
     * Creates group entity from a stdclass.
     */
    public function test_create_group_from_stdclass() {
        $record = new \stdClass();
        $record->id = 1;
        $record->groupprojectid = 1;
        $record->name = "Test";
        $record->idnumber = "";
        $record->size = 10;
        $record->timecreated = $record->timemodified = time();
        $group = entity_factory::create_group_from_stdclass($record);
        $this->assertEquals($record->id, $group->getId());
        $this->assertEquals($record->groupprojectid, $group->getGroupprojectid());
        $this->assertEquals($record->name, $group->getName());
        $this->assertEquals($record->idnumber, $group->getIdnumber());
        $this->assertEquals($record->size, $group->getSize());
        $this->assertEquals($record->timecreated, $group->getTimecreated());
    }

    /**
     * Creates role entity from a stdclass.
     */
    public function test_create_role_from_stdclass() {
        $record = new \stdClass();
        $record->id = 1;
        $record->name = "Test";
        $record->description = "Test";
        $record->timecreated = $record->timemodified = time();
        $role = entity_factory::create_role_from_stdclass($record);
        $this->assertEquals($record->id, $role->getId());
        $this->assertEquals($record->name, $role->getName());
        $this->assertEquals($record->timecreated, $role->getTimecreated());
    }

    /**
     * Creates user_assign entity from a stdclass.
     */
    public function test_create_user_assign_from_stdclass(){
        $record = new \stdClass();
        $record->id = 1;
        $record->userid = 2;
        $record->groupid = 3;
        $record->roleid = 4;
        $user_assign = entity_factory::create_user_assign_from_stdclass($record);
        $this->assertEquals($record->id, $user_assign->getId());
        $this->assertEquals($record->userid, $user_assign->getUserid());
        $this->assertEquals($record->groupid, $user_assign->getGroupid());
        $this->assertEquals($record->roleid, $user_assign->getRoleid());
    }

    /**
     * Creates comment entity from a stdclass.
     */
    public function test_create_comment_from_stdclass() {
        $record = new \stdClass();
        $record->id = 1;
        $record->groupid = 3;
        $record->userid = 2;
        $record->comment = "Test";
        $record->timecreated = time();
        $comment = entity_factory::create_comment_from_stdclass($record);
        $this->assertEquals($record->id, $comment->getId());
        $this->assertEquals($record->userid, $comment->getUserid());
        $this->assertEquals($record->groupid, $comment->getGroupid());
        $this->assertEquals($record->comment, $comment->getComment());
        $this->assertEquals($record->timecreated, $comment->getTimecreated());
    }

    /**
     * Creates capability entity from a stdclass.
     */
    public function test_crate_capability_from_stdclass(){
        $record = new \stdClass();
        $record->id = 1;
        $record->roleid = 3;
        $record->capabilityid = 2;
        $capability = entity_factory::crate_capability_from_stdclass($record);
        $this->assertEquals($record->id, $capability->getId());
        $this->assertEquals($record->roleid, $capability->getRoleid());
        $this->assertEquals($record->capabilityid, $capability->getCapibilityid());
    }

    /**
     * Creates grade entity from a stdclass.
     */
    public function test_create_grade_from_stdclass(){
        $record = new \stdClass();
        $record->id = 1;
        $record->groupid = 3;
        $record->grader = 3;
        $record->grade = 13;
        $record->timecreated = $record->timemodified = time();
        $grade = entity_factory::create_grade_from_stdclass($record);
        $this->assertEquals($record->id, $grade->getId());
        $this->assertEquals($record->groupid, $grade->getGroupid());
        $this->assertEquals($record->grader, $grade->getGrader());
        $this->assertEquals($record->grade, $grade->getGrade());
        $this->assertEquals($record->timecreated, $grade->getTimecreated());
    }

}