<?php

/**
 * Grade group form.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 */

namespace mod_groupproject\output;

use mod_groupproject\local\entities\grade;
use mod_groupproject\local\loaders\entity_loader;

global $CFG;

require_once ($CFG->dirroot . "/mod/groupproject/locallib.php");

class grade_group extends \moodleform
{
    protected function definition()
    {
        global $DB;

        $mform = $this->_form;
        $strrequired = get_string('required');
        $groupid = $this->_customdata['groupid'];
        $group = entity_loader::group_loader($groupid);

        $mform->addElement('header', 'header', $group->getName());

        $o = "";
        $attendees_string = get_string('attendees', 'mod_groupproject');
        $o.= "<h5>{$attendees_string}</h5>";
        foreach ($group->get_users() as $user){
            $user_obj = $DB->get_record('user', ['id' => $user->userid]);
            if(!empty($user->roleid)){
                $role = entity_loader::role_loader($user->roleid)->getName();
            }else {
                $role = get_string('no_role', 'mod_groupproject');
            }
            $o .= "<a href=\"/user/profile.php?id={$user_obj->id}\">" . fullname($user_obj) . "</a> - " . $role . "<br>";
        }

        $upload_file_string = get_string('upload_file', 'mod_groupproject');
        $o.= "<h5>{$upload_file_string}</h5>";
        $o .= get_group_file($group) . "<br>";

        $mform->addElement('html', $o);

        $groupproject = entity_loader::groupproject_loader($group->getGroupprojectid());

        $gradetype = $groupproject->getGrade();
        //Scale
        if($gradetype < 0){
            $scale = $DB->get_record('scale', ['id' => -1 * $gradetype]);
            $options = explode(',',$scale->scale);
            $mform->addElement('select','grade', get_string('grade', 'mod_groupproject'), $options);
            $mform->setType('grade',PARAM_RAW);
            $mform->addRule('grade', $strrequired, 'required', null);
            if($data = $DB->get_record(grade::$TABLE, ['groupid' => $groupid])){
                $mform->setDefault('grade', $data->grade - 1);
            }

            $mform->addElement('submit','submit', get_string('submit'));
        }
        //Point
        else if($gradetype > 0){
            $gradeitem = $groupproject->get_gradeitem();

            $mform->addElement('text','grade', get_string('grade', 'mod_groupproject'));
            $mform->setType('grade',PARAM_INT);
            $mform->addRule('grade', $strrequired, 'required', null);
            $mform->addHelpButton('grade','grade_help', 'mod_groupproject');

            $mform->addElement('html', '<div>'.get_string('grade_info','mod_groupproject', $gradeitem).'<div>');

            if($data = $DB->get_record(grade::$TABLE, ['groupid' => $groupid])){
                $mform->setDefault('grade', $data->grade);
            }

            $mform->addElement('submit','submit', get_string('submit'));
        }
        //None
        else {
            $mform->addElement('html', '<div>'.get_string('no_grade_info','mod_groupproject' ).'<div>');
        }
    }

    function validation($data, $files)
    {
        $groupid = $this->_customdata['groupid'];
        $group = entity_loader::group_loader($groupid);
        $groupproject = entity_loader::groupproject_loader($group->getGroupprojectid());
        $gradetype = $groupproject->getGrade();
        $gradeitem = $groupproject->get_gradeitem();

        $errors = parent::validation($data, $files);

        if($gradetype > 0){
            if(!is_numeric($data['grade']) || (int)$data['grade'] > $gradeitem->grademax || (int)$data['grade'] < $gradeitem->grademin){
                $errors['grade'] = get_string('invalidgrade', 'mod_groupproject');
            }
        }

        return $errors;
    }
}