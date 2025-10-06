<?php
/**
 * Plugin Name: Camera Battle Results Saver
 * Description: Saves Camera Battle widget results to WordPress database
 * Version: 1.0
 * Author: Your Name
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class CameraBattleSaver {
    
    private $table_name;
    private $summary_table_name;
    
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'camera_battle_results';
        $this->summary_table_name = $wpdb->prefix . 'camera_battle_summary';
        
        // Activation hook
        register_activation_hook(__FILE__, array($this, 'create_tables'));
        
        // REST API endpoint
        add_action('rest_api_init', array($this, 'register_routes'));
        
        // Enqueue nonce for frontend
        add_action('wp_enqueue_scripts', array($this, 'enqueue_nonce'));
    }
    
    /**
     * Create database tables on plugin activation
     */
    public function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Table for individual battle results
        $sql1 = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            session_id varchar(255) NOT NULL,
            container_id varchar(255) NOT NULL,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            battle_timestamp varchar(50) NOT NULL,
            image1_id int(11) NOT NULL,
            image1_title varchar(255) NOT NULL,
            image2_id int(11) NOT NULL,
            image2_title varchar(255) NOT NULL,
            winner_id int(11) NOT NULL,
            winner_title varchar(255) NOT NULL,
            PRIMARY KEY  (id),
            KEY session_id (session_id),
            KEY container_id (container_id),
            KEY winner_id (winner_id)
        ) $charset_collate;";
        
        // Table for session summary with clicks and complete wins
        $sql2 = "CREATE TABLE IF NOT EXISTS {$this->summary_table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            session_id varchar(255) NOT NULL,
            container_id varchar(255) NOT NULL,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            image_id int(11) NOT NULL,
            image_title varchar(255) NOT NULL,
            clicks int(11) DEFAULT 0,
            complete_wins int(11) DEFAULT 0,
            appearances int(11) DEFAULT 0,
            PRIMARY KEY  (id),
            KEY session_id (session_id),
            KEY container_id (container_id),
            KEY image_id (image_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql1);
        dbDelta($sql2);
    }
    
    /**
     * Register REST API routes
     */
    public function register_routes() {
        register_rest_route('camera-battle/v1', '/save-results', array(
            'methods' => 'POST',
            'callback' => array($this, 'save_results'),
            'permission_callback' => '__return_true', // Allow public access
        ));
        
        register_rest_route('camera-battle/v1', '/get-results/(?P<session>[a-zA-Z0-9_-]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_results'),
            'permission_callback' => '__return_true',
        ));
    }
    
    /**
     * Enqueue nonce for REST API requests
     */
    public function enqueue_nonce() {
        wp_localize_script('jquery', 'wpApiSettings', array(
            'root' => esc_url_raw(rest_url()),
            'nonce' => wp_create_nonce('wp_rest')
        ));
    }
    
    /**
     * Save battle results to database
     */
    public function save_results($request) {
        global $wpdb;
        
        $params = $request->get_json_params();
        
        if (empty($params['session']) || empty($params['container_id']) || empty($params['results'])) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => 'Missing required fields'
            ), 400);
        }
        
        $session_id = sanitize_text_field($params['session']);
        $container_id = sanitize_text_field($params['container_id']);
        $results = $params['results'];
        $summary = isset($params['summary']) ? $params['summary'] : array();
        
        // Start transaction
        $wpdb->query('START TRANSACTION');
        
        try {
            // Insert individual battle results
            foreach ($results as $result) {
                $wpdb->insert(
                    $this->table_name,
                    array(
                        'session_id' => $session_id,
                        'container_id' => $container_id,
                        'battle_timestamp' => sanitize_text_field($result['ts']),
                        'image1_id' => intval($result['image1_id']),
                        'image1_title' => sanitize_text_field($result['image1_title']),
                        'image2_id' => intval($result['image2_id']),
                        'image2_title' => sanitize_text_field($result['image2_title']),
                        'winner_id' => intval($result['winner_id']),
                        'winner_title' => sanitize_text_field($result['winner_title'])
                    ),
                    array('%s', '%s', '%s', '%d', '%s', '%d', '%s', '%d', '%s')
                );
            }
            
            // Insert summary data with clicks and complete wins
            foreach ($summary as $item) {
                $wpdb->insert(
                    $this->summary_table_name,
                    array(
                        'session_id' => $session_id,
                        'container_id' => $container_id,
                        'image_id' => intval($item['id']),
                        'image_title' => sanitize_text_field($item['title']),
                        'clicks' => intval($item['clicks']),
                        'complete_wins' => intval($item['completeWins']),
                        'appearances' => intval($item['appearances'])
                    ),
                    array('%s', '%s', '%d', '%s', '%d', '%d', '%d')
                );
            }
            
            $wpdb->query('COMMIT');
            
            return new WP_REST_Response(array(
                'success' => true,
                'message' => 'Results saved successfully',
                'records_saved' => count($results)
            ), 200);
            
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            
            return new WP_REST_Response(array(
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ), 500);
        }
    }
    
    /**
     * Get results for a specific session
     */
    public function get_results($request) {
        global $wpdb;
        
        $session_id = sanitize_text_field($request['session']);
        
        // Get battle results
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE session_id = %s ORDER BY timestamp DESC",
            $session_id
        ), ARRAY_A);
        
        // Get summary data
        $summary = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$this->summary_table_name} WHERE session_id = %s ORDER BY timestamp DESC",
            $session_id
        ), ARRAY_A);
        
        return new WP_REST_Response(array(
            'success' => true,
            'results' => $results,
            'summary' => $summary
        ), 200);
    }
}

// Initialize the plugin
new CameraBattleSaver();
