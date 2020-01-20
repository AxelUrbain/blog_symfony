<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['required' => true , 'label' => 'Titre',
                'attr' => array(
                    'placeholder' => 'Mon titre'
                )])
            ->add('content', TextareaType::class, ['required' => false , 'label' => 'Contenu',
                'attr' => array(
                    'placeholder' => 'Mon contenu par défaut'
                )])
            ->add('author', TextType::class, ['required' => false , 'label' => 'Auteur',
                'attr' => array(
                    'placeholder' => 'Emmanuel Macron'
                )])
            ->add('categories' , EntityType::class, ['class' => Category::class,
                'label' => 'Catégories',
                'multiple' => true])
            ->add('nb_views', IntegerType::class, ['required' => true ,
                'label' => 'Nombre de Vue',
                'data' => '1'])
            ->add('published', CheckboxType::class, ['required' => false , 'label' => 'Publié'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
