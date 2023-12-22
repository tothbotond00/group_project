<?php

/**
 * Contains the class for fetching the important dates in mod_groupproject for a given module instance and a user.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 */

declare(strict_types=1);

namespace mod_groupproject;

use core\activity_dates;
use mod_groupproject\local\loaders\entity_loader;

class dates extends activity_dates {

    protected function get_dates(): array
    {
        $groupproject = entity_loader::groupproject_loader($this->cm->instance);
        $dates = [];
        if(!empty($groupproject->getDuedate())){
            $date = [
                'dataid' => 'duedate',
                'label' => get_string('duedate', 'mod_groupproject'),
                'timestamp' => (int) $groupproject->getDuedate(),
            ];
            $dates[] = $date;
        }
        return $dates;
    }
}