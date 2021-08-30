<?php

declare(strict_types=1);

namespace App\Domain\Checkers;


use App\Domain\Entities\Transaction;
use App\Domain\Services\ApiService;

class GringottsWizardingBankChecker implements CheckerInterface
{
    public static function check(Transaction $transaction, ApiService $apiService): bool
    {
        {
            $url = 'https://run.mocky.io/v3/9f9ea9d3-a2e0-4573-97d0-924618381272';

            $service = $apiService->autorizeService($url);
            return $service['message'] === 'Autorizado';

        }
    }
}