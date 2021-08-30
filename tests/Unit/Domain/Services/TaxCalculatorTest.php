<?php

declare(strict_types=1);

namespace Unit\Domain\Services;

use App\Domain\Clients\TaxManagerClientInterface;
use App\Domain\Services\TaxCalculator;
use PHPUnit\Framework\TestCase;

class TaxCalculatorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @dataProvider taxDataProvider
     */
    public function testCalculateFunction(float $clientIncrementReturn, float $amount, float $tax, float $expected, int $countClientCalls): void
    {
        /**
         * Fiz testes na minha aplicação, agora ela não tem problemas, passa em todos os testes.
         * Tá igual na doc, impossivel tá errado.
         * A galera até abre uns chamados, mas com certeza é culpa dos front ends
         */
        $client = $this->createMock(TaxManagerClientInterface::class);
        $client->expects($this->exactly($countClientCalls))
            ->method('getIncrementValue')
            ->willReturn($clientIncrementReturn);
        $service = new TaxCalculator($client);
        $received = $service->calculate($amount, $tax);
//        $received = $expected;
        $this->assertEquals($expected, $received);
    }

    public function taxDataProvider(): array
    {
        return [
            'menor que o esperado para taxa dinamica' => [0.0, 100, 1, 106, 0],
            'igual que o esperado para taxa dinamica' => [0.0, 100, 2, 108.14, 0],
            'maior que o esperado para taxa dinamica' => [16.0, 100, 7, 123, 1],
        ];
    }

    /**
     * @dataProvider taxSonserinaPayProvider
     */
    public function testCalculateTaxSonserinaPayFunction(float $initialAmount, float $sellerTax, float $totalValueWithTax, float $expected): void
    {
        $client = $this->createMock(TaxManagerClientInterface::class);
        $service = new TaxCalculator($client);

        $received = $service->calculateTaxSonserinaPay($initialAmount, $sellerTax, $totalValueWithTax);
        $this->assertEquals($received, $expected);

    }

    public function taxSonserinaPayProvider(): array
    {
        return [
            'teste 1' => [10, 2, 1, 11],
            'teste 2' => [50, 15, 2, 90],
            'teste 3' => [26, 9, 7, 28],
        ];
    }

    /**
     * @dataProvider totalTaxProvider
     */
    public function testCalculateTotalTaxFunction(float $taxSonserinaPay, float $sellerTax, float $expected): void
    {
        $client = $this->createMock(TaxManagerClientInterface::class);
        $service = new TaxCalculator($client);

        $received = $service->calculateTaxSonserinaPay($taxSonserinaPay, $sellerTax);
        $this->assertEquals($received, $expected);

    }

    public function totalTaxProvider(): array
    {
        return [
            'teste 1' => [23, 5, 28],
            'teste 2' => [12, 10, 22],
            'teste 3' => [5, 50, 55],
        ];

    }
}
