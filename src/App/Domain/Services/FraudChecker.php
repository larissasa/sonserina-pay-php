<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Checkers\CheckerInterface;
use App\Domain\Checkers\GringottsWizardingBankChecker;
use App\Domain\Checkers\MinistryOfMagicChecker;
use App\Domain\Entities\Transaction;

class FraudChecker
{
    public function check(Transaction $transaction, $reverseOrder): bool
    {
        $checked = false;

        /** @var CheckerInterface $checker */
        foreach ($this->getList($reverseOrder) as $checker) {
            if($checker::check($transaction)){
                $checked = true;
                break;
            }
        }

        return $checked;
    }

    private function getList(bool $reverseOrder = false): array
    {
        $list = [
            GringottsWizardingBankChecker::class,
            MinistryOfMagicChecker::class
        ];

        return $reverseOrder ? array_reverse($list) : $list;
    }
}
