<?php

namespace mod_assessment\Dto;

class AssessmentRecordDto
{
    public ?int $id = null;
    public ?int $assessmentId = null;
    public ?int $userId = null;
    public ?string $createdAt = null;

    /** @var AssessmentRecordItemDto[] */
    public array $assessmentRecordItems = [];

    /**
     * Create DTO from a simple array (for session storage)
     */
    public static function fromArray(array $data): self
    {
        $assessmentRecordDto = new self();
        $assessmentRecordDto->id = $data['id'] ?? null;
        $assessmentRecordDto->assessmentId = $data['assessmentId'] ?? null;
        $assessmentRecordDto->userId = $data['userId'] ?? null;
        $assessmentRecordDto->createdAt = $data['createdAt'] ?? null;

        $assessmentRecordDto->assessmentRecordItems = [];
        foreach ($data['assessmentRecordItems'] ?? [] as $assessmentRecordItemData) {
            $assessmentRecordDto->assessmentRecordItems[] = AssessmentRecordItemDto::fromArray($assessmentRecordItemData);
        }

        return $assessmentRecordDto;
    }

    /**
     * Convert DTO into a simple array (for session storage)
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'assessmentId' => $this->assessmentId,
            'userId' => $this->userId,
            'createdAt' => $this->createdAt,
            'assessmentRecordItems' => array_map(
                fn(AssessmentRecordItemDto $assessmentRecordItemDto) => $assessmentRecordItemDto->toArray(),
                $this->assessmentRecordItems
            ),
        ];
    }
}
