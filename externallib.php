<?php

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->dirroot/user/externallib.php");

use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_warnings;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use mod_groupproject\local\generator\template_generator;

class mod_groupproject_external extends \core_external\external_api {

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

    public static function post_comment_returns() {
        return new external_warnings();
    }

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

    public static function get_comments($count, $userid, $groupid){
        return template_generator::generate_new_comments((int)$count, $groupid, $userid);
    }

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