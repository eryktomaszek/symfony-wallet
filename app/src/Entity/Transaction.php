<?php

/**
 * Transaction entity.
 */

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Transaction.
 *
 * @psalm-suppress MissingConstructor
 */
#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    /**
     * Primary key.
     *
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Amount.
     *
     * @var float|null
     */
    #[ORM\Column(type: 'float')]
    #[Assert\NotNull(message: 'transaction.amount.not_null')]
    #[Assert\Positive(message: 'transaction.amount.positive')]
    private ?float $amount = null;

    /**
     * Description.
     *
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'transaction.description.not_blank')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'transaction.description.min_length',
        maxMessage: 'transaction.description.max_length'
    )]
    private ?string $description = null;

    /**
     * Type (income or expense).
     *
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'transaction.type.not_blank')]
    #[Assert\Choice(choices: ['income', 'expense'], message: 'transaction.type.invalid_choice')]
    private ?string $type = null;

    /**
     * Date.
     *
     * @var \DateTimeInterface|null
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: 'transaction.date.not_null')]
    #[Assert\Type(
        type: \DateTimeInterface::class,
        message: 'transaction.date.invalid_type'
    )]
    private ?\DateTimeInterface $date = null;

    /**
     * Wallet associated with the transaction.
     *
     * @var Wallet|null
     */
    #[ORM\ManyToOne(targetEntity: Wallet::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'transaction.wallet.not_null')]
    private ?Wallet $wallet = null;

    /**
     * Category associated with the transaction.
     *
     * @var Category|null
     */
    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'transaction.category.not_null')]
    private ?Category $category = null;

    // Getter and Setter methods remain the same
}
