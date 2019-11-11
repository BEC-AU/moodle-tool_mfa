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
 * Moodle MFA plugin lib
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Main hook.
 * If MFA Plugin is ready check tool_mfa_authenticated USER property and
 * start MFA authentication if it's not set or false.
 *
 * @return void
 * @throws \moodle_exception
 */
function tool_mfa_after_require_login() {
    global $USER, $ME;

    if (!tool_mfa_ready()) {
        return;
    }

    if (empty($USER->tool_mfa_authenticated) || !$USER->tool_mfa_authenticated) {
        if ($ME != '/admin/tool/mfa/auth.php') {
            redirect(new moodle_url('/admin/tool/mfa/auth.php', array('wantsurl' => $ME)));
        }
    }
}

/**
 * Checks if MFA Plugin is enabled and has enabled factor.
 * If plugin is disabled or there is no enabled factors,
 * it means there is nothing to do from user side.
 * Thus, login flow shouldn't be extended with MFA.
 *
 * @return bool
 * @throws \dml_exception
 */
function tool_mfa_ready() {
    $pluginenabled = get_config('tool_mfa', 'enabled');
    $enabledfactors = \tool_mfa\plugininfo\factor::get_enabled_factors();

    if (empty($pluginenabled) || count($enabledfactors) == 0) {
        return false;
    }

    return true;
}

/**
 * Logout user.
 *
 * @return void
 */
function tool_mfa_logout() {
    $authsequence = get_enabled_auth_plugins();
    foreach ($authsequence as $authname) {
        $authplugin = get_auth_plugin($authname);
        $authplugin->logoutpage_hook();
    }
    require_logout();
}

/**
 * Sets config variable for given factor.
 *
 * @param array $data
 * @param string $factor
 *
 * @return bool true or exception
 */
function tool_mfa_set_factor_config($data, $factor) {
    foreach ($data as $key => $value) {
        set_config($key, $value, $factor);
    }
    return true;
}

/**
 * Checks that given factor exists.
 *
 * @param string $factorname
 *
 * @return bool
 */
function tool_mfa_factor_exists($factorname) {
    $factors = \tool_mfa\plugininfo\factor::get_factors();
    foreach ($factors as $factor) {
        if ($factorname == $factor->name) {
            return true;
        }
    }
    return false;
}

/**
 * Extends navigation bar and injects MFA Preferences menu to user preferences.
 *
 * @param navigation_node $navigation
 * @param stdClass $user
 * @param context_user $usercontext
 * @param stdClass $course
 * @param context_course $coursecontext
 *
 * @return void or null
 * @throws \moodle_exception
 */
function tool_mfa_extend_navigation_user_settings($navigation, $user, $usercontext, $course, $coursecontext) {
    global $PAGE;

    // Only inject if user is on the preferences page.
    $onpreferencepage = $PAGE->url->compare(new moodle_url('/user/preferences.php'), URL_MATCH_BASE);
    if (!$onpreferencepage) {
        return null;
    }

    $url = new moodle_url('/admin/tool/mfa/user_preferences.php');
    $node = navigation_node::create(get_string('preferences:header', 'tool_mfa'), $url,
        navigation_node::TYPE_SETTING);
    $usernode = $navigation->find('useraccount', navigation_node::TYPE_CONTAINER);
    $usernode->add_node($node);
}
