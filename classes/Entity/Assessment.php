<?php 

namespace mod_assessment\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use mod_assessment\Entity\Trait\AuditTrait;
use mod_assessment\Entity\Trait\IdTrait;
use mod_assessment\Entity\Trait\NameTrait;

class Assessment
{
    use IdTrait;
    use NameTrait;
    use AuditTrait;

    private ?int $course = null;
    private ?AssessmentType $assessmentType = null;
    /** @var Collection<int,AssessmentRecord> */
    private Collection $assessmentRecords;

    // Moodle
    private ?int $timecreated = null;
    private ?int $timemodified = null;
    private ?string $intro = null;
    private ?int $introformat = null;

    public function __construct()
    {
        $this->assessmentRecords = new ArrayCollection();
    }

    public function getCourse(): ?int
    {
        return $this->course;
    }

    public function setCourse(?int $course): static
    {
        $this->course = $course;

        return $this;
    }

    public function getAssessmentType(): ?AssessmentType
    {
        return $this->assessmentType;
    }

    public function setAssessmentType(?int $assessmentType): static
    {
        $this->assessmentType = $assessmentType;

        return $this;
    }

    // Moodle
    public function getTimecreated(): ?int
    {
        return $this->timecreated;
    }

    public function setTimecreated(?int $timecreated): static
    {
        $this->timecreated = $timecreated;

        return $this;
    }

    public function getTimemodified(): ?int
    {
        return $this->timemodified;
    }

    public function setTimemodified(?int $timemodified): static
    {
        $this->timemodified = $timemodified;

        return $this;
    }

    public function getIntro(): ?string
    {
        return $this->intro;
    }

    public function setIntro(?string $intro): static
    {
        $this->intro = $intro;

        return $this;
    }
    
    public function getIntroformat(): ?int
    {
        return $this->introformat;
    }

    public function setIntroformat(?int $introformat): static
    {
        $this->introformat = $introformat;

        return $this;
    }
}
