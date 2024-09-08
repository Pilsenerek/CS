<?php
declare(strict_types=1);

namespace App\Model;

class Installment
{
    public function __construct(
        private readonly int $number,
        private readonly int $amount,
        private readonly int $interest,
        private readonly int $capital
    )
    {
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getInterest(): int
    {
        return $this->interest;
    }

    public function getCapital(): int
    {
        return $this->capital;
    }


}
