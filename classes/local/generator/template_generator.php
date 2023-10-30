<?php

namespace mod_groupproject\local\generator;

use mod_groupproject\local\entities\groupproject;

class template_generator
{
    public static function generate_student_group_project_data(groupproject $groupproject){
        global $USER;
        $group = $groupproject->userHasGroup();
        if(!$group){
            return array('hasGroup' => false);
        }

        $comments = $group->getComments();
        $data = array('hasGroup' => true, 'comments' => array());
        foreach ($comments as $comment){
            $data['comments'][] = array('userpix' => '',
                    'message' => $comment->getMessage(),
                    'time' => $comment->getTimecreated(),
                    'side' => $comment->getUserid() == $USER->id ? 'own' : 'other');
        }
        return $data;
    }
}