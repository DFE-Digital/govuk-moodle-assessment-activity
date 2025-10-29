<?php

namespace mod_assessment\Validator;

use mod_assessment\Dto\AssessmentRecordDto;
use mod_assessment\Entity\AssessmentTypeItem;
use mod_assessment\Util\EntityManagerProvider;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidAssessmentRecordDtoValidator extends ConstraintValidator
{
    /**
     * @param AssessmentRecordDto $value
     */
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$value instanceof AssessmentRecordDto) {
            return; // Safety check
        }
        $assessmentRecordDto = $value;

        $entityManager = EntityManagerProvider::getEntityManager();

        // Validate each AssessmentRecordItemDto in turn
        foreach ($assessmentRecordDto->assessmentRecordItems as $index => $assessmentRecordItemDto) {
            if ($assessmentRecordItemDto->assessmentTypeItemId === null) {
                return; // Nothing to check yet
            }

            $assessmentTypeItem = $entityManager->find(AssessmentTypeItem::class, $assessmentRecordItemDto->assessmentTypeItemId);
            if (!$assessmentTypeItem) {
                $this->context->buildViolation('Assessment type item not found.')
                    ->atPath(sprintf('assessmentRecordItems[%d].value', $index))
                    ->addViolation();
            } else {
                // Required fields cannot be empty
                if ($assessmentTypeItem->getIsRequired() && ($assessmentRecordItemDto->value === null || $assessmentRecordItemDto->value === '')) {
                    $this->context->buildViolation(sprintf("'%s' cannot be empty.", $assessmentTypeItem->getDescription()))
                        ->atPath(sprintf('assessmentRecordItems[%d].value', $index))
                        ->addViolation();
                }
            }
        }
    }
}
