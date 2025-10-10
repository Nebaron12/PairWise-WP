<?php
/**
 * Plugin Name: PairWise Battler
 * Description: Interactive image comparison widget for Elementor with database storage and admin dashboard
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: pairwise-battler
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Elementor tested up to: 3.16.0
 * Elementor Pro tested up to: 3.16.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Main PairWise Battler Plugin Class
 */
final class PairWise_Battler {
    
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
        register_activation_hook(__FILE__, array($this, 'activate'));
    }
    
    /**
     * Plugin activation - create database tables
     */
    public function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Table for summary data with clicks and complete wins per image
        $table_summary = $wpdb->prefix . 'pairwise_summary';
        $sql_summary = "CREATE TABLE IF NOT EXISTS $table_summary (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            session_id varchar(255) NOT NULL,
            image_name varchar(255) NOT NULL,
            clicks int(11) DEFAULT 0,
            complete_wins int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY session_id (session_id),
            KEY image_name (image_name)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_summary);
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
        
        // Register REST API endpoints
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Add admin scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    public function admin_notice_missing_elementor() {
        if (isset($_GET['activate'])) unset($_GET['activate']);
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'pairwise-battler'),
            '<strong>' . esc_html__('PairWise Battler', 'pairwise-battler') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'pairwise-battler') . '</strong>'
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    
    public function admin_notice_minimum_elementor_version() {
        if (isset($_GET['activate'])) unset($_GET['activate']);
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'pairwise-battler'),
            '<strong>' . esc_html__('PairWise Battler', 'pairwise-battler') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'pairwise-battler') . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    
    public function admin_notice_minimum_php_version() {
        if (isset($_GET['activate'])) unset($_GET['activate']);
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'pairwise-battler'),
            '<strong>' . esc_html__('PairWise Battler', 'pairwise-battler') . '</strong>',
            '<strong>' . esc_html__('PHP', 'pairwise-battler') . '</strong>',
            self::MINIMUM_PHP_VERSION
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    
    public function register_widgets($widgets_manager) {
        require_once(__DIR__ . '/widget-class.php');
        $widgets_manager->register(new \PairWise_Battler_Widget());
    }
    
    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('pairwise-battler/v1', '/save-results', array(
            'methods' => 'POST',
            'callback' => array($this, 'save_results'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route('pairwise-battler/v1', '/get-results', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_results'),
            'permission_callback' => array($this, 'check_admin_permission')
        ));
    }
    
    public function check_admin_permission() {
        return current_user_can('manage_options');
    }
    
    /**
     * Save results to database
     */
    public function save_results($request) {
        global $wpdb;
        
        $params = $request->get_json_params();
        
        // Validate required fields
        if (empty($params['session']) || empty($params['summary'])) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => 'Missing required fields'
            ), 400);
        }
        
        $session_id = sanitize_text_field($params['session']);
        $summary = $params['summary'];

        $table_summary = $wpdb->prefix . 'pairwise_summary';

        // Start transaction
        $wpdb->query('START TRANSACTION');

        try {
            // Insert summary data with clicks and complete wins for each image
            foreach ($summary as $item) {
                $insert_result = $wpdb->insert(
                    $table_summary,
                    array(
                        'session_id' => $session_id,
                        'image_name' => sanitize_text_field($item['title']),
                        'clicks' => intval($item['clicks']),
                        'complete_wins' => intval($item['completeWins'])
                    ),
                    array('%s', '%s', '%d', '%d')
                );
                
                if ($insert_result === false) {
                    throw new Exception('Database insert failed: ' . $wpdb->last_error);
                }
            }

            $wpdb->query('COMMIT');

            return new WP_REST_Response(array(
                'success' => true,
                'message' => 'Results saved successfully',
                'records_saved' => count($summary)
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
     * Get results from database
     */
    public function get_results($request) {
        global $wpdb;
        
        $session_id = $request->get_param('session_id');
        $table_summary = $wpdb->prefix . 'pairwise_summary';

        if ($session_id) {
            $results = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_summary WHERE session_id = %s ORDER BY complete_wins DESC, clicks DESC",
                $session_id
            ), ARRAY_A);
        } else {
            $results = $wpdb->get_results("SELECT * FROM $table_summary ORDER BY created_at DESC", ARRAY_A);
        }

        return new WP_REST_Response(array(
            'success' => true,
            'results' => $results
        ), 200);
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'PairWise Battler',
            'PairWise Battler',
            'manage_options',
            'pairwise-battler',
            array($this, 'render_admin_page'),
            'dashicons-images-alt2',
            30
        );
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if ($hook !== 'toplevel_page_pairwise-battler') {
            return;
        }

        wp_add_inline_style('wp-admin', '
            .pw-admin-wrap { max-width: 1200px; margin: 20px 0; }
            .pw-admin-header { background: #fff; padding: 20px; margin-bottom: 20px; border-left: 4px solid #2271b1; }
            .pw-admin-header h1 { margin: 0 0 10px; }
            .pw-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
            .pw-stat-card { background: #fff; padding: 20px; border-left: 4px solid #2271b1; }
            .pw-stat-card h3 { margin: 0 0 10px; font-size: 14px; color: #666; text-transform: uppercase; }
            .pw-stat-card .number { font-size: 32px; font-weight: bold; color: #2271b1; }
            .pw-tabs { background: #fff; margin-bottom: 20px; border-bottom: 1px solid #ccd0d4; }
            .pw-tabs button { background: none; border: none; padding: 15px 20px; cursor: pointer; font-size: 14px; color: #50575e; border-bottom: 2px solid transparent; }
            .pw-tabs button.active { color: #2271b1; border-bottom-color: #2271b1; font-weight: 600; }
            .pw-tabs button:hover { color: #2271b1; }
            .pw-tab-content { background: #fff; padding: 20px; }
            .pw-table { width: 100%; border-collapse: collapse; }
            .pw-table th { text-align: left; padding: 12px; background: #f6f7f7; border-bottom: 2px solid #ccd0d4; font-weight: 600; }
            .pw-table td { padding: 12px; border-bottom: 1px solid #e5e5e5; }
            .pw-table tr:hover { background: #f6f7f7; }
            .pw-session-select { padding: 8px 12px; font-size: 14px; margin-bottom: 15px; }
            .pw-filter-bar { display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap; align-items: center; }
            .pw-badge { display: inline-block; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: 600; }
            .pw-badge.high { background: #d4edda; color: #155724; }
            .pw-badge.medium { background: #fff3cd; color: #856404; }
            .pw-badge.low { background: #f8d7da; color: #721c24; }
            .pw-export-btn { background: #2271b1; color: #fff; border: none; padding: 10px 20px; cursor: pointer; border-radius: 3px; }
            .pw-export-btn:hover { background: #135e96; }
            .pw-no-data { text-align: center; padding: 40px; color: #666; }
            .pw-totals-row { background: #e8f4f8; font-weight: bold; }
        ');
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        global $wpdb;
        $table_results = $wpdb->prefix . 'pairwise_results';
        $table_summary = $wpdb->prefix . 'pairwise_summary';

        // Handle CSV export
        if (isset($_GET['export']) && $_GET['export'] === 'csv' && isset($_GET['session'])) {
            $this->export_session_csv(sanitize_text_field($_GET['session']));
            exit;
        }

        // Handle Export All
        if (isset($_GET['export_all']) && $_GET['export_all'] === 'csv') {
            $this->export_all_csv();
            exit;
        }

        // Get all sessions with stats
        $sessions = $wpdb->get_results(
            "SELECT DISTINCT session_id, COUNT(*) as total_votes, MIN(created_at) as first_vote, MAX(created_at) as last_vote 
             FROM $table_summary 
             GROUP BY session_id 
             ORDER BY last_vote DESC",
            ARRAY_A
        );
        
        // Get selected session (default to most recent)
        $selected_session = isset($_GET['session']) ? sanitize_text_field($_GET['session']) : '';
        if (empty($selected_session) && !empty($sessions)) {
            $selected_session = $sessions[0]['session_id'];
        }
        
        // Get overall stats
        $total_votes = $wpdb->get_var("SELECT SUM(clicks) FROM $table_summary");
        $total_sessions = count($sessions);
        $unique_users = $wpdb->get_var(
            "SELECT COUNT(DISTINCT session_id) FROM $table_summary"
        );

        ?>
        <div class="wrap pw-admin-wrap">
            <div class="pw-admin-header">
                <h1>📊 PairWise Battler Results</h1>
                <p>View and analyze results from your image comparison tests</p>
            </div>
            
            <div class="pw-stats-grid">
                <div class="pw-stat-card">
                    <h3>Total Clicks</h3>
                    <div class="number"><?php echo number_format($total_votes); ?></div>
                </div>
                <div class="pw-stat-card">
                    <h3>Sessions</h3>
                    <div class="number"><?php echo number_format($total_sessions); ?></div>
                </div>
                <div class="pw-stat-card">
                    <h3>Tests Completed</h3>
                    <div class="number"><?php echo number_format($unique_users); ?></div>
                </div>
            </div>
            
            <div class="pw-tabs">
                <button class="pw-tab-btn active" onclick="switchTab(event, 'overall')">Overall Results</button>
                <button class="pw-tab-btn" onclick="switchTab(event, 'per-user')">Per User Results</button>
            </div>

            <!-- Overall Results Tab -->
            <div id="overall" class="pw-tab-content">
                <div class="pw-filter-bar">
                    <button class="pw-export-btn" onclick="exportAllData()">
                        Export All Data
                    </button>
                </div>
                <?php $this->render_overall_results(); ?>
            </div>

            <!-- Per User Results Tab -->
            <div id="per-user" class="pw-tab-content" style="display:none;">
                <?php if (!empty($sessions)): ?>
                    <div class="pw-filter-bar">
                        <label for="session-select"><strong>Select Session:</strong></label>
                        <select id="session-select" class="pw-session-select" onchange="window.location.href='?page=pairwise-battler&tab=per-user&session=' + this.value">
                            <?php foreach ($sessions as $session): ?>
                                <option value="<?php echo esc_attr($session['session_id']); ?>" 
                                        <?php selected($selected_session, $session['session_id']); ?>>
                                    <?php echo esc_html($session['session_id']); ?> 
                                    (<?php echo $session['total_votes']; ?> votes, 
                                    <?php echo date('M j, Y', strtotime($session['last_vote'])); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <button class="pw-export-btn" onclick="exportSessionData('<?php echo esc_js($selected_session); ?>')">
                            Export CSV
                        </button>
                    </div>
                <?php endif; ?>
                <?php $this->render_per_user_results($selected_session); ?>
            </div>
        </div>
        
        <script>
        function switchTab(evt, tabName) {
            // Hide all tab content
            var tabContent = document.getElementsByClassName("pw-tab-content");
            for (var i = 0; i < tabContent.length; i++) {
                tabContent[i].style.display = "none";
            }
            
            // Remove active class from all buttons
            var tabButtons = document.getElementsByClassName("pw-tab-btn");
            for (var i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove("active");
            }
            
            // Show current tab and mark button as active
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.classList.add("active");
        }
        
        function exportSessionData(sessionId) {
            window.location.href = '<?php echo admin_url('admin.php'); ?>?page=pairwise-battler&export=csv&session=' + sessionId;
        }
        
        function exportAllData() {
            window.location.href = '<?php echo admin_url('admin.php'); ?>?page=pairwise-battler&export_all=csv';
        }
        
        // Check if we need to switch to per-user tab on page load
        <?php if (isset($_GET['tab']) && $_GET['tab'] === 'per-user'): ?>
        document.addEventListener('DOMContentLoaded', function() {
            var perUserTab = document.querySelector('.pw-tab-btn:nth-child(2)');
            if (perUserTab) {
                perUserTab.click();
            }
        });
        <?php endif; ?>
        </script>
        <?php
    }
    
    /**
     * Render overall results
     */
    private function render_overall_results() {
        global $wpdb;
        $table_summary = $wpdb->prefix . 'pairwise_summary';

        // Get aggregated results across ALL users and ALL sessions
        // Ordered by complete wins first, then click rate as tiebreaker
        $results = $wpdb->get_results(
            "SELECT 
                image_name,
                SUM(clicks) as total_clicks,
                SUM(complete_wins) as total_complete_wins,
                ROUND(SUM(complete_wins) * 100.0 / NULLIF(COUNT(*), 0), 1) as win_rate
             FROM $table_summary
             GROUP BY image_name
             ORDER BY total_complete_wins DESC, win_rate DESC",
            ARRAY_A
        );

        if (empty($results)) {
            echo '<div class="pw-no-data">No results yet. Start collecting votes first!</div>';
            return;
        }

        ?>
        <h2>Overall Results - All Users & Sessions</h2>
        <p style="color: #666; margin-bottom: 20px;">Aggregated data from all tests ever conducted</p>
        <table class="pw-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Image</th>
                    <th>Total Clicks</th>
                    <th>Complete Wins</th>
                    <th>Win Rate</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $rank = 1;
                foreach ($results as $row): 
                    $rate = floatval($row['win_rate']);
                    $badge_class = $rate >= 60 ? 'high' : ($rate >= 40 ? 'medium' : 'low');
                ?>
                    <tr>
                        <td><strong>#<?php echo $rank++; ?></strong></td>
                        <td><?php echo esc_html($row['image_name']); ?></td>
                        <td><?php echo number_format($row['total_clicks']); ?></td>
                        <td><?php echo number_format($row['total_complete_wins']); ?></td>
                        <td><span class="pw-badge <?php echo $badge_class; ?>"><?php echo $row['win_rate']; ?>%</span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
    
    /**
     * Render per-user results
     */
    private function render_per_user_results($session_id) {
        global $wpdb;
        $table_summary = $wpdb->prefix . 'pairwise_summary';

        if (empty($session_id)) {
            echo '<div class="pw-no-data">No session data available.</div>';
            return;
        }

        // Get all unique images for this session
        $images = $wpdb->get_results($wpdb->prepare(
            "SELECT DISTINCT image_name 
             FROM $table_summary
             WHERE session_id = %s
             ORDER BY image_name",
            $session_id
        ), ARRAY_A);

        if (empty($images)) {
            echo '<div class="pw-no-data">No user data yet for this session.</div>';
            return;
        }

        // Get all user data for this session (each row is a separate test completion)
        $user_results = $wpdb->get_results($wpdb->prepare(
            "SELECT 
                id,
                image_name,
                clicks,
                complete_wins,
                created_at
             FROM $table_summary
             WHERE session_id = %s
             ORDER BY created_at DESC",
            $session_id
        ), ARRAY_A);

        // Organize data by unique test completion (using created_at as unique identifier)
        $organized_data = array();
        $totals = array();

        // Initialize totals
        foreach ($images as $img) {
            $totals[$img['image_name']] = 0;
        }

        foreach ($user_results as $row) {
            // Use timestamp as unique key for each test
            $unique_key = $row['created_at'];

            if (!isset($organized_data[$unique_key])) {
                $organized_data[$unique_key] = array(
                    'timestamp' => $row['created_at'],
                    'images' => array()
                );
            }

            $organized_data[$unique_key]['images'][$row['image_name']] = array(
                'clicks' => $row['clicks'],
                'complete_wins' => $row['complete_wins']
            );

            // Add to totals if this image won
            if ($row['complete_wins'] > 0) {
                $totals[$row['image_name']]++;
            }
        }

        ?>
        <h2>Per User Results for Session: <?php echo esc_html($session_id); ?></h2>
        <p style="color: #666; margin-bottom: 20px;">Each row represents one completed test</p>
        
        <table class="pw-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Completed</th>
                    <?php foreach ($images as $img): ?>
                        <th><?php echo esc_html($img['image_name']); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <!-- Totals Row at Top -->
                <tr class="pw-totals-row">
                    <td colspan="2"><strong>Total Wins</strong></td>
                    <?php foreach ($images as $img): ?>
                        <td style="text-align: center;">
                            <span style="font-size: 18px; color: #2271b1;">
                                <?php echo $totals[$img['image_name']]; ?>
                            </span>
                        </td>
                    <?php endforeach; ?>
                </tr>
                
                <!-- Individual Test Results -->
                <?php 
                $result_number = count($organized_data);
                foreach ($organized_data as $unique_key => $data): 
                ?>
                    <tr>
                        <td>Result <?php echo $result_number--; ?></td>
                        <td><?php echo date('M j, Y g:i a', strtotime($data['timestamp'])); ?></td>
                        <?php foreach ($images as $img): ?>
                            <td>
                                <?php 
                                $image_title = $img['image_name'];
                                if (isset($data['images'][$image_title])):
                                    $img_data = $data['images'][$image_title];
                                    $clicks = $img_data['clicks'];
                                    $complete_wins = $img_data['complete_wins'];
                                ?>
                                    <strong><?php echo $clicks; ?></strong> clicks
                                    <?php if ($complete_wins > 0): ?>
                                        <br><span class="pw-badge high" style="margin-top: 4px;">Winner</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: #ccc;">-</span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
    
    /**
     * Export session to CSV
     */
    private function export_session_csv($session_id) {
        global $wpdb;
        $table_summary = $wpdb->prefix . 'pairwise_summary';

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_summary WHERE session_id = %s ORDER BY complete_wins DESC, clicks DESC",
            $session_id
        ));

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="pairwise-results-' . $session_id . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, array('Image Name', 'Clicks', 'Complete Wins', 'Created At'));

        foreach ($results as $result) {
            fputcsv($output, array($result->image_name, $result->clicks, $result->complete_wins, $result->created_at));
        }

        fclose($output);
    }
    
    /**
     * Export all data to CSV
     */
    private function export_all_csv() {
        global $wpdb;
        $table_summary = $wpdb->prefix . 'pairwise_summary';

        $results = $wpdb->get_results("SELECT * FROM $table_summary ORDER BY session_id, complete_wins DESC, clicks DESC");

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="pairwise-all-results-' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, array('Session ID', 'Image Name', 'Clicks', 'Complete Wins', 'Created At'));

        foreach ($results as $result) {
            fputcsv($output, array(
                $result->session_id,
                $result->image_name,
                $result->clicks,
                $result->complete_wins,
                $result->created_at
            ));
        }

        fclose($output);
    }
}

PairWise_Battler::instance();
