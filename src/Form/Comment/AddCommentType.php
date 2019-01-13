<?php
/**
 * Created by PhpStorm.
 * User: q
 * Date: 13.11.18
 * Time: 4:57
 */

namespace App\Form\Comment;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextareaType::class, [
                'attr' => ['class' => 'form-control',
                    'placeholder' => 'Article text'],
                'label_attr' => ['class' => 'sr-only'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Comment::class,
            'attr' => ['novalidate' => 'novalidate'],
        ));
    }
}
