<?php

namespace App\Form;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom : '])
            ->add('imageFile', VichFileType::class, [
                'label' => 'Image : (dimensions pour le caroussel : 1000 x 400) ',
                'required' => false,
            ])
            ->add('position', ChoiceType::class, [
                'label' => 'Position : ',
                'attr' => [
                    'class' => 'form-select',
                ],
                'required' => false,
                'choices'  => $options['positions'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'positions' => null,
        ]);
    }
}
