<?php
declare(strict_types=1);

namespace App\Model;

class InstallmentResponse
{
    public function __construct(
        private readonly int $number,
        private readonly string $amount,
        private readonly string $interest,
        private readonly string $capital
    )
    {
    }

    public static function createFromInstallment(Installment $installment):static{
        return new InstallmentResponse(
            $installment->getNumber(),
            number_format($installment->getAmount()/100, 2, ',', ' '),
            number_format($installment->getInterest()/100, 2, ',', ' '),
            number_format($installment->getCapital()/100, 2, ',', ' ')
        );
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getInterest(): string
    {
        return $this->interest;
    }

    public function getCapital(): string
    {
        return $this->capital;
    }
}
