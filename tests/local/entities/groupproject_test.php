<?php

/**
 * Groupproject entity tests.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 * @coversDefaultClass mod_groupproject\local\entities\groupproject
 */

namespace mod_groupproject\local\entities;

use mod_groupproject\local\loaders\entity_loader;

class groupproject_test extends \advanced_testcase {

    /**
     * Test for groupproject creation
     */
    public function test_create(){
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
        $this->assertNotEmpty($id);
    }

    /**
     * Test for groupproject modify
     */
    public function test_update(){
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
        $groupproject->setName("Test 2");
        $groupproject->setCourse(2);
        $groupproject->setIntro("asd");
        $groupproject->setIntroformat(11);
        $groupproject->setDuedate(1);
        $groupproject->setGrade(1);
        $groupproject->setTimecreated(time()+1);
        $this->assertNotEquals($record->course, $groupproject->getCourse());
        $this->assertNotEquals($record->name, $groupproject->getName());
        $this->assertNotEquals($record->intro, $groupproject->getIntro());
        $this->assertNotEquals($record->introformat, $groupproject->getIntroformat());
        $this->assertNotEquals($record->duedate, $groupproject->getDuedate());
        $this->assertNotEquals($record->grade, $groupproject->getGrade());
        $this->assertNotEquals($record->timecreated, $groupproject->getTimecreated());
    }

    /**
     * Test for groupproject deletion
     */
    public function test_delete(){
        global $DB;

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
        $groupproject->delete();
        $this->assertEquals(false, $DB->record_exists(groupproject::$TABLE,['id' => $id]));
    }

    /**
     * Tests the loading group function
     */
    public function test_load_groups(){
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

        $record = new \stdClass();
        $record->groupprojectid = $id;
        $record->name = "Test";
        $record->idnumber = "";
        $record->size = 10;
        $record->timecreated = $record->timemodified = time();
        group::create($record);

        $groupproject = entity_loader::groupproject_loader($id);
        $this->assertNotEmpty($groupproject->getGroups());
    }

    /**
     * Tests if the user is a member of a group
     */
    public function test_user_has_group(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->course = 1;
        $record->name = "Test";
        $record->intro = "";
        $record->introformat = 10;
        $record->duedate = 0;
        $record->grade = 0;
        $record->timecreated = $record->timemodified = time();
        $gid = groupproject::create($record);

        $record = new \stdClass();
        $record->groupprojectid = $gid;
        $record->name = "Test";
        $record->idnumber = "";
        $record->size = 10;
        $record->timecreated = $record->timemodified = time();
        $id = group::create($record);

        $record = new \stdClass();
        $record->userid = 2;
        $record->groupid = $id;
        $record->roleid = 4;
        user_assign::create($record);
        $groupproject = entity_loader::groupproject_loader($gid);

        $this->assertNotEmpty($groupproject->user_has_group(2));
    }

}
