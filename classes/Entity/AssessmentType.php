<?php 

namespace mod_assessment\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use mod_assessment\Entity\Trait\GuidanceTrait;
use mod_assessment\Entity\Trait\IdTrait;
use mod_assessment\Entity\Trait\NameTrait;

class AssessmentType
{
    use IdTrait;
    use NameTrait;
    use GuidanceTrait;

    /** @var Collection<int,AssessmentTypeSection> */
    private Collection $assessmentTypeSections;

    public function __construct()
    {
        $this->assessmentTypeSections = new ArrayCollection();
    }

    /** @return Collection<int, AssessmentTypeSection> */
    public function getAssessmentTypeSections(): Collection
    {
        return $this->assessmentTypeSections;
    }

    public function addAssessmentTypeSection(AssessmentTypeSection $assessmentTypeSection): static
    {
        if (!$this->assessmentTypeSections->contains($assessmentTypeSection)) {
            $this->assessmentTypeSections[] = $assessmentTypeSection;
            $assessmentTypeSection->setAssessmentType($this);
        }

        return $this;
    }

    public function removeAssessmentTypeSection(AssessmentTypeSection $assessmentTypeSection): static
    {
        if ($this->assessmentTypeSections->removeElement($assessmentTypeSection)) {
            if ($assessmentTypeSection->getAssessmentType() === $this) {
                $assessmentTypeSection->setAssessmentType(null);
            }
        }

        return $this;
    }
}
