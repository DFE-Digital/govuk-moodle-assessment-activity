<?php

namespace mod_assessment\Controller;

use Doctrine\ORM\EntityManager;
use mod_assessment\Entity\Assessment;
use mod_assessment\Entity\AssessmentRecord;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;

class AssessmentController extends SwpdpController
{
    private readonly EntityManager $entityManager;
    private readonly TwigEnvironment $twig;

    public function __construct(private readonly ContainerInterface $container)
    {
        $this->entityManager = $this->container->get('doctrine.entity_manager');
        $this->twig = $this->container->get('twig');
    }

    public function view(int $id): Response
    {
        global $COURSE, $PAGE, $USER;
        // Moodle: Force the page type to mod-data-edit for CSS reasons
        // TODO: Unpick this
        # Disabled temporarily to measure impact of removing this
        #$PAGE->requires->css('/mod/assessment/assessment.css');
        #$PAGE->set_pagetype('mod-data-edit');

        $repo = $this->entityManager->getRepository(Assessment::class);
        $assessment = $repo->find($id);

        // Implement access restrictions based on the use of Moodle groups.
        // Find the current user's groups
        $groupIds = groups_get_user_groups($COURSE->id, $USER->id)[0];
        $userIds = [];
        // A user can always view their own AssessmentRecords
        $userIds[$USER->id] = true;
        // Calculate the superset of user IDs in any of these groups
        foreach ($groupIds as $groupId) {
            $userIdsInGroup = groups_get_members($groupId);
            foreach (array_keys($userIdsInGroup) as $userIdInGroup) {
                $userIds[$userIdInGroup] = true;
            }
        }
        $userIds = array_keys($userIds);

        // Retrieve any AssessmentRecords created by any of the users to which the current user has access
        $repo = $this->entityManager->getRepository(AssessmentRecord::class);
        $assessmentRecords = $repo->createQueryBuilder('ar')
            ->where('ar.assessment = :assessment')
            ->setParameter('assessment', $assessment)
            ->andWhere('ar.userId IN (:user_ids)')
            ->setParameter('user_ids', $userIds)
            ->orderBy('ar.createdAt', 'desc')
            ->getQuery()
            ->getResult()
        ;

        $html = $this->twig->render('assessment/view.html.twig', [
            'assessment' => $assessment,
            'assessmentRecords' => $assessmentRecords,
        ]);

        return new Response($html);
    }
}
