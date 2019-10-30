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
 * MFA factor abstract class.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mfa\local\factor;

defined('MOODLE_INTERNAL') || die();

abstract class object_factor_base implements object_factor {
    /**
     * Factor name.
     *
     * @var name
     */
    public $name;

    public function __construct($name) {
        $this->name = $name;
    }

    public function is_enabled() {
        $status = get_config('tool_mfa', $this->name.'enable');
        if ($status == 1) {
            return true;
        }
        return false;
    }

    public function get_weight() {
        $weight = get_config('tool_mfa', $this->name.'weight');
        return $weight;
    }
}