<?php
declare(strict_types=1);

namespace App\DoctrineType;

use App\Enum\LoanStatus;

class LoanStatusType extends AbstractEnumType
{
    public static function getEnumsClass(): string
    {
        return LoanStatus::class;
    }

    public function getName(): string
    {
        return 'loan_status';
    }
}
