<?php

declare(strict_types=1);

namespace App\Domain\Checkers;


use App\Domain\Entities\Transaction;
use App\Domain\Services\ApiService;

class MinistryOfMagicChecker implements CheckerInterface
{
    public static function check(Transaction $transaction, ApiService $apiService): bool
    {
        $url = 'http://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6';

        $service = $apiService->autorizeService($url);
        return $service['message'] == 'Autorizado';

    }
}