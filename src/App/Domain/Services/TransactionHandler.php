<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Entities\Buyer;
use App\Domain\Entities\Notification;
use App\Domain\Entities\Transaction;
use App\Domain\Exceptions\TransactionNotAuthorizedException;
use App\Domain\Repositories\TransactionRepositoryInterface;
use DateTime;
use Exception;
use function PHPUnit\Framework\throwException;

class TransactionHandler
{
    /**
     * @var TransactionRepositoryInterface
     */
    private TransactionRepositoryInterface $repository;

    /**
     * @var TaxCalculator
     */
    private TaxCalculator $taxCalculator;

    /**
     * @var FraudChecker
     */
    private FraudChecker $fraudChecker;

    /**
     * @var Notifier
     */
    private Notifier $notifier;

    public function __construct(
        TransactionRepositoryInterface $repository,
        TaxCalculator $taxCalculator,
        FraudChecker $fraudChecker,
        Notifier $notifier
    )
    {
        $this->repository = $repository;
        $this->taxCalculator = $taxCalculator;
        $this->fraudChecker = $fraudChecker;
        $this->notifier = $notifier;
    }

    /**
     * @throws Exception
     */
    public function create(Transaction $transaction): Transaction
    {
        /**
         * Draco: Aqui valida se pode fazer a transação, a Granger falou que tem uns chamados estranhos dizendo que
         * o cliente tá conseguindo sacar dinheiro da carteira do lojista, mas com certeza é culpa da empresa
         * que faz a analise anti fraude, eles são trouxas né? Meu sistema não pode fazer nada pra resolver isso.
         */
        if (!$this->fraudChecker->check($transaction)) {
            throw new TransactionNotAuthorizedException('Transação Não Autorizada');
        }

        /**
         * Goyle: esse trecho de código calcula o valor total com a taxa do sonserinapay, pra saber o valor total da taxa tem
         * que calcular inicialAmount + sellerTaxa - valorTotalWithTax = taxaSonserinaPay
         * pra saber o total de taxas tem que somar a taxa do sonserinapay com a taxa do lojista
         * mas eu não sei pra que isso serve não, só fix o que o Draco me mandou fazer
         */
        $totalValueWithTax = $this->taxCalculator->calculate($transaction->getInitialAmount(), $transaction->getSellerTax());
        $taxSonserinaPay   = $this->taxCalculator->calculateTaxSonserinaPay($transaction->getInitialAmount(), $transaction->getSellerTax(), $totalValueWithTax);
        $totalTax          = $this->taxCalculator->calculateTotalTax($taxSonserinaPay, $transaction->getSellerTax());


        /**
         * Draco: Salva a data de criação da transação
         */
        $transaction->setCreatedDate(new DateTime());

        /**
         * Draco: Salva o total Amount calculado com a taxa,
         * A taxa do sonserinapay e o total de taxas
         */
        $transaction->setTotalAmount($totalValueWithTax);
        $transaction->setSlytherinPayTax($taxSonserinaPay);
        $transaction->setTotalTax($totalTax);
        /**
         * Crabbe: Aqui salva a transação
         * Draco: As vezes a gente da erro na hora de salvar ai a gente já mandou notificação pro cliente, mas paciência né?
         */
        $this->repository->save($transaction);

        /**
         * Draco: Era pra notificar o cliente e o lojista né? Mas esse cara tá dando problema, com certeza
         * é culpa do Crabbe que não fez a classe de notificação direito
         */

        $this->sendNotification($transaction);
        return $transaction;

    }

    public function sendNotification(Transaction $transaction)
    {

        $recipients = [
            $transaction->getBuyer(),
            $transaction->getSeller()
        ];

        try {
            foreach ($recipients as $recipient) {
                $this->notifier->notify(new Notification($recipient, "Transação aprovada"));
            }
        } catch (Exception $exception) {
            throw new Exception('Notification Failed');
        }



    }
}
