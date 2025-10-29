<?php

namespace mod_assessment\Form;

use mod_assessment\Dto\AssessmentRecordItemDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class AssessmentRecordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('assessmentRecordItems', CollectionType::class, [
                'entry_type' => AssessmentRecordItemType::class,
                'entry_options' => [
                    'data_class' => AssessmentRecordItemDto::class,
                ],
            ]);
    }
}
