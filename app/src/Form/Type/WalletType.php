<?php

namespace App\Form\Type;

use App\Entity\Wallet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class WalletType.
 */
class WalletType extends AbstractType
{
    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string, mixed> $options Form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'label.title',
                    'required' => true,
                    'attr' => ['max_length' => 64],
                ]
            )
            ->add(
                'description',
                TextType::class,
                [
                    'label' => 'label.description',
                    'required' => false,
                ]
            )
            ->add(
                'balance',
                NumberType::class,
                [
                    'label' => 'label.balance',
                    'required' => true,
                    'attr' => [
                        'min' => 0,
                    ],
                ]
            );
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Wallet::class]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return 'wallet';
    }
}
