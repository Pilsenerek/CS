<?php
declare(strict_types=1);

namespace App\Entity;

use App\Enum\LoanStatus;
use App\Repository\LoanRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoanRepository::class)]
class Loan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $interestRate;

    #[ORM\Column(options: ["default" => 0])]
    private int $interestAmount = 0;

    #[ORM\Column]
    private int $amount;

    #[ORM\Column]
    private int $installments;

    #[ORM\Column(
        type: Types::SMALLINT,
        nullable: false,
        enumType: LoanStatus::class,
        options: ["default" => LoanStatus::NEW])
    ]
    private LoanStatus $status = LoanStatus::NEW;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ["default" => "CURRENT_TIMESTAMP"])]
    private \DateTimeInterface $createdAt;

    public function __construct(int $interestRate, int $amount, int $installments)
    {
        $this->interestRate = $interestRate;
        $this->amount = $amount;
        $this->installments = $installments;
        $this->createdAt = new \DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInterestRate(): int
    {
        return $this->interestRate;
    }

//    public function setInterestRate(int $interestRate): static
//    {
//        $this->interestRate = $interestRate;
//
//        return $this;
//    }

    public function getInterestAmount(): int
    {
        return $this->interestAmount;
    }

    public function setInterestAmount(int $interestAmount): static
    {
        $this->interestAmount = $interestAmount;

        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

//    public function setAmount(int $amount): static
//    {
//        $this->amount = $amount;
//
//        return $this;
//    }

    public function getInstallments(): int
    {
        return $this->installments;
    }

//    public function setInstallments(int $installments): static
//    {
//        $this->installments = $installments;
//
//        return $this;
//    }

    public function getStatus(): LoanStatus
    {
        return $this->status;
    }

    public function setStatus(LoanStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

//    public function setCreatedAt(\DateTimeInterface $createdAt): static
//    {
//        $this->createdAt = $createdAt;
//
//        return $this;
//    }
}
