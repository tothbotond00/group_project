<?php

use mod_groupproject\local\entities\group;
use mod_groupproject\local\entities\groupproject;
use mod_groupproject\local\entities\role;
use mod_groupproject\local\entities\user_assign;
use mod_groupproject\local\generator\template_generator;
use \mod_groupproject\local\loaders\entity_loader;
use mod_groupproject\output\comment_form;
use mod_groupproject\output\group_form;
use mod_groupproject\output\group_table;
use mod_groupproject\output\role_form;
use mod_groupproject\output\role_table;

defined('MOODLE_INTERNAL') || die();

/** Include the files that are required by this module */
require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/groupproject/lib.php');
require_once($CFG->libdir . '/filelib.php');

function get_renderer() {
    global $PAGE;
    return $PAGE->get_renderer('mod_groupproject', null, RENDERER_TARGET_GENERAL);
}

function view(groupproject $groupproject, context $context, bool $success = false) : string {
    global $OUTPUT;

    if(has_capability('mod/groupproject:managegroup', $context)){
        $o = '';
        if($success) $o .= $OUTPUT->notification(get_string('savesuccess', 'mod_groupproject'), 'success');
        $o .=  manage_groups($groupproject);
        return $o;
    }

    return view_comments($groupproject, $context);
}

function view_comments(groupproject $groupproject, context $context) : string{
    $o = '';

    $renderer = get_renderer();
    $group = $groupproject->userHasGroup();
    $o .= $renderer->render_from_template('mod_groupproject/student_group_comments', template_generator::generate_student_group_project_data($groupproject));
    if($group){
        $mform = new comment_form(new moodle_url('/mod/groupproject/group_chat.php',
            ['id' => $context->instanceid]));
        $o .= $mform->render();
    }
    return $o;
}

function manage_groups(groupproject $groupproject): string {
    global $PAGE,$CFG;
    $o = '';

    $table = new group_table('mod_groupproject_groups', $PAGE->url);
    $table->build_table();
    $table->out(10, true);
    $o .= html_writer::tag('a',
        html_writer::tag('button',get_string('create_group', 'mod_groupproject'),
        array('class' => 'btn btn-outline-secondary btn-sm text-nowrap', 'style' => 'margin-top:20px;')),
        array('href' => $CFG->www . "/mod/groupproject/group.php?id={$groupproject->getCourseModule()->id}&groupid=0&action=add"));
    return $o;
}

function add_group(groupproject $groupproject, context $context): string {
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

function modify_group(groupproject $groupproject, context $context, int $groupid): string {
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

function delete_group(groupproject $groupproject, context $context, int $groupid){
    entity_loader::group_loader($groupid)->delete();
    $url = new moodle_url('/mod/groupproject/manage_groups.php', ['id' => $context->instanceid, 'success' => true]);
    redirect($url);
}

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

function add_role() : string {
    $form = new role_form(new moodle_url('/mod/groupproject/role.php',
        ['roleid' => 0, 'action' => "add"]),);

    if($data = $form->get_data()){
        $data->timecreated = time();
        $data->timemodified = $data->timecreated;
        $data->description = json_encode($data->description);
        role::create($data);
        $url = new moodle_url('/mod/groupproject/manage_roles.php');
        redirect($url);
    }

    return $form->render();
}

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
        $url = new moodle_url('/mod/groupproject/manage_roles.php');
        redirect($url);
    }

    return $form->render();
}

function delete_role_gp($roleid) {
    entity_loader::role_loader($roleid)->delete();
    $url = new moodle_url('/mod/groupproject/manage_roles.php');
    redirect($url);
}

function user_view(groupproject $groupproject, context $context, int $groupid) : string {
    global $PAGE, $OUTPUT;
    $o = '';
    $jQueryData = $_POST["jQueryData"];
    $jQueryData = json_decode($jQueryData);
    if(!empty($jQueryData)){
        $response = user_assign::process_json_data($jQueryData, $groupid);
        if($response->success){
            $url = new moodle_url('/mod/groupproject/manage_groups.php', ['id' => $context->instanceid, 'success' => true]);
            redirect($url);
        }else {
            $o .= $OUTPUT->notification(get_string($response->code, 'mod_groupproject'), 'error');
        }
    }

    $users = $groupproject->getPossibleUsers();
    $roles = role::getAllRoles();
    $group = entity_loader::group_loader($groupid);
    $size = $group->getSize();
    $groupusers = $group->getUsers();

    $o .= html_writer::tag('h1', get_string('add_users', 'mod_groupproject'));
    $o .= html_writer::tag('div','',['id' => 'user_view']);
    $o .= html_writer::end_tag('div');
    $PAGE->requires->js_call_amd('mod_groupproject/add_users', 'init',
        [$users, $roles, $size, $context->instanceid, $groupid, $groupusers]);

    return $o;
}