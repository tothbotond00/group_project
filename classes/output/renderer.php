<?php

/**
 * Renderer.
 *
 * @package    mod_groupproject
 * @copyright  2023 Tóth Botond
 */

namespace mod_groupproject\output;

defined('MOODLE_INTERNAL') || die();

class renderer extends \plugin_renderer_base {

    /** @var string a unique ID. */
    public $htmlid;
}