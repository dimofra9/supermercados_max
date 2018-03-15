<?php
/**
 * Plugin Name: LiveChat WooCommerce
 * Plugin URI: http://www.livechatinc.com/integrations/cms/woocommerce
 * Description: Live chat software for live help, online sales and customer support. This plugin allows to quickly install LiveChat on any WooCommerce website.
 * Version: 1.1.10
 * Author: LiveChat
 * Author URI: http://www.livechatinc.com
 * Text Domain: woocommerce-livechat
 *
 * Copyright: © 2015 LiveChat.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    // Check if there is admin user
    if ( is_admin() ) {
        require_once( dirname( __FILE__ ) . '/src/class-woocommerce-livechat-admin.php' );
        $woocommerce_livechat = new WooCommerce_LiveChat_Admin();
    } else {
        require_once( dirname( __FILE__ ) . '/src/class-woocommerce-livechat.php' );
        $woocommerce_livechat = new WooCommerce_LiveChat();
    }

    $woocommerce_livechat->init();
    $woocommerce_livechat->init_public_ajax();
}
