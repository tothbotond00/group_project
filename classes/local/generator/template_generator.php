<?php

namespace mod_groupproject\local\generator;

use mod_groupproject\local\entities\comment;
use mod_groupproject\local\entities\group;
use mod_groupproject\local\entities\groupproject;
use mod_groupproject\local\loaders\entity_loader;

class template_generator
{
    public static function generate_student_group_project_data(groupproject $groupproject){
        global $USER, $OUTPUT;
        $group = $groupproject->userHasGroup();
        if(!$group){
            return array('hasGroup' => false);
        }

        $comments = $group->getComments();
        $data = self::generate_data($group, $USER, $OUTPUT, $comments);
        return $data;
    }

    public static function generate_new_comments(int $count, $groupid, $userid){
        global $DB, $OUTPUT;

        $group = entity_loader::group_loader($groupid);
        $user  = $DB->get_record('user', ['id' => $userid]);
        $comments = comment::getGroupComments($count,$groupid,$userid);

        $data = self::generate_data($group, $user, $OUTPUT, $comments);
        return $data['comments'];
    }

    private static function generate_user_pix(group $group){
        global $OUTPUT, $PAGE, $DB;
        if(empty($PAGE->context)) $PAGE->set_context(\context_system::instance());
        $userpix = [];
        foreach ($group->getUsers() as $user){
            $userpicture = new \user_picture($DB->get_record('user', ['id' => $user->userid]));
            $userpicture->popup = true;
            $userpix[$user->userid] = $OUTPUT->render($userpicture);
        }
        return $userpix;
    }

    /**
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