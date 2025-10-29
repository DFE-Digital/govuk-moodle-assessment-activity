<?php

namespace mod_assessment\Entity\Trait;

trait DescriptionTrait
{
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
