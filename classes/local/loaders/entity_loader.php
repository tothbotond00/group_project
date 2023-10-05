<?php

namespace mod_groupproject\local\loaders;

use mod_groupproject\local\entities\comment;
use mod_groupproject\local\entities\file;
use mod_groupproject\local\entities\group;
use mod_groupproject\local\entities\groupproject;
use mod_groupproject\local\entities\role;
use mod_groupproject\local\entities\user_assign;
use mod_groupproject\local\factories\entity_factory;

class entity_loader {
    public static function groupproject_loader($id) : ?groupproject {
        global $DB;

        $record = $DB->get_record(groupproject::$TABLE, array('id' => $id));

        if(empty($record)) return null;

        return entity_factory::create_groupproject_from_stdclass($record);
    }

    public static function group_loader($id) : ?group {
        global $DB;

        $record = $DB->get_record(group::$TABLE, array('id' => $id));

        if(empty($record)) return null;

        return entity_factory::create_group_from_stdclass($record);
    }

    public static function role_loader($id) : ?role {
        global $DB;

        $record = $DB->get_record(role::$TABLE, array('id' => $id));

        if(empty($record)) return null;

        return entity_factory::create_role_from_stdclass($record);
    }

    public static function user_assign_loader($id) : ?user_assign {
        global $DB;

        $record = $DB->get_record(user_assign::$TABLE, array('id' => $id));

        if(empty($record)) return null;

        return entity_factory::create_user_assign_from_stdclass($record);
    }

    public static function file_loader($id) : ?file {
        global $DB;

        $record = $DB->get_record(file::$TABLE, array('id' => $id));

        if(empty($record)) return null;

        return entity_factory::create_file_from_stdclass($record);
    }

    public static function comment_loader($id) : ?comment {
        global $DB;

        $record = $DB->get_record(comment::$TABLE, array('id' => $id));

        if(empty($record)) return null;

        return entity_factory::create_comment_from_stdclass($record);
    }
}