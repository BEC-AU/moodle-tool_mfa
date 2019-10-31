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
 * MFA preferences form
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_mfa\local\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/formslib.php");

class preferences_form extends \moodleform
{
    /**
     * {@inheritDoc}
     * @see moodleform::definition()
     */
    public function definition()
    {
        global $OUTPUT;
        $mform = $this->_form;
        $mform = $this->define_configured_factors($mform);
        $mform = $this->define_available_factors($mform);

    }

    public function define_configured_factors($mform) {
        global $OUTPUT;

        $mform->addElement('html', $OUTPUT->heading(get_string('preferences:configuredfactors', 'tool_mfa'), 4));
        $mform->addElement('html', $OUTPUT->heading('TBA', 5));

        return $mform;
    }

    public function define_available_factors($mform) {
        global $OUTPUT;

        $mform->addElement('html', $OUTPUT->heading(get_string('preferences:availablefactors', 'tool_mfa'), 4));

        $headers = get_strings(array('name', 'weight', 'action'), 'tool_mfa');

        $table = new \html_table();
        $table->id = 'available_factors';
        $table->attributes['class'] = 'generaltable';
        $table->head  = array($headers->name, $headers->weight, $headers->action);
        $table->colclasses = array('leftalign', 'centeralign', 'centeralign');
        $table->data  = array();

        $factors = \tool_mfa\plugininfo\factor::get_enabled_factors();

        foreach ($factors as $factor) {
            $url = "action.php?sesskey=" . sesskey();

            $action = "<a href=\"$url&amp;action=add&amp;factor=$factor->name\">";
            $action .= get_string('addfactor', 'tool_mfa') . '</a>';

            $row = new \html_table_row(array($factor->get_display_name(), $factor->get_weight(), $action));
            $table->data[] = $row;
        }

        $return = $OUTPUT->box_start('generalbox');
        $return .= \html_writer::table($table);
        $return .= $OUTPUT->box_end();

        $mform->addElement('html', $return);
        return $mform;
    }
}
