<?php

/**
 * Grade entity tests.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 * @coversDefaultClass mod_groupproject\local\entities\grade
 */

namespace mod_groupproject\local\entities;

use mod_groupproject\local\loaders\entity_loader;

class grade_test extends \advanced_testcase {

    /**
     * Test for grade creation
     */
    public function test_create(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->groupid = 3;
        $record->grader = 3;
        $record->grade = 13;
        $record->timecreated = $record->timemodified = time();
        $id = grade::create($record);
        $this->assertNotEmpty($id);
    }

    /**
     * Test for grade modify
     */
    public function test_update(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->groupid = 3;
        $record->grader = 3;
        $record->grade = 13;
        $record->timecreated = $record->timemodified = time();
        $id = grade::create($record);
        $this->assertNotEmpty($id);
        $grade = entity_loader::grade_loader($id);
        $grade->setGroupid(10);
        $grade->setGrader(11);
        $grade->setGrade(13);
        $grade->setTimecreated(time()+1);
        $grade->update();
        $this->assertNotEquals($record->groupid, $grade->getGroupid());
        $this->assertNotEquals($record->grader, $grade->getGrader());
        $this->assertNotEquals($record->grade, $grade->getGrader());
        $this->assertNotEquals($record->timecreated, $grade->getTimecreated());
    }

    /**
     * Test for grade deletion
     */
    public function test_delete(){
        global $DB;

        $this->resetAfterTest();
        $record = new \stdClass();
        $record->groupid = 3;
        $record->grader = 3;
        $record->grade = 13;
        $record->timecreated = $record->timemodified = time();
        $id = grade::create($record);
        $this->assertNotEmpty($id);
        $grade = entity_loader::grade_loader($id);
        $grade->delete();
        $this->assertEquals(false,$DB->record_exists(grade::$TABLE,['id' => $id]));
    }

    /**
     * Test for grade converting
     */
    public function test_convert_grade(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->groupid = 3;
        $record->grader = 3;
        $record->grade = 2;
        $record->timecreated = $record->timemodified = time();
        $id = grade::create($record);
        $this->assertNotEmpty($id);
        $grade = entity_loader::grade_loader($id);
        $converted = $grade->getGrade();
        $this->assertEquals(2, $converted);
    }
}