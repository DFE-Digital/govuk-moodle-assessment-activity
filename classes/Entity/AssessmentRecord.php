<?php 

namespace mod_assessment\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use mod_assessment\Entity\Trait\AuditTrait;
use mod_assessment\Entity\Trait\IdTrait;

class AssessmentRecord
{
    use IdTrait;
    use AuditTrait;

    private ?Assessment $assessment = null;
    private ?int $userId = null;

    /** @var Collection<int,AssessmentRecordItem> */
    private Collection $assessmentRecordItems;

    public function __construct()
    {
        $this->assessmentRecordItems = new ArrayCollection();
    }

    public function getAssessment(): ?Assessment
    {
        return $this->assessment;
    }

    public function setAssessment(?Assessment $assessment): static
    {
        $this->assessment = $assessment;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): static 
    {
        $this->userId = $userId;

        return $this;
    }

    /** @return Collection<int, AssessmentRecordItem> */
    public function getAssessmentRecordItems(): Collection
    {
        return $this->assessmentRecordItems;
    }

    public function addAssessmentRecordItem(AssessmentRecordItem $assessmentRecordItem): static
    {
        if (!$this->assessmentRecordItems->contains($assessmentRecordItem)) {
            $this->assessmentRecordItems[] = $assessmentRecordItem;
            $assessmentRecordItem->setAssessmentRecord($this);
        }

        return $this;
    }

    public function removeAssessmentRecordItem(AssessmentRecordItem $assessmentRecordItem): static
    {
        if ($this->assessmentRecordItems->removeElement($assessmentRecordItem)) {
            if ($assessmentRecordItem->getAssessmentRecord() === $this) {
                $assessmentRecordItem->setAssessmentRecord(null);
            }
        }

        return $this;
    }

    public function getAssessmentRecordItemValue(string $assessmentTypeItemName): ?string
    {
        foreach ($this->getAssessmentRecordItems() as $assessmentRecordItem) {
            if ($assessmentRecordItem->getAssessmentTypeItem()->getName() === $assessmentTypeItemName) {
                return $assessmentRecordItem->getValue();
            }
        }

        return null;
    }
}
