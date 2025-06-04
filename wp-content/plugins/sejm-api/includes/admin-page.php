<?php
/**
 * Admin page for MP importer
 */

class MP_Admin {
    /**
     * Initialize the admin page
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
        add_action('admin_init', array(__CLASS__, 'handle_import'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));
        
        // Add AJAX endpoints
        add_action('wp_ajax_mp_check_import_status', array(__CLASS__, 'ajax_check_import_status'));
        add_action('wp_ajax_mp_stop_import', array(__CLASS__, 'ajax_stop_import'));
        add_action('wp_ajax_mp_continue_import', array(__CLASS__, 'ajax_continue_import'));
        add_action('wp_ajax_mp_emergency_stop', array(__CLASS__, 'ajax_emergency_stop'));
        add_action('wp_ajax_mp_process_next_batch', array(__CLASS__, 'ajax_process_next_batch'));
    }
    
    /**
     * Enqueue scripts and styles
     */
    public static function enqueue_scripts($hook) {
        if ('mp_page_mp-import' !== $hook) {
            return;
        }
        
        // Add version to files to avoid caching
        $version = time();
        
        wp_enqueue_script('mp-admin-js', plugins_url('/assets/js/admin.js', dirname(__FILE__)), array('jquery'), $version, true);
        wp_localize_script('mp-admin-js', 'mp_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mp_admin_nonce'),
            'import_url' => admin_url('edit.php?post_type=mp&page=mp-import'),
        ));
        
        wp_enqueue_style('mp-admin-css', plugins_url('/assets/css/admin.css', dirname(__FILE__)), array(), $version);
    }
    
    /**
     * Add admin menu item
     */
    public static function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=mp',
            'Import posłów',
            'Import posłów',
            'manage_options',
            'mp-import',
            array(__CLASS__, 'render_admin_page')
        );
    }
    
    /**
     * Render the admin page
     */
    public static function render_admin_page() {
        $import_status = MP_API_Handler::get_import_status();
        $is_import_running = self::check_import_running(); // Check if import is actually running
        
        // Ensure all required keys exist with default values
        $import_status = wp_parse_args($import_status, array(
            'status' => 'not_running',
            'imported' => 0,
            'total' => 0,
            'current' => 0
        ));
        
        // FIXED: Ensure values are numbers
        $import_status['imported'] = intval($import_status['imported']);
        $import_status['total'] = intval($import_status['total']);
        $import_status['current'] = intval($import_status['current']);
        
        if ($is_import_running && $import_status['status'] !== 'running') {
            // If an active import is detected that is not tracked by status
            $import_status['status'] = 'running';
            $import_status['imported'] = 0;
            $import_status['total'] = 0;
            $import_status['current'] = 0;
        }
        ?>
        <div class="wrap">
            <h1>Importuj członków parlamentu</h1>
            
            <?php if ($is_import_running): ?>
            <div class="notice notice-warning" id="mp-emergency-actions">
                <p>
                    <strong>Wykryto trwający proces importu!</strong> 
                    Jeśli masz problemy z importem użyj tego przycisku, aby zatrzymać wszystkie trwające procesy importu.
                </p>
                <form method="post" action="">
                    <?php wp_nonce_field('mp_emergency_stop_action', 'mp_emergency_stop_nonce'); ?>
                    <p><input type="hidden" name="action" value="emergency_stop">
                    <button type="submit" id="mp-emergency-stop" class="button button-secondary">Zatrzymaj wszystkie importy</button></p>
                </form>
            </div>
            <?php endif; ?>
            
            <div class="card mp-import-card">
                <h2><span class="dashicons dashicons-database-import"></span> Import z Sejm API</h2>
                <p>Spowoduje to zaimportowanie wszystkich aktualnych członków parlamentu <br>z API Sejmu (<a href="https://api.sejm.gov.pl/" target="_blank">https://api.sejm.gov.pl/</a>).</p>
                <p>API Sejmu zwraca podstawowe dane posłów z aktualnej kadencji, w tym: imię, nazwisko, okręg, przynależność klubową oraz zdjęcie. Import nie nadpisuje pola biografii, które należy uzupełnić ręcznie.</p>
                
                <div id="mp-import-status" class="<?php echo ($import_status['status'] === 'running') ? 'is-active' : ''; ?>">
                    <?php if ($import_status['status'] === 'running') : ?>
                        <div class="mp-progress-wrapper">
                            <div class="mp-progress-bar">
                                <?php 
                                $progress_percent = 0;
                                if ($import_status['total'] > 0 && is_numeric($import_status['total']) && 
                                    is_numeric($import_status['current'])) {
                                    $progress_percent = min(100, round(($import_status['current'] / $import_status['total']) * 100));
                                }
                                ?>
                                <div class="mp-progress-bar-inner" style="width: <?php echo esc_attr($progress_percent); ?>%"></div>
                            </div>
                            <div class="mp-progress-text">
                                Postęp: zaimportowano <strong><span id="mp-imported"><?php echo esc_html($import_status['imported']); ?></span></strong> z <span id="mp-total"><?php echo esc_html($import_status['total']); ?></span> posłów
                            </div>
                        </div>
                        
                        <div class="mp-actions">
                            <button id="mp-stop-import" class="button button-secondary">Zatrzymaj import</button>
                        </div>
                    <?php else : ?>
                        <div class="mp-actions">
                            <form method="post" action="" id="mp-import-form">
                                <?php wp_nonce_field('mp_import_action', 'mp_import_nonce'); ?>
                                <input type="hidden" name="action" value="import_mps">
                                <button type="submit" class="button button-primary">Importuj posłów</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div id="mp-import-completed" class="mp-notice mp-notice-success p-reset" style="display: none;">
                    <p>Import zakończony pomyślnie! Zaimportowano <span id="mp-completed-count">0</span> posłów.</p>
                </div>
                
                <div id="mp-import-stopped" class="mp-notice mp-notice-warning" style="display: none;">
                    <p>Import zatrzymany. Dotychczas zaimportowano <span id="mp-stopped-count">0</span> posłów.</p>
                    <button id="mp-continue-import" class="button">Kontunuuj import</button>
                </div>
                
                <div id="mp-import-error" class="mp-notice mp-notice-error" style="display: none;">
                    <p>Błąd podczas importowania: <span id="mp-error-message"></span></p>
                </div>
            </div>
            
            <?php if (isset($_GET['api_test']) && $_GET['api_test'] === 'complete') : ?>
                <div class="notice notice-info">
                    <p>Test API zakończony. Sprawdź dziennik błędów, aby uzyskać szczegóły.</p>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <h2><span class="dashicons dashicons-cloud-saved"></span> Testowanie API</h2>
                <p>Użyj tego przycisku, aby przetestować połączenie z API Sejmu.</p>
                
                <form method="post" action="">
                    <?php wp_nonce_field('mp_test_api_action', 'mp_test_api_nonce'); ?>
                    <input type="hidden" name="action" value="test_api">
                    <p>
                        <button type="submit" class="button">Testuj połączenie z API</button>
                    </p>
                </form>
            </div>
        </div>
        <?php
    }
    
    /**
     * Check if import is actually running (by checking WP Cron processes)
     */
    private static function check_import_running() {
        global $wpdb;
        
        // Check lock option
        $lock_time = get_option('mp_import_lock', 0);
        if ($lock_time > 0 && (time() - $lock_time) < 3600) { // 1 hour
            return true;
        }
        
        // Check entries in debug.log
        $log_file = WP_CONTENT_DIR . '/debug.log';
        if (file_exists($log_file)) {
            $log_content = file_get_contents($log_file);
            $last_5_min = date('d-M-Y H:i', time() - 300); // 5 minutes ago
            
            if (strpos($log_content, 'MP Plugin: Fetching MP details') !== false && 
                strpos($log_content, $last_5_min) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * AJAX handler to check import status
     */
    public static function ajax_check_import_status() {
        check_ajax_referer('mp_admin_nonce', 'nonce');
        
        $status = MP_API_Handler::get_import_status();
        
        // Ensure all required keys exist with default values
        $status = wp_parse_args($status, array(
            'status' => 'not_running',
            'imported' => 0,
            'total' => 0,
            'current' => 0
        ));
        
        // FIXED: Ensure values are numbers
        $status['imported'] = intval($status['imported']);
        $status['total'] = intval($status['total']);
        $status['current'] = intval($status['current']);
        
        // Check if we actually have import data
        if ($status['status'] !== 'not_running' && $status['total'] === 0) {
            // Check if we have data saved in options
            $all_mps = get_option('mp_all_mps', null);
            if ($all_mps !== null) {
                $status['total'] = count($all_mps);
            }
        }
        
        // Add logs
        error_log('MP Plugin: Status check requested. Status: ' . $status['status'] . 
                  ', Imported: ' . $status['imported'] .
                  ', Total: ' . $status['total'] .
                  ', Current: ' . $status['current']);
        
        // If status is "running", but there's no current data in get_import_status
        // try to check the last activity in logs
        if (($status['status'] === 'running' || $status['status'] === 'in_progress') && self::check_import_running()) {
            // Update status based on the last log entry
            $status = self::update_status_from_logs($status);
        }
        
        wp_send_json($status);
    }

    /**
     * Update status from logs
     */
    private static function update_status_from_logs($status) {
        $log_file = WP_CONTENT_DIR . '/debug.log';
        if (file_exists($log_file)) {
            $log_content = file_get_contents($log_file);
            
            // FIXED: Look for the last import progress entry
            preg_match_all('/MP Plugin: Import progress - imported (\d+) of (\d+) MPs/', $log_content, $matches);
            
            if (!empty($matches[1]) && !empty($matches[2])) {
                $last_imported = end($matches[1]);
                $total = end($matches[2]);
                
                $status['imported'] = $last_imported;
                $status['total'] = $total;
                $status['current'] = $last_imported; // Use imported as current, because this is our progress
                
                error_log('MP Plugin: Updated status from logs. Imported: ' . $last_imported . ' of ' . $total);
            }
        }
        
        return $status;
    }
    
    /**
     * AJAX handler to stop import
     */
    public static function ajax_stop_import() {
        check_ajax_referer('mp_admin_nonce', 'nonce');
        
        $result = MP_API_Handler::stop_import();
        wp_send_json(array(
            'success' => $result,
            'status' => MP_API_Handler::get_import_status()
        ));
    }
    
    /**
     * AJAX handler to continue import
     */
    public static function ajax_continue_import() {
        check_ajax_referer('mp_admin_nonce', 'nonce');
        
        // Remove stop flag
        delete_option('mp_import_stop');
        update_option('mp_import_progress', 'running');
        
        $result = self::process_import_batch();
        wp_send_json($result);
    }
    
    /**
     * AJAX handler to emergency stop all imports
     */
    public static function ajax_emergency_stop() {
        check_ajax_referer('mp_admin_nonce', 'nonce');
        
        self::emergency_stop_all_imports();
        
        wp_send_json(array(
            'success' => true,
            'message' => 'All import processes have been stopped'
        ));
    }
    
    /**
     * AJAX handler to process next batch
     */
    public static function ajax_process_next_batch() {
        check_ajax_referer('mp_admin_nonce', 'nonce');
        
        $result = self::process_import_batch();
        wp_send_json($result);
    }
    
    /**
     * Emergency stop all imports
     */
    private static function emergency_stop_all_imports() {
        // 1. Set stop flag
        update_option('mp_import_stop', true);
        
        // 2. Remove import lock
        delete_option('mp_import_lock');
        
        // 3. Reset all import-related options
        MP_API_Handler::reset_import();
        
        // 4. Create marker file that import processes check
        $marker_file = WP_CONTENT_DIR . '/uploads/mp_stop_import';
        @file_put_contents($marker_file, 'stop');
        
        return true;
    }
    
    /**
     * Handle import action
     */
    public static function handle_import() {
        // Handle emergency stop
        if (isset($_POST['action']) && $_POST['action'] === 'emergency_stop') {
            if (!isset($_POST['mp_emergency_stop_nonce']) || !wp_verify_nonce($_POST['mp_emergency_stop_nonce'], 'mp_emergency_stop_action')) {
                wp_die('Security check failed');
            }
            
            if (!current_user_can('manage_options')) {
                wp_die('You do not have sufficient permissions');
            }
            
            self::emergency_stop_all_imports();
            
            wp_redirect(admin_url('edit.php?post_type=mp&page=mp-import&emergency_stop=complete'));
            exit;
        }
        
        // Handle API test
        if (isset($_POST['action']) && $_POST['action'] === 'test_api') {
            if (!isset($_POST['mp_test_api_nonce']) || !wp_verify_nonce($_POST['mp_test_api_nonce'], 'mp_test_api_action')) {
                wp_die('Security check failed');
            }
            
            if (!current_user_can('manage_options')) {
                wp_die('You do not have sufficient permissions');
            }
            
            error_log('MP Plugin: Testing API connection...');
            
            // Test all possible URLs for term endpoint
            $test_urls = [
                MP_API_Handler::API_BASE_URL . '/term',
                MP_API_Handler::API_BASE_URL . '/terms',
                MP_API_Handler::API_BASE_URL . '/sejm/term',
                MP_API_Handler::API_BASE_URL . '/sejm/terms'
            ];
            
            foreach ($test_urls as $url) {
                error_log('MP Plugin API Test: Testing URL ' . $url);
                $term_response = wp_remote_get($url);
                if (is_wp_error($term_response)) {
                    error_log('MP Plugin API Test Error: ' . $term_response->get_error_message());
                } else {
                    $term_status = wp_remote_retrieve_response_code($term_response);
                    $term_body = wp_remote_retrieve_body($term_response);
                    error_log('MP Plugin API Test: URL ' . $url . ' returned status ' . $term_status);
                    if ($term_status === 200) {
                        error_log('MP Plugin API Test: Response ' . $term_body);
                    }
                }
            }
            
            // Get current term
            $term = MP_API_Handler::get_current_term();
            if ($term) {
                error_log('MP Plugin API Test: Current term is ' . $term);
                
                // Test different URL variants for fetching MPs
                $mp_urls = [
                    MP_API_Handler::API_BASE_URL . '/term/' . $term . '/MP',
                    MP_API_Handler::API_BASE_URL . '/term/' . $term . '/mp',
                    MP_API_Handler::API_BASE_URL . '/sejm/term/' . $term . '/MP',
                    MP_API_Handler::API_BASE_URL . '/sejm/term/' . $term . '/mp'
                ];
                
                foreach ($mp_urls as $url) {
                    error_log('MP Plugin API Test: Testing URL ' . $url);
                    $mp_response = wp_remote_get($url);
                    if (is_wp_error($mp_response)) {
                        error_log('MP Plugin API Test Error: ' . $mp_response->get_error_message());
                    } else {
                        $mp_status = wp_remote_retrieve_response_code($mp_response);
                        error_log('MP Plugin API Test: URL ' . $url . ' returned status ' . $mp_status);
                        if ($mp_status === 200) {
                            $mp_body = wp_remote_retrieve_body($mp_response);
                            error_log('MP Plugin API Test: Response (first 500 chars) ' . substr($mp_body, 0, 500));
                        }
                    }
                }
            } else {
                error_log('MP Plugin API Test: Could not determine current term');
            }
            
            wp_redirect(admin_url('edit.php?post_type=mp&page=mp-import&api_test=complete'));
            exit;
        }
        
        // Handle import
        if (!isset($_POST['action']) || $_POST['action'] !== 'import_mps') {
            return;
        }
        
        if (!isset($_POST['mp_import_nonce']) || !wp_verify_nonce($_POST['mp_import_nonce'], 'mp_import_action')) {
            wp_die('Security check failed');
        }
        
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions');
        }
        
        // Check if import is already running
        if (self::check_import_running()) {
            wp_redirect(admin_url('edit.php?post_type=mp&page=mp-import&error=' . urlencode('Import is already running. Stop the previous process first.')));
            exit;
        }
        
        // Set import lock
        update_option('mp_import_lock', time());
        
        // Remove stop marker
        $marker_file = WP_CONTENT_DIR . '/uploads/mp_stop_import';
        if (file_exists($marker_file)) {
            @unlink($marker_file);
        }
        
        // Reset any previous import data
        MP_API_Handler::reset_import();
        
        // Start the import process
        $result = self::process_import_batch();
        
        if (is_wp_error($result)) {
            wp_redirect(admin_url('edit.php?post_type=mp&page=mp-import&error=' . urlencode($result->get_error_message())));
            exit;
        }
        
        wp_redirect(admin_url('edit.php?post_type=mp&page=mp-import'));
        exit;
    }
    
    /**
     * Process a batch of MPs
     */
    private static function process_import_batch() {
        $result = MP_API_Handler::import_mps_with_progress();
        
        if (is_wp_error($result)) {
            return $result;
        }
        
        return $result;
    }
}
