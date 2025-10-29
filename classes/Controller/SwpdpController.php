<?php

namespace mod_assessment\Controller;

use mod_assessment\Entity\Assessment;

class SwpdpController
{
    private ?Assessment $assessment = null;

    public function getAssessment(): ?Assessment
    {
        return $this->assessment;
    }

    public function setAssessment(?Assessment $assessment): static
    {
        $this->assessment = $assessment;

        return $this;
    }
}
