<?php

/**
 * User assign entity tests.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 * @coversDefaultClass mod_groupproject\local\entities\user_assign
 */

namespace mod_groupproject\local\entities;

use mod_groupproject\local\loaders\entity_loader;

class user_assign_test extends \advanced_testcase {

    /**
     * Test for user assign creation
     */
    public function test_create(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->userid = 2;
        $record->groupid = 3;
        $record->roleid = 4;
        $id = user_assign::create($record);
        $this->assertNotEmpty($id);
    }

    /**
     * Test for user assign modify
     */
    public function test_update(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->userid = 2;
        $record->groupid = 3;
        $record->roleid = 4;
        $id = user_assign::create($record);
        $user_assign = entity_loader::user_assign_loader($id);
        $user_assign->setUserid(10);
        $user_assign->setGroupid(11);
        $user_assign->setRoleid(12);
        $this->assertNotEquals($record->userid, $user_assign->getUserid());
        $this->assertNotEquals($record->groupid, $user_assign->getGroupid());
        $this->assertNotEquals($record->roleid, $user_assign->getRoleid());
    }

    /**
     * Test for user assign deletion
     */
    public function test_delete(){
        global $DB;

        $this->resetAfterTest();
        $record = new \stdClass();
        $record->userid = 2;
        $record->groupid = 3;
        $record->roleid = 4;
        $id = user_assign::create($record);
        $user_assign = entity_loader::user_assign_loader($id);
        $user_assign->delete();
        $this->assertEquals(false,$DB->record_exists(user_assign::$TABLE,['id' => $id]));
    }
}