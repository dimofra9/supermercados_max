<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once( 'interface-woocommerce-livechat.php' );
require_once( 'class-woocommerce-livechat.php' );

/**
 * WooCommerce_LiveChat_Admin class for managing LiveChat settings.
 */
class WooCommerce_LiveChat_Admin extends WooCommerce_LiveChat implements WooCommerce_LiveChat_Interface
{
    /**
     * API url's
     */
    const LC_ACOUNT_DETAILS_URL_PATTERN = 'https://api.livechatinc.com/v2/license/%licenseId%';
    const LC_CHECK_LICENSE              = 'https://api.livechatinc.com/licence/operator/';
    const LC_NEW_LICENSE                = 'https://api.livechatinc.com/v2/license/';

    /**
     * @var object current user info
     */
    private $user_info;
    /**
     * @var string plugin version
     */
    private $plugin_version;
    /**
     * @var string force template name (for rendering errors)
     */
    private $custom_template;
    /**
     * @var array of parameters for custom template
     */
    private $custom_template_params = array();

    /**
     * Set up base plugin info.
     */
    public function __construct() {
        $this->set_up_plugin_version();
    }

    /**
     * Set up base actions.
     */
    public function init() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'wp_ajax_wc-livechat-update-settings', array( $this, 'update_settings' ) );
        add_action( 'wp_ajax_wc-livechat-check-cart', array( $this, 'check_cart' ) );
        add_action( 'wp_ajax_wc-livechat-js-urls', array( $this, 'js_urls' ) );
        add_action( 'wp_ajax_wc-livechat-script', array( $this, 'render_script' ) );

        if ( array_key_exists('page', $_GET) && 'wc-livechat' == $_GET['page'] ) {
            add_action( 'admin_head', array( $this, 'admin_head_block' ) );
        }
    }

    public function js_urls() {
        $this->get_renderer()->render(
            'admin-head-block-template.php',
            array(
                'check_license_url' => self::LC_CHECK_LICENSE,
                'set_settings_url'  => admin_url() . 'admin-ajax.php',
                'new_license_url'   => self::LC_NEW_LICENSE,
            )
        );

        wp_die();
    }

    /**
     * Render admin head block.
     */
    public function admin_head_block() {
        wp_enqueue_script( 'wc-livechat-data', admin_url() . 'admin-ajax.php?action=wc-livechat-js-urls&t=' . time() );
        wp_enqueue_script( 'wc-livechat', plugins_url('js/wc-livechat.js', __FILE__), array('jquery', 'wc-livechat-data'), $this->plugin_version, true );
        wp_enqueue_style( 'wc-livechat-style', plugins_url('css/style.css', __FILE__), array(), $this->plugin_version );
        wp_enqueue_style( 'wc-livechat-fonts', '//fonts.googleapis.com/css?family=Lato:300' );
    }

    /**
     * Add plugin to admin menu.
     */
    public function admin_menu() {
        add_submenu_page( 'woocommerce', 'LiveChat', 'LiveChat', 'manage_options', 'wc-livechat', array( $this, 'settings_action' ) );
    }

    /**
     * Update user settings (license id  and custom params)
     */
    public function update_settings() {
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
            if ( array_key_exists( 'licenseId', $_POST ) ) {
                $updated = $this->set_up_license_id( (int) $_POST['licenseId'] );
            } else if ( array_key_exists( 'licenseEmail', $_POST ) ) {
                $updated = $this->set_up_license_email( $_POST['licenseEmail'] );
            } else if ( array_key_exists( 'customDataSettings', $_POST ) ) {
                $settings = explode( ':', $_POST['customDataSettings'] );
                if ( 2 === count( $settings ) ) {
                    $updated = $this->update_custom_data_settings( $settings[0], $settings[1] );
                }
            }
        }

        echo ( $updated === true ) ? json_encode( 'ok' ) : json_encode( 'err' );
        wp_die();
    }

    /**
     * Returns plugin settings template depends on LiveChat user details.
     *
     * @return string
     */
    public function settings_action() {
        if ( array_key_exists( 'reset', $_GET ) && 1 == $_GET['reset'] ) {
            // Reset user email and redirect to plugin login/register page.
            $this->reset_settings();
            $redirect_url = ( false === strpos(wp_get_referer(), '&reset=1') ) ?
                wp_get_referer() : str_replace( '&reset=1', '', wp_get_referer() );
            if ( headers_sent() ) {
                die( '<script> location.replace("' . $redirect_url . '"); </script>' );
            }
            wp_redirect( $redirect_url );
            wp_die();
        }

        $user_email = $this->get_user_property( 'user_email' );
        $username = $this->get_user_property( 'user_login' );

        if ( null === ( $license_id = $this->get_license() ) ) {
            // If there is no give license, render plugin login/register page.
            return $this->get_renderer()->render(
                'create-new-account-template.php',
                array(
                    'username'  => $username,
                    'user_email' => $user_email,
                )
            );
        }

        if ( false === $this->is_license_active( $license_id ) ) {
            // If license is not active, render inactive license page.
            if ( null !== $this->custom_template ) {
                return $this->get_renderer()->render(
                    $this->custom_template,
                    array_merge(
                        array(
                            'username'  => $username,
                            'user_email' => $user_email,
                        ),
                        $this->custom_template_params
                    )
                );
            }
            return $this->get_renderer()->render(
                'inactive-license-template.php',
                array(
                    'license_email' => $this->get_license_email(),
                    'username'  => $username,
                    'user_email' => $user_email,
                )
            );
        }
        // By default, render settings page.
        return $this->get_renderer()->render(
            'settings-template.php',
            array(
                'settings'                      => $this->get_custom_data_settings(),
                'settings_products_count_key'   => self::LC_S_PRODUCST_COUNTS_KEY,
                'settings_products_key'         => self::LC_S_PRODUCTS_KEY,
                'settings_shipping_address_key' => self::LC_S_SHIPPING_ADDRESS_KEY,
                'settings_total_value_key'      => self::LC_S_TOTAL_VALUE_KEY,
                'settings_last_order_key'       => self::LC_S_LAST_ORDER_KEY,
                'license_email'                 => $this->get_license_email(),
                'username'                      => $username,
                'user_email'                    => $user_email,
            )
        );
    }

    /**
     * Update custom data settings by given key and value.
     *
     * @param string $key
     * @param string $value
     * @return boolean
     */
    private function update_custom_data_settings( $key, $value ) {
        $current_settings       = $this->get_custom_data_settings();
        $current_settings[$key] = (boolean) $value;

        return update_option( self::LC_SETTINGS, $current_settings );
    }

    /**
     * Checks if license is active.
     * @param string $license_id
     * @return boolean
     */
    private function is_license_active( $license_id ) {
        $res = wp_remote_get( str_replace( '%licenseId%', $license_id, self::LC_ACOUNT_DETAILS_URL_PATTERN ), array( 'timeout' => 30 ) );

        if ( $res instanceof WP_Error ) {
            $this->custom_template = 'connection-error-template.php';

            $this->custom_template_params = array(
                'code'    => $res->get_error_code(),
                'message' => $res->get_error_message()
            );

            return false;
        }

        if ( 200 === $res['response']['code'] ) {
            $body = json_decode( $res['body'], true );

            if ( is_array( $body ) && array_key_exists( 'license_active', $body ) && true === $body['license_active'] ) {
                return true;
            }
        } else {
            $this->custom_template = 'api-error-template.php';

            $this->custom_template_params = array(
                'code'    => $res['response']['code'],
                'message' => $res['response']['message']
            );
        }


        return false;
    }

    /**
     * Reset user license id.
     */
    private function reset_settings() {
        delete_option( self::LC_LICENSE_ID );
        delete_option( self::LC_LICENSE_EMAIL );
        delete_option( self::LC_SETTINGS );
    }

    /**
     * Set up license email
     *
     * @param string $license_email
     */
    private function set_up_license_email( $license_email ) {
        update_option( self::LC_LICENSE_EMAIL, $license_email );
    }

    /**
     * Returns user LiveChat license email.
     * @return string
     */
    private function get_license_email() {
        return get_option( self::LC_LICENSE_EMAIL, null );
    }

    /**
     * Set up user license id and turn on all custom data settings.
     *
     * @param integer $license_id
     * @return boolean
     */
    private function set_up_license_id( $license_id ) {
        // valid licenseId
        if (is_int( $license_id ) && $license_id >= 0) {
            // Turn on all custom data settings.
            $default_settings = array(
                self::LC_S_PRODUCST_COUNTS_KEY  => 1,
                self::LC_S_PRODUCTS_KEY         => 1,
                self::LC_S_SHIPPING_ADDRESS_KEY => 1,
                self::LC_S_TOTAL_VALUE_KEY      => 1,
                self::LC_S_LAST_ORDER_KEY       => 1,
            );

            update_option( self::LC_SETTINGS, $default_settings );
            // Set up license ID
            update_option( self::LC_LICENSE_ID, $license_id );
            return true;
        }

        return false;
    }

    /**
     * Returns user property.
     *
     * @param string $property_name
     * @param string $default
     * @return string
     */
    private function get_user_property( $property_name, $default = null ) {
        if ( null === $this->user_info ) {
            $this->user_info = wp_get_current_user();
        }

        return ( null !== ($res = $this->user_info->get( $property_name ) ) ) ? $res : $default;
    }

    /**
     * Set up plugin version.
     */
    private function set_up_plugin_version() {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

        $plugin_dir = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) . '/..' ) );

        if (
            array_key_exists('livechat.php', $plugin_dir) &&
            is_array($plugin_dir['livechat.php']) &&
            array_key_exists('Version', $plugin_dir['livechat.php'])
        ) {
            $this->plugin_version = $plugin_dir['livechat.php']['Version'];
        }
    }
}
