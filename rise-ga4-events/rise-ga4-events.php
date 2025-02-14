<?php
/*
Plugin Name: GA4 Automatic Events
Plugin URI: https://RiseSEO.com.au/
Description: Automatically track phone calls, form submissions, and email clicks in GA4
Version: 1.0
Author: Rise
Author URI: https://RiseSEO.com.au/
Text Domain: rise-ga4-events
*/

// Prevent direct access
if (!defined('ABSPATH')) exit;

class RiseGA4Events {
    private $options;
    private $plugin_path;
    private $plugin_url;

    public function __construct() {
        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->plugin_url = plugin_dir_url(__FILE__);
        
        // Initialize
        add_action('init', array($this, 'init'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_menu', array($this, 'add_menu_page'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
        add_action('wp_head', array($this, 'inject_ga4_script'));
        add_action('wp_footer', array($this, 'inject_tracking_code'));
        
        // Add filter for settings sanitization
        add_filter('pre_update_option_rise_ga4_settings', array($this, 'sanitize_settings'), 10, 2);
    }

    public function init() {
        $defaults = array(
            'measurement_id' => '',
            'track_phone' => 'no',
            'track_email' => 'no',
            'track_forms' => 'no',
            'thank_you_page' => '/thank-you/'
        );
        
        $this->options = wp_parse_args(get_option('rise_ga4_settings'), $defaults);
    }

    public function sanitize_settings($new_value, $old_value) {
        // Ensure all our checkbox fields exist
        $new_value['track_phone'] = isset($new_value['track_phone']) ? 'yes' : 'no';
        $new_value['track_email'] = isset($new_value['track_email']) ? 'yes' : 'no';
        $new_value['track_forms'] = isset($new_value['track_forms']) ? 'yes' : 'no';
        
        // Sanitize text fields
        $new_value['measurement_id'] = sanitize_text_field($new_value['measurement_id']);
        $new_value['thank_you_page'] = sanitize_text_field($new_value['thank_you_page']);
        
        return $new_value;
    }

    public function register_settings() {
        register_setting('rise_ga4_settings', 'rise_ga4_settings');
    }

    public function admin_scripts($hook) {
        if ('settings_page_rise-ga4-events' !== $hook) {
            return;
        }

        wp_enqueue_style('rise-ga4-admin', $this->plugin_url . 'assets/css/admin.css', array(), '1.0.0');
        wp_enqueue_style('google-fonts-manrope', 'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap', array(), null);
        wp_enqueue_script('rise-ga4-admin', $this->plugin_url . 'assets/js/admin.js', array('jquery'), '1.0.0', true);
    }

    public function add_menu_page() {
        add_options_page(
            'GA4 Automatic Events', 
            'GA4 Automatic Events', 
            'manage_options', 
            'rise-ga4-events', 
            array($this, 'render_settings_page')
        );
    }

    public function render_settings_page() {
        ?>
        <div class="rise-utm-wrapper">
            <div class="rise-utm-header">
                <div class="rise-utm-header-content">
                    <h1>GA4 Automatic Events</h1>
                    <p class="rise-utm-header-description">Configure your GA4 tracking settings and event preferences.</p>
                </div>
                <img src="https://proclouddevelopment.com.au/test/wp-content/plugins/rise-ga4-events/assets/images/rise-logo.png" alt="Rise Logo" class="rise-utm-logo">
            </div>

            <form method="post" action="options.php">
                <?php
                settings_fields('rise_ga4_settings');
                ?>

                <div class="rise-utm-card">
                    <h2 class="rise-utm-card-title">
                        <span class="dashicons dashicons-analytics"></span>
                        Google Analytics 4 Configuration
                    </h2>
                    <div class="rise-utm-section">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="rise_ga4_measurement_id">GA4 Measurement ID</label>
                                </th>
                                <td>
                                    <input type="text" 
                                           id="rise_ga4_measurement_id"
                                           name="rise_ga4_settings[measurement_id]" 
                                           value="<?php echo esc_attr($this->options['measurement_id']); ?>"
                                           class="regular-text"
                                           placeholder="G-XXXXXXXXXX" />
                                    <p class="description">Enter your Google Analytics 4 Measurement ID</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="rise-utm-card">
                    <h2 class="rise-utm-card-title">
                        <span class="dashicons dashicons-controls-repeat"></span>
                        Event Tracking Settings
                    </h2>
                    <div class="rise-utm-section">
                        <table class="form-table">
                            <tr>
                                <th scope="row">Phone Call Tracking</th>
                                <td>
                                    <label class="rise-utm-toggle">
                                        <input type="checkbox" 
                                               name="rise_ga4_settings[track_phone]" 
                                               value="yes" 
                                               <?php checked($this->options['track_phone'], 'yes'); ?>>
                                        <span class="rise-utm-toggle-slider"></span>
                                        Track clicks on telephone links
                                    </label>
                                    <p class="description">Tracks when visitors click on tel: links</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Email Click Tracking</th>
                                <td>
                                    <label class="rise-utm-toggle">
                                        <input type="checkbox" 
                                               name="rise_ga4_settings[track_email]" 
                                               value="yes" 
                                               <?php checked($this->options['track_email'], 'yes'); ?>>
                                        <span class="rise-utm-toggle-slider"></span>
                                        Track clicks on email links
                                    </label>
                                    <p class="description">Tracks when visitors click on mailto: links</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Form Submission Tracking</th>
                                <td>
                                    <label class="rise-utm-toggle">
                                        <input type="checkbox" 
                                               name="rise_ga4_settings[track_forms]" 
                                               value="yes" 
                                               <?php checked($this->options['track_forms'], 'yes'); ?>>
                                        <span class="rise-utm-toggle-slider"></span>
                                        Track form submissions
                                    </label>
                                    <p class="description">Tracks when visitors submit forms</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="rise_ga4_thank_you">Thank You Page Path</label>
                                </th>
                                <td>
                                    <input type="text" 
                                           id="rise_ga4_thank_you"
                                           name="rise_ga4_settings[thank_you_page]" 
                                           value="<?php echo esc_attr($this->options['thank_you_page']); ?>"
                                           class="regular-text"
                                           placeholder="/thank-you/" />
                                    <p class="description">Enter the path of your thank you page (e.g., /thank-you/)</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="rise-utm-footer">
                    <div class="rise-support-link">
                        <span class="dashicons dashicons-editor-help"></span>
                        Need help? <a href="mailto:Support@riseseo.com.au">Contact Support</a>
                    </div>
                    <?php submit_button('Save Changes', 'rise-submit'); ?>
                </div>
            </form>
        </div>
        <?php
    }

    public function inject_ga4_script() {
        if (empty($this->options['measurement_id'])) return;
        ?>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($this->options['measurement_id']); ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?php echo esc_attr($this->options['measurement_id']); ?>');
        </script>
        <?php
    }

    public function inject_tracking_code() {
        if (empty($this->options['measurement_id'])) return;
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            function getCookie(name) {
                let matches = document.cookie.match(new RegExp(
                    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
                ));
                return matches ? decodeURIComponent(matches[1]) : undefined;
            }

            function setCookie(name, value, days) {
                let date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                document.cookie = name + "=" + value + "; expires=" + date.toUTCString() + "; path=/";
            }

            <?php if ($this->options['track_phone'] === 'yes'): ?>
            // Track phone calls
            document.querySelectorAll('a[href^="tel:"]').forEach(link => {
                link.addEventListener('click', function(e) {
                    const phoneNumber = this.getAttribute('href').replace('tel:', '');
                    const cookieName = 'rise_phone_' + phoneNumber.replace(/\D/g, '');
                    
                    if (!getCookie(cookieName)) {
                        gtag('event', 'phone_call', {
                            'event_category': 'Lead',
                            'event_label': phoneNumber,
                            'value': 1
                        });
                        setCookie(cookieName, 'true', 30);
                    }
                });
            });
            <?php endif; ?>

            <?php if ($this->options['track_email'] === 'yes'): ?>
            // Track email clicks
            document.querySelectorAll('a[href^="mailto:"]').forEach(link => {
                link.addEventListener('click', function(e) {
                    const email = this.getAttribute('href').replace('mailto:', '');
                    const cookieName = 'rise_email_' + btoa(email);
                    
                    if (!getCookie(cookieName)) {
                        gtag('event', 'email_click', {
                            'event_category': 'Lead',
                            'event_label': email,
                            'value': 1
                        });
                        setCookie(cookieName, 'true', 30);
                    }
                });
            });
            <?php endif; ?>

            <?php if ($this->options['track_forms'] === 'yes'): ?>
            // Track form submissions via thank you page
            if (window.location.pathname.includes('<?php echo esc_js($this->options['thank_you_page']); ?>')) {
                const cookieName = 'rise_form_submission';
                
                if (!getCookie(cookieName)) {
                    gtag('event', 'form_submission', {
                        'event_category': 'Lead',
                        'event_label': document.title,
                        'page_location': window.location.href,
                        'value': 1
                    });
                    setCookie(cookieName, 'true', 30);
                }
            }
            <?php endif; ?>
        });
        </script>
        <?php
    }
}

// Initialize the plugin
new RiseGA4Events();

// Activation hook
register_activation_hook(__FILE__, function() {
    // Add default options if they don't exist
    if (!get_option('rise_ga4_settings')) {
        add_option('rise_ga4_settings', array(
            'measurement_id' => '',
            'track_phone' => 'no',
            'track_email' => 'no',
            'track_forms' => 'no',
            'thank_you_page' => '/thank-you/'
        ));
    }
});