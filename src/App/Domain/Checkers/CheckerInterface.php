<?php

declare(strict_types=1);

namespace App\Domain\Checkers;

use App\Domain\Entities\Transaction;
use App\Domain\Services\ApiService;

interface CheckerInterface
{
    /**
     * @param Transaction $transaction
     * @return bool
     */
    public static function check(Transaction $transaction, ApiService $apiService): bool;
}