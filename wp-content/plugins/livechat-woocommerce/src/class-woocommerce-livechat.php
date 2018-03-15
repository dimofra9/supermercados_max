<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once( 'interface-woocommerce-livechat.php' );

/**
 * WooCommerce_LiveChat class for render JS script in public pages.
 */
class WooCommerce_LiveChat implements WooCommerce_LiveChat_Interface
{
    /**
     * Default settings params keys.
     */
    const LC_LICENSE_ID             = 'wc-lc_license';
    const LC_LICENSE_EMAIL          = 'wc-lc_licenseEmail';
    const LC_SETTINGS               = 'wc-lc_customDataSettings';
    /**
     * Custom params settings keys.
     */
    const LC_S_PRODUCTS_KEY          = 'wc-lc_cdsProducts';
    const LC_S_LAST_ORDER_KEY        = 'wc-lc_lastOrder';
    const LC_S_PRODUCST_COUNTS_KEY   = 'wc-lc_cdsProductsCount';
    const LC_S_TOTAL_VALUE_KEY       = 'wc-lc_cdsTotalValue';
    const LC_S_SHIPPING_ADDRESS_KEY  = 'wc-lc_cdsShippingAddress';
    const LC_S_LAST_ORDER_ID         = 'wc-lc_lastOrderId';
    const LC_S_COOKIE_EXPIRATION     = 108000;

    /**
     * @var Render_Manager
     */
    protected $renderer;

    /**
     * Set up base action.
     */
    public function init() {
        add_action('wp_enqueue_scripts', array($this, 'livechat_woocommerce_enqueue_wc_livechat_script'));
    }

    public function livechat_woocommerce_enqueue_wc_livechat_script() {
        if ( null !== ($licenseId = $this->get_license() ) ) {
            wp_enqueue_script( 'wc-livechat-script', admin_url() . 'admin-ajax.php?action=wc-livechat-script&t=' . time() );
        }
    }

    /**
     * Set up public ajax action.
     */
    public function init_public_ajax() {
        add_action( 'wp_ajax_nopriv_wc-livechat-check-cart', array( $this, 'check_cart' ) );
        add_action( 'woocommerce_checkout_order_processed', array( $this, 'set_last_order_id' ), 10, 1 );
        add_action( 'wp_ajax_nopriv_wc-livechat-script', array( $this, 'render_script' ) );
    }

    /**
     * Function for rendering script based on user settings.
     *
     * @return string
     */
    public function render_script() {
        $currentUserData = wp_get_current_user()->data;

        $visitor_email = null;
        $visitor_name = null;

        if (property_exists($currentUserData, 'user_email')) {
            $visitor_email = $currentUserData->user_email;
        }
        if (property_exists($currentUserData, 'display_name')) {
            $visitor_name = $currentUserData->display_name;
        }

        $custom_lc_data = $this->get_custom_livechat_data();

        if ( null !== ($licenseId = $this->get_license() ) ) {
            $response = $this->get_renderer()->render(
                'script-template.php',
                array(
                    'license_id'    => $licenseId,
                    'custom_data'   => $custom_lc_data,
                    'ajax_url'      => admin_url() . 'admin-ajax.php',
                    'visitor_email' => $visitor_email,
                    'visitor_name'  => $visitor_name
                ),
                false
            );
            die($response);
        }

        wp_die();
    }

    /**
     * Function for AJAX request for checking current state of cart.
     */
    public function check_cart() {

        $custom_lc_data = $this->get_custom_livechat_data(false);
        $data = array();

        foreach ( $custom_lc_data as $key => $value ) {
            $data[] = array(
                'name' => $key,
                'value' => $value
            );
        }

        // echo json_encode( $data );
        wp_send_json($data);
        wp_die();
    }

    /**
     * Set force custom data.
     *
     * @param array $data
     */
    public function set_last_order_id( $data) {
        setcookie( self::LC_S_LAST_ORDER_ID, $data, time() + self::LC_S_COOKIE_EXPIRATION, '/' );
    }

    /**
     * Returns force custom data.
     *
     * @return mixed array|null
     */
    private function get_last_order_admin_url() {
        if (array_key_exists(self::LC_S_LAST_ORDER_ID, $_COOKIE ) ) {
            return admin_url( 'post.php?post=' . $_COOKIE[self::LC_S_LAST_ORDER_ID] . '&action=edit' );
        }

        return null;
    }

    /**
     * Returns custom LiveChat params based on user settings.
     * @return array
     */
    private function get_custom_livechat_data($escapeProductTitle = true) {
        $settings = $this->get_custom_data_settings();
        $data = array();

        if ( array_key_exists( self::LC_S_LAST_ORDER_KEY, $settings ) && 1 == $settings[self::LC_S_LAST_ORDER_KEY] ) {
            if ( null !== ($last_order_url = $this->get_last_order_admin_url() ) ) {
                $data['Last order'] = $last_order_url;
            }
        }

        if ( array_key_exists( self::LC_S_PRODUCTS_KEY, $settings ) && 1 == $settings[self::LC_S_PRODUCTS_KEY] ) {
            $data['Products'] = (string) $this->get_products_data($escapeProductTitle);
        }
        if ( array_key_exists( self::LC_S_PRODUCST_COUNTS_KEY, $settings ) && 1 == $settings[self::LC_S_PRODUCST_COUNTS_KEY] ) {
            if ( ( $products_count = WC()->cart->get_cart_contents_count() ) > 0 ) {
                $data['Products count'] = (string) $products_count;
            }
        }
        if ( array_key_exists( self::LC_S_TOTAL_VALUE_KEY, $settings ) && 1 == $settings[self::LC_S_TOTAL_VALUE_KEY] ) {
            if ( ($total = WC()->cart->cart_contents_total) > 0 ) {
                $data['Total value'] = (string) $total . ' ' . get_woocommerce_currency();
            }
        }
        if ( array_key_exists(self::LC_S_SHIPPING_ADDRESS_KEY, $settings) && 1 == $settings[self::LC_S_SHIPPING_ADDRESS_KEY] ) {
            $data['Shipping address'] = (string) $this->get_shipping_address();
        }

        return $data;
    }

    /**
     * Returns shipping address.
     * @return string
     */
    private function get_shipping_address() {
        $res = null;

        if (
            property_exists( WC(), 'customer' ) &&
            method_exists( WC()->customer, 'get_shipping_address' ) &&
            method_exists( WC()->customer, 'get_shipping_address_2' ) &&
            method_exists( WC()->customer, 'get_shipping_city' )
        ) {
            $res = implode(' ', array(
                WC()->customer->get_shipping_address(),
                WC()->customer->get_shipping_address_2(),
                WC()->customer->get_shipping_city(),
            ) );
        }

        return trim( $res );
    }

    /**
     * Returns cart products data (product_name: product_url, ..)
     * @return string
     */
    private function get_products_data($escapeTitle = true) {
        $res = array();
        foreach ( WC()->cart->cart_contents as $product ) {
            if ( array_key_exists( 'data', $product ) ) {
                $wc_product = wc_get_product( $product['data'] );
                $url = htmlspecialchars_decode( $wc_product->get_permalink() );
                $title = $escapeTitle ? str_replace("'", "\'", $wc_product->get_title()) : $wc_product->get_title();

                $res[] = $this->get_renderer()->render(
                    'product-data-template.php',
                    array(
                        'url'   => $url,
                        'title' => $title
                    ),
                    false
                );
            }
        }

        return trim( implode( ' ', $res ) );
    }

    /**
     * Returns Render_Manager
     * @return Render_Manager
     */
    protected function get_renderer() {
        if ( null === $this->renderer ) {
            require_once 'service/class-render-manager.php';
            $this->renderer = new Render_Manager();
        }

        return $this->renderer;
    }

    /**
     * Returns user LiveChat license ID.
     * @return integer
     */
    protected function get_license() {
        return get_option( self::LC_LICENSE_ID, null );
    }

    /**
     * Returns cusrom data settings.
     * @return array
     */
    protected function get_custom_data_settings() {
        $res = get_option( self::LC_SETTINGS, array() );

        return ( is_array( $res ) ) ? $res : array();
    }
}
