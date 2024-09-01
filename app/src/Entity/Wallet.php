<?php

/**
 * Wallet entity.
 */

namespace App\Entity;

use App\Repository\WalletRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Wallet.
 *
 * @psalm-suppress MissingConstructor
 */
#[ORM\Entity(repositoryClass: WalletRepository::class)]
#[ORM\Table(name: 'wallets')]
class Wallet
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
     * Title.
     *
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Type('string')]
    #[Assert\NotBlank(message: 'wallet.title.not_blank')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'wallet.title.min_length',
        maxMessage: 'wallet.title.max_length'
    )]
    private ?string $title = null;

    /**
     * Description.
     *
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Type('string')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'wallet.description.max_length'
    )]
    private ?string $description = null;

    /**
     * Balance.
     *
     * @var float|null
     */
    #[ORM\Column(type: 'float')]
    #[Assert\NotNull(message: 'wallet.balance.not_null')]
    #[Assert\Type(type: 'float', message: 'wallet.balance.type')]
    #[Assert\GreaterThanOrEqual(
        value: 0,
        message: 'wallet.balance.greater_than_or_equal'
    )]
    private ?float $balance = null;

    /**
     * Created at.
     *
     * @var \DateTimeInterface|null
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\Type(\DateTimeInterface::class, message: 'wallet.created_at.type')]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeInterface $createdAt = null;

    /**
     * Updated at.
     *
     * @var \DateTimeInterface|null
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\Type(\DateTimeInterface::class, message: 'wallet.updated_at.type')]
    #[Gedmo\Timestampable(on: 'update')]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * Slug.
     *
     * @var string|null
     */
    #[Gedmo\Slug(fields: ['title'])]
    #[ORM\Column(length: 255)]
    #[Assert\Type('string', message: 'wallet.slug.type')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'wallet.slug.min_length',
        maxMessage: 'wallet.slug.max_length'
    )]
    private ?string $slug = null;

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
     * Getter for title.
     *
     * @return string|null Title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Setter for title.
     *
     * @param string $title Title
     *
     * @return static
     */
    public function setTitle(string $title): static
    {
        $this->title = $title;

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
     * @param string|null $description Description
     *
     * @return static
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Getter for balance.
     *
     * @return float|null Balance
     */
    public function getBalance(): ?float
    {
        return $this->balance;
    }

    /**
     * Setter for balance.
     *
     * @param float $balance Balance
     *
     * @return static
     */
    public function setBalance(float $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Getter for created at.
     *
     * @return \DateTimeInterface|null Created at
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Setter for created at.
     *
     * @param \DateTimeInterface $createdAt Created at
     *
     * @return static
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Getter for updated at.
     *
     * @return \DateTimeInterface|null Updated at
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Setter for updated at.
     *
     * @param \DateTimeInterface $updatedAt Updated at
     *
     * @return static
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Getter for slug.
     *
     * @return string|null Slug
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Setter for slug.
     *
     * @param string $slug Slug
     *
     * @return static
     */
    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
