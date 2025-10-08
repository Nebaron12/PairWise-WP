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

        // Table for individual results
        $table_results = $wpdb->prefix . 'pairwise_results';
        $sql_results = "CREATE TABLE IF NOT EXISTS $table_results (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            session_id varchar(100) NOT NULL,
            image_name varchar(255) NOT NULL,
            clicks int(11) NOT NULL DEFAULT 0,
            complete_wins int(11) NOT NULL DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY session_id (session_id)
        ) $charset_collate;";

        // Table for summary data
        $table_summary = $wpdb->prefix . 'pairwise_summary';
        $sql_summary = "CREATE TABLE IF NOT EXISTS $table_summary (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            session_id varchar(100) NOT NULL,
            session_data text NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY session_id (session_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_results);
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
        $session_id = sanitize_text_field($params['sessionId']);
        $results = $params['results'];

        $table_results = $wpdb->prefix . 'pairwise_results';
        $table_summary = $wpdb->prefix . 'pairwise_summary';

        // Save individual results
        foreach ($results as $result) {
            $wpdb->insert(
                $table_results,
                array(
                    'session_id' => $session_id,
                    'image_name' => sanitize_text_field($result['name']),
                    'clicks' => intval($result['clicks']),
                    'complete_wins' => intval($result['completeWins'])
                )
            );
        }

        // Save summary data
        $wpdb->insert(
            $table_summary,
            array(
                'session_id' => $session_id,
                'session_data' => wp_json_encode($results)
            )
        );

        return new WP_REST_Response(array('success' => true), 200);
    }
    
    /**
     * Get results from database
     */
    public function get_results($request) {
        global $wpdb;
        
        $session_id = $request->get_param('session_id');
        $table_results = $wpdb->prefix . 'pairwise_results';

        if ($session_id) {
            $results = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_results WHERE session_id = %s ORDER BY complete_wins DESC, clicks DESC",
                $session_id
            ));
        } else {
            $results = $wpdb->get_results("SELECT * FROM $table_results ORDER BY created_at DESC");
        }

        return new WP_REST_Response($results, 200);
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

        wp_enqueue_style('pairwise-battler-admin', false);
        wp_add_inline_style('pairwise-battler-admin', '
            .pw-admin-wrap { max-width: 1200px; margin: 20px; }
            .pw-tabs { border-bottom: 1px solid #ccc; margin-bottom: 20px; }
            .pw-tab { display: inline-block; padding: 10px 20px; cursor: pointer; background: #f0f0f0; border: 1px solid #ccc; border-bottom: none; margin-right: 5px; }
            .pw-tab.active { background: white; font-weight: bold; }
            .pw-tab-content { display: none; }
            .pw-tab-content.active { display: block; }
            .pw-results-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            .pw-results-table th, .pw-results-table td { padding: 12px; text-align: left; border: 1px solid #ddd; }
            .pw-results-table th { background: #f5f5f5; font-weight: bold; }
            .pw-results-table tr:hover { background: #f9f9f9; }
            .pw-rank { font-weight: bold; color: #0073aa; }
            .pw-winner { background: #d4edda; }
            .pw-export-btn { margin: 10px 0; padding: 8px 15px; background: #0073aa; color: white; border: none; cursor: pointer; border-radius: 3px; }
            .pw-export-btn:hover { background: #005a87; }
            .pw-session-filter { margin: 10px 0; padding: 8px; border: 1px solid #ddd; border-radius: 3px; }
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

        // Get all sessions
        $sessions = $wpdb->get_col("SELECT DISTINCT session_id FROM $table_results ORDER BY created_at DESC");

        ?>
        <div class="pw-admin-wrap">
            <h1>PairWise Battler Results</h1>
            
            <div class="pw-tabs">
                <div class="pw-tab active" data-tab="overall">Overall Results</div>
                <div class="pw-tab" data-tab="per-user">Per-User Results</div>
            </div>

            <div id="overall" class="pw-tab-content active">
                <?php $this->render_overall_results(); ?>
            </div>

            <div id="per-user" class="pw-tab-content">
                <?php $this->render_per_user_results($sessions); ?>
            </div>

            <script>
                document.querySelectorAll('.pw-tab').forEach(tab => {
                    tab.addEventListener('click', function() {
                        document.querySelectorAll('.pw-tab').forEach(t => t.classList.remove('active'));
                        document.querySelectorAll('.pw-tab-content').forEach(c => c.classList.remove('active'));
                        this.classList.add('active');
                        document.getElementById(this.dataset.tab).classList.add('active');
                    });
                });

                function filterPerUserResults() {
                    const session = document.getElementById('session-filter').value;
                    if (session) {
                        window.location.href = '?page=pairwise-battler&filter_session=' + session;
                    } else {
                        window.location.href = '?page=pairwise-battler';
                    }
                }
            </script>
        </div>
        <?php
    }
    
    /**
     * Render overall results
     */
    private function render_overall_results() {
        global $wpdb;
        $table_results = $wpdb->prefix . 'pairwise_results';

        $results = $wpdb->get_results(
            "SELECT image_name, SUM(clicks) as total_clicks, SUM(complete_wins) as total_wins 
            FROM $table_results 
            GROUP BY image_name 
            ORDER BY total_wins DESC, total_clicks DESC"
        );

        echo '<button class="pw-export-btn" onclick="window.location.href=\'?page=pairwise-battler&export_all=csv\'">Export All Data (CSV)</button>';

        if (empty($results)) {
            echo '<p>No results yet.</p>';
            return;
        }

        echo '<table class="pw-results-table">';
        echo '<thead><tr><th>Rank</th><th>Image</th><th>Complete Wins</th><th>Total Clicks</th><th>Click Rate</th></tr></thead>';
        echo '<tbody>';

        $rank = 1;
        foreach ($results as $result) {
            $click_rate = $result->total_clicks > 0 ? round(($result->total_wins / $result->total_clicks) * 100, 1) : 0;
            $row_class = $rank === 1 ? 'pw-winner' : '';
            
            echo "<tr class='$row_class'>";
            echo "<td class='pw-rank'>#$rank</td>";
            echo "<td>" . esc_html($result->image_name) . "</td>";
            echo "<td>" . esc_html($result->total_wins) . "</td>";
            echo "<td>" . esc_html($result->total_clicks) . "</td>";
            echo "<td>" . esc_html($click_rate) . "%</td>";
            echo "</tr>";
            
            $rank++;
        }

        echo '</tbody></table>';
    }
    
    /**
     * Render per-user results
     */
    private function render_per_user_results($sessions) {
        global $wpdb;
        $table_results = $wpdb->prefix . 'pairwise_results';

        $selected_session = isset($_GET['filter_session']) ? sanitize_text_field($_GET['filter_session']) : '';

        echo '<label for="session-filter">Filter by Session: </label>';
        echo '<select id="session-filter" class="pw-session-filter" onchange="filterPerUserResults()">';
        echo '<option value="">-- All Sessions --</option>';
        foreach ($sessions as $session) {
            $selected = $selected_session === $session ? 'selected' : '';
            echo "<option value='" . esc_attr($session) . "' $selected>" . esc_html($session) . "</option>";
        }
        echo '</select>';

        if ($selected_session) {
            echo '<button class="pw-export-btn" onclick="window.location.href=\'?page=pairwise-battler&export=csv&session=' . esc_attr($selected_session) . '\'">Export This Session (CSV)</button>';
        }

        // Get results based on filter
        if ($selected_session) {
            $results = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_results WHERE session_id = %s ORDER BY session_id, complete_wins DESC, clicks DESC",
                $selected_session
            ));
        } else {
            $results = $wpdb->get_results(
                "SELECT * FROM $table_results ORDER BY session_id, complete_wins DESC, clicks DESC"
            );
        }

        if (empty($results)) {
            echo '<p>No results found.</p>';
            return;
        }

        // Organize data by session
        $organized_data = array();
        $all_images = array();

        foreach ($results as $result) {
            if (!isset($organized_data[$result->session_id])) {
                $organized_data[$result->session_id] = array();
            }
            $organized_data[$result->session_id][$result->image_name] = array(
                'clicks' => $result->clicks,
                'wins' => $result->complete_wins
            );
            if (!in_array($result->image_name, $all_images)) {
                $all_images[] = $result->image_name;
            }
        }

        // Calculate totals
        $totals = array();
        foreach ($all_images as $image) {
            $totals[$image] = array('clicks' => 0, 'wins' => 0);
        }

        foreach ($organized_data as $session_data) {
            foreach ($session_data as $image => $data) {
                $totals[$image]['clicks'] += $data['clicks'];
                $totals[$image]['wins'] += $data['wins'];
            }
        }

        // Display table
        echo '<table class="pw-results-table">';
        echo '<thead><tr><th>Result #</th>';
        foreach ($all_images as $image) {
            echo '<th>' . esc_html($image) . '</th>';
        }
        echo '</tr></thead><tbody>';

        $result_number = count($organized_data);
        foreach ($organized_data as $session_id => $session_data) {
            echo '<tr>';
            echo '<td><strong>Result ' . $result_number . '</strong><br><small>' . esc_html($session_id) . '</small></td>';
            
            foreach ($all_images as $image) {
                if (isset($session_data[$image])) {
                    $data = $session_data[$image];
                    echo '<td>' . $data['wins'] . ' wins<br>' . $data['clicks'] . ' clicks</td>';
                } else {
                    echo '<td>-</td>';
                }
            }
            
            echo '</tr>';
            $result_number--;
        }

        // Totals row
        echo '<tr class="pw-totals-row">';
        echo '<td>TOTALS</td>';
        foreach ($all_images as $image) {
            echo '<td>' . $totals[$image]['wins'] . ' wins<br>' . $totals[$image]['clicks'] . ' clicks</td>';
        }
        echo '</tr>';

        echo '</tbody></table>';
    }
    
    /**
     * Export session to CSV
     */
    private function export_session_csv($session_id) {
        global $wpdb;
        $table_results = $wpdb->prefix . 'pairwise_results';

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_results WHERE session_id = %s ORDER BY complete_wins DESC, clicks DESC",
            $session_id
        ));

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="pairwise-results-' . $session_id . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, array('Image Name', 'Clicks', 'Complete Wins'));

        foreach ($results as $result) {
            fputcsv($output, array($result->image_name, $result->clicks, $result->complete_wins));
        }

        fclose($output);
    }
    
    /**
     * Export all data to CSV
     */
    private function export_all_csv() {
        global $wpdb;
        $table_results = $wpdb->prefix . 'pairwise_results';

        $results = $wpdb->get_results("SELECT * FROM $table_results ORDER BY session_id, complete_wins DESC, clicks DESC");

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
