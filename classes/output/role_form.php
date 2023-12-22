<?php

/**
 * Role form.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 */

namespace mod_groupproject\output;

use mod_groupproject\local\entities\capability;
use mod_groupproject\local\entities\role;
use mod_groupproject\local\loaders\entity_loader;

class role_form extends \moodleform {
    protected function definition()
    {
        global $CFG, $DB;

        $mform = $this->_form;
        $strrequired = get_string('required');
        $roleid = $this->_customdata['roleid'];

        $mform->addElement('header', 'header', get_string('add_role_header', 'mod_groupproject'));

        $mform->addElement('text','name', get_string('rolename', 'mod_groupproject'));
        $mform->setType('name',PARAM_TEXT);
        $mform->addRule('name', $strrequired, 'required', null, 'client');

        $mform->addElement('editor','description', get_string('roledescription', 'mod_groupproject'));
        $mform->setType('description',PARAM_RAW);
        $mform->addRule('description', $strrequired, 'required', null, 'client');

        $capabilities = capability::get_valid_capabilities();

        $options = [
            'multiple' => true,
            'noselectionstring' => get_string('none', 'mod_groupproject'),
        ];


        $mform->addElement('autocomplete', 'capabilities', get_string('role_capabilities', 'mod_groupproject'), $capabilities, $options);
        $mform->setType('capabilities', PARAM_INT);

        if(!empty($roleid)){
            $role = entity_loader::role_loader($roleid);

            $mform->setDefault('name',$role->getName());
            $mform->setDefault('description',$role->getDescription());
            $mform->setDefault('capabilities',capability::get_role_assignments($role->getId()));
        }

        $mform->addElement('submit','submit', get_string('create'));
    }

    function validation($data, $files)
    {
        $roleid = $this->_customdata['roleid'];
        if(!empty($roleid)) $role = entity_loader::role_loader($roleid);

        $errors = parent::validation($data, $files);

        if( role::attribute_exist('name', $data['name'])){
            if( !empty($role) && $role->getName() !== $data['name']){
                $errors['name'] = get_string('value_exists', 'mod_groupproject');
            }
        }

        return $errors;
    }
}

