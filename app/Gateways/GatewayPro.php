<?php

declare(strict_types=1);

namespace BeycanPress\CryptoPay\PP\Gateways;

// @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

use BeycanPress\CryptoPay\Integrator\Type;

class GatewayPro extends AbstractGateway
{
    /**
     * Gateway constructor.
     */
    public function __construct()
    {
        parent::__construct(
            'cryptopay',
            esc_html__('CryptoPay', 'pp-cryptopay')
        );
    }

    /**
     * @return Type
     */
    public static function getType(): Type
    {
        return Type::PRO;
    }
}
