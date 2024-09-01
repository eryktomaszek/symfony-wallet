<?php

/**
 * Transaction entity.
 */

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * Getter for Id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for amount.
     *
     * @return float|null Amount
     */
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /**
     * Setter for amount.
     *
     * @param float $amount Amount
     *
     * @return static
     */
    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Getter for description.
     *
     * @return string|null Description
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Setter for description.
     *
     * @param string $description Description
     *
     * @return static
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Getter for type.
     *
     * @return string|null Type
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Setter for type.
     *
     * @param string $type Type
     *
     * @return static
     */
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Getter for date.
     *
     * @return \DateTimeInterface|null Date
     */
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    /**
     * Setter for date.
     *
     * @param \DateTimeInterface $date Date
     *
     * @return static
     */
    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Getter for wallet.
     *
     * @return Wallet|null Wallet
     */
    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    /**
     * Setter for wallet.
     *
     * @param Wallet|null $wallet Wallet
     *
     * @return static
     */
    public function setWallet(?Wallet $wallet): static
    {
        $this->wallet = $wallet;

        return $this;
    }

    /**
     * Getter for category.
     *
     * @return Category|null Category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Setter for category.
     *
     * @param Category|null $category Category
     *
     * @return static
     */
    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Tags associated with the transaction.
     *
     * @var Collection<int, Tag>|Tag[]
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'transactions', fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(name: 'transaction_tags')]
    private Collection $tags;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * Getter for tags.
     *
     * @return Collection<int, Tag>|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * Add a tag.
     *
     * @param Tag $tag Tag entity
     *
     * @return static
     */
    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    /**
     * Remove a tag.
     *
     * @param Tag $tag Tag entity
     *
     * @return static
     */
    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}
