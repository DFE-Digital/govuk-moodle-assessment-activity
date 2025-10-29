<?php 

namespace mod_assessment\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use mod_assessment\Entity\Trait\DisplayOrderTrait;
use mod_assessment\Entity\Trait\GuidanceTrait;
use mod_assessment\Entity\Trait\IdTrait;
use mod_assessment\Entity\Trait\NameTrait;

class AssessmentTypeSection
{
    use GuidanceTrait;
    use IdTrait;
    use NameTrait;
    use DisplayOrderTrait;

    private ?AssessmentType $assessmentType = null;
    private ?AssessmentTypeSection $parentAssessmentTypeSection = null;

    /** @var Collection<int,AssessmentTypeItem> */
    private Collection $assessmentTypeItems;
    /** @var Collection<int,AssessmentTypeSection> */
    private Collection $childAssessmentTypeSections;

    public function __construct()
    {
        $this->assessmentTypeItems = new ArrayCollection();
        $this->childAssessmentTypeSections = new ArrayCollection();
    }

    public function getAssessmentType(): ?AssessmentType
    {
        return $this->assessmentType;
    }

    public function setAssessmentType(?AssessmentType $assessmentType): static 
    {
        $this->assessmentType = $assessmentType;

        return $this;
    }

    public function getParentAssessmentTypeSection(): ?AssessmentTypeSection
    {
        return $this->parentAssessmentTypeSection;
    }

    public function setParentAssessmentTypeSection(?AssessmentTypeSection $parentAssessmentTypeSection): static 
    {
        $this->parentAssessmentTypeSection = $parentAssessmentTypeSection;

        return $this;
    }

    /** @return Collection<int, AssessmentTypeItem> */
    public function getAssessmentTypeItems(): Collection
    {
        return $this->assessmentTypeItems;
    }

    public function addAssessmentTypeItem(AssessmentTypeItem $assessmentTypeItem): static
    {
        if (!$this->assessmentTypeItems->contains($assessmentTypeItem)) {
            $this->assessmentTypeItems[] = $assessmentTypeItem;
            $assessmentTypeItem->setAssessmentTypeSection($this);
        }

        return $this;
    }

    public function removeAssessmentTypeItem(AssessmentTypeItem $assessmentTypeItem): static
    {
        if ($this->assessmentTypeItems->removeElement($assessmentTypeItem)) {
            if ($assessmentTypeItem->getAssessmentTypeSection() === $this) {
                $assessmentTypeItem->setAssessmentTypeSection(null);
            }
        }

        return $this;
    }

}
