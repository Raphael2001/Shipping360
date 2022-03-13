<?php
class Shipping360_WC_Settings_Tab
{
    // $BASE = "https://shipping360-cukp6uab4a-uc.a.run.app/api/v1";

    /* Bootstraps the class and hooks required actions & filters.
     *
     */
    public static function init()
    {
        add_filter('woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50);
        add_action('woocommerce_settings_tabs_settings_tab_shipping360', __CLASS__ . '::settings_tab');
        add_action('woocommerce_update_options_settings_tab_shipping360', __CLASS__ . '::update_settings');
    }
    
    
    /* Add a new settings tab to the WooCommerce settings tabs array.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
    public static function add_settings_tab($settings_tabs)
    {
        $settings_tabs['settings_tab_shipping360'] = __('Shipping 360', 'woocommerce-settings-tab-shipping360');

        return $settings_tabs;
    }


    /* Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
     *
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
    public static function settings_tab()
    {
        if (isset($_POST['token'])) {
            self::check_if_valid_shipping360($_POST['token']);
        }
        woocommerce_admin_fields(self::get_settings());
    }

    /* Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     *
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */
    public static function update_settings()
    {
        woocommerce_update_options(self::get_settings());
    }

    private static function check_if_valid_shipping360($code)
    {
        if (isset($code)) {
            $url = "https://shipping360-cukp6uab4a-uc.a.run.app/api/v1"."/token?token=$code";
            
            $response= self::basic_get_with_curl_shipping360($url);
            $result = json_decode($response);
            $result = $result->body;
            if ($result == false) {
                ?>
                <br/><p style="color:red;">לא הצלחנו למצוא את הטוקן, אנא בדקו ונסו שוב<p>
                <?php
                update_option('token', "");
            } else {
                update_option('mail', $result->email);
                update_option('token', $result->uid);
                update_option('need_reload', "true");
            }
        }
    }

    public static function basic_get_with_curl_shipping360($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array('Content-Type: application/json; UTF-8')
        );
        $result = curl_exec($curl);

        curl_close($curl);
        return $result;
    }
  

    /* Get all the settings for this plugin for @see woocommerce_admin_fields() function.
     *
     * @return array Array of settings for @see woocommerce_admin_fields() function.
     */
    public static function get_settings()
    {
        $settings = array(
            'התחברות למערכת' => array(
                'name'     => __('התחברות למערכת', 'woocommerce-settings-tab-shipping360'),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_settings_tab_shipping360_section_title'
            ),
            'טוקן' => array(
                'name' => __('טוקן', 'woocommerce-settings-tab-shipping360'),
                'type' => 'text',
                'desc' => __('טוקן מהמערכת', 'woocommerce-settings-tab-shipping360'),
                'id'   => 'token'
            ),

            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_settings_tab_shipping360_section_end'
            )
        ); ?>
        <br/><p style="color:green; font-size:16px"> לפרטים נוספים והטמעה אנא התקשרו למספר 0548172972 ,Flexible Checkout Fields by WP Desk הערה: תוסף זה מתחבר עם תוסף <p>

        <?php

        return apply_filters('wc_settings_tab_shipping360_settings', $settings);
    }
}

Shipping360_WC_Settings_Tab::init();
