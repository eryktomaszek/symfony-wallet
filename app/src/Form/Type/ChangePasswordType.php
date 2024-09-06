<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ChangePasswordType.
 */
class ChangePasswordType extends AbstractType
{
    /**
     * Build form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string, mixed> $options Form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'label.current_password',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'password.current.not_blank']),
                ],
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'label.new_password',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'password.new.not_blank']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'password.new.min_length',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'label' => 'label.confirm_password',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'password.confirm.not_blank']),
                ],
            ]);
    }

    /**
     * Configure options.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }

    /**
     * Get block prefix.
     *
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'change_password';
    }
}
