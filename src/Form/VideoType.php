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
            ->add('name', TextType::class, ['label' => 'Titre de la vidéo : '])
            ->add('url', UrlType::class, ['label' => 'Url de la vidéo : '])
            ->add('description', CKEditorType::class, ['label' => 'Description : ', 'config' => ['toolbar' => 'standard']])
            ->add('author', TextType::class, ['label' => 'Auteur de la vidéo : '])
            ->add('link_author', UrlType::class, ['label' => 'Lien de l\'auteur : '])
            ->add('is_best', CheckboxType::class, [
                'label' => 'Souhaitez-vous l\'afficher dans les meilleures vidéos de la page d\'Accueil',
                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
