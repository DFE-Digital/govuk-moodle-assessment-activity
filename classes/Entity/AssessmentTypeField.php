<?php 

namespace mod_assessment\Entity;

class AssessmentTypeField extends AssessmentTypeItem
{
    private ?string $dataType = null;
    private bool $isRequired = false;

    public function getDataType(): ?string
    {
        return $this->dataType;
    }

    public function setDataType(?string $dataType): static 
    {
        $this->dataType = $dataType;

        return $this;
    }

    public function getIsRequired(): bool
    {
        return $this->isRequired;
    }

    public function setIsRequired($isRequired): static 
    {
        $this->isRequired = $isRequired;

        return $this;
    }
}
