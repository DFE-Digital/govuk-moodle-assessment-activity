<?php
namespace mod_assessment\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AssessmentTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Assessment type name',
                'required' => false,
                'attr' => [
                    'style' => 'width: 600px;'
                ],
            ])
            ->add('guidance_before', TextareaType::class, [
                'label' => 'Guidance before',
                'required' => false,
                'attr' => [
                    'style' => 'width: 600px;'
                ],
            ])
            ->add('guidance_after', TextareaType::class, [
                'label' => 'Guidance after',
                'required' => false,
                'attr' => [
                    'style' => 'width: 600px;'
                ],
            ]);
    }
}
