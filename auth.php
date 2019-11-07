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
 * MFA page
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/admin/tool/mfa/lib.php');
require_once($CFG->libdir.'/adminlib.php');

use tool_mfa\local\form\login_form;

$wantsurl  = optional_param('wantsurl', '', PARAM_LOCALURL);

if (empty($wantsurl)) {
    $wantsurl = '/';
}

$context = context_user::instance($USER->id);
$PAGE->set_context($context);
$PAGE->set_url('/admin/tool/mfa/auth.php');
$PAGE->set_pagelayout('popup');
$pagetitle = $SITE->shortname.': '.get_string('mfa', 'tool_mfa');
$PAGE->set_title($pagetitle);


$OUTPUT = $PAGE->get_renderer('tool_mfa');

$params = array('wantsurl' => $wantsurl);
$currenturl = new moodle_url('/admin/tool/mfa/auth.php', $params);

$userfactors = \tool_mfa\plugininfo\factor::get_enabled_user_factor_types();

if (count($userfactors) > 0) {
    $nextfactor = \tool_mfa\plugininfo\factor::get_next_user_factor();
    $factorname = $nextfactor->name;
    $gracemode = false;
} else {
    $factorname = null;
    $gracemode = true;
}

$form = new login_form($currenturl, array('factor_name' => $factorname, 'grace_mode' => $gracemode));

if ($form->is_cancelled()) {
    tool_mfa_logout();
    redirect(new moodle_url('/'));
}

if ($form->is_submitted()) {
    if ($data = $form->get_data()) {
        $property = 'factor_'.$factorname.'_authenticated';
        $USER->$property = true;

        if (\tool_mfa\plugininfo\factor::get_next_user_factor()) {
            redirect($currenturl);
        } else {
            $USER->tool_mfa_authenticated = true;
            if ($gracemode) {
                redirect(new moodle_url('/admin/tool/mfa/user_preferences.php'));
            } else {
                redirect(new moodle_url($wantsurl));
            }
        }
    }
}

echo $OUTPUT->header();

if ($gracemode) {
    echo $OUTPUT->heading(get_string('pluginname', 'tool_mfa'));
} else {
    echo $OUTPUT->heading(get_string('pluginname', 'factor_'.$factorname));
}

$form->display();
echo $OUTPUT->footer();
