<?php

/**
 * @package   mod_groupproject
 * @copyright 2023 Tóth Botond
 */

global $CFG;

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_groupproject_mod_form extends moodleform_mod {

    function definition() {
        global $CFG;
        $mform = $this->_form;

        $config = get_config('groupproject');

        //-------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $this->standard_intro_elements();

        //-------------------------------------------------------

        $this->standard_grading_coursemodule_elements();

        $mform->addElement('date_time_selector', 'duedate', get_string('duedate', 'mod_groupproject'), ['optional' => true]);

        $this->standard_coursemodule_elements();

        //-------------------------------------------------------
        $this->add_action_buttons();
    }
}