<?php

/**
 * Admin settings page renderer.
 * @package   mod_groupproject
 * @copyright  2023 Tóth Botond
 */

defined('MOODLE_INTERNAL') || die;

$groupproject = new admin_category('groupproject_category',
    new lang_string('pluginname', 'mod_groupproject'), $module->is_enabled() === false);
$ADMIN->add('modsettings', $groupproject);
$settings->visiblename = new lang_string('manage_groupproject', 'mod_groupproject');
$settings->hidden = true;
$ADMIN->add('groupproject_category', $settings);
$ADMIN->add('groupproject_category', new admin_externalpage('groupproject_roles',
    get_string('manage_roles', 'mod_groupproject'),
    new moodle_url('/mod/groupproject/manage_roles.php')));

if ($ADMIN->fulltree) {
}