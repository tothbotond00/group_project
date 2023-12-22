<?php

/**
 * Template genenrator.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 */

namespace mod_groupproject\local\generator;

use mod_groupproject\local\entities\comment;
use mod_groupproject\local\entities\group;
use mod_groupproject\local\entities\groupproject;
use mod_groupproject\local\loaders\entity_loader;

class template_generator
{

    /**
     * Generates the student data for the JQuery frontend.
     * @param groupproject $groupproject
     * @return array|false[]
     * @throws \coding_exception
     */
    public static function generate_student_group_project_data(groupproject $groupproject){
        global $USER, $OUTPUT;
        $group = $groupproject->user_has_group();
        if(!$group){
            return array('hasGroup' => false);
        }

        $comments = $group->get_comments();
        $data = self::generate_data($group, $USER, $OUTPUT, $comments);
        return $data;
    }

    /**
     * Generates the new comments for the group (for synchronisation).
     * @param int $count
     * @param $groupid
     * @param $userid
     * @return mixed
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function generate_new_comments(int $count, $groupid, $userid){
        global $DB, $OUTPUT;

        $group = entity_loader::group_loader($groupid);
        $user  = $DB->get_record('user', ['id' => $userid]);
        $comments = comment::get_group_comments($count,$groupid,$userid);

        $data = self::generate_data($group, $user, $OUTPUT, $comments);
        return $data['comments'];
    }

    /**
     * Generates userpix for every user in the group.
     * @param group $group
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    private static function generate_user_pix(group $group){
        global $OUTPUT, $PAGE, $DB;
        if(empty($PAGE->context)) $PAGE->set_context(\context_system::instance());
        $userpix = [];
        foreach ($group->get_users() as $user){
            $userpicture = new \user_picture($DB->get_record('user', ['id' => $user->userid]));
            $userpicture->popup = true;
            $userpix[$user->userid] = $OUTPUT->render($userpicture);
        }
        return $userpix;
    }

    /**
     * Generates the JQuery data with every comment instance.
     * @param bool|group $group
     * @param mixed $USER
     * @param $OUTPUT
     * @param array $comments
     * @return array
     * @throws \coding_exception
     */
    public static function generate_data(bool|group $group, mixed $USER, $OUTPUT, array $comments): array {
        $userpix = self::generate_user_pix($group);
        $userpicture = new \user_picture($USER);
        $userpicture->popup = true;
        $pic = $OUTPUT->render($userpicture);
        $data = array(
            'hasGroup' => true,
            'comments' => array(),
            'userpix' => $pic,
            'userid' => $USER->id,
            'groupid' => $group->getId()
        );
        foreach ($comments as $comment) {
            $data['comments'][] = array(
                'userpix' => $userpix[$comment->getUserid()],
                'message' => $comment->getComment(),
                'time' => date("Y.m.d H:i", $comment->getTimecreated()),
                'side' => $comment->getUserid() == $USER->id ? 'own' : 'other'
            );
        }
        return $data;
    }
}