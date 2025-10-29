<?php

namespace mod_assessment\Entity\Trait;

trait GuidanceTrait
{
    private ?string $guidanceBefore = null;
    private ?string $guidanceAfter = null;

    public function getGuidanceBefore(): ?string
    {
        return $this->guidanceBefore;
    }

    public function setGuidanceBefore(?string $guidanceBefore): static
    {
        $this->guidanceBefore = $guidanceBefore;

        return $this;
    }

    public function getGuidanceAfter(): ?string
    {
        return $this->guidanceAfter;
    }

    public function setGuidanceAfter(?string $guidanceAfter): static
    {
        $this->guidanceAfter = $guidanceAfter;

        return $this;
    }
}
