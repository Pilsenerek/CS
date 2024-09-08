<?php
declare(strict_types=1);

namespace App\Tests;

use App\Entity\Loan;
use App\Model\Installment;
use App\Service\LoanCalcService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LoanCalcServiceTest extends TestCase
{

    public static function provideTestData(): array
    {
        return [
            [new Loan(500, 100000, 6), [
                new Installment(1, 16911, 417, 16494),
                new Installment(2, 16911, 348, 16563),
                new Installment(3, 16911, 279, 16632),
                new Installment(4, 16911, 210, 16701),
                new Installment(5, 16911, 140, 16771),
                new Installment(6, 16910, 70, 16840),
            ]],
            [new Loan(1000, 10000000, 7), [
                new Installment(1, 1476585, 83333, 1393252),
                new Installment(2, 1476586, 71723, 1404863),
                new Installment(3, 1476586, 60016, 1416570),
                new Installment(4, 1476586, 48211, 1428375),
                new Installment(5, 1476586, 36308, 1440278),
                new Installment(6, 1476586, 24306, 1452280),
                new Installment(7, 1476585, 12203, 1464382),
            ]],
        ];
    }

    #[DataProvider('provideTestData')]
    public function testLoanCalc(Loan $loan, array $expected): void
    {
        $calc = new LoanCalcService($loan->getInterestRate());
        $result = $calc->calculateInstallments($loan);
        $this->assertEquals($expected, $result);
    }
}
