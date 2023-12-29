<?php

/**
 * Group entity tests.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 * @coversDefaultClass mod_groupproject\local\entities\group
 */

namespace mod_groupproject\local\entities;

use mod_groupproject\local\loaders\entity_loader;

class group_test extends \advanced_testcase {

    /**
     * Test for group creation
     */
    public function test_create(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->groupprojectid = 1;
        $record->name = "Test";
        $record->idnumber = "";
        $record->size = 10;
        $record->timecreated = $record->timemodified = time();
        $id = group::create($record);
        $this->assertNotEmpty($id);
    }

    /**
     * Test for group modify
     */
    public function test_update(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->groupprojectid = 1;
        $record->name = "Test";
        $record->idnumber = "";
        $record->size = 10;
        $record->timecreated = $record->timemodified = time();
        $id = group::create($record);
        $group = entity_loader::group_loader($id);
        $group->setName("Test 2");
        $group->setGroupprojectid(2);
        $group->setIdnumber("TEST");
        $group->setSize(0);
        $group->setTimecreated(time() + 1);
        $this->assertNotEquals($record->groupprojectid, $group->getGroupprojectid());
        $this->assertNotEquals($record->name, $group->getName());
        $this->assertNotEquals($record->idnumber, $group->getIdnumber());
        $this->assertNotEquals($record->size, $group->getSize());
        $this->assertNotEquals($record->timecreated, $group->getTimecreated());
    }

    /**
     * Test for group deletion
     */
    public function test_delete(){
        global $DB;

        $this->resetAfterTest();
        $record = new \stdClass();
        $record->groupprojectid = 1;
        $record->name = "Test";
        $record->idnumber = "";
        $record->size = 10;
        $record->timecreated = $record->timemodified = time();
        $id = group::create($record);
        $group = entity_loader::group_loader($id);
        $group->delete();
        $this->assertEquals(false, $DB->record_exists(group::$TABLE,['id' => $id]));
    }

    /**
     * Test for user group query
     */
    public function test_get_users(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->groupprojectid = 1;
        $record->name = "Test";
        $record->idnumber = "";
        $record->size = 10;
        $record->timecreated = $record->timemodified = time();
        $id = group::create($record);
        $group = entity_loader::group_loader($id);

        $record = new \stdClass();
        $record->userid = 2;
        $record->groupid = $id;
        $record->roleid = 4;
        $id = user_assign::create($record);

        $this->assertNotEmpty($group->get_users());
    }

    /**
     * Test for user group query by id
     */
    public function test_get_user_ids(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->groupprojectid = 1;
        $record->name = "Test";
        $record->idnumber = "";
        $record->size = 10;
        $record->timecreated = $record->timemodified = time();
        $id = group::create($record);
        $group = entity_loader::group_loader($id);

        $record = new \stdClass();
        $record->userid = 2;
        $record->groupid = $id;
        $record->roleid = 4;
        $id = user_assign::create($record);

        $this->assertNotEmpty($group->get_users());
    }

    /**
     * Test for user role check
     */
    public function test_get_user_role_id(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->groupprojectid = 1;
        $record->name = "Test";
        $record->idnumber = "";
        $record->size = 10;
        $record->timecreated = $record->timemodified = time();
        $id = group::create($record);
        $group = entity_loader::group_loader($id);

        $record = new \stdClass();
        $record->userid = 2;
        $record->groupid = $id;
        $record->roleid = 4;
        $id = user_assign::create($record);

        $this->assertEquals($group->get_user_role_id(2), 4);
    }

}