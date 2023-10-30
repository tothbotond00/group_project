<?php

use mod_groupproject\local\entities\group;
use mod_groupproject\local\entities\groupproject;
use mod_groupproject\local\generator\template_generator;
use mod_groupproject\output\add_group;
use mod_groupproject\output\group_table;

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
        if($success) echo $OUTPUT->notification(get_string('savesuccess', 'mod_groupproject'), 'success');
        return manage_groups($groupproject);
    }

    return view_comments($groupproject);
}

function view_comments(groupproject $groupproject) : string{
    $o = '';

    $renderer = get_renderer();
    $o .= $renderer->render_from_template('mod_groupproject/student_group_comments', template_generator::generate_student_group_project_data($groupproject));
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
        array('class' => 'btn btn-outline-secondary btn-sm text-nowrap',
            '')), array('href' => $CFG->www . "/mod/groupproject/add_group.php?id={$groupproject->getCourseModule()->id}"));
    return $o;
}

function add_group(groupproject $groupproject, context $context): string {
    $form = new add_group(new moodle_url('/mod/groupproject/add_group', ['id' => $context->instanceid]),
        array('groupprojectid' => $groupproject->getId()));

    if($data = $form->get_data()){
        $data->groupprojectid = $groupproject->getId();
        $data->timecreated = time();
        $data->timemodified = $data->timecreated;
        $url = new moodle_url('/mod/groupproject/manage_groups', ['id' => $context->instanceid, 'success' => true]);
        redirect($url);
    }

    return $form->render();
}

function modify_group(groupproject $groupproject, context $context): string {
    return '';
}

function delete_group(groupproject $groupproject, context $context, int $groupid){
    \mod_groupproject\local\loaders\entity_loader::group_loader($groupid)->delete();
    $url = new moodle_url('/mod/groupproject/manage_groups', ['id' => $context->instanceid, 'success' => true]);
    redirect($url);
}