<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ImageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
            $builder
                ->add('File', FileType::class,[
                    'mapped' => false,
                    'constraints' => [
                        new File([
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'image/jpg',
                            ],
                            'mimeTypesMessage' => 'Please upload a valid image file',
                        ])
                    ],
                ])
            ->add('NumLikes', null, ['attr' => ['class'=>'form-control']])
            ->add('NumViews', null, ['attr' => ['class'=>'form-control']])
            ->add('NumDownloads', null, ['attr' => ['class'=>'form-control']])
            ->add('Category', EntityType::class, array(
                'class' => Category::class,
                'choice_label' => 'name'))
            ->add('Send', SubmitType::class, ['attr' => ['class'=>'pull-right btn btn-lg sr-button']]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
