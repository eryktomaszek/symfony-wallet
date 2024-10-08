<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Transaction.
 *
 * Entity representing a financial transaction within a wallet.
 */
#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'float')]
    #[Assert\NotNull(message: 'transaction.amount.not_null')]
    #[Assert\Positive(message: 'transaction.amount.positive')]
    private ?float $amount = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'transaction.description.not_blank')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'transaction.description.min_length',
        maxMessage: 'transaction.description.max_length'
    )]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'transaction.type.not_blank')]
    #[Assert\Choice(choices: ['income', 'expense'], message: 'transaction.type.invalid_choice')]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: 'transaction.date.not_null')]
    #[Assert\Type(
        type: \DateTimeInterface::class,
        message: 'transaction.date.invalid_type'
    )]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(targetEntity: Wallet::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'transaction.wallet.not_null')]
    private ?Wallet $wallet = null;

    #[ORM\ManyToOne(targetEntity: Category::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'transaction.category.not_null')]
    private ?Category $category = null;

    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type(User::class)]
    private ?User $author = null;

    #[ORM\ManyToMany(targetEntity: Tag::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(name: 'transaction_tags')]
    private Collection $tags;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $balanceAfter = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

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
     * @return static The current Transaction entity
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
     * @return static The current Transaction entity
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
     * @return static The current Transaction entity
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
     * @return static The current Transaction entity
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
     * @return static The current Transaction entity
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
     * @return static The current Transaction entity
     */
    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Getter for tags.
     *
     * @return Collection<Tag> Collection of tags
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
     * @return static The current Transaction entity
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
     * @return static The current Transaction entity
     */
    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * Getter for author.
     *
     * @return User|null Author
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Setter for author.
     *
     * @param User|null $author Author
     *
     * @return static The current Transaction entity
     */
    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Getter for balanceAfter.
     *
     * @return float|null Balance after the transaction
     */
    public function getBalanceAfter(): ?float
    {
        return $this->balanceAfter;
    }

    /**
     * Setter for balanceAfter.
     *
     * @param float|null $balanceAfter The balance after the transaction
     */
    public function setBalanceAfter(?float $balanceAfter): void
    {
        $this->balanceAfter = $balanceAfter;
    }
}
