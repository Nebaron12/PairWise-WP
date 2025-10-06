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
        
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Enqueue admin styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        
        // Add AJAX handler for CSV export
        add_action('wp_ajax_cb_export_csv', array($this, 'export_csv'));
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
     * Add admin menu item
     */
    public function add_admin_menu() {
        add_menu_page(
            'Camera Battle Results',           // Page title
            'Camera Battle',                   // Menu title
            'manage_options',                  // Capability
            'camera-battle-results',           // Menu slug
            array($this, 'render_admin_page'), // Callback function
            'dashicons-camera',                // Icon
            30                                 // Position
        );
    }
    
    /**
     * Enqueue admin styles
     */
    public function enqueue_admin_styles($hook) {
        // Only load on our admin page
        if ($hook !== 'toplevel_page_camera-battle-results') {
            return;
        }
        
        // Add inline CSS
        wp_add_inline_style('wp-admin', $this->get_admin_css());
    }
    
    /**
     * Get admin CSS styles
     */
    private function get_admin_css() {
        return '
            .cb-admin-wrap { max-width: 1200px; margin: 20px 0; }
            .cb-admin-header { background: #fff; padding: 20px; margin-bottom: 20px; border-left: 4px solid #2271b1; }
            .cb-admin-header h1 { margin: 0 0 10px; }
            .cb-stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px; }
            .cb-stat-card { background: #fff; padding: 20px; border-left: 4px solid #2271b1; }
            .cb-stat-card h3 { margin: 0 0 10px; font-size: 14px; color: #666; text-transform: uppercase; }
            .cb-stat-card .number { font-size: 32px; font-weight: bold; color: #2271b1; }
            .cb-tabs { background: #fff; margin-bottom: 20px; border-bottom: 1px solid #ccd0d4; }
            .cb-tabs button { background: none; border: none; padding: 15px 20px; cursor: pointer; font-size: 14px; color: #50575e; border-bottom: 2px solid transparent; }
            .cb-tabs button.active { color: #2271b1; border-bottom-color: #2271b1; font-weight: 600; }
            .cb-tabs button:hover { color: #2271b1; }
            .cb-tab-content { background: #fff; padding: 20px; }
            .cb-table { width: 100%; border-collapse: collapse; }
            .cb-table th { text-align: left; padding: 12px; background: #f6f7f7; border-bottom: 2px solid #ccd0d4; font-weight: 600; }
            .cb-table td { padding: 12px; border-bottom: 1px solid #e5e5e5; }
            .cb-table tr:hover { background: #f6f7f7; }
            .cb-session-select { padding: 8px 12px; font-size: 14px; margin-bottom: 15px; }
            .cb-filter-bar { display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap; align-items: center; }
            .cb-badge { display: inline-block; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: 600; }
            .cb-badge.high { background: #d4edda; color: #155724; }
            .cb-badge.medium { background: #fff3cd; color: #856404; }
            .cb-badge.low { background: #f8d7da; color: #721c24; }
            .cb-export-btn { background: #2271b1; color: #fff; border: none; padding: 10px 20px; cursor: pointer; border-radius: 3px; }
            .cb-export-btn:hover { background: #135e96; }
            .cb-no-data { text-align: center; padding: 40px; color: #666; }
        ';
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        global $wpdb;
        
        // Get all sessions
        $sessions = $wpdb->get_results(
            "SELECT DISTINCT session_id, COUNT(*) as total_votes, MIN(timestamp) as first_vote, MAX(timestamp) as last_vote 
             FROM {$this->table_name} 
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
        $total_votes = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}");
        $total_sessions = count($sessions);
        $unique_users = $wpdb->get_var(
            "SELECT COUNT(DISTINCT container_id) FROM {$this->summary_table_name}"
        );
        
        ?>
        <div class="wrap cb-admin-wrap">
            <div class="cb-admin-header">
                <h1>📸 Camera Battle Results</h1>
                <p>View and analyze results from your image comparison tests</p>
            </div>
            
            <div class="cb-stats-grid">
                <div class="cb-stat-card">
                    <h3>Total Votes</h3>
                    <div class="number"><?php echo number_format($total_votes); ?></div>
                </div>
                <div class="cb-stat-card">
                    <h3>Sessions</h3>
                    <div class="number"><?php echo number_format($total_sessions); ?></div>
                </div>
                <div class="cb-stat-card">
                    <h3>Unique Users</h3>
                    <div class="number"><?php echo number_format($unique_users); ?></div>
                </div>
            </div>
            
            <div class="cb-tabs">
                <button class="cb-tab-btn active" onclick="switchTab(event, 'overall')">Overall Results</button>
                <button class="cb-tab-btn" onclick="switchTab(event, 'per-user')">Per User Results</button>
            </div>
            
            <!-- Overall Results Tab -->
            <div id="overall" class="cb-tab-content">
                <?php $this->render_overall_results(); ?>
            </div>
            
            <!-- Per User Results Tab -->
            <div id="per-user" class="cb-tab-content" style="display:none;">
                <?php if (!empty($sessions)): ?>
                    <div class="cb-filter-bar">
                        <label for="session-select"><strong>Select Session:</strong></label>
                        <select id="session-select" class="cb-session-select" onchange="window.location.href='?page=camera-battle-results&tab=per-user&session=' + this.value">
                            <?php foreach ($sessions as $session): ?>
                                <option value="<?php echo esc_attr($session['session_id']); ?>" 
                                        <?php selected($selected_session, $session['session_id']); ?>>
                                    <?php echo esc_html($session['session_id']); ?> 
                                    (<?php echo $session['total_votes']; ?> votes, 
                                    <?php echo date('M j, Y', strtotime($session['last_vote'])); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <button class="cb-export-btn" onclick="exportSessionData('<?php echo esc_js($selected_session); ?>')">
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
            var tabContent = document.getElementsByClassName("cb-tab-content");
            for (var i = 0; i < tabContent.length; i++) {
                tabContent[i].style.display = "none";
            }
            
            // Remove active class from all buttons
            var tabButtons = document.getElementsByClassName("cb-tab-btn");
            for (var i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove("active");
            }
            
            // Show current tab and mark button as active
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.classList.add("active");
        }
        
        function exportSessionData(sessionId) {
            window.location.href = '<?php echo admin_url('admin-ajax.php'); ?>?action=cb_export_csv&session=' + sessionId;
        }
        
        // Check if we need to switch to per-user tab on page load
        <?php if (isset($_GET['tab']) && $_GET['tab'] === 'per-user'): ?>
        document.addEventListener('DOMContentLoaded', function() {
            var perUserTab = document.querySelector('.cb-tab-btn:nth-child(2)');
            if (perUserTab) {
                perUserTab.click();
            }
        });
        <?php endif; ?>
        </script>
        <?php
    }
    
    /**
     * Render overall results (all users, all sessions combined)
     */
    private function render_overall_results() {
        global $wpdb;
        
        // Get aggregated results across ALL users and ALL sessions
        $results = $wpdb->get_results(
            "SELECT 
                image_title,
                SUM(clicks) as total_clicks,
                SUM(complete_wins) as total_complete_wins,
                SUM(appearances) as total_appearances,
                ROUND(SUM(clicks) * 100.0 / NULLIF(SUM(appearances), 0), 1) as click_rate
             FROM {$this->summary_table_name}
             GROUP BY image_title
             ORDER BY total_clicks DESC",
            ARRAY_A
        );
        
        if (empty($results)) {
            echo '<div class="cb-no-data">No results yet. Start collecting votes first!</div>';
            return;
        }
        
        ?>
        <h2>Overall Results - All Users & Sessions</h2>
        <p style="color: #666; margin-bottom: 20px;">Aggregated data from all tests ever conducted</p>
        <table class="cb-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Image</th>
                    <th>Total Clicks</th>
                    <th>Complete Wins</th>
                    <th>Appearances</th>
                    <th>Click Rate</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $rank = 1;
                foreach ($results as $row): 
                    $rate = floatval($row['click_rate']);
                    $badge_class = $rate >= 60 ? 'high' : ($rate >= 40 ? 'medium' : 'low');
                ?>
                    <tr>
                        <td><strong>#<?php echo $rank++; ?></strong></td>
                        <td><?php echo esc_html($row['image_title']); ?></td>
                        <td><?php echo number_format($row['total_clicks']); ?></td>
                        <td><?php echo number_format($row['total_complete_wins']); ?></td>
                        <td><?php echo number_format($row['total_appearances']); ?></td>
                        <td><span class="cb-badge <?php echo $badge_class; ?>"><?php echo $row['click_rate']; ?>%</span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }
    
    /**
     * Render per user results
     */
    private function render_per_user_results($session_id) {
        global $wpdb;
        
        if (empty($session_id)) {
            echo '<div class="cb-no-data">No session data available.</div>';
            return;
        }
        
        // Get all unique images for this session
        $images = $wpdb->get_results($wpdb->prepare(
            "SELECT DISTINCT image_title 
             FROM {$this->summary_table_name}
             WHERE session_id = %s
             ORDER BY image_title",
            $session_id
        ), ARRAY_A);
        
        if (empty($images)) {
            echo '<div class="cb-no-data">No user data yet for this session.</div>';
            return;
        }
        
        // Get all user data for this session (each row is a separate test completion)
        $user_results = $wpdb->get_results($wpdb->prepare(
            "SELECT 
                id,
                container_id,
                image_title,
                clicks,
                complete_wins,
                appearances,
                timestamp
             FROM {$this->summary_table_name}
             WHERE session_id = %s
             ORDER BY timestamp DESC",
            $session_id
        ), ARRAY_A);
        
        // Organize data by unique test completion (using id + timestamp as unique identifier)
        $organized_data = array();
        $totals = array();
        
        // Initialize totals
        foreach ($images as $img) {
            $totals[$img['image_title']] = 0;
        }
        
        foreach ($user_results as $row) {
            // Use combination of container_id and timestamp as unique key for each test
            $unique_key = $row['container_id'] . '_' . $row['timestamp'];
            
            if (!isset($organized_data[$unique_key])) {
                $organized_data[$unique_key] = array(
                    'container_id' => $row['container_id'],
                    'timestamp' => $row['timestamp'],
                    'images' => array()
                );
            }
            
            $organized_data[$unique_key]['images'][$row['image_title']] = array(
                'clicks' => $row['clicks'],
                'complete_wins' => $row['complete_wins'],
                'appearances' => $row['appearances']
            );
            
            // Add to totals if this image won
            if ($row['complete_wins'] > 0) {
                $totals[$row['image_title']]++;
            }
        }
        
        ?>
        <h2>Per User Results for Session: <?php echo esc_html($session_id); ?></h2>
        <p style="color: #666; margin-bottom: 20px;">Each row represents one completed test</p>
        
        <table class="cb-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Completed</th>
                    <?php foreach ($images as $img): ?>
                        <th><?php echo esc_html($img['image_title']); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <!-- Totals Row at Top -->
                <tr style="background: #e8f4f8; font-weight: bold;">
                    <td colspan="2"><strong>Total Wins</strong></td>
                    <?php foreach ($images as $img): ?>
                        <td style="text-align: center;">
                            <span style="font-size: 18px; color: #2271b1;">
                                <?php echo $totals[$img['image_title']]; ?>
                            </span>
                        </td>
                    <?php endforeach; ?>
                </tr>
                
                <!-- Individual Test Results -->
                <?php foreach ($organized_data as $unique_key => $data): ?>
                    <tr>
                        <td><?php echo esc_html($data['container_id']); ?></td>
                        <td><?php echo date('M j, Y g:i a', strtotime($data['timestamp'])); ?></td>
                        <?php foreach ($images as $img): ?>
                            <td>
                                <?php 
                                $image_title = $img['image_title'];
                                if (isset($data['images'][$image_title])):
                                    $img_data = $data['images'][$image_title];
                                    $clicks = $img_data['clicks'];
                                    $complete_wins = $img_data['complete_wins'];
                                    $appearances = $img_data['appearances'];
                                    $click_rate = $appearances > 0 ? round(($clicks / $appearances) * 100, 1) : 0;
                                    $badge_class = $click_rate >= 60 ? 'high' : ($click_rate >= 40 ? 'medium' : 'low');
                                ?>
                                    <strong><?php echo $clicks; ?></strong> clicks
                                    <?php if ($complete_wins > 0): ?>
                                        <br><span class="cb-badge high" style="margin-top: 4px;">Winner</span>
                                    <?php endif; ?>
                                    <br><span style="font-size: 11px; color: #666;"><?php echo $click_rate; ?>% rate</span>
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
    
    /**
     * Export session data as CSV
     */
    public function export_csv() {
        global $wpdb;
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $session_id = isset($_GET['session']) ? sanitize_text_field($_GET['session']) : '';
        
        if (empty($session_id)) {
            wp_die('No session specified');
        }
        
        // Get all data for this session
        $raw_data = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE session_id = %s ORDER BY timestamp",
            $session_id
        ), ARRAY_A);
        
        $summary_data = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$this->summary_table_name} WHERE session_id = %s ORDER BY container_id, clicks DESC",
            $session_id
        ), ARRAY_A);
        
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="camera-battle-' . $session_id . '-' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Write summary data
        fputcsv($output, array('SUMMARY DATA'));
        fputcsv($output, array('User', 'Image', 'Clicks', 'Complete Wins', 'Appearances', 'Timestamp'));
        
        foreach ($summary_data as $row) {
            fputcsv($output, array(
                $row['container_id'],
                $row['image_title'],
                $row['clicks'],
                $row['complete_wins'],
                $row['appearances'],
                $row['timestamp']
            ));
        }
        
        // Add separator
        fputcsv($output, array(''));
        fputcsv($output, array(''));
        
        // Write raw vote data
        fputcsv($output, array('RAW VOTE DATA'));
        fputcsv($output, array('Timestamp', 'User', 'Image 1', 'Image 2', 'Winner'));
        
        foreach ($raw_data as $row) {
            fputcsv($output, array(
                $row['timestamp'],
                $row['container_id'],
                $row['image1_title'],
                $row['image2_title'],
                $row['winner_title']
            ));
        }
        
        fclose($output);
        exit;
    }
}

// Initialize the plugin
new CameraBattleSaver();
