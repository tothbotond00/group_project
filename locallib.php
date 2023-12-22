<?php

/**
 * Library of functions and constants for module groupproject for own usage.
 *
 * @package   mod_groupproject
 * @copyright 2023 Tóth Botond
 */

use mod_groupproject\local\entities\capability;
use mod_groupproject\local\entities\group;
use mod_groupproject\local\entities\groupproject;
use mod_groupproject\local\entities\role;
use mod_groupproject\local\entities\user_assign;
use mod_groupproject\local\generator\template_generator;
use \mod_groupproject\local\loaders\entity_loader;
use mod_groupproject\output\comment_form;
use mod_groupproject\output\grade_group;
use mod_groupproject\output\group_form;
use mod_groupproject\output\group_table;
use mod_groupproject\output\role_form;
use mod_groupproject\output\role_table;
use mod_groupproject\output\submission_form;

global $CFG;

defined('MOODLE_INTERNAL') || die();

/** Include the files that are required by this module */
require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/groupproject/lib.php');
require_once($CFG->libdir . '/filelib.php');

//CONSTANTS

define('GROUPPROJECT_SUBMISSION_FILEAREA', 'groupproject_submission');

define('GROUPPROJECT_FELXIBLE_CAPIBILITIES', array(
    'mod/groupproject:adduser',
    'mod/groupproject:modifygroup',
    'mod/groupproject:managegroup',
    'mod/groupproject:submitfile'
));

/**
 * Default renderer for groupproject activity
 * @return renderer_base
 */
function get_renderer() {
    global $PAGE;
    return $PAGE->get_renderer('mod_groupproject', null, RENDERER_TARGET_GENERAL);
}

/**
 * Standard view for the activity used by view.php document. Shows the groups if the user has the capability else it shows the group chat.
 * @param groupproject $groupproject
 * @param context $context
 * @param bool $success
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 */
function view(groupproject $groupproject, context $context, bool $success = false) : string {
    global $OUTPUT;

    if(capability::has_capability($groupproject,'mod/groupproject:managegroup', $context)){
        $o = '';
        if($success) $o .= $OUTPUT->notification(get_string('savesuccess', 'mod_groupproject'), 'success');
        $o .=  manage_groups($groupproject, $context);
        return $o;
    }

    return view_comments($groupproject, $context);
}

/**
 * Shows the comments for user if it has a group. Else the page will be empty.
 * @param groupproject $groupproject
 * @param context $context
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function view_comments(groupproject $groupproject, context $context, $groupid = null) : string{
    $o = '';

    $renderer = get_renderer();
    $group = $groupproject->user_has_group();
    $o .= $renderer->render_from_template('mod_groupproject/student_group_comments', template_generator::generate_student_group_project_data($groupproject, $groupid));
    if($group){
        if(capability::has_capability($groupproject,'mod/groupproject:post_comment', $context, $group->getId())){
            $mform = new comment_form(new moodle_url('/mod/groupproject/group_chat.php',
                ['id' => $context->instanceid]));
            $o .= $mform->render();
        }
    }
    return $o;
}

/**
 * Shows the manage groups view where all the groups and the actions related to them will be shown.
 * @param groupproject $groupproject
 * @param $context
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function manage_groups(groupproject $groupproject, $context): string {
    global $PAGE,$CFG;
    $o = '';

    if(!capability::has_capability($groupproject,'mod/groupproject:managegroup', $context)){
        return view_comments($groupproject, $context);
    }

    $table = new group_table('mod_groupproject_groups', $PAGE->url, $groupproject);
    $table->build_table();
    $table->out(10, true);
    $o .= html_writer::tag('a',
        html_writer::tag('button',get_string('create_group', 'mod_groupproject'),
        array('class' => 'btn btn-outline-secondary btn-sm text-nowrap', 'style' => 'margin-top:20px;')),
        array('href' => $CFG->www . "/mod/groupproject/group.php?id={$groupproject->get_course_module()->id}&groupid=0&action=add"));
    $o .= html_writer::tag('a', get_string('manage_roles', 'mod_groupproject'),
            array('href' => (new moodle_url("/mod/groupproject/role.php"))->out(), 'style' => "margin-left:70%;"));
    return $o;
}

/**
 * Displays the add group form and handles data that is sent through the moodleform.
 * @param groupproject $groupproject
 * @param context $context
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function add_group(groupproject $groupproject, context $context): string {
    if(!capability::has_capability($groupproject,'mod/groupproject:creategroup', $context)){
        throw new moodle_exception('noacceess');
    }

    $form = new group_form(new moodle_url('/mod/groupproject/group.php',
        ['id' => $context->instanceid, 'groupid' => 0, 'action' => "add"]),
        array('groupprojectid' => $groupproject->getId()));

    if($data = $form->get_data()){
        $data->groupprojectid = $groupproject->getId();
        $data->timecreated = time();
        $data->timemodified = $data->timecreated;
        group::create($data);
        $url = new moodle_url('/mod/groupproject/manage_groups.php', ['id' => $context->instanceid, 'success' => true]);
        redirect($url);
    }

    return $form->render();
}

/**
 * Displays the modify group form and also makes it possible to change group details though the moodle form. Also handles form data.
 * @param groupproject $groupproject
 * @param context $context
 * @param int $groupid
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function modify_group(groupproject $groupproject, context $context, int $groupid): string {
    if(!capability::has_capability($groupproject,'mod/groupproject:modifygroup', $context, $groupid)){
        throw new moodle_exception('noacceess');
    }

    $form = new group_form(new moodle_url('/mod/groupproject/group.php',
            ['id' => $context->instanceid, 'groupid' => $groupid, 'action' => 'modify']),
        array('groupprojectid' => $groupproject->getId(), 'groupid' => $groupid));

    if($data = $form->get_data()){
        $group = entity_loader::group_loader($groupid);
        $group->setName($data->name);
        $group->setIdnumber($data->idnumber);
        $group->setSize($data->size);
        $group->setTimemodified(time());
        $group->update();
        $url = new moodle_url('/mod/groupproject/manage_groups.php', ['id' => $context->instanceid, 'success' => true]);
        redirect($url);
    }

    return $form->render();
}

/**
 * Creates grade for every single user in the group (basically the group itself). Updates every grade_grade for the given grade-item.
 * @param groupproject $groupproject
 * @param context $context
 * @param int $groupid
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function grade_group(groupproject $groupproject, context $context, int $groupid): string{
    if(!capability::has_capability($groupproject,'mod/groupproject:gradegroup', $context, $groupid)){
        throw new moodle_exception('noacceess');
    }

    $o = "";
    $form = new grade_group(new moodle_url('/mod/groupproject/group.php',
        ['id' => $context->instanceid, 'groupid' => $groupid, 'action' => 'grade']),
        array('groupprojectid' => $groupproject->getId(), 'groupid' => $groupid));

    if($data = $form->get_data()){
        $url = new moodle_url('/mod/groupproject/manage_groups.php', ['id' => $context->instanceid, 'success' => true]);
        $group = entity_loader::group_loader($groupid);
        $group->grade_users($data->grade);
        redirect($url);
    }

    $o .= $form->render();
    return $o;
}

/**
 * Simply deletes the group and redirects to the view document.
 * @param groupproject $groupproject
 * @param context $context
 * @param int $groupid
 * @return void
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function delete_group(groupproject $groupproject, context $context, int $groupid){
    if(!capability::has_capability($groupproject,'mod/groupproject:deletegroup', $context, $groupid)){
        throw new moodle_exception('noacceess');
    }
    entity_loader::group_loader($groupid)->delete();
    $url = new moodle_url('/mod/groupproject/manage_groups.php', ['id' => $context->instanceid, 'success' => true]);
    redirect($url);
}

/**
 * Builds the roles table and displays it for the user.
 * @return string
 * @throws coding_exception
 */
function manage_roles() : string {
    global $PAGE,$CFG;
    $o = '';

    $table = new role_table('mod_groupproject_roles', $PAGE->url);
    $table->build_table();
    $table->out(25, true);
    $o .= html_writer::tag('a',
        html_writer::tag('button',get_string('create_role', 'mod_groupproject'),
            array('class' => 'btn btn-outline-secondary btn-sm text-nowrap', 'style' => 'margin-top:20px;')),
            array('href' => $CFG->www . "/mod/groupproject/role.php?action=add&roleid=0"));
    return $o;
}

/**
 * Shows the role add form and handles data that has been given to it.
 * @return string
 * @throws dml_exception
 * @throws moodle_exception
 */
function add_role() : string {
    $form = new role_form(new moodle_url('/mod/groupproject/role.php',
        ['roleid' => 0, 'action' => "add"]),);

    if($data = $form->get_data()){
        $data->timecreated = time();
        $data->timemodified = $data->timecreated;
        $data->description = json_encode($data->description);
        $roleid = role::create($data);
        entity_loader::role_loader($roleid)->update_capabilities($data->capabilities);
        $url = new moodle_url('/mod/groupproject/manage_roles.php');
        redirect($url);
    }

    return $form->render();
}

/**
 * Shows the role modification form for the user. Also handles data that the form will post.
 * @param int $roleid
 * @return string
 * @throws dml_exception
 * @throws moodle_exception
 */
function modify_role(int $roleid) : string {
    $form = new role_form(new moodle_url('/mod/groupproject/role.php',
        ['roleid' => $roleid, 'action' => "modify"]),
        array('roleid' => $roleid));

    if($data = $form->get_data()){
        $role = entity_loader::role_loader($roleid);
        $role->setName($data->name);
        $role->setDescription(json_encode($data->description));
        $role->setTimemodified(time());
        $role->update();
        $role->update_capabilities($data->capabilities);
        $url = new moodle_url('/mod/groupproject/manage_roles.php');
        redirect($url);
    }

    return $form->render();
}

/**
 * Deletes a role with a given roleid. Redirects to manage_roles.
 * @param $roleid
 * @return void
 * @throws dml_exception
 * @throws moodle_exception
 */
function delete_role_gp($roleid) {
    entity_loader::role_loader($roleid)->delete();
    $url = new moodle_url('/mod/groupproject/manage_roles.php');
    redirect($url);
}

/**
 * User adding view. Makes it possible to add new users through a JQuery frontend and handle the new data and give feedback to the end user.
 * @param groupproject $groupproject
 * @param context $context
 * @param int $groupid
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function user_view(groupproject $groupproject, context $context, int $groupid) : string {
    if(!capability::has_capability($groupproject,'mod/groupproject:adduser', $context, $groupid)){
        throw new moodle_exception('noacceess');
    }
    global $PAGE, $OUTPUT;
    $o = '';
    $jQueryData = $_POST["jQueryData"];
    $jQueryData = json_decode($jQueryData);
    if(!empty($jQueryData) || is_array($jQueryData)){
        $response = user_assign::process_json_data($jQueryData, $groupid);
        if($response->success){
            $url = new moodle_url('/mod/groupproject/manage_groups.php', ['id' => $context->instanceid, 'success' => true]);
            redirect($url);
        }else {
            $o .= $OUTPUT->notification(get_string($response->code, 'mod_groupproject'), 'error');
        }
    }

    $users = $groupproject->get_possible_users($groupid);
    $roles = role::get_all_roles();
    $group = entity_loader::group_loader($groupid);
    $size = $group->getSize();
    $groupusers = $group->get_users();

    $o .= html_writer::tag('h1', get_string('add_users', 'mod_groupproject'));
    $o .= html_writer::tag('div','',['id' => 'user_view']);
    $o .= html_writer::end_tag('div');
    $PAGE->requires->js_call_amd('mod_groupproject/add_users', 'init',
        [$users, $roles, $size, $context->instanceid, $groupid, $groupusers]);

    return $o;
}

/**
 * Group submission view where a person from the group can submit the file for the project itself.
 * @param groupproject $groupproject
 * @param context $context
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function group_submissions(groupproject $groupproject, context $context) : string{
    global $OUTPUT;

    $o = '';
    $group = $groupproject->user_has_group();
    if(empty($group)) return '';
    if(!capability::has_capability($groupproject,'mod/groupproject:submitfile', $context, $group->getId())){
        throw new moodle_exception('noacceess');
    }

    if(!empty($groupproject->getDuedate()) && $groupproject->getDuedate() < time()){
        $o .= $OUTPUT->notification(get_string('duedateover', 'mod_groupproject'), 'error');
        return $o;
    }
    $mform = new submission_form(new moodle_url('/mod/groupproject/group_submission.php',
        ['id' => $context->instanceid]),['context' => $context, 'groupid' => $group->getId()]);

    if($data = $mform->get_data()){
        file_save_draft_area_files(
            $data->submission,
            $context->id,
            'mod_groupproject',
            GROUPPROJECT_SUBMISSION_FILEAREA,
            $group->getId(),
        );
    }

    $o .= $mform->render();

    return $o;
}

/**
 * Returns the uploaded file link for the group. Returns no file found if the group hasn't uploaded the file yet.
 * @param group $group
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 */
function get_group_file(group $group) : string
{
    global $DB;
    $o = "";
    $file_storage = get_file_storage();
    $filearea = GROUPPROJECT_SUBMISSION_FILEAREA;
    $file = $DB->get_records_sql("SELECT *
                                    FROM {files} f
                                   WHERE f.component = 'mod_groupproject' 
                                     AND f.filearea = '{$filearea}' 
                                     AND itemid = {$group->getId()}
                                     AND f.filesize <> 0 
                                     HAVING MAX(f.timemodified)");
    if(empty($file)) $o .= get_string('no_file', 'mod_groupproject');
    else {
        $uploaded_file = $file_storage->get_file_by_id(array_keys($file)[0]);

        $url = \html_writer::tag('a',$uploaded_file->get_filename(),['href' => moodle_url::make_pluginfile_url(
                $uploaded_file->get_contextid(),
                $uploaded_file->get_component(),
                $uploaded_file->get_filearea(),
                $uploaded_file->get_itemid(),
                $uploaded_file->get_filepath(),
                $uploaded_file->get_filename(),
                true
            )->out()]) . \html_writer::end_tag('a');
        $o .= $url . "<br>";
    }
    return $o;
}