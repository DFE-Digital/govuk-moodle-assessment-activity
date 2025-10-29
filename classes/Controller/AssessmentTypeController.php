<?php

namespace mod_assessment\Controller;

use Doctrine\ORM\EntityManager;
use mod_assessment\Entity\Assessment;
use mod_assessment\Entity\AssessmentType;
use mod_assessment\Form\AssessmentTypeType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment as TwigEnvironment;

class AssessmentTypeController extends SwpdpController
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

    public function list(): Response
    {
        $assessmentTypeRepo = $this->entityManager->getRepository(AssessmentType::class);
        $assessmentTypes = $assessmentTypeRepo->findAll();

        $twigHtml = $this->twig->render('assessment_type/list.html.twig', [
            'assessmentTypes' => $assessmentTypes
        ]);

        // As AssessmentTypes are independent of an Assessment, pick the first Assessment
        $assessmentRepo = $this->entityManager->getRepository(Assessment::class);
        $assessments = $assessmentRepo->findAll();
        $this->setAssessment($assessments[0]);

        return new Response($twigHtml);
    }

    public function addEdit(Request $request): Response
    {
        if ($request->get('id')) {
            $assessmentType = $this->entityManager->find(AssessmentType::class, $request->get('id'));
        } else {
            $assessmentType = new AssessmentType();
            $this->entityManager->persist($assessmentType);
        }

        $form = $this->formFactory->create(AssessmentTypeType::class, $assessmentType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->session->getFlashBag()->add('success', 'Assessment type saved');

            $url = $this->urlGenerator->generate('assessment_type_edit', [
                'id' => $assessmentType->getId(),
            ]);
            
            return new RedirectResponse($url);
        }

        $twigHtml = $this->twig->render('assessment_type/add_edit.html.twig', [
            'assessmentType' => $assessmentType,
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
