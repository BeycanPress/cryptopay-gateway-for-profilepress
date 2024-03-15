<?php

declare(strict_types=1);

namespace BeycanPress\CryptoPay\PP\Models;

use BeycanPress\CryptoPayLite\Models\AbstractTransaction;

class TransactionsLite extends AbstractTransaction
{
    public string $addon = 'profilepress';

    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct('profilepress_transaction');
    }
}
