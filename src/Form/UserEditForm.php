<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
             ->add('roles', ChoiceType::class, [
                 'choices' => [
                     'User' => 'ROLE_USER',
                     'Admin' => 'ROLE_ADMIN',
                     'Super Admin' => 'ROLE_SUPER_ADMIN',
                 ],
                 'expanded' => true,
                 'multiple' => true,
             ])
            ->add('lastName')
            ->add('firstName');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
