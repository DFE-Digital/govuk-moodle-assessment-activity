<?php

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$routes = new RouteCollection();

// AssessmentType
$routes->add('assessment_type_index', new Route(
    '/assessment-types',
    ['_controller' => 'mod_assessment\Controller\AssessmentTypeController::list']
));

$routes->add('assessment_type_new', new Route(
    '/assessment-types/new',
    ['_controller' => 'mod_assessment\Controller\AssessmentTypeController::addEdit']
));

$routes->add('assessment_type_edit', new Route(
    '/assessment-types/{id}/edit',
    ['_controller' => 'mod_assessment\Controller\AssessmentTypeController::addEdit'],
    ['id' => '\d+']
));

$routes->add('assessment_type_delete', new Route(
    '/assessment-types/{id}/delete',
    ['_controller' => 'mod_assessment\Controller\AssessmentTypeController::delete'],
    ['id' => '\d+']
));

// AssessmentTypeSection
$routes->add('assessment_type_section_new', new Route(
    '/assessment-types/{assessmentTypeId}/assessment-type-sections/new',
    ['_controller' => 'mod_assessment\Controller\AssessmentTypeSectionController::addEdit'],
    ['assessmentTypeId' => '\d+']
));

$routes->add('assessment_type_section_edit', new Route(
    '/assessment-type-sections/{id}/edit',
    ['_controller' => 'mod_assessment\Controller\AssessmentTypeSectionController::addEdit'],
    ['id' => '\d+']
));

$routes->add('assessment_type_section_delete', new Route(
    '/assessment-type-sections/{id}/delete',
    ['_controller' => 'mod_assessment\Controller\AssessmentTypeSectionController::delete'],
    ['id' => '\d+']
));

// AssessmentTypeItem
$routes->add('assessment_type_item_new', new Route(
    '/assessment-type-sections/{assessmentTypeSectionId}/assessment-type-items/new',
    ['_controller' => 'mod_assessment\Controller\AssessmentTypeItemController::addEdit'],
    ['assessmentTypeSectionId' => '\d+']
));

$routes->add('assessment_type_item_edit', new Route(
    '/assessment-type-items/{id}/edit',
    ['_controller' => 'mod_assessment\Controller\AssessmentTypeItemController::addEdit'],
    ['id' => '\d+']
));

$routes->add('assessment_type_item_delete', new Route(
    '/assessment-type-items/{id}/delete',
    ['_controller' => 'mod_assessment\Controller\AssessmentTypeItemController::delete'],
    ['id' => '\d+']
));

// AssessmentRecord
$routes->add('assessment_record_new', new Route(
    '/assessments/{assessmentId}/assessment-records/new',
    ['_controller' => 'mod_assessment\Controller\AssessmentRecordController::addEdit'],
    ['assessmentId' => '\d+']
));

$routes->add('assessment_record_new_review', new Route(
    '/assessments/{assessmentId}/assessment-records/new/review',
    ['_controller' => 'mod_assessment\Controller\AssessmentRecordController::review'],
    ['assessmentId' => '\d+']
));

$routes->add('assessment_record_edit', new Route(
    '/assessment-records/{id}/edit',
    ['_controller' => 'mod_assessment\Controller\AssessmentRecordController::addEdit'],
    ['id' => '\d+']
));

$routes->add('assessment_record_edit_review', new Route(
    '/assessment-records/{id}/review',
    ['_controller' => 'mod_assessment\Controller\AssessmentRecordController::review'],
    ['id' => '\d+']
));

$routes->add('assessment_record_delete', new Route(
    '/assessment-records/{id}/delete',
    ['_controller' => 'mod_assessment\Controller\AssessmentRecordController::delete'],
    ['id' => '\d+']
));

return $routes;
