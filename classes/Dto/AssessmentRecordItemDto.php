<?php

namespace mod_assessment\Dto;

class AssessmentRecordItemDto
{
    public ?int $id = null;
    public ?int $assessmentRecordId = null;
    public ?int $assessmentTypeItemId = null;
    public mixed $value = null;

    /**
     * Create DTO from a simple array (for session storage)
     */
    public static function fromArray(array $data): self
    {
        $assessmentRecordItemDto = new self();
        $assessmentRecordItemDto->id = $data['id'] ?? null;
        $assessmentRecordItemDto->assessmentRecordId = $data['assessmentRecordId'] ?? null;
        $assessmentRecordItemDto->assessmentTypeItemId = $data['assessmentTypeItemId'] ?? null;
        $assessmentRecordItemDto->value = $data['value'] ?? null;

        return $assessmentRecordItemDto;
    }

    /**
     * Convert DTO into a simple array (for session storage)
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'assessmentRecordId' => $this->assessmentRecordId,
            'assessmentTypeItemId' => $this->assessmentTypeItemId,
            'value' => $this->value,
        ];
    }
}
