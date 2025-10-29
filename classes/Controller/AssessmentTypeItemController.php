<?php

namespace mod_assessment\Controller;

use Doctrine\ORM\EntityManager;
use mod_assessment\Entity\Assessment;
use mod_assessment\Entity\AssessmentTypeField;
use mod_assessment\Entity\AssessmentTypeItem;
use mod_assessment\Entity\AssessmentTypeSection;
use mod_assessment\Form\AssessmentTypeItemType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment as TwigEnvironment;

class AssessmentTypeItemController extends SwpdpController
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
        $this->urlGenerator = $this->container->get('router');
    }

    public function addEdit(Request $request): Response
    {
        if ($request->get('id')) {
            $assessmentTypeItem = $this->entityManager->find(AssessmentTypeItem::class, $request->get('id'));
        } elseif ($request->get('assessmentTypeSectionId')) {
            $assessmentTypeSection = $this->entityManager->find(AssessmentTypeSection::class, $request->get('assessmentTypeSectionId'));
            $type = $request->get('type');
            if ($type === 'other') {
            } else {
                // Default AssessmentTypeItem superclass
                $assessmentTypeItem = new AssessmentTypeField();
            }
            $assessmentTypeItem->setAssessmentTypeSection($assessmentTypeSection);
            $this->entityManager->persist($assessmentTypeItem);
        }

        $form = $this->formFactory->create(AssessmentTypeItemType::class, $assessmentTypeItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->session->getFlashBag()->add('success', 'Assessment type item saved');

            $url = $this->urlGenerator->generate('assessment_type_section_edit', [
                'id' => $assessmentTypeItem->getAssessmentTypeSection()->getId(),
            ]);

            return new RedirectResponse($url);
        }

        $twigHtml = $this->twig->render('assessment_type_item/add_edit.html.twig', [
            'assessmentTypeItem' => $assessmentTypeItem,
            'form' => $form->createView(),
            'flash_messages' => $this->session->getFlashBag()->all(),
        ]);

        // As AssessmentTypes are independent of an Assessment, pick the first Assessment
        $assessmentRepo = $this->entityManager->getRepository(Assessment::class);
        $assessments = $assessmentRepo->findAll();
        $this->setAssessment($assessments[0]);

        return new Response($twigHtml);
    }
}
