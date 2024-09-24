<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class RegistrationFormType.
 *
 * Handles the form building for user registration.
 */
class RegistrationFormType extends AbstractType
{
    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Build the registration form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options Additional options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'validators.email.not_blank']),
                    new Email(['message' => 'validators.email.invalid']),
                ],
                'label' => 'label.email',
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'attr' => ['class' => 'password-field'],
                'label' => false,
                'first_options' => [
                    'label' => 'label.password',
                    'constraints' => [
                        new NotBlank(['message' => 'validators.password.not_blank']),
                        new Length(['min' => 6, 'minMessage' => 'validators.password.length']),
                    ],
                ],
                'second_options' => [
                    'label' => 'label.password_repeat',
                ],
                'invalid_message' => 'validators.password.mismatch',
                'mapped' => true,
            ]);
    }

    /**
     * Configure the options for the registration form.
     *
     * @param OptionsResolver $resolver The options resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
