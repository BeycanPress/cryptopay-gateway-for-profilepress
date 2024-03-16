<?php

declare(strict_types=1);

namespace BeycanPress\CryptoPay\PP;

use BeycanPress\CryptoPay\Integrator\Hook;
use BeycanPress\CryptoPay\Integrator\Helpers;
use BeycanPress\CryptoPay\Integrator\Session;
use ProfilePress\Core\Membership\Models\Order\OrderEntity;
use ProfilePress\Core\Membership\Models\Order\OrderFactory;
use ProfilePress\Core\Membership\Models\Subscription\SubscriptionFactory;
use ProfilePress\Core\Membership\PaymentMethods\AbstractPaymentMethod;

class Loader
{
    /**
     * Loader constructor.
     */
    public function __construct()
    {
        Helpers::registerIntegration('profilepress');

        // add transaction page
        Helpers::createTransactionPage(
            esc_html__('ProfilePress Transactions', 'pp-cryptopay'),
            'profilepress',
            10,
            [
                'orderId' => function ($tx) {
                    return Helpers::run('view', 'components/link', [
                        'url' => sprintf(admin_url('admin.php?page=ppress-orders&ppress_order_action=edit&id=1'), $tx->orderId), // @phpcs:ignore
                        'text' => sprintf(esc_html__('View order #%d', 'gf-cryptopay'), $tx->orderId)
                    ]);
                }
            ]
        );

        Hook::addAction('payment_finished_profilepress', [$this, 'paymentFinished']);
        Hook::addFilter('payment_redirect_urls_profilepress', [$this, 'paymentRedirectUrls']);

        add_action('init', [Helpers::class, 'listenSPP']);
        add_filter('ppress_payment_methods', [$this, 'registerGateways']);
        add_action('ppress_myaccount_order_header_actions', [$this, 'addOrderActions']);
    }

    /**
     * @param object $data
     * @return void
     */
    public function paymentFinished(object $data): void
    {
        $orderKey = $data->getParams()->get('orderKey');
        $order = OrderFactory::fromOrderKey($orderKey);

        if ($data->getStatus()) {
            $order->complete_order($data->getHash());
            $order->delete_meta('cryptopay_payment_url');
            $subscription = SubscriptionFactory::fromId($order->subscription_id);
            if ($subscription->exists()) {
                $subscription->activate_subscription();
            }
        } else {
            $order->fail_order();
        }
    }

    /**
     * @param object $data
     * @return array<string>
     */
    public function paymentRedirectUrls(object $data): array
    {
        $orderKey = $data->getParams()->get('orderKey');
        $gateway = $data->getParams()->get('gateway');

        return [
            'success' => ppress_get_success_url($orderKey, $gateway),
            'failed' => ppress_get_cancel_url($orderKey)
        ];
    }

    /**
     * @param OrderEntity $order
     * @return void
     */
    public function addOrderActions(OrderEntity $order): void
    {
        if ($order->is_pending() || $order->is_failed()) {
            $paymentUrl = $order->get_meta('cryptopay_payment_url');
            if (!Session::has((string) $this->getSPPToken($paymentUrl))) {
                $paymentUrl = Gateways\GatewayLite::createSPPFromOrder($order);
            }

            $text = esc_html__('Pay now', 'pp-cryptopay');

            if ($order->is_failed()) {
                $text = esc_html__('Retry payment', 'pp-cryptopay');
            }

            echo Helpers::run('view', 'components/link', [
                'url' => esc_url_raw($paymentUrl),
                'text' => $text
            ]);
        }
    }

    /**
     * @param string $url
     * @return string|null
     */
    private function getSPPToken(string $url): ?string
    {
        /** @var array<mixed> $matches */
        preg_match('/[?&]cp_spp=([^&]+)/', $url, $matches);
        return isset($matches[1]) ? $matches[1] : null;
    }


    /**
     * @param array<AbstractPaymentMethod> $methods
     * @return array<AbstractPaymentMethod>
     */
    public function registerGateways(array $methods): array
    {
        if (Helpers::exists()) {
            $methods[] = Gateways\GatewayPro::get_instance();
        }

        if (Helpers::liteExists()) {
            $methods[] = Gateways\GatewayLite::get_instance();
        }

        return $methods;
    }
}
