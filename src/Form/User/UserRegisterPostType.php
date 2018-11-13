<?php
/**
 * Created by PhpStorm.
 * User: q
 * Date: 13.11.18
 * Time: 3:37
 */

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegisterPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'attr' => ['class' => 'form-control',
                    'placeholder' => 'Email'],
                'label_attr' => ['class' => 'sr-only'],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'first_options' => ['label_attr' => ['class' => 'sr-only'],
                    'attr' => ['placeholder' => 'Password',
                        'class' => 'form-control']],
                'second_options' => ['label_attr' => ['class' => 'sr-only'],
                    'attr' => ['placeholder' => 'Repeat Password',
                        'class' => 'form-control']],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'attr' => ['novalidate' => 'novalidate'],
        ));
    }
}