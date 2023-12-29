<?php

defined('MOODLE_INTERNAL') || die();

/**
 * mod_groupproject data generator class.
 *
 * @package    mod_groupproject
 * @category   test
 * @copyright  2023 Tóth Botond
 */
class mod_groupproject_generator extends testing_module_generator {
    public function create_instance($record = null, array $options = null) {
            $record = (object)(array)$record;

            $defaultsettings = array(
                'grade'                             => 100,
                'duedate'                           => 0,
            );

            foreach ($defaultsettings as $name => $value) {
                if (!isset($record->{$name})) {
                    $record->{$name} = $value;
                }
            }

            return parent::create_instance($record, (array)$options);
    }
}