<?php

namespace mod_assessment\Form;

use mod_assessment\Entity\AssessmentRecord;
use mod_assessment\Entity\AssessmentTypeItem;
use mod_assessment\Form\DataTransformer\GovukDateInputTransformer;
use mod_assessment\Util\EntityManagerProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AssessmentRecordItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Moodle
        global $USER;
        $currentUserId = (int) $USER->id;

        $entityManager = EntityManagerProvider::getEntityManager();

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($currentUserId, $entityManager) {
            $assessmentRecordItemDto = $event->getData();
            $form = $event->getForm();

            if (!$assessmentRecordItemDto) {
                return;
            }

            // DUMMY OUT A PERMISSION CHECK
            $assessmentRecord = $assessmentRecordItemDto->assessmentRecordId ? $entityManager->find(AssessmentRecord::class, $assessmentRecordItemDto->assessmentRecordId) : null;
            $assessmentTypeItem = $entityManager->find(AssessmentTypeItem::class, $assessmentRecordItemDto->assessmentTypeItemId);
            $assessmentTypeSection = $assessmentTypeItem->getAssessmentTypeSection();

            $isCurrentUserAssessmentRecordOwner = is_null($assessmentRecord) || $assessmentRecord->getUserId() === $currentUserId;
            $isObserverField = preg_match("/observer's/i", $assessmentTypeSection->getName());

            $isDisabled = false;
            if ($isCurrentUserAssessmentRecordOwner && $isObserverField) {
                $isDisabled = true; // The field will be rendered but its value will not be POSTed back when the form is submitted
            }
            if (!$isCurrentUserAssessmentRecordOwner && !$isObserverField) {
                $isDisabled = true; // The field will be rendered but its value will not be POSTed back when the form is submitted
            }

            switch ($assessmentTypeItem->getDataType()) {
                case 'checkbox':
                    $fieldType = CheckboxType::class;
                    break;
                case 'date':
                    $fieldType = DateType::class;
                    break;
                case 'textarea':
                    $fieldType = TextareaType::class;
                    break;
                default:
                    $fieldType = TextType::class;
            }

            $useGovukTwig = false;

            $fieldOptions = [
                'disabled' => $isDisabled,
                'label' => $assessmentTypeItem->getDescription(),
            ];

            if (!$useGovukTwig && $fieldType === DateType::class) {
                $fieldType = TextType::class;
            }
            if ($useGovukTwig && $fieldType === DateType::class) {
                $fieldOptions['compound'] = true;
                $fieldOptions['auto_initialize'] = false;
                $dateField = $form->getConfig()->getFormFactory()->createNamedBuilder('value', FormType::class, null, $fieldOptions);

                // Add subfields
                $dateField->add('day', TextType::class);
                $dateField->add('month', TextType::class);
                $dateField->add('year', TextType::class);
                $dateField->addModelTransformer(new GovukDateInputTransformer());

                $form->add($dateField->getForm());
            } else {
                 $form->add('value', $fieldType, $fieldOptions);
            }
        });
    }
}
