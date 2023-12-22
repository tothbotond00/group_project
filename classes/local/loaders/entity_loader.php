<?php

/**
 * Entity loader.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 */

namespace mod_groupproject\local\loaders;

use mod_groupproject\local\entities\capability;
use mod_groupproject\local\entities\comment;
use mod_groupproject\local\entities\grade;
use mod_groupproject\local\entities\group;
use mod_groupproject\local\entities\groupproject;
use mod_groupproject\local\entities\role;
use mod_groupproject\local\entities\user_assign;
use mod_groupproject\local\factories\entity_factory;

class entity_loader {

    /**
     * Returns a groupproject entity based on the id.
     * @param $id
     * @return groupproject|null
     * @throws \dml_exception
     */
    public static function groupproject_loader($id) : ?groupproject {
        global $DB;

        $record = $DB->get_record(groupproject::$TABLE, array('id' => $id));

        if(empty($record)) return null;

        return entity_factory::create_groupproject_from_stdclass($record);
    }

    /**
     * Returns a group entity based on the id.
     * @param $id
     * @return group|null
     * @throws \dml_exception
     */
    public static function group_loader($id) : ?group {
        global $DB;

        $record = $DB->get_record(group::$TABLE, array('id' => $id));

        if(empty($record)) return null;

        return entity_factory::create_group_from_stdclass($record);
    }

    /**
     * Returns a role entity based on the id.
     * @param $id
     * @return role|null
     * @throws \dml_exception
     */
    public static function role_loader($id) : ?role {
        global $DB;

        $record = $DB->get_record(role::$TABLE, array('id' => $id));

        if(empty($record)) return null;

        return entity_factory::create_role_from_stdclass($record);
    }

    /**
     * Returns a user_assign entity based on the id.
     * @param $id
     * @return user_assign|null
     * @throws \dml_exception
     */
    public static function user_assign_loader($id) : ?user_assign {
        global $DB;

        $record = $DB->get_record(user_assign::$TABLE, array('id' => $id));

        if(empty($record)) return null;

        return entity_factory::create_user_assign_from_stdclass($record);
    }

    /**
     * Returns a comment entity based on the id.
     * @param $id
     * @return comment|null
     * @throws \dml_exception
     */
    public static function comment_loader($id) : ?comment {
        global $DB;

        $record = $DB->get_record(comment::$TABLE, array('id' => $id));

        if(empty($record)) return null;

        return entity_factory::create_comment_from_stdclass($record);
    }

    /**
     * Returns a capability entity based on the id.
     * @param $id
     * @return capability|null
     * @throws \dml_exception
     */
    public static function capability_loader($id) : ?capability{
        global $DB;

        $record = $DB->get_record(capability::$TABLE, array('id' => $id));

        if(empty($record)) return null;

        return entity_factory::crate_capability_from_stdclass($record);
    }

    /**
     * Returns a grade entity based on the id.
     * @param $id
     * @return grade|null
     * @throws \dml_exception
     */
    public static function grade_loader($id) : ?grade{
        global $DB;

        $record = $DB->get_record(grade::$TABLE, array('id' => $id));

        if(empty($record)) return null;

        return entity_factory::create_grade_from_stdclass($record);
    }
}