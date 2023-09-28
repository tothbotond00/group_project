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
 * Group class.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_groupproject\local\entities;
class group {
    /** @var int $id ID */
    private $id;
    /** @var string $name Name of the group  */
    private $name;
    /** @var ?string $idnumber Idnumber of group */
    private $idnumber;
    /** @var int $size Size of the group */
    private $size;
    /** @var int $timecreated Group creation unix timestamp */
    private $timecreated;
    /** @var int $timemodified Group creation unix timestamp */
    private $timemodified;

    /**
     * @param int $id
     * @param string $name
     * @param ?string $idnumber
     * @param int $size
     * @param int $timecreated
     * @param int $timemodified
     */
    public function __construct(int $id, string $name, ?string $idnumber, int $size, int $timecreated, int $timemodified)
    {
        $this->id = $id;
        $this->name = $name;
        $this->idnumber = $idnumber;
        $this->size = $size;
        $this->timecreated = $timecreated;
        $this->timemodified = $timemodified;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getIdnumber(): ?string
    {
        return $this->idnumber;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return int
     */
    public function getTimecreated(): int
    {
        return $this->timecreated;
    }

    /**
     * @return int
     */
    public function getTimemodified(): int
    {
        return $this->timemodified;
    }

}