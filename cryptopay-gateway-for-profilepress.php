<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

// @phpcs:disable PSR1.Files.SideEffects
// @phpcs:disable PSR12.Files.FileHeader
// @phpcs:disable Generic.Files.InlineHTML
// @phpcs:disable Generic.Files.LineLength

/**
 * Plugin Name: CryptoPay Gateway for ProfilePress
 * Version:     1.0.1
 * Plugin URI:  https://beycanpress.com/cryptopay/
 * Description: Adds Cryptocurrency payment gateway (CryptoPay) for ProfilePress.
 * Author:      BeycanPress LLC
 * Author URI:  https://beycanpress.com
 * License:     GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: pp-cryptopay
 * Tags: Bitcoin, Ethereum, Crypto, Payment, ProfilePress
 * Requires at least: 5.0
 * Tested up to: 6.6
 * Requires PHP: 8.1
*/

// Autoload
require_once __DIR__ . '/vendor/autoload.php';

define('PP_CRYPTOPAY_FILE', __FILE__);
define('PP_CRYPTOPAY_VERSION', '1.0.1');
define('PP_CRYPTOPAY_KEY', basename(__DIR__));
define('PP_CRYPTOPAY_URL', plugin_dir_url(__FILE__));
define('PP_CRYPTOPAY_DIR', plugin_dir_path(__FILE__));
define('PP_CRYPTOPAY_SLUG', plugin_basename(__FILE__));

use BeycanPress\CryptoPay\Integrator\Helpers;

/**
 * @return void
 */
function ppressCryptoPayRegisterModels(): void
{
    Helpers::registerModel(BeycanPress\CryptoPay\PP\Models\TransactionsPro::class);
    Helpers::registerLiteModel(BeycanPress\CryptoPay\PP\Models\TransactionsLite::class);
}

ppressCryptoPayRegisterModels();

load_plugin_textdomain('pp-cryptopay', false, basename(__DIR__) . '/languages');

add_action('plugins_loaded', function (): void {
    ppressCryptoPayRegisterModels();

    if (!defined('PPRESS_VERSION_NUMBER')) {
        Helpers::requirePluginMessage('ProfilePress', 'https://wordpress.org/plugins/wp-user-avatar/');
    } elseif (Helpers::bothExists()) {
        new BeycanPress\CryptoPay\PP\Loader();
    } else {
        Helpers::requireCryptoPayMessage('ProfilePress');
    }
});
