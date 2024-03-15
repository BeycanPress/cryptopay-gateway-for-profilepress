<?php

declare(strict_types=1);

namespace BeycanPress\CryptoPay\PP\Gateways;

// @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

use BeycanPress\CryptoPay\Integrator\Type;

class GatewayLite extends AbstractGateway
{
    /**
     * GatewayLite constructor.
     */
    public function __construct()
    {
        parent::__construct(
            'cryptopay_lite',
            esc_html__('CryptoPay Lite', 'pp-cryptopay')
        );
    }

    /**
     * @return Type
     */
    public static function getType(): Type
    {
        return Type::LITE;
    }
}
