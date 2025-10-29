<?php 

namespace mod_assessment\Entity;

use mod_assessment\Entity\Trait\DescriptionTrait;
use mod_assessment\Entity\Trait\DisplayOrderTrait;
use mod_assessment\Entity\Trait\GuidanceTrait;
use mod_assessment\Entity\Trait\IdTrait;
use mod_assessment\Entity\Trait\NameTrait;
use mod_assessment\Entity\Trait\TypeTrait;

class AssessmentTypeItem
{
    use IdTrait;
    use TypeTrait;
    use NameTrait;
    use DescriptionTrait;
    use GuidanceTrait;
    use DisplayOrderTrait;

    private ?AssessmentTypeSection $assessmentTypeSection = null;

    public function getAssessmentTypeSection(): ?AssessmentTypeSection
    {
        return $this->assessmentTypeSection;
    }

    public function setAssessmentTypeSection(?AssessmentTypeSection $assessmentTypeSection): static 
    {
        $this->assessmentTypeSection = $assessmentTypeSection;

        return $this;
    }
}
