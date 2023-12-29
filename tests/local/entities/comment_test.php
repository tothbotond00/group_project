<?php

/**
 * Comment entity tests.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 * @coversDefaultClass mod_groupproject\local\entities\comment
 */

namespace mod_groupproject\local\entities;

use mod_groupproject\local\loaders\entity_loader;

class comment_test extends \advanced_testcase {

    /**
     * Test for comment creation
     */
    public function test_create(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->groupid = 3;
        $record->userid = 2;
        $record->comment = "Test";
        $record->timecreated = time();
        $id = comment::create($record);
        $this->assertNotEmpty($id);
    }

    /**
     * Test for comment modify
     */
    public function test_update(){
        $this->resetAfterTest();
        $record = new \stdClass();
        $record->groupid = 3;
        $record->userid = 2;
        $record->comment = "Test";
        $record->timecreated = time();
        $id = comment::create($record);
        $this->assertNotEmpty($id);
        $comment = entity_loader::comment_loader($id);
        $comment->setComment("Test2");
        $comment->setGroupid(4);
        $comment->setUserid(5);
        $comment->setTimecreated(time()+1);
        $comment->update();
        $this->assertNotEquals($record->userid, $comment->getUserid());
        $this->assertNotEquals($record->groupid, $comment->getGroupid());
        $this->assertNotEquals($record->comment, $comment->getComment());
        $this->assertNotEquals($record->timecreated, $comment->getTimecreated());
    }

    /**
     * Test for comment deletion
     */
    public function test_delete(){
        global $DB;

        $this->resetAfterTest();
        $record = new \stdClass();
        $record->groupid = 3;
        $record->userid = 2;
        $record->comment = "Test";
        $record->timecreated = time();
        $id = comment::create($record);
        $this->assertNotEmpty($id);
        $comment = entity_loader::comment_loader($id);
        $comment->delete();
        $this->assertEquals(false,$DB->record_exists(comment::$TABLE,['id' => $id]));
    }
}