<?php

namespace mod_assessment\Entity\Trait;

trait DisplayOrderTrait
{
    private ?int $displayOrder = null;

    public function getDisplayOrder(): ?int
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder(?int $displayOrder): static
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }
}
