<?php

namespace mod_assessment\Mapper;

use Doctrine\ORM\EntityManagerInterface;
use mod_assessment\Dto\AssessmentRecordDto;
use mod_assessment\Dto\AssessmentRecordItemDto;
use mod_assessment\Entity\Assessment;
use mod_assessment\Entity\AssessmentRecord;
use mod_assessment\Entity\AssessmentRecordItem;
use mod_assessment\Entity\AssessmentTypeItem;

final class AssessmentRecordMapper
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * Update an AssessmentRecord entity using data from an AssessmentRecord DTO
     */
    public function updateEntity(AssessmentRecord $assessmentRecord, AssessmentRecordDto $assessmentRecordDto): AssessmentRecord
    {
        if ($assessmentRecordDto->assessmentId) {
            $assessmentRepo = $this->entityManager->getRepository(Assessment::class);
            $assessment = $assessmentRepo->find($assessmentRecordDto->assessmentId);
            $assessmentRecord->setAssessment($assessment);
        }

        $assessmentRecord->setUserId($assessmentRecordDto->userId);

        // Synchronise AssessmentRecordItems
        $existingAssessmentRecordItems = $assessmentRecord->getAssessmentRecordItems();
        $existingAssessmentRecordItemsIndexedOnAssessmentTypeItemId = [];
        foreach ($existingAssessmentRecordItems as $existingAssessmentRecordItem) {
            if ($existingAssessmentRecordItem->getAssessmentTypeItem()->getId() !== null) {
                $existingAssessmentRecordItemsIndexedOnAssessmentTypeItemId[$existingAssessmentRecordItem->getAssessmentTypeItem()->getId()] = $existingAssessmentRecordItem;
            }
        }

        // Iterate through DTO AssessmentRecordItems and update/create, based on the AssessmentRecordItem's AssessmentTypeItem
        $newAssessmentRecordItems = [];
        foreach ($assessmentRecordDto->assessmentRecordItems as $assessmentRecordItemDto) {
            $assessmentRecordItem = null;

            if ($assessmentRecordItemDto->assessmentTypeItemId && isset($existingAssessmentRecordItemsIndexedOnAssessmentTypeItemId[$assessmentRecordItemDto->assessmentTypeItemId])) {
                $assessmentRecordItem = $existingAssessmentRecordItemsIndexedOnAssessmentTypeItemId[$assessmentRecordItemDto->assessmentTypeItemId];
            } else {
                $assessmentRecordItem = new AssessmentRecordItem();
                $assessmentRecord->addAssessmentRecordItem($assessmentRecordItem);
            }

            // Update basic fields
            if ($assessmentRecordItemDto->assessmentTypeItemId) {
                $assesmentTypeItemRepo = $this->entityManager->getRepository(AssessmentTypeItem::class);
                $assessmentTypeItem = $assesmentTypeItemRepo->find($assessmentRecordItemDto->assessmentTypeItemId);
                $assessmentRecordItem->setAssessmentTypeItem($assessmentTypeItem);
            }

            $assessmentRecordItem->setValue($assessmentRecordItemDto->value);

            $newAssessmentRecordItems[] = $assessmentRecordItem;
        }

        // Remove any AssessmentRecordItems no longer present in DTO
        foreach ($existingAssessmentRecordItems as $existingAssessmentRecordItem) {
            if (!in_array($existingAssessmentRecordItem, $newAssessmentRecordItems, true)) {
                $assessmentRecord->removeAssessmentRecordItem($existingAssessmentRecordItem);
            }
        }

        return $assessmentRecord;
    }

    /**
     * Convert AssessmentRecord entity to a DTO snapshot
     */
    public function entityToDto(AssessmentRecord $assessmentRecord): AssessmentRecordDto
    {
        $assessmentRecordDto = new AssessmentRecordDto();;
        $assessmentRecordDto->id = $assessmentRecord->getId();
        $assessmentRecordDto->assessmentId = $assessmentRecord->getAssessment()?->getId();
        $assessmentRecordDto->userId = $assessmentRecord->getUserId();
        $assessmentRecordDto->createdAt = $assessmentRecord->getCreatedAt()?->format('c');

        foreach ($assessmentRecord->getAssessmentRecordItems() as $assessmentRecordItem) {
            $assessmentRecordDto->assessmentRecordItems[] = $this->assessmentRecordItemEntityToDto($assessmentRecordItem);
        }

        return $assessmentRecordDto;
    }

    /**
     * Convert AssessmentRecordItem entity to a DTO snapshot
     */
    private function assessmentRecordItemEntityToDto(AssessmentRecordItem $assessmentRecordItem): AssessmentRecordItemDto
    {
        $assessmentRecordItemDto = new AssessmentRecordItemDto();
        $assessmentRecordItemDto->id = $assessmentRecordItem->getId();
        $assessmentRecordItemDto->assessmentRecordId = $assessmentRecordItem->getAssessmentRecord()?->getId();
        $assessmentRecordItemDto->assessmentTypeItemId = $assessmentRecordItem->getAssessmentTypeItem()?->getId();
        $assessmentRecordItemDto->value = $assessmentRecordItem->getValue();

        // Normalize booleans to strings for safe form binding
        if (is_bool($assessmentRecordItemDto->value)) {
            //$assessmentRecordItemDto->value = $assessmentRecordItemDto->value ? '1' : '0';
        }

        return $assessmentRecordItemDto;
    }
}
