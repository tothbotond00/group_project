<?php

/**
 * Entity loader tests.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 * @coversDefaultClass mod_groupproject\local\loaders\entity_loader
 */

namespace mod_groupproject\local\loaders;

use mod_groupproject\local\entities\capability;
use mod_groupproject\local\entities\comment;
use mod_groupproject\local\entities\grade;
use mod_groupproject\local\entities\group;
use mod_groupproject\local\entities\groupproject;
use mod_groupproject\local\entities\role;
use mod_groupproject\local\entities\user_assign;
use mod_groupproject\local\factories\entity_factory;

class entity_loader_test extends \advanced_testcase {

    /**
     * Returns a groupproject entity based on the id.
     */
    public function test_groupproject_loader() {
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->course = 1;
        $record->name = "Test";
        $record->intro = "";
        $record->introformat = 10;
        $record->duedate = 0;
        $record->grade = 0;
        $record->timecreated = $record->timemodified = time();
        $id = groupproject::create($record);
        $groupproject = entity_loader::groupproject_loader($id);
        $this->assertEquals($record->course, $groupproject->getCourse());
        $this->assertEquals($record->name, $groupproject->getName());
        $this->assertEquals($record->intro, $groupproject->getIntro());
        $this->assertEquals($record->introformat, $groupproject->getIntroformat());
        $this->assertEquals($record->duedate, $groupproject->getDuedate());
        $this->assertEquals($record->grade, $groupproject->getGrade());
        $this->assertEquals($record->timecreated, $groupproject->getTimecreated());
    }

    /**
     * Returns a group entity based on the id.
     */
    public function test_group_loader(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->groupprojectid = 1;
        $record->name = "Test";
        $record->idnumber = "";
        $record->size = 10;
        $record->timecreated = $record->timemodified = time();
        $id = group::create($record);
        $group = entity_loader::group_loader($id);
        $this->assertEquals($record->groupprojectid, $group->getGroupprojectid());
        $this->assertEquals($record->name, $group->getName());
        $this->assertEquals($record->idnumber, $group->getIdnumber());
        $this->assertEquals($record->size, $group->getSize());
        $this->assertEquals($record->timecreated, $group->getTimecreated());
    }

    /**
     * Returns a role entity based on the id.
     */
    public function test_role_loader(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->name = "Test";
        $record->description = "Test";
        $record->timecreated = $record->timemodified = time();
        $id = role::create($record);
        $role = entity_loader::role_loader($id);
        $this->assertEquals($record->name, $role->getName());
        $this->assertEquals($record->timecreated, $role->getTimecreated());
    }

    /**
     * Returns a user_assign entity based on the id.
     */
    public function test_user_assign_loader(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->userid = 2;
        $record->groupid = 3;
        $record->roleid = 4;
        $id = user_assign::create($record);
        $user_assign = entity_loader::user_assign_loader($id);
        $this->assertEquals($record->userid, $user_assign->getUserid());
        $this->assertEquals($record->groupid, $user_assign->getGroupid());
        $this->assertEquals($record->roleid, $user_assign->getRoleid());
    }

    /**
     * Returns a comment entity based on the id.
     */
    public function test_comment_loader(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->groupid = 3;
        $record->userid = 2;
        $record->comment = "Test";
        $record->timecreated = time();
        $id = comment::create($record);
        $comment = entity_loader::comment_loader($id);
        $this->assertEquals($record->userid, $comment->getUserid());
        $this->assertEquals($record->groupid, $comment->getGroupid());
        $this->assertEquals($record->comment, $comment->getComment());
        $this->assertEquals($record->timecreated, $comment->getTimecreated());
    }

    /**
     * Returns a capability entity based on the id.
     */
    public function test_capability_loader(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->roleid = 3;
        $record->capabilityid = 2;
        $id = capability::create($record);
        $capability = entity_loader::capability_loader($id);
        $this->assertEquals($record->roleid, $capability->getRoleid());
        $this->assertEquals($record->capabilityid, $capability->getCapibilityid());
    }

    /**
     * Returns a grade entity based on the id.
     */
    public function test_grade_loader(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->groupid = 3;
        $record->grader = 3;
        $record->grade = 13;
        $record->timecreated = $record->timemodified = time();
        $id = grade::create($record);
        $grade = entity_loader::grade_loader($id);
        $this->assertEquals($record->groupid, $grade->getGroupid());
        $this->assertEquals($record->grader, $grade->getGrader());
        $this->assertEquals($record->grade, $grade->getGrade());
        $this->assertEquals($record->timecreated, $grade->getTimecreated());
    }
}