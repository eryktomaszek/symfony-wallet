<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Form\Type;

use App\Entity\Category;
use App\Entity\Tag;
use App\Entity\Transaction;
use App\Entity\Wallet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TransactionType.
 */
class TransactionType extends AbstractType
{
    /**
     * Constructor.
     *
     * @param Security            $security   Security service
     * @param TranslatorInterface $translator Translator service
     */
    public function __construct(private readonly Security $security, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Build form.
     *
     * @param FormBuilderInterface $builder Form builder
     * @param array                $options Form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', NumberType::class, [
                'label' => 'label.amount',
                'required' => true,
            ])
            ->add('description', TextType::class, [
                'label' => 'label.description',
                'required' => true,
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'label.type',
                'choices' => [
                    $this->translator->trans('label.income') => 'income',
                    $this->translator->trans('label.expense') => 'expense',
                ],
                'required' => true,
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'label.date',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('wallet', EntityType::class, [
                'class' => Wallet::class,
                'choice_label' => 'title',
                'label' => 'label.wallet',
                'required' => true,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'label.category',
                'required' => true,
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'title',
                'label' => 'label.tags',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            /** @var Transaction $transaction */
            $transaction = $event->getData();
            $form = $event->getForm();

            $user = $this->security->getUser();

            if ($user instanceof \Symfony\Component\Security\Core\User\UserInterface) {
                $form->getData()->setAuthor($user);
            }
        });
    }

    /**
     * Configure options.
     *
     * @param OptionsResolver $resolver Resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Transaction::class]);
    }
}
