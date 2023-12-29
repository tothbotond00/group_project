<?php

/**
 * Entity factory.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 */

namespace mod_groupproject\local\factories;

use mod_groupproject\local\entities\capability;
use mod_groupproject\local\entities\comment;
use mod_groupproject\local\entities\grade;
use mod_groupproject\local\entities\group;
use mod_groupproject\local\entities\groupproject;
use mod_groupproject\local\entities\role;
use mod_groupproject\local\entities\user_assign;

class entity_factory {

    /**
     * Creates groupproject entity from a stdclass.
     * @param \stdClass $record
     * @return groupproject
     */
    public static function create_groupproject_from_stdclass(\stdClass $record) : groupproject {
        return new groupproject(
            $record->id,
            $record->course,
            $record->name,
            $record->intro,
            $record->introformat,
            $record->duedate,
            $record->grade,
            $record->timecreated,
            $record->timemodified
        );
    }

    /**
     * Creates group entity from a stdclass.
     * @param \stdClass $record
     * @return group
     */
    public static function create_group_from_stdclass(\stdClass $record) : group {
        return new group(
            $record->id,
            $record->groupprojectid,
            $record->name,
            $record->idnumber,
            $record->size,
            $record->timecreated,
            $record->timemodified
        );
    }

    /**
     * Creates role entity from a stdclass.
     * @param \stdClass $record
     * @return role
     */
    public static function create_role_from_stdclass(\stdClass $record) : role {
        return  new role(
            $record->id,
            $record->name,
            $record->description,
            $record->timecreated,
            $record->timemodified
        );
    }

    /**
     * Creates user_assign entity from a stdclass.
     * @param \stdClass $record
     * @return user_assign
     */
    public static function create_user_assign_from_stdclass(\stdClass $record) : user_assign {
        return new user_assign(
            $record->id,
            $record->userid,
            $record->groupid,
            $record->roleid
        );
    }

    /**
     * Creates comment entity from a stdclass.
     * @param \stdClass $record
     * @return comment
     */
    public static function create_comment_from_stdclass(\stdClass $record) : comment {
        return new comment(
            $record->id,
            $record->groupid,
            $record->userid,
            $record->comment,
            $record->timecreated
        );
    }

    /**
     * Creates capability entity from a stdclass.
     * @param \stdClass $record
     * @return capability
     */
    public static function crate_capability_from_stdclass(\stdClass $record): capability {
        return new capability($record->id, $record->roleid, $record->capabilityid);
    }

    /**
     * Creates grade entity from a stdclass.
     * @param \stdClass $record
     * @return grade
     */
    public static function create_grade_from_stdclass(\stdClass $record): grade {
        return new grade($record->id, $record->groupid, $record->grader, $record->grade, $record->timemodified, $record->timecreated);
    }
}