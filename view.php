<?php

// Moodle (minimal)
require_once(__DIR__.'/../../config.php');
require_login();

// Course module id.
$id = optional_param('id', 0, PARAM_INT);

$cm = get_coursemodule_from_id('assessment', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$moduleinstance = $DB->get_record('assessment', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);

$PAGE->set_title(format_string($moduleinstance->name));
$pageurl = new moodle_url('/mod/assessment/view.php');
$PAGE->set_url($pageurl);
// Apply gds-container CSS fixes
$PAGE->requires->css('/mod/assessment/gds_container_fixes.css');
// END: Moodle (minimal)

// Symfony
use mod_assessment\Controller\AssessmentController;

$container = require_once(__DIR__ . '/bootstrap.php');
$controller = new AssessmentController($container);
$response = $controller->view($moduleinstance->id);

echo $OUTPUT->header();
echo $response->getContent(); // Twig HTML
echo $OUTPUT->footer();
