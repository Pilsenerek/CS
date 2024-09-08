<?php
declare(strict_types=1);

namespace App\Model;

use App\Entity\Loan;

class LoanResponse
{
    public function __construct(
        private readonly int $id,
        private readonly string $amount,
        private readonly int $installments,
        private readonly string $interestAmount,
        private readonly string $interestRate,
        private readonly string $createdAt
    )
    {
    }

    public static function createFromLoan(Loan $loan): static
    {
        return new LoanResponse(
            $loan->getId(),
            number_format($loan->getAmount() / 100, 2, ',', ' '),
            $loan->getInstallments(),
            number_format($loan->getInterestAmount() / 100, 2, ',', ' '),
            number_format($loan->getInterestRate() / 100, 2, ',', ' '),
            $loan->getCreatedAt()->format('Y-m-d H:i:s'),
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getInstallments(): int
    {
        return $this->installments;
    }

    public function getInterestAmount(): string
    {
        return $this->interestAmount;
    }

    public function getInterestRate(): string
    {
        return $this->interestRate;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
