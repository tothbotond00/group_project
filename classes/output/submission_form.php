<?php

/**
 * Submission form.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 */

namespace mod_groupproject\output;

global $CFG;

require_once($CFG->dirroot . '/mod/groupproject/locallib.php');

class submission_form extends \moodleform
{

    protected function definition()
    {
        $mform = $this->_form;
        $customdata = $this->_customdata;

        $filemanageroptions = array(
            'maxbytes' => get_max_upload_file_size(),
            'maxfiles' => 1,
            'subdirs' => 0,
            'accepted_types' => '*',
        );
        $data = new \stdClass();
        $data = file_prepare_standard_editor($data,
                                            'files',
                                            $filemanageroptions,
                                            $customdata['context'],
                                            'mod_groupproject',
                                            GROUPPROJECT_SUBMISSION_FILEAREA,
                                            $customdata['groupid']);
        $data = file_prepare_standard_filemanager($data,
                                                'files',
                                                $filemanageroptions,
                                                $customdata['context'],
                                                'mod_groupproject',
                                                GROUPPROJECT_SUBMISSION_FILEAREA,
                                                $customdata['groupid']);
        $mform->addElement('filemanager', 'submission', get_string('submission', 'mod_groupproject'),
            null, $filemanageroptions);
        $mform->addRule('submission', null, 'required');
        $mform->setDefault('submission',$data->files_editor['itemid']);

        $mform->addElement('submit','submit',get_string('submit'));
    }
}