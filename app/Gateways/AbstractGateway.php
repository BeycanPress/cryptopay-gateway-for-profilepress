<?php

declare(strict_types=1);

namespace BeycanPress\CryptoPay\PP\Gateways;

// @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

use BeycanPress\CryptoPay\Integrator\Type;
use BeycanPress\CryptoPay\Integrator\Helpers;
use ProfilePress\Core\Membership\Models\Order\OrderEntity;
use ProfilePress\Core\Membership\Models\Order\OrderFactory;
use ProfilePress\Core\Membership\Controllers\CheckoutResponse;
use ProfilePress\Core\Membership\PaymentMethods\AbstractPaymentMethod;

abstract class AbstractGateway extends AbstractPaymentMethod
{
    /**
     * @var string
     */
    // @phpcs:ignore
    protected $id;

    /**
     * @var bool
     */
    // @phpcs:ignore
    protected $backend_only = false;

    /**
     * @var string
     */
    // @phpcs:ignore
    protected $method_title;

    /**
     * @var string
     */
    // @phpcs:ignore
    protected $method_description;

    /**
     * @var string
     */
    // @phpcs:ignore
    protected $title;

    /**
     * @var string
     */
    // @phpcs:ignore
    protected $description;

    /**
     * @var array<string>
     */
    // @phpcs:ignore
    protected $supports;

    /**
     * @param string $id
     * @param string $title
     * Gateway constructor.
     */
    public function __construct(string $id, string $title)
    {
        $this->id                 = $id;
        $this->method_title       = $title;
        $this->title              = $title;
        $this->method_description = esc_html__(
            'Your customers can pay with supported blockchain networks and currencies under these networks',
            'pp-cryptopay'
        );
        $this->description        = esc_html__(
            'You can pay with supported blockchain networks and currencies under these networks.',
            'pp-cryptopay'
        );

        /** @disregard */
        $this->supports = [
            self::SUBSCRIPTIONS
        ];
    }

    /**
     * @return array<string,array<mixed>>
     */
    public function admin_settings(): array
    {
        return parent::admin_settings();
    }

    /**
     * @return string
     */
    public function get_icon(): string
    {
        return '<img src="' . esc_url_raw(\PP_CRYPTOPAY_URL . '/assets/images/icon.png') . '" alt="' . esc_attr($this->get_title()) . '" />'; // phpcs:ignore
    }

    /**
     * @param string $transactionId
     * @param OrderEntity $order
     * @return string
     */
    // @phpcs:ignore
    public function link_transaction_id($transactionId, $order)
    {
        return Helpers::run('view', 'components/link', [
            'url' => sprintf(admin_url('admin.php?page=%s_profilepress_transactions&s=%s'), $this->id, $transactionId),
            'text' => esc_html__('View transaction', 'pp-cryptopay')
        ]);
    }

    /**
     * @return bool|\WP_Error
     */
    public function validate_fields(): bool|\WP_Error
    {
        return true;
    }

    /**
     * @param int $orderId
     * @param int $subscriptionId
     * @param int $customerId
     * @return CheckoutResponse
     */
    // @phpcs:ignore
    public function process_payment($orderId, $subscriptionId, $customerId): CheckoutResponse
    {
        $order = OrderFactory::fromId($orderId);

        $paymentUrl = self::createSPPFromOrder($order);

        $order->add_meta('cryptopay_payment_url', $paymentUrl);
        $order->add_meta('cryptopay_id', $this->id);

        return (new CheckoutResponse())
        ->set_is_success(true)
        ->set_gateway_response(
            esc_html__('You will be redirected to the payment page.', 'pp-cryptopay')
        )
        ->set_redirect_url($paymentUrl);
    }

    /**
     * @return Type
     */
    abstract public static function getType(): Type;

    /**
     * @param OrderEntity $order
     * @return string
     */
    public static function createSPPFromOrder(OrderEntity $order): string
    {
        return Helpers::createSPP([
            'addon' => 'profilepress',
            'addonName' => 'ProfilePress',
            'type' => static::getType(),
            'order' => [
                'id' => $order->get_id(),
                'currency' => ppress_get_currency(),
                'amount' => ppress_sanitize_amount($order->get_total())
            ],
            'params' => [
                'orderKey' => $order->get_order_key(),
                'customerId' => $order->get_customer_id(),
                'subscriptionId' => $order->get_subscription_id()
            ],
        ]);
    }

    /**
     * @return void
     */
    public function process_webhook(): void
    {
        // don't need to do anything here
    }
}
