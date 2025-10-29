<?php

namespace mod_assessment\Entity\Trait;

trait TypeTrait
{
    private ?string $type = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }
}
