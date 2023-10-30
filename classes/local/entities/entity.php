<?php

namespace mod_groupproject\local\entities;

abstract class entity {

    /** @var string $TABLE DB table of class */
    public static $TABLE = '';

    /** @var int $id ID */
    protected $id = 0;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public static function create($record){
        global $DB;

        return $DB->insert_record(static::$TABLE, $record);
    }

    public function update(){
        global $DB;

        if($DB->record_exists(static::$TABLE, array('id' => $this->id))){
            return $DB->update_record(static::$TABLE, (object)(array) $this);
        }
        return false;
    }

    public function delete(){
        global $DB;

        $DB->delete_records(static::$TABLE, array('id' => $this->id));
    }

    public static function attribute_exist($attribute, $value, $parentcolumn, $parentid): bool{
        global $DB;

        $records = $DB->get_records(static::$TABLE, array($attribute => $value, $parentcolumn => $parentid));
        return count($records) !== 0;
    }
}