<?php

namespace mod_assessment\Entity\Trait;

trait NameTrait
{
    private ?string $name = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name = null): static
    {
        $this->name = $name;

        return $this;
    }
}
