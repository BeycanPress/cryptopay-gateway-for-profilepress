<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

// @phpcs:disable PSR1.Files.SideEffects
// @phpcs:disable PSR12.Files.FileHeader
// @phpcs:disable Generic.Files.InlineHTML
// @phpcs:disable Generic.Files.LineLength

/**
 * Plugin Name: ProfilePress - CryptoPay Gateway
 * Version:     1.0.0
 * Plugin URI:  https://beycanpress.com/cryptopay/
 * Description: Adds Cryptocurrency payment gateway (CryptoPay) for ProfilePress.
 * Author:      BeycanPress LLC
 * Author URI:  https://beycanpress.com
 * License:     GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: pp-cryptopay
 * Tags: Cryptopay, Cryptocurrency, WooCommerce, WordPress, MetaMask, Trust, Binance, Wallet, Ethereum, Bitcoin, Binance smart chain, Payment, Plugin, Gateway, Moralis, Converter, API, coin market cap, CMC
 * Requires at least: 5.0
 * Tested up to: 6.4.3
 * Requires PHP: 8.1
*/

// Autoload
require_once __DIR__ . '/vendor/autoload.php';

define('PP_CRYPTOPAY_FILE', __FILE__);
define('PP_CRYPTOPAY_VERSION', '1.0.0');
define('PP_CRYPTOPAY_KEY', basename(__DIR__));
define('PP_CRYPTOPAY_URL', plugin_dir_url(__FILE__));
define('PP_CRYPTOPAY_DIR', plugin_dir_path(__FILE__));
define('PP_CRYPTOPAY_SLUG', plugin_basename(__FILE__));

use BeycanPress\CryptoPay\Integrator\Helpers;

Helpers::registerModel(BeycanPress\CryptoPay\PP\Models\TransactionsPro::class);
Helpers::registerLiteModel(BeycanPress\CryptoPay\PP\Models\TransactionsLite::class);

load_plugin_textdomain('pp-cryptopay', false, basename(__DIR__) . '/languages');

add_action('plugins_loaded', function (): void {
    if (!defined('PPRESS_VERSION_NUMBER')) {
        add_action('admin_notices', function (): void {
            ?>
                <div class="notice notice-error">
                    <p><?php echo sprintf(esc_html__('ProfilePress - CryptoPay Gateway: This plugin requires ProfilePress to work. You can download ProfilePress by %s.', 'pp-cryptopay'), '<a href="https://wordpress.org/plugins/wp-user-avatar/" target="_blank">' . esc_html__('clicking here', 'pp-cryptopay') . '</a>'); ?></p>
                </div>
            <?php
        });
    } elseif (Helpers::bothExists()) {
        new BeycanPress\CryptoPay\PP\Loader();
    } else {
        add_action('admin_notices', function (): void {
            ?>
                <div class="notice notice-error">
                    <p><?php echo sprintf(esc_html__('ProfilePress - CryptoPay Gateway: This plugin is an extra feature plugin so it cannot do anything on its own. It needs CryptoPay to work. You can buy CryptoPay by %s.', 'pp-cryptopay'), '<a href="https://beycanpress.com/product/cryptopay-all-in-one-cryptocurrency-payments-for-wordpress/?utm_source=wp_org_addons&utm_medium=pp" target="_blank">' . esc_html__('clicking here', 'pp-cryptopay') . '</a>'); ?></p>
                </div>
            <?php
        });
    }
});
