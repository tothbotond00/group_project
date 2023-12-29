<?php

/**
 * Capability entity tests.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 * @coversDefaultClass mod_groupproject\local\entities\capability
 */

namespace mod_groupproject\local\entities;

use mod_groupproject\local\factories\entity_factory;
use mod_groupproject\local\loaders\entity_loader;

class capability_test extends \advanced_testcase {

    /**
     * Test for grade creation
     */
    public function test_create(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->roleid = 3;
        $record->capabilityid = 2;
        $id = capability::create($record);
        $this->assertNotEmpty($id);
    }

    /**
     * Test for grade modify
     */
    public function test_update(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->roleid = 3;
        $record->capabilityid = 2;
        $id = capability::create($record);
        $capability = entity_loader::capability_loader($id);
        $capability->setRoleid(10);
        $capability->setCapibilityid(11);
        $this->assertNotEquals($record->roleid, $capability->getRoleid());
        $this->assertNotEquals($record->capabilityid, $capability->getCapibilityid());
    }

    /**
     * Test for grade deletion
     */
    public function test_delete(){
        global $DB;

        $this->resetAfterTest();
        $record = new \stdClass();
        $record->roleid = 3;
        $record->capabilityid = 2;
        $id = capability::create($record);
        $capability = entity_loader::capability_loader($id);
        $capability->delete();
        $this->assertEquals(false, $DB->record_exists(capability::$TABLE,['id' => $id]));
    }

    /**
     * Test for role assignments query
     */
    public function test_get_role_assignments(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->name = "Test";
        $record->description = "Test";
        $record->timecreated = $record->timemodified = time();
        $id = role::create($record);
        $role = entity_loader::role_loader($id);
        $role->update_capabilities([1,2,3]);
        $this->assertEquals(true, $role->has_capability('moodle/site:config'));
        $ra = capability::get_role_assignments($id);
        $this->assertNotEmpty($ra);
    }

    /**
     * Test for capability check for specific user
     */
    public function test_has_capability(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->roleid = 3;
        $record->capabilityid = 2;
        $id = capability::create($record);
        $capability = entity_loader::capability_loader($id);
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
        $this->assertEquals(false,capability::has_capability($groupproject,'moodle/site:config',\context_course::instance(1)));
    }
}