<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Loan;
use App\Model\Installment;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class LoanCalcService
{
    private const INSTALLMENTS_PER_YEAR = 12;
    private const PRECISION = 99;

    public function __construct(
        #[Autowire('%loanInterest%')] private int $loanInterest
    )
    {
    }

    /**
     * @return Installment[]
     */
    public function calculateInstallments(Loan $loan): array
    {
        $result = [];
        $amount = $loan->getAmount();
        $capitalToPay = (string)$amount;
        $installments = $loan->getInstallments();
        for ($i = 1; $i <= $installments; $i++) {
            $interest = $this->loanInterest / 10000;
            $interestYear = \bcdiv((string)$interest, (string)self::INSTALLMENTS_PER_YEAR, self::PRECISION);
            $pow = \bcpow(\bcadd('1', $interestYear, self::PRECISION), (string)$installments, self::PRECISION);
            $upPart = \bcmul($pow, $interestYear, self::PRECISION);
            $downPart = \bcsub($pow, '1', self::PRECISION);
            $installmentAmount = \bcmul((string)$amount, \bcdiv($upPart, $downPart, self::PRECISION), self::PRECISION);
            $interestAmount = \bcmul($capitalToPay, $interestYear, self::PRECISION);
            $capital = \bcsub($installmentAmount, $interestAmount, self::PRECISION);
            $capitalToPay = \bcsub($capitalToPay, $capital, self::PRECISION);
            $result[] = $this->prepareInstallment($i, $interestAmount, $capital);
        }

        return $result;
    }

    private function prepareInstallment(
        int    $number,
        string $interest,
        string $capital
    ): Installment
    {
        return new Installment(
            $number,
            (int)\round((float)$interest) + (int)\round((float)$capital),
            (int)\round((float)$interest),
            (int)\round((float)$capital)
        );
    }
}
