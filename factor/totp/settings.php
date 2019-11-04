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
 * Settings
 *
 * @package     factor_totp
 * @subpackage  tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_configcheckbox('factor_totp/enabled',
    new lang_string('settings:enable', 'factor_totp'),
    new lang_string('settings:enable_help', 'factor_totp'), 0));

$settings->add(new admin_setting_configtext('factor_totp/weight',
    new lang_string('settings:weight', 'factor_totp'),
    new lang_string('settings:weight_help', 'factor_totp'), 0, PARAM_INT));

$settings->add(new admin_setting_configtext('factor_totp/secret_length',
    new lang_string('settings:secretlength', 'factor_totp'),
    new lang_string('settings:secretlength_help', 'factor_totp'), 16, PARAM_INT));
// TODO: Add validation for secret_length to be between 4 and 64.
