<?php

namespace mod_assessment\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class GovukDateInputTransformer implements DataTransformerInterface
{
    /**
     * Transforms a model (string like "2024-05-10") to an array for the GOV.UK date input.
     *
     * @param string|null $value
     * @return array|null
     */
    public function transform(mixed $value) :mixed
    {
        if (empty($value)) {
            return [
                'day' => '',
                'month' => '',
                'year' => '',
            ];
        }

        try {
            $date = $value;
        } catch (\Exception $e) {
            // Gracefully degrade if value is not a valid date string
            return [
                'day' => '',
                'month' => '',
                'year' => '',
            ];
        }

        return [
            'day' => $date->format('d'),
            'month' => $date->format('m'),
            'year' => $date->format('Y'),
        ];
    }

    /**
     * Transforms the submitted GOV.UK date input array to a string (YYYY-MM-DD).
     *
     * @param array|null $value
     * @return string|null
     */
    public function reverseTransform(mixed $value): mixed
    {
        if (!is_array($value)) {
            return null;
        }

        $day = $value['day'] ?? null;
        $month = $value['month'] ?? null;
        $year = $value['year'] ?? null;

        if (empty($day) && empty($month) && empty($year)) {
            return null; // allow empty
        }

        // Validate numeric and range
        if (!ctype_digit((string)$day) || !ctype_digit((string)$month) || !ctype_digit((string)$year)) {
            return null;
        }

        if (!checkdate((int)$month, (int)$day, (int)$year)) {
            return null;
        }

        $a = sprintf('%04d-%02d-%02d', $year, $month, $day);
        return $a;
    }
}

