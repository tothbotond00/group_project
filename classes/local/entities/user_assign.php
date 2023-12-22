<?php

/**
 * User assign entity.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 */

namespace mod_groupproject\local\entities;

class user_assign extends entity {

    public static $TABLE = 'groupproject_user_assign';

    /** @var int $userid User id */
    protected $userid;
    /** @var int $groupid Group id */
    protected $groupid;
    /** @var ?int $roleid Group id */
    protected $roleid;

    /**
     * @param int $userid
     * @param int $groupid
     * @param int $roleid
     */
    public function __construct(
        int $id,
        int $userid,
        int $groupid,
        ?int $roleid)
    {
        $this->id = $id;
        $this->userid = $userid;
        $this->groupid = $groupid;
        $this->roleid = $roleid;
    }

    /**
     * Processes the json data from JQuery frontend side.
     * @param mixed $jQueryData
     * @param int $groupid
     * @return \stdClass
     */
    public static function process_json_data(mixed $jQueryData,int $groupid)
    {
        $response = new \stdClass();
        $counter = [];
        foreach ($jQueryData as $data){
            $counter[$data->userid]++;
        }
        foreach ($counter as $key => $item){
            if((int)$item > 1){
                $response->success = false;
                $response->code = 'user_twice';
                return $response;
            }
        }

        self::delete_all($groupid);

        self::create_all($jQueryData, $groupid);
        $response->success = true;
        return $response;
    }

    /**
     * Makes the group empty.
     * @param int $groupid
     * @return void
     * @throws \dml_exception
     */
    private static function delete_all(int $groupid)
    {
        global $DB;
        $DB->delete_records(user_assign::$TABLE, ['groupid' => $groupid]);
    }

    /**
     * Creates the data from the given json from the JQuery frontend.
     * @param mixed $jQueryData
     * @param int $groupid
     * @return void
     */
        public static function create_all(mixed $jQueryData, int $groupid): void
    {
        foreach ($jQueryData as $data) {
            if ($data->userid == 0) {
                continue;
            }
            $data->groupid = $groupid;
            user_assign::create($data);
        }
    }

    /**
     * @return int
     */
    public function getUserid(): int
    {
        return $this->userid;
    }

    /**
     * @param int $userid
     */
    public function setUserid(int $userid): void
    {
        $this->userid = $userid;
    }

    /**
     * @return int
     */
    public function getGroupid(): int
    {
        return $this->groupid;
    }

    /**
     * @param int $groupid
     */
    public function setGroupid(int $groupid): void
    {
        $this->groupid = $groupid;
    }

    /**
     * @return ?int
     */
    public function getRoleid(): ?int
    {
        return $this->roleid;
    }

    /**
     * @param int $roleid
     */
    public function setRoleid(?int $roleid): void
    {
        $this->roleid = $roleid;
    }

}