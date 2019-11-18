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
 * Configure user factor page
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

use tool_mfa\local\form\setup_factor_form;

require_login(null, false);
if (isguestuser()) {
    throw new require_login_exception('Guests are not allowed here.');
}

$action = optional_param('action', '', PARAM_ALPHANUMEXT);
$factor = optional_param('factor', '', PARAM_ALPHANUMEXT);
$factorid = optional_param('factorid', '', PARAM_INT);

$params = array('action' => $action, 'factor' => $factor);
$currenturl = new moodle_url('/admin/tool/mfa/action.php', $params);

$returnurl = new moodle_url('/admin/tool/mfa/user_preferences.php');

if (empty($factor) || empty($action)) {
    print_error('error:directaccess', 'tool_mfa', $returnurl);
}

if (!tool_mfa_factor_exists($factor)) {
    print_error('error:factornotfound', 'tool_mfa', $returnurl, $factor);
}

if (!in_array($action, \tool_mfa\plugininfo\factor::get_factor_actions())) {
    print_error('error:actionnotfound', 'tool_mfa', $returnurl, $action);
}

$factorobject = \tool_mfa\plugininfo\factor::get_factor($factor);

$context = context_user::instance($USER->id);
$PAGE->set_context($context);
$PAGE->set_url('/admin/tool/mfa/action.php');
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string($action.'factor', 'tool_mfa'));
$PAGE->set_cacheable(false);

switch ($action) {
    case 'setup':
        if (!$factorobject->has_setup()) {
            redirect($returnurl);
        }

        $OUTPUT = $PAGE->get_renderer('tool_mfa');
        $form = new setup_factor_form($currenturl, array('factorname' => $factor));

        if ($form->is_submitted()) {
            $form->is_validated();

            if ($form->is_cancelled()) {
                redirect($returnurl);
            }

            if ($data = $form->get_data()) {
                if ($factorobject && $factorobject->setup_user_factor($data)) {
                    $event = \tool_mfa\event\user_setup_factor::user_setup_factor_event($USER, $factorobject->get_display_name());
                    $event->trigger();
                    redirect($returnurl);
                }

                print_error('error:setupfactor', 'tool_mfa', $returnurl);
            }
        }

        echo $OUTPUT->header();
        $form->display();

        break;

    case 'revoke':
        if ($factorobject && $factorobject->has_revoke()) {
            if (!$factorobject->revoke_user_factor($factorid)) {
                print_error('error:revokefactor', 'tool_mfa', $returnurl);
            }
        }

        redirect($returnurl);
        break;

    default:
        break;
}

echo $OUTPUT->footer();
