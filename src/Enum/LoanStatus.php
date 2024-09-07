<?php
declare(strict_types=1);

namespace App\Enum;

enum LoanStatus: int
{
    public const DEFAULT = LoanStatus::NEW;

    case EXCLUDED = 0;
    case NEW = 1;
}
