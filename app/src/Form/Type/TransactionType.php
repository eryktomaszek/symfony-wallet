<?php

namespace App\Form\Type;

use App\Entity\Transaction;
use App\Entity\Category;
use App\Entity\Wallet;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TransactionType.
 */
class TransactionType extends AbstractType
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
                'amount',
                NumberType::class,
                [
                    'label' => 'label.amount',
                    'required' => true,
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
                'type',
                ChoiceType::class,
                [
                    'label' => 'label.type',
                    'choices' => [
                        'Income' => 'income',
                        'Expense' => 'expense',
                    ],
                    'required' => true,
                ]
            )
            ->add(
                'date',
                DateTimeType::class,
                [
                    'label' => 'label.date',
                    'widget' => 'single_text',
                    'required' => true,
                ]
            )
            ->add(
                'wallet',
                EntityType::class,
                [
                    'class' => Wallet::class,
                    'choice_label' => 'title',
                    'label' => 'label.wallet',
                    'required' => true,
                ]
            )
            ->add(
                'category',
                EntityType::class,
                [
                    'class' => Category::class,
                    'choice_label' => 'name',
                    'label' => 'label.category',
                    'required' => true,
                ]
            )
            ->add(
                'tags',
                EntityType::class,
                [
                    'class' => Tag::class,
                    'choice_label' => 'title',
                    'label' => 'label.tags',
                    'multiple' => true,
                    'expanded' => true,
                    'required' => false,
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
        $resolver->setDefaults(['data_class' => Transaction::class]);
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return 'transaction';
    }
}
