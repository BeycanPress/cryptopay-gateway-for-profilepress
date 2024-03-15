<?php

declare(strict_types=1);

namespace BeycanPress\CryptoPay\PP\Models;

use BeycanPress\CryptoPay\Models\AbstractTransaction;

class TransactionsPro extends AbstractTransaction
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
