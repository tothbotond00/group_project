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
 * This file keeps track of upgrades to
 * the forum module
 *
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 *
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installation to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the methods of database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @package   mod_groupproject
 * @copyright 2023 Tóth Botond
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_groupproject_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2023111400) {

        // Define table groupproject table to be dropped.
        // This table was replaced by the usage of the mdl_files table.
        $table = new xmldb_table('groupproject_files');

        // Conditionally launch drop table for groupproject.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Groupproject savepoint reached.
        upgrade_mod_savepoint(true, 2023111400, 'groupproject');
    }

    if ($oldversion < 2023111401) {

        // Define table groupproject_capabilities to be created.
        $table = new xmldb_table('groupproject_capabilities');

        // Adding fields to table groupproject_capabilities.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('roleid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('capabilityid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table groupproject_capabilities.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('roleid', XMLDB_KEY_FOREIGN, ['roleid'], 'groupproject_roles', ['id']);
        $table->add_key('capabilityid', XMLDB_KEY_FOREIGN, ['capabilityid'], 'capabilities', ['id']);

        // Conditionally launch create table for groupproject_capabilities.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Groupproject savepoint reached.
        upgrade_mod_savepoint(true, 2023111401, 'groupproject');
    }

    if ($oldversion < 2023111502) {

        // Define table groupproject_capabilities to be created.
        $table = new xmldb_table('groupproject_capibilities');
        $dbman->drop_table($table);
        $table = new xmldb_table('groupproject_capabilities');

        // Adding fields to table groupproject_capabilities.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('roleid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('capabilityid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table groupproject_capabilities.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('roleid', XMLDB_KEY_FOREIGN, ['roleid'], 'groupproject_roles', ['id']);
        $table->add_key('capabilityid', XMLDB_KEY_FOREIGN, ['capabilityid'], 'capabilities', ['id']);

        // Conditionally launch create table for groupproject_capabilities.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Groupproject savepoint reached.
        upgrade_mod_savepoint(true, 2023111502, 'groupproject');
    }


    return true;
}
