<?php

namespace mod_groupproject\output;

class comment_form extends \moodleform
{

    protected function definition()
    {
        $mform = $this->_form;

        $mform->addElement('editor', 'comment', get_string('comment_write', 'mod_groupproject'));

        $mform->addElement('submit', 'submit', get_string('submit'));
    }
}