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
 * Email factor class.
 *
 * @package     factor_email
 * @subpackage  tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace factor_email;

defined('MOODLE_INTERNAL') || die();

use tool_mfa\local\factor\object_factor_base;

class factor extends object_factor_base {
    /**
     * E-Mail Factor implementation.
     *
     * {@inheritDoc}
     */
    public function login_form_definition($mform) {
        $userfactors = $this->get_active_user_factors();

        if (count($userfactors) > 0) {
            $mform->addElement('hidden', 'secret');
            $mform->setType('secret', PARAM_ALPHANUM);

            $mform->addElement('text', 'verificationcode', get_string('verificationcode', 'factor_email'));
            $mform->setType("verificationcode", PARAM_ALPHANUM);
        }

        return $mform;
    }

    /**
     * E-Mail Factor implementation.
     *
     * {@inheritDoc}
     */
    public function login_form_definition_after_data($mform) {
        $secretfield = $mform->getElement('secret');
        $secret = $secretfield->getValue();

        if (empty($secret)) {
            $secret = random_int(100000, 999999);
            $secretfield->setValue($secret);
            $this->email_verification_code($secret);
        }
        return $mform;
    }

    /**
     * Sends and e-mail to user with given verification code.
     *
     */
    public function email_verification_code($secret) {
        global $USER;
        $noreplyuser = \core_user::get_noreply_user();
        $subject = get_string('email:subject', 'factor_email');
        $message = get_string('email:message', 'factor_email', $secret);
        $messagehtml = text_to_html($message);
        email_to_user($USER, $noreplyuser, $subject, $message, $messagehtml);
    }

    /**
     * E-Mail Factor implementation.
     *
     * {@inheritDoc}
     */
    public function login_form_validation($data) {
        $return = array();

        if ($data['verificationcode'] != $data['secret']) {
            $return['verificationcode'] = get_string('error:wrongverification', 'factor_email');
        }

        return $return;
    }

    /**
     * E-Mail Factor implementation.
     *
     * {@inheritDoc}
     */
    public function get_all_user_factors() {
        global $DB, $USER;

        $records = $DB->get_records('tool_mfa', array('userid' => $USER->id, 'factor' => $this->name));

        if (!empty($records)) {
            return $records;
        }

        // Null records returned, build new record.
        $record = array(
            'userid' => $USER->id,
            'factor' => $this->name,
            'label' => $USER->email,
            'createdfromip' => $USER->lastip,
            'timecreated' => time(),
            'revoked' => 0,
        );
        $record['id'] = $DB->insert_record('tool_mfa', $record, true);
        return [(object) $record];
    }
}
