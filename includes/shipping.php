<?php


if (!defined('WPINC')) {
    die;
}

/*
* Check if WooCommerce is active
*/
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    function wdshipping_shipping_method()
    {
        if (!class_exists('WDShipping_Shipping_Method')) {
            class WDShipping_Shipping_Method extends WC_Shipping_Method
            {
                /**
                 * Constructor for your shipping class
                 *
                 * @access public
                 * @return void
                 */
                public function __construct()
                {
                    $this->id                 = 'wdshipping';
                    $this->method_title       = __('WD Shipping', 'wdshipping');
                    $this->method_description = __('Custom Shipping Method for WDShipping', 'wdshipping');

                    $this->init();

                    $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
                    $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('WDShipping Shipping', 'wdshipping');
                }

                /**
                 * Init your settings
                 *
                 * @access public
                 * @return void
                 */
                public function init()
                {
                    // Load the settings API
                    $this->init_form_fields();
                    $this->init_settings();

                    // Save settings in admin if you have any defined
                    add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
                }

                /**
                 * Define settings field for this shipping
                 * @return void
                 */
                public function init_form_fields()
                {
                    $this->form_fields = array(

                        'enabled' => array(
                            'title' => __('Enable', 'wdshipping'),
                            'type' => 'checkbox',
                            'description' => __('Enable this shipping.', 'wdshipping'),
                            'default' => 'yes'
                        ),

                        'title' => array(
                            'title' => __('Title', 'wdshipping'),
                            'type' => 'text',
                            'description' => __('Title to be display on site', 'wdshipping'),
                            'default' => __('WDShipping Shipping', 'wdshipping')
                        )
                    );
                }

                /**
                 * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters.
                 *
                 * @access public
                 * @param mixed $package
                 * @return void
                 */
                public function calculate_shipping($package = array())
                {
                    $distance_arr = array(30, 45, 60, 80, 100, 120, 140, 160, 180, 200, 230, 260, 300, 350, 400);
                    $options = array(
                        1 => array(95, 105, 120, 125, 135, 145, 175, 200, 215, 235, 260, 290, 330, 335, 370),
                        2 => array(95, 105, 120, 130, 145, 155, 190, 215, 230, 245, 275, 315, 350, 370, 390),
                        3 => array(100, 110, 120, 135, 155, 170, 200, 230, 235, 260, 285, 330, 370, 390, 440),
                        4 => array(100, 110, 125, 145, 160, 190, 205, 235, 250, 280, 305, 345, 395, 430, 475),
                        5 => array(100, 110, 125, 145, 160, 190, 215, 240, 260, 290, 330, 355, 400, 460, 515),
                        6 => array(105, 120, 125, 155, 175, 200, 230, 250, 275, 305, 340, 370, 415, 475, 520),
                        7 => array(120, 135, 150, 170, 195, 210, 240, 265, 290, 330, 350, 395, 440, 490, 535),
                        8 => array(135, 145, 155, 195, 220, 235, 250, 275, 300, 340, 370, 400, 450, 500, 550),
                        9 => array(140, 150, 185, 205, 235, 250, 280, 300, 320, 350, 385, 410, 460, 510, 560),
                        10 => array(140, 170, 190, 210, 250, 260, 285, 315, 340, 360, 395, 435, 475, 520, 570),
                        11 => array(145, 185, 200, 225, 260, 275, 305, 340, 355, 385, 400, 440, 490, 535, 580),
                        12 => array(155, 185, 210, 240, 270, 295, 330, 345, 360, 395, 415, 450, 500, 550, 590),
                        13 => array(155, 190, 220, 260, 300, 315, 340, 375, 390, 420, 440, 475, 520, 565, 620),
                        14 => array(160, 205, 235, 285, 315, 330, 350, 385, 395, 435, 450, 490, 535, 580, 625),
                        15 => array(170, 210, 250, 290, 330, 360, 375, 400, 415, 455, 475, 510, 560, 600, 650),
                        16 => array(190, 225, 260, 300, 340, 380, 430, 435, 445, 480, 500, 535, 580, 625, 670),
                        17 => array(200, 230, 275, 315, 350, 385, 430, 455, 465, 490, 520, 560, 600, 650, 695),
                        18 => array(210, 240, 285, 325, 350, 395, 435, 465, 485, 500, 540, 580, 625, 665, 720),
                        19 => array(220, 245, 285, 335, 360, 405, 440, 475, 490, 520, 560, 590, 640, 685, 730),
                        20 => array(220, 250, 285, 340, 360, 405, 445, 480, 500, 540, 565, 605, 650, 695, 750),
                        21 => array(220, 250, 295, 350, 370, 415, 445, 490, 515, 555, 580, 620, 660, 720, 770),
                        23 => array(230, 260, 325, 360, 380, 430, 465, 510, 545, 570, 610, 640, 695, 750, 810),
                        25 => array(230, 260, 325, 380, 395, 455, 485, 535, 560, 595, 625, 655, 720, 775, 820),
                    );
                    $weight = 0;
                    $distance = 0;
                    $cost = 0;
                    $category = 1;
                    $categories = get_option('wdshipping_categories');
                    //Calculate distance
                    $address = WC()->checkout->get_value('s_address') . ', ' . WC()->checkout->get_value('s_address_2') . ', ' . WC()->checkout->get_value('s_postcode') . ', ' . WC()->checkout->get_value('s_city') . ', ' . WC()->checkout->get_value('s_country');
                    $distance_url = wd_shipping_get_url($address);
                    if ($distance_url) {
                        $response = wd_shipping_get_web_page(wd_shipping_get_url($address));
                        $result = json_decode($response);
                        $distance = intval($result->routes[0]->sections[0]->summary->length) / 1000;
                    }
                    foreach ($package['contents'] as $item_id => $values) {
                        $_product = $values['data'];
                        $weight = $weight + $_product->get_weight() * $values['quantity'];
                        $item_cat = $values['data']->category_ids;
                        if (empty($item_cat)) {
                            $terms = get_the_terms($values['data']->parent_id, 'product_cat');
                            $item_cat = array();
                            foreach ($terms as $term) {
                                array_push($item_cat, $term->term_id);
                            }
                        }
                        if (!array_intersect($item_cat, $categories)) {
                            $category = 0;
                        }
                    }
                    $weight = wc_get_weight($weight, 'kg');

                    if ($distance) {
                        foreach ($distance_arr as $index => $value) {
                            if ($distance <= $value) {
                                $distance_index = $index;
                                break;
                            }
                        }
                    }
                    if (isset($distance_index)) {
                        foreach ($options as $index => $value) {
                            if ($weight <= (intval($index) * 1000)) {
                                $cost = $value[$distance_index];
                                break;
                            }
                        }
                    }
                    $rate = array(
                        'id' => $this->id,
                        'label' => $this->title,
                        'cost' => $cost
                    );

                    if ($cost != 0 && $category != 0) {
                        $this->add_rate($rate);
                    }

                    add_action('woocommerce_review_order_after_shipping', 'wd_shipping_error_text', 20);
                }
            }
        }
    }

    add_action('woocommerce_shipping_init', 'wdshipping_shipping_method');

    function add_wdshipping_shipping_method($methods)
    {
        $methods[] = 'WDShipping_Shipping_Method';
        return $methods;
    }

    add_filter('woocommerce_shipping_methods', 'add_wdshipping_shipping_method');

    function wdshipping_validate_order($posted)
    {
        $packages = WC()->shipping->get_packages();

        $chosen_methods = WC()->session->get('chosen_shipping_methods');

        if (is_array($chosen_methods) && in_array('wdshipping', $chosen_methods)) {
            foreach ($packages as $i => $package) {
                if ($chosen_methods[$i] != "wdshipping") {
                    continue;
                }

                $WDShipping_Shipping_Method = new WDShipping_Shipping_Method();
                $weight = 0;

                foreach ($package['contents'] as $item_id => $values) {
                    $_product = $values['data'];
                    $weight = $weight + $_product->get_weight() * $values['quantity'];
                }

                $weight = wc_get_weight($weight, 'kg');
            }
        }
    }

    add_action('woocommerce_review_order_before_cart_contents', 'wdshipping_validate_order', 10);
    add_action('woocommerce_after_checkout_validation', 'wdshipping_validate_order', 10);
}


function wd_shipping_get_web_page($url)
{
    $options = array(
        CURLOPT_RETURNTRANSFER => true,   // return web page
        CURLOPT_HEADER         => false,  // don't return headers
        CURLOPT_FOLLOWLOCATION => true,   // follow redirects
        CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
        CURLOPT_ENCODING       => "",     // handle compressed
        CURLOPT_USERAGENT      => "test", // name of client
        CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
        CURLOPT_TIMEOUT        => 120,    // time-out on response
    );

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);

    $content  = curl_exec($ch);

    curl_close($ch);

    return $content;
}

function wd_shipping_get_url($address)
{
    $error = 0;
    $address = str_replace(" ", "+", $address);
    $address = str_replace(",", "", $address);
    $address = urlencode($address);
    $response = wd_shipping_get_web_page("https://geocode.search.hereapi.com/v1/geocode?q=" . $address . "&apiKey=jHp51y6binH55FV-LI2sGyVUFAYKmdHhTemFPCD11TA");
    $lat_store = get_option('wd_shipping_warehouse_lat');
    $lng_store = get_option('wd_shipping_warehouse_lng');

    $result = json_decode($response);
    if (isset($result->items[0])) {
        $lat = $result->items[0]->position->lat;
        $lng = $result->items[0]->position->lng;
    } else {
        return false;
    }

    return "https://router.hereapi.com/v8/routes?transportMode=car&origin=" . $lat_store . "," . $lng_store . "&destination=" . $lat . "," . $lng . "&return=summary&apiKey=jHp51y6binH55FV-LI2sGyVUFAYKmdHhTemFPCD11TA";
}


function wd_shipping_error_text()
{
    echo '</tr><tr id="wd_shipping_error_text" class="shipping info"><th>&nbsp;</th><td><a href="' . get_option('wd_shipping_error_link') . '" target="_blank">' . get_option('wd_shipping_error_text') . '</a></td>';
}

//add the action
add_action('woocommerce_review_order_after_shipping', 'wd_shipping_error_text', 20);
