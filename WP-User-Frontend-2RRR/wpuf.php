<?php

/*
Plugin Name: WP User Frontend
Plugin URI: http://tareq.wedevs.com/2011/01/new-plugin-wordpress-user-frontend/
Description: Post, Edit, Delete posts and edit profile without coming to backend
Author: Tareq Hasan
Version: 1.1.0-fork-2RRR-4.4
Author URI: http://tareq.weDevs.com
Contributors: tareq1988,professor99

Modified by Andy Bruin (professor99) of KnockThemDeadProductions for 2RRR. 

== Changelog ==

= 1.1.0-fork-2RRR-4.4 professor99 =
* Changed posting_msg to updating_msg
* wpuf-ajax.php replaced by wpuf-cat.php
* Changed version to 1.1.0-fork-2RRR-4.4 
* Removed enqueuing of wpuf.js
* Localizing of wpuf javascript object moved to wpuf_post_localize()
* Added language constructs
* Load only wpuf-global.css

= 1.1.0-fork-2RRR-4.3 professor99 =
* Bugfix: Changed version to 1.1.0-fork-2RRR-4.3 

= 1.1.0-fork-2RRR-4.2 professor99 =
* Bugfix: Changed version to 1.1.0-fork-2RRR-4.2 (eliminates update prompt)

= 1.1-fork-2RRR-4.0 professor99 =
* Added WPUF_Main::version plugin version variable
* Added get_plugin_data() and get_plugin_version() functions
* Require downgrade.php
* Moved $wpuf creation to file start

= 1.1-fork-2RRR-3.0 professor99 =
* Added delete_msg and delete_confirm_msg to wpuf object.
* Moved featured image functions and variables to lib/featured_image.php
* Added lib/featured_image.php to required list

= 1.1-fork-2RRR-2.0 professor99 =
* Addition of $submit_msg and $update_msg variables for Ajax submits.

= 1.1-fork-2RRR-1.0 professor99 =
* Set TinyMCE to start in Visual mode.
*/

//Invoked here so other require files can use $wpuf functions
$wpuf = new WPUF_Main();

if ( !class_exists( 'WeDevs_Settings_API' ) ) {
    require_once dirname( __FILE__ ) . '/lib/class.settings-api.php';
}

require_once 'wpuf-functions.php';
require_once 'admin/settings-options.php';
require_once 'admin/form-builder.php';

if ( is_admin() ) {
    require_once 'admin/settings.php';
    require_once 'admin/custom-fields.php';
    require_once 'admin/taxonomy.php';
    require_once 'admin/subscription.php';
    require_once 'admin/transaction.php';
    require_once 'admin/downgrade.php';    
}

require_once 'wpuf-dashboard.php';
require_once 'wpuf-add-post.php';
require_once 'wpuf-edit-post.php';
require_once 'wpuf-editprofile.php';
require_once 'wpuf-edit-user.php';
require_once 'wpuf-cat.php';

require_once 'wpuf-subscription.php';
require_once 'wpuf-payment.php';
require_once 'lib/attachment.php';
require_once 'lib/featured_image.php';
require_once 'lib/gateway/paypal.php';

//BugFix: Use of Wordpress dashboard can leave tinymce editor in "HTML" mode.
//This forces tinymce to "Visual" mode on start.
add_filter( 'wp_default_editor', create_function('', 'return "tinymce";') );

/**
 * WPUF Main Class
 * 
 * @author Tareq Hasan
 * @package WP User Frontend
 * @subpackage WPUF_Main
 */
class WPUF_Main {
    var $version = '';

    function __construct() {
        //Get plugin version
        $this->version = $this->get_plugin_version();

        register_activation_hook( __FILE__, array($this, 'install') );
        register_deactivation_hook( __FILE__, array($this, 'uninstall') );

        add_action( 'admin_init', array($this, 'block_admin_access') );

        add_action( 'init', array($this, 'load_textdomain') );
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts') );
    }

    /**
     * Create tables on plugin activation
     *
     * @author Tareq Hasan
     * @global object $wpdb
     */
    function install() {
        global $wpdb;

        flush_rewrite_rules( false );

        $sql_custom = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wpuf_customfields (
         `id` int(11) NOT NULL AUTO_INCREMENT,
         `field` varchar(30) NOT NULL,
         `type` varchar(20) NOT NULL,
         `values` text NOT NULL,
         `label` varchar(200) NOT NULL,
         `desc` varchar(200) NOT NULL,
         `required` varchar(5) NOT NULL,
         `region` varchar(20) NOT NULL DEFAULT 'top',
         `order` int(1) NOT NULL,
         PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8";

        $sql_subscription = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wpuf_subscription (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `description` text NOT NULL,
        `count` int(5) DEFAULT '0',
        `duration` int(5) NOT NULL DEFAULT '0',
        `cost` float NOT NULL DEFAULT '0',
        `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

        $sql_transaction = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wpuf_transaction (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `user_id` bigint(20) DEFAULT NULL,
        `status` varchar(255) NOT NULL DEFAULT 'pending_payment',
        `cost` varchar(255) DEFAULT '',
        `post_id` bigint(20) DEFAULT NULL,
        `pack_id` bigint(20) DEFAULT NULL,
        `payer_first_name` longtext,
        `payer_last_name` longtext,
        `payer_email` longtext,
        `payment_type` longtext,
        `payer_address` longtext,
        `transaction_id` longtext,
        `created` datetime NOT NULL,
        PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

        $wpdb->query( $sql_custom );
        $wpdb->query( $sql_subscription );
        $wpdb->query( $sql_transaction );
    }

    function uninstall() {
    }

    /**
     * Get plugin header data
     *
     * @author Andrew Bruin (professor99)
     * @since 1.1-fork-2RRR-4.0
     *
     * @return array
     */
    function get_plugin_data() {
        $default_headers = array(
            'Name' => __('Plugin Name', 'wpuf' ),
            'PluginURI' => __('Plugin URI', 'wpuf' ),
            'Version' => __('Version'),
            'Description' => __('Description', 'wpuf' ),
            'Author' => __('Author', 'wpuf' ),
            'AuthorURI' => __('Author URI', 'wpuf' ),
			'Contributors' => __('Contributors', 'wpuf' )
        );

        return get_file_data( __FILE__, $default_headers, 'plugin' );
    }

    /**
     * Get plugin version
     *
     * @author Andrew Bruin (professor99)
     * @since 1.1-fork-2RRR-4.0
     *
     * @return string
     */
    function get_plugin_version() {
        $plugin_data = $this->get_plugin_data();
        return $plugin_data['Version'];
    }
	
    /**
     * Enqueues Styles and Scripts
     *
     * @author Tareq Hasan
     * @since 0.2
     */
    function enqueue_scripts() {
        $path = plugins_url( 'wp-user-frontend' );

        //for multisite upload limit filter
        if ( is_multisite() ) {
            require_once ABSPATH . '/wp-admin/includes/ms.php';
        }

        require_once ABSPATH . '/wp-admin/includes/template.php';

        wp_enqueue_style( 'wpuf-global', $path . '/css/wpuf-global.css' );
    }

    /**
     * Block user access to admin panel for specific roles
     *
     * @author Tareq Hasan
     * @global string $pagenow
     */
    function block_admin_access() {
        global $pagenow;

        $access_level = wpuf_get_option( 'admin_access' );
        $valid_pages = array('admin-ajax.php', 'async-upload.php', 'media-upload.php');

        if ( !current_user_can( $access_level ) && !in_array( $pagenow, $valid_pages ) ) {
            wp_die( __( 'Access Denied. Your site administrator has blocked your access to the WordPress back-office.', 'wpuf' ) );
        }
    }

    /**
     * Load the translation file for current language.
     *
     * @author Tareq Hasan
     * @since version 0.7
     */
    function load_textdomain() {
        $locale = apply_filters( 'wpuf_locale', get_locale() );
        $mofile = dirname( __FILE__ ) . "/languages/wpuf-$locale.mo";

        if ( file_exists( $mofile ) ) {
            load_textdomain( 'wpuf', $mofile );
        }
    }

    /**
     * The main logging function
     *
     * @author Tareq Hasan
     * @uses error_log
     *
     * @param string $type type of the error. e.g: debug, error, info
     * @param string $msg
     */
    public static function log( $type = '', $msg = '' ) {
        if ( WP_DEBUG == true ) {
            $msg = sprintf( "[%s][%s] %s\n", date( 'd.m.Y h:i:s' ), $type, $msg );
            error_log( $msg, 3, dirname( __FILE__ ) . '/log.txt' );
        }
    }

}


