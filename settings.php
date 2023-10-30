<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   mod_groupproject
 * @copyright  2023 Tóth Botond
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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