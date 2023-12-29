<?php

/**
 * Role entity tests.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 * @coversDefaultClass mod_groupproject\local\entities\role
 */

namespace mod_groupproject\local\entities;

use mod_groupproject\local\loaders\entity_loader;

class role_test extends \advanced_testcase {

    /**
     * Test for role creation
     */
    public function test_create(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->name = "Test";
        $record->description = "Test";
        $record->timecreated = $record->timemodified = time();
        $id = role::create($record);
        $this->assertNotEmpty($id);
    }

    /**
     * Test for role modify
     */
    public function test_update(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->name = "Test";
        $record->description = "Test";
        $record->timecreated = $record->timemodified = time();
        $id = role::create($record);
        $role = entity_loader::role_loader($id);
        $role->setName("Test 2");
        $role->setDescription("Test 2");
        $role->setTimecreated(time()+1);
        $this->assertNotEquals($record->name, $role->getName());
        $this->assertNotEquals($record->timecreated, $role->getTimecreated());
    }

    /**
     * Test for role deletion
     */
    public function test_delete(){
        global $DB;

        $this->resetAfterTest();
        $record = new \stdClass();
        $record->name = "Test";
        $record->description = "Test";
        $record->timecreated = $record->timemodified = time();
        $id = role::create($record);
        $role = entity_loader::role_loader($id);
        $role->delete();
        $this->assertEquals(false, $DB->record_exists(role::$TABLE,['id' => $id]));
    }

    /**
     * Test for update capabilities function
     */
    public function test_update_capabilities(){
        global $DB;

        $this->resetAfterTest();
        $record = new \stdClass();
        $record->name = "Test";
        $record->description = "Test";
        $record->timecreated = $record->timemodified = time();
        $id = role::create($record);
        $role = entity_loader::role_loader($id);
        $role->update_capabilities([1,2,3]);
        $DB->record_exists(capability::$TABLE, ['roleid' => $id, 'capabilityid' => 1]);
    }

    /**
     * Checks if user has capability
     */
    public function test_has_capability(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->name = "Test";
        $record->description = "Test";
        $record->timecreated = $record->timemodified = time();
        $id = role::create($record);
        $role = entity_loader::role_loader($id);
        $role->update_capabilities([1,2,3]);
        $this->assertEquals(true, $role->has_capability('moodle/site:config'));
    }
}