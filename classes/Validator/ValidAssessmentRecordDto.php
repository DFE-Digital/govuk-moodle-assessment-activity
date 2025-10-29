<?php

namespace mod_assessment\Validator;

use Symfony\Component\Validator\Constraint;

class ValidAssessmentRecordDto extends Constraint
{
    public string $message = 'Invalid value for this assessment record.';

    public function validatedBy(): string
    {
        return static::class . 'Validator';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
