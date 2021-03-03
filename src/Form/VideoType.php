<?php

namespace App\Form;

use App\Entity\Video;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre de la vidéo : '])
            ->add('url', UrlType::class, ['label' => 'Url de la vidéo : '])
            ->add('description', CKEditorType::class, [
                'label' => 'Description : ',
                'config' => ['toolbar' => 'standard']
            ])
            ->add('author', TextType::class, ['label' => 'Auteur de la vidéo : '])
            ->add('author_link', UrlType::class, [
                'label' => 'Url du site de l\'auteur : ',
                'required' => false,
            ])
            ->add('is_best', CheckboxType::class, [
                'label' => 'Cochez la case si vous souhaitez la voir apparaitre dans les meilleures vidéos sur la page Accueil',
                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Sélectionnez la catégorie : ',
                'attr' => [
                    'class' => 'form-select',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
