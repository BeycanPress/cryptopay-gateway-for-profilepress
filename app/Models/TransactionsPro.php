<?php

declare(strict_types=1);

namespace BeycanPress\CryptoPay\PP\Models;

use BeycanPress\CryptoPay\Models\AbstractTransaction;

class TransactionsPro extends AbstractTransaction
{
    public string $addon = 'pp';

    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct('pp_transaction');
    }
}
