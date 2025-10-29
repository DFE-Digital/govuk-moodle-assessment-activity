<?php

namespace mod_assessment\Controller;

use Doctrine\ORM\EntityManager;
use mod_assessment\Entity\Assessment;
use mod_assessment\Entity\AssessmentType;
use mod_assessment\Entity\AssessmentTypeSection;
use mod_assessment\Form\AssessmentTypeSectionType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment as TwigEnvironment;

class AssessmentTypeSectionController extends SwpdpController
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
            $assessmentTypeSection = $this->entityManager->find(AssessmentTypeSection::class, $request->get('id'));
        } elseif ($request->get('assessmentTypeId')) {
            $assessmentType = $this->entityManager->find(AssessmentType::class, $request->get('assessmentTypeId'));
            $assessmentTypeSection = new AssessmentTypeSection();
            $assessmentTypeSection->setAssessmentType($assessmentType);
            $this->entityManager->persist($assessmentTypeSection);
        }

        $form = $this->formFactory->create(AssessmentTypeSectionType::class, $assessmentTypeSection);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->session->getFlashBag()->add('success', 'Assessment type section saved');

            $url = $this->urlGenerator->generate('assessment_type_edit', [
                'id' => $assessmentTypeSection->getAssessmentType()->getId(),
            ]);

            return new RedirectResponse($url);
        }

        $twigHtml = $this->twig->render('assessment_type_section/add_edit.html.twig', [
            'assessmentTypeSection' => $assessmentTypeSection,
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
