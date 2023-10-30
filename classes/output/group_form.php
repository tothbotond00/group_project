<?php

namespace mod_groupproject\output;

use mod_groupproject\local\entities\group;
use mod_groupproject\local\loaders\entity_loader;

class group_form extends \moodleform
{
    protected function definition()
    {
        $mform = $this->_form;
        $strrequired = get_string('required');
        $groupid = $this->_customdata['groupid'];

        $mform->addElement('header', 'header', get_string('add_group_header', 'mod_groupproject'));

        $mform->addElement('text','name', get_string('groupname', 'mod_groupproject'));
        $mform->setType('name',PARAM_TEXT);
        $mform->addRule('name', $strrequired, 'required', null, 'client');

        $mform->addElement('text','idnumber', get_string('groupidnumber', 'mod_groupproject'));
        $mform->setType('idnumber',PARAM_TEXT);

        $mform->addElement('text','size', get_string('groupsize', 'mod_groupproject'));
        $mform->setType('size',PARAM_RAW);
        $mform->addRule('size', $strrequired, 'required', null, 'client');

        if(!empty($groupid)){
            $group = entity_loader::group_loader($groupid);

            $mform->setDefault('name',$group->getName());
            $mform->setDefault('idnumber',$group->getIdnumber());
            $mform->setDefault('size',$group->getSize());
        }

        $mform->addElement('submit','submit', get_string('submit'));
    }

    function validation($data, $files)
    {
        $id = $this->_customdata['groupprojectid'];
        $groupid = $this->_customdata['groupid'];
        if(!empty($groupid)) $group = entity_loader::group_loader($groupid);

        $errors = parent::validation($data, $files);

        if( group::attribute_exist('name', $data['name'], 'groupprojectid', $id)){
            if( !empty($group) && $group->getName() !== $data['name']){
                $errors['name'] = get_string('value_exists', 'mod_groupproject');
            }
        }

        if( group::attribute_exist('idnumber', $data['idnumber'], 'groupprojectid', $id)){
            if( !empty($group) && $group->getIdnumber() !== $data['idnumber']){
                $errors['idnumber'] = get_string('value_exists', 'mod_groupproject');
            }
        }

        if(!is_numeric($data['size']) || (int)$data['size'] < 0 ){
            $errors['size'] = get_string('invalidsize', 'mod_groupproject');
        }

        return $errors;
    }
}