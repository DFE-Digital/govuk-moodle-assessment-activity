<?php

namespace mod_assessment\Entity\Trait;

trait IdTrait
{
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
