<?php

namespace mod_assessment\Controller;

use Doctrine\ORM\EntityManager;
use mod_assessment\Dto\AssessmentRecordDto;
use mod_assessment\Entity\Assessment;
use mod_assessment\Entity\AssessmentRecord;
use mod_assessment\Entity\AssessmentRecordItem;
use mod_assessment\Entity\AssessmentTypeField;
use mod_assessment\Form\AssessmentRecordType;
use mod_assessment\Mapper\AssessmentRecordMapper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment as TwigEnvironment;

class AssessmentRecordController extends SwpdpController
{
    private readonly EntityManager $entityManager;
    private readonly FormFactory $formFactory;
    private readonly Session $session;
    private readonly TwigEnvironment $twig;
    private readonly UrlGeneratorInterface $urlGenerator;

    public function __construct(private readonly ContainerInterface $container)
    {
        $this->entityManager = $this->container->get('doctrine.entity_manager');
        $this->formFactory = $container->get('form.factory');
        $this->session = $container->get('session');
        $this->twig = $this->container->get('twig');
        $this->urlGenerator = $container->get('router');
    }

    public function addEdit(Request $request): Response
    {
        global $PAGE, $USER;
        
        // Moodle: Force the page type to mod-data-edit for CSS reasons
        // TODO: Unpick this
        $PAGE->requires->css('/mod/assessment/assessment.css');
        $PAGE->set_pagetype('mod-data-edit');

        if ($request->get('id')) {
            $assessmentRecord = $this->entityManager->find(AssessmentRecord::class, $request->get('id'));
        } elseif ($request->get('assessmentId')) {
            $assessment = $this->entityManager->find(Assessment::class, $request->get('assessmentId'));
            $assessmentRecord = new AssessmentRecord();
            $assessmentRecord->setAssessment($assessment);
            $assessmentRecord->setUserId($USER->id);

            // Create AssessmentRecordItems for each AssessmentTypeItem
            foreach ($assessment->getAssessmentType()->getAssessmentTypeSections() as $assessmentTypeSection) {
                foreach ($assessmentTypeSection->getAssessmentTypeItems() as $assessmentTypeItem) {
                    if (!$assessmentTypeItem instanceof AssessmentTypeField) {
                        continue;
                    }
                    if ($assessmentTypeItem->getDataType() === 'static') {
                        continue;
                    }
                    $assessmentRecordItem = new AssessmentRecordItem();
                    $assessmentRecordItem->setAssessmentTypeItem($assessmentTypeItem);
                    $assessmentRecord->addAssessmentRecordItem($assessmentRecordItem);
                }
            }

            $this->entityManager->persist($assessmentRecord);
        }

        // Define the session key for this AssessmentRecord
        $sessionKey = 'AssessmentRecord_' . ($assessmentRecord->getId() ?? 'new');

        // If 'change' is not present in the URL, remove the DTO from the session
        if (!$request->get('change')) {
            $this->session->remove($sessionKey);
        }

        // Get the DTO, either from the session or the AssessmentRecord entity
        $assessmentRecordMapper = new AssessmentRecordMapper($this->entityManager);
        if ($this->session->has($sessionKey)) {
            $assessmentRecordDto = AssessmentRecordDto::fromArray($this->session->get($sessionKey));
        } else {
            $assessmentRecordDto = $assessmentRecordMapper->entityToDto($assessmentRecord);
        }

        $form = $this->formFactory->create(AssessmentRecordType::class, $assessmentRecordDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->get('action') === 'review') {
                // Save DTO to session
                $this->session->set($sessionKey, $assessmentRecordDto->toArray());

                if ($assessmentRecord->getId()) {
                    $url = $this->urlGenerator->generate('assessment_record_edit_review', [
                        'id' => $assessmentRecordDto->id,
                    ]);

                    return new RedirectResponse($url);
                } else {
                    $url = $this->urlGenerator->generate('assessment_record_new_review', [
                        'assessmentId' => $assessmentRecord->getAssessment()->getId(),
                    ]);

                    return new RedirectResponse($url);
                }
            }

            // Save has been clicked
            $assessmentRecordMapper->updateEntity($assessmentRecord, $assessmentRecordDto);
            $this->entityManager->flush();
            $this->session->getFlashBag()->add('success', 'Assessment record saved');

            // The session data has now been used: invalidate it
            $this->session->remove($sessionKey);

            $courseModule = get_coursemodule_from_instance('assessment', $assessmentRecord->getAssessment()->getId(), $assessmentRecord->getAssessment()->getCourse());

            return new RedirectResponse('/mod/assessment/view.php?id=' . $courseModule->id);
        }

        $this->setAssessment($assessmentRecord->getAssessment());
        $twigHtml = $this->twig->render('assessment_record/add_edit.html.twig', [
            'assessmentRecord' => $assessmentRecord,
            'form' => $form->createView(),
            'flash_messages' => $this->session->getFlashBag()->all(),
        ]);

        return new Response($twigHtml);
    }

    public function review(Request $request): Response
    {
        global $USER;

        if ($request->get('id')) {
            $assessmentRecord = $this->entityManager->find(AssessmentRecord::class, $request->get('id'));
        } else {
            $assessment = $this->entityManager->find(Assessment::class, $request->get('assessmentId'));
            $assessmentRecord = new AssessmentRecord();
            $assessmentRecord->setAssessment($assessment);
            $assessmentRecord->setUserId($USER->id);
            $this->entityManager->persist($assessmentRecord);
        }

        // Retrieve the DTO from the session
        $sessionKey = 'AssessmentRecord_' . ($request->get('id') ?? 'new');
        $assessmentRecordDto = AssessmentRecordDto::fromArray($this->session->get($sessionKey));

        // Update the AssessmentRecord from the DTO, but don't persist it yet: unpersisted, it will be rendered in review.html.twig 
        $assessmentRecordMapper = new AssessmentRecordMapper($this->entityManager);
        $assessmentRecordMapper->updateEntity($assessmentRecord, $assessmentRecordDto);

        if ($request->getMethod() === 'POST') {
            $this->entityManager->flush();
            $this->session->getFlashBag()->add('success', 'Assessment record saved');

            // The session data has now been used: invalidate it
            $this->session->remove($sessionKey);

            $courseModule = get_coursemodule_from_instance('assessment', $assessmentRecord->getAssessment()->getId(), $assessmentRecord->getAssessment()->getCourse());

            return new RedirectResponse('/mod/assessment/view.php?id=' . $courseModule->id);
        }

        $this->setAssessment($assessmentRecord->getAssessment());
        $twigHtml = $this->twig->render('assessment_record/review.html.twig', [
            'assessmentRecord' => $assessmentRecord,
        ]);

        return new Response($twigHtml);
    }

    public function delete(Request $request): Response
    {
        $assessmentRecord = $this->entityManager->find(AssessmentRecord::class, $request->get('id'));

        $courseModule = get_coursemodule_from_instance('assessment', $assessmentRecord->getAssessment()->getId(), $assessmentRecord->getAssessment()->getCourse());
        
        // If the URL contains confirmation, delete the AssessmentRecord
        if ($request->get('confirm')) {
            $this->entityManager->remove($assessmentRecord);
            $this->entityManager->flush();
            $this->session->getFlashBag()->add('success', 'Assessment rrecord deleted');

            return new RedirectResponse('/mod/assessment/view.php?id=' . $courseModule->id);
        }

        // Render 'confirm delete'
        $this->setAssessment($assessmentRecord->getAssessment());
        $twigHtml = $this->twig->render('assessment_record/delete.html.twig', [
            'assessmentRecord' => $assessmentRecord,
            'courseModule' => $courseModule,
        ]);

        return new Response($twigHtml);
    }
}
