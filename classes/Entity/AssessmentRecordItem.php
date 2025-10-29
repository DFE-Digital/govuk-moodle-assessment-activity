<?php 

namespace mod_assessment\Entity;

use mod_assessment\Entity\Trait\IdTrait;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class AssessmentRecordItem
{
    use IdTrait;

    private ?AssessmentRecord $assessmentRecord = null;
    private ?AssessmentTypeItem $assessmentTypeItem = null;
    private mixed $value = null;

    public function getAssessmentRecord(): ?AssessmentRecord
    {
        return $this->assessmentRecord;
    }

    public function setAssessmentRecord(?AssessmentRecord $assessmentRecord): static
    {
        $this->assessmentRecord = $assessmentRecord;

        return $this;
    } 

    public function getAssessmentTypeItem(): ?AssessmentTypeItem
    {
        return $this->assessmentTypeItem;
    }

    public function setAssessmentTypeItem(?AssessmentTypeItem $assessmentTypeItem): static
    {
        $this->assessmentTypeItem = $assessmentTypeItem;

        return $this;
    }

    public function getValue():  mixed
    {
        $useGovukTwig = false;
        if ($useGovukTwig) {
            $assessmentTypeItem = $this->getAssessmentTypeItem();
            if ($assessmentTypeItem instanceof AssessmentTypeField) {
                if ($assessmentTypeItem->getDataType() === 'date') {
                    $value = $this->value;
                    $dateTime = \DateTime::createFromFormat('Y-m-d', $value ?? '');
                    if ($dateTime === false) {
                        $dateTime = null;
                    }

                    return $dateTime;
                }
            }
        }

        return $this->value;
    }

    public function setValue(mixed $value): static
    {
        $useGovukTwig = false;
        if ($useGovukTwig) {
            if ($value instanceof \DateTimeInterface) {
                $this->value = $value->format('Y-m-d');
                return $this;
            }
        }

        $this->value = $value;

        return $this;
    }
}
