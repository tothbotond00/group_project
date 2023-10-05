<?php

namespace mod_groupproject\local\factories;

use mod_groupproject\local\entities\comment;
use mod_groupproject\local\entities\file;
use mod_groupproject\local\entities\group;
use mod_groupproject\local\entities\groupproject;
use mod_groupproject\local\entities\role;
use mod_groupproject\local\entities\user_assign;

class entity_factory {

    public static function create_groupproject_from_stdclass(\stdClass $record) : groupproject {
        return new groupproject(
            $record->id,
            $record->course,
            $record->name,
            $record->intro,
            $record->introformat,
            $record->timecreated,
            $record->timemodified
        );
    }

    public static function create_group_from_stdclass(\stdClass $record) : group {
        return new group(
            $record->id,
            $record->name,
            $record->idnumber,
            $record->size,
            $record->timecreated,
            $record->timemodified
        );
    }

    public static function create_role_from_stdclass(\stdClass $record) : role {
        return  new role(
            $record->id,
            $record->name,
            $record->description,
            $record->timecreated,
            $record->timemodified
        );
    }

    public static function create_user_assign_from_stdclass(\stdClass $record) : user_assign {
        return new user_assign(
            $record->id,
            $record->groupid,
            $record->userid,
            $record->roleid
        );
    }

    public static function create_file_from_stdclass(\stdClass $record) : file {
        return new file(
            $record->id,
            $record->groupid,
            $record->fileid,
            $record->timecreated,
            $record->timemodified
        );
    }

    public static function create_comment_from_stdclass(\stdClass $record) : comment {
        return new comment(
            $record->id,
            $record->groupid,
            $record->userid,
            $record->comment,
            $record->timecreated
        );
    }
}