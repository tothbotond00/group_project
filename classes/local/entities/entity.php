<?php

/**
 * Entity abstract class.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 */

namespace mod_groupproject\local\entities;

abstract class entity  {

    /** @var string $TABLE DB table of class */
    public static $TABLE = '';

    /** @var int $id ID */
    protected $id = 0;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Creates the record in the database.
     * @param $record
     * @return bool|int
     * @throws \dml_exception
     */
    public static function create($record){
        global $DB;

        return $DB->insert_record(static::$TABLE, $record);
    }

    /**
     * Updates the record in the database if it exists.
     * @return bool
     * @throws \dml_exception
     */
    public function update(){
        global $DB;

        if($DB->record_exists(static::$TABLE, array('id' => $this->id))){
            $obj = json_decode($this->to_json($this), true);
            return $DB->update_record(static::$TABLE, (object)(array) $obj);
        }
        return false;
    }

    /**
     * Deletes the entity from the database.
     * @return void
     * @throws \dml_exception
     */
    public function delete(){
        global $DB;

        $DB->delete_records(static::$TABLE, array('id' => $this->id));
    }

    /**
     * Checks if the attribute with the given value exists.
     * @param $attribute
     * @param $value
     * @param $parentcolumn
     * @param $parentid
     * @return bool
     * @throws \dml_exception
     */
    public static function attribute_exist($attribute, $value, $parentcolumn = '', $parentid = 0): bool{
        global $DB;
        if($parentid === 0){
            $records = $DB->get_records(static::$TABLE, array($attribute => $value));
        }else {
            $records = $DB->get_records(static::$TABLE, array($attribute => $value, $parentcolumn => $parentid));
        }
        return count($records) !== 0;
    }

    /**
     * Converts the object to JSON.
     * @return false|string
     */
    protected function to_json()
    {
        $properties = $this->get_properties();
        $object     = new \stdClass();
        $object->_class      = get_class($this);
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }
        return json_encode($object);
    }

    /**
     * @return array
     */
    protected function get_properties()
    {
        return get_object_vars($this);
    }
}