<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('brief')
            ->add('pageAmount')
            ->add('people', Select2EntityType::class, [
                'multiple' => true,
                'remote_route' => 'app_person_select2',
                'class' => Person::class,
                'primary_key' => 'id',
                'text_property' => 'name',
                'language' => 'en',
                'scroll' => true,
                'width' => 500
            ]);

        if ($options['edit']) {
            $builder
                ->add('saveAsOriginal', SubmitType::class, [
                    'label' => 'Save as original'
                ])
                ->add('saveAsDraft', SubmitType::class, [
                    'label' => 'Save as draft'
                ]);

            /** @var Book $book */
            $book = $builder->getData();

            if ($book->isDraft()) {
                $originalBook = $book->getOriginalBook();

                $lastSavedPart = null !== $originalBook->getLastSaveDate()
                    ? "(last saved {$originalBook->getLastSaveDate()->format(DATE_RFC7231)})"
                    : '';

                $builder->add('resetToOriginal', SubmitType::class, [
                    'label' => "Reset draft to original $lastSavedPart"
                ]);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
            'edit' => true,
        ]);
    }
}
