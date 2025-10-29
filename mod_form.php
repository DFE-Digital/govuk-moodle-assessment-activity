<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * The main mod_assessment configuration form.
 *
 * @package     mod_assessment
 * @copyright   2025 Dave Small
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_assessment\Entity\AssessmentType;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package     mod_assessment
 * @copyright   2025 Dave Small
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assessment_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('assessmentname', 'mod_assessment'), ['size' => '100']);

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'assessmentname', 'mod_assessment');

        // SWPDP: Adding asssessment_type_id field, specific to the Assessment module
        // This will not be the eventual implementation, but for now retrieve all AssessmentTypes
        // and build a simple array of choices.
        $container = require_once(__DIR__ . '/bootstrap.php');
        $entityManager = $container->get('doctrine.entity_manager');
        $repo = $entityManager->getRepository(AssessmentType::class);
        $assessmentTypes = $repo->findAll();
        $choices = [];
        $choices[''] = '--- Select assessment type ---';
        foreach ($assessmentTypes as $assessmentType) {
            $choices[$assessmentType->getId()] = $assessmentType->getName();
        }
        $mform->addElement(
            'select',
            'assessment_type_id',
            get_string('assessmenttype', 'mod_assessment'),
            $choices,
        );
        // END: Adding asssessment_type_id field

        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        // Adding the rest of mod_assessment settings, spreading all them into this fieldset
        // ... or adding more fieldsets ('header' elements) if needed for better logic.
        $mform->addElement('static', 'label1', 'assessmentsettings', get_string('assessmentsettings', 'mod_assessment'));
        $mform->addElement('header', 'assessmentfieldset', get_string('assessmentfieldset', 'mod_assessment'));

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
    }
}
