<?php

/**
 * External service handling class.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 */

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once("$CFG->dirroot/user/externallib.php");

use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_warnings;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use mod_groupproject\local\generator\template_generator;

class mod_groupproject_external extends \core_external\external_api {

    /**
     * post_comment external function parameter list
     * @return external_function_parameters
     */
    public static function post_comment_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(
                    PARAM_INT,
                    'Userid, the original poster',
                    VALUE_REQUIRED),
                'groupid' => new external_value(PARAM_INT,
                    'Groupid, the group the user posts the comment in',
                    VALUE_REQUIRED),
                'content' => new external_value(PARAM_RAW,
                    'The content of the comment',
                    VALUE_REQUIRED),
            )
        );
    }

    /**
     * post_comments endpoint data receiving end.
     * @param int $userid
     * @param int $groupid
     * @param string $content
     * @return array
     * @throws dml_exception
     */
    public static function post_comment(int $userid, int $groupid, string $content) {
        global $DB;
        $record = new stdClass();
        $record->userid = $userid;
        $record->groupid = $groupid;
        $record->comment = $content;
        $record->timecreated = time();
        \mod_groupproject\local\entities\comment::create($record);
        return [];
    }

    /**
     * post_comments endpoint returns definition.
     * @return external_warnings
     */
    public static function post_comment_returns() {
        return new external_warnings();
    }

    /**
     * get_comment external function parameter list
     * @return external_function_parameters
     */
    public static function get_comments_parameters(){
        return new external_function_parameters(
            array(
                'count' => new external_value(
                    PARAM_INT,
                    'The amont of comments the user has',
                    VALUE_REQUIRED),
                'userid' => new external_value(PARAM_INT,
                    'Userid, the original poster',
                    VALUE_REQUIRED),
                'groupid' => new external_value(PARAM_RAW,
                    'Groupid, the group the user wants to get the comments from',
                    VALUE_REQUIRED),
            )
        );
    }

    /**
     * get_comments endpoint data return.
     * @param $count
     * @param $userid
     * @param $groupid
     * @return mixed
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function get_comments($count, $userid, $groupid){
        return template_generator::generate_new_comments((int)$count, $groupid, $userid);
    }

    /**
     * get_comments endpoint returns definition.
     * @return void
     */
    public static function get_comments_returns (){
        new external_multiple_structure(
            new external_single_structure(
                array (
                    'userpix'  => new external_value(PARAM_RAW, 'User picture html componenet'),
                    'message'  => new external_value(PARAM_RAW, 'The message sent by the user'),
                    'time'     => new external_value(PARAM_RAW, 'The time the message was send in'),
                    'side'     => new external_value(PARAM_RAW, 'The side in which the comment has to be presented'),
                ), 'List of posted comment information'));
    }
}