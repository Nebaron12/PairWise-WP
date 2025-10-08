<?php
/**
 * Plugin Name: Camera Battle Elementor Widget
 * Description: Custom Elementor widget for image comparison tests
 * Version: 1.0
 * Author: Your Name
 * Requires Plugins: elementor
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Camera Battle Elementor Widget Class
 */
final class Camera_Battle_Elementor_Widget {
    
    const VERSION = '1.0.0';
    const MINIMUM_ELEMENTOR_VERSION = '3.0.0';
    const MINIMUM_PHP_VERSION = '7.0';
    
    private static $_instance = null;
    
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
    }
    
    public function init() {
        // Check if Elementor is installed and activated
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', array($this, 'admin_notice_missing_elementor'));
            return;
        }
        
        // Check for required Elementor version
        if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            add_action('admin_notices', array($this, 'admin_notice_minimum_elementor_version'));
            return;
        }
        
        // Check for required PHP version
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', array($this, 'admin_notice_minimum_php_version'));
            return;
        }
        
        // Register widget
        add_action('elementor/widgets/register', array($this, 'register_widgets'));
        
        // Enqueue scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    public function admin_notice_missing_elementor() {
        if (isset($_GET['activate'])) unset($_GET['activate']);
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'camera-battle'),
            '<strong>' . esc_html__('Camera Battle Elementor Widget', 'camera-battle') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'camera-battle') . '</strong>'
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    
    public function admin_notice_minimum_elementor_version() {
        if (isset($_GET['activate'])) unset($_GET['activate']);
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'camera-battle'),
            '<strong>' . esc_html__('Camera Battle Elementor Widget', 'camera-battle') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'camera-battle') . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    
    public function admin_notice_minimum_php_version() {
        if (isset($_GET['activate'])) unset($_GET['activate']);
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'camera-battle'),
            '<strong>' . esc_html__('Camera Battle Elementor Widget', 'camera-battle') . '</strong>',
            '<strong>' . esc_html__('PHP', 'camera-battle') . '</strong>',
            self::MINIMUM_PHP_VERSION
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    
    public function register_widgets($widgets_manager) {
        require_once(__DIR__ . '/widgets/camera-battle-widget.php');
        $widgets_manager->register(new \Camera_Battle_Widget());
    }
    
    public function enqueue_scripts() {
        // Scripts are enqueued inline within the widget for better compatibility
    }
}

Camera_Battle_Elementor_Widget::instance();
