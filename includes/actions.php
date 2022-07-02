<?php

//Get price
add_action('wp_ajax_get_price', 'get_price');

function get_price()
{
    $options = get_option('wd_shipping_table');
    $distance = $_POST['distance'];
    $weight = $_POST['weight'];
    echo $options[$weight][$distance];
    wp_die();
}

//Save price
add_action('wp_ajax_save_price', 'save_price');

function save_price()
{
    $options = get_option('wd_shipping_table');
    $price = $_POST['price'];
    $weight = $_POST['weight'];
    $distance = $_POST['distance'];
    $options[$weight][$distance] = $price;
    update_option('wd_shipping_table', $options);
    echo "Successfully updated price";
    wp_die();
}

//Add weight

add_action('wp_ajax_add_weight', 'add_weight');

function add_weight()
{
    $options = get_option('wd_shipping_table');
    $weight = $_POST['weight'];
    if (intval($weight) < intval(max(array_keys($options)))) {
        $error = new WP_Error('001', __('Please enter weight higher than current maximum'), 'Some information');
        wp_send_json_error($error);
    } else {
        wp_send_json_success($options[0]);
    }
    wp_die();
}


//Save weight

add_action('wp_ajax_save_weight', 'save_weight');

function save_weight()
{
    $options = get_option('wd_shipping_table');
    $weight = intval($_POST['weight']);
    $price = $_POST['price'];
    if ($weight < intval(max(array_keys($options)))) {
        $error = new WP_Error('001', __('Please enter weight higher than current maximum'), 'Some information');
        wp_send_json_error($error);
    } else {
        $options[$weight] = $price;
        update_option('wd_shipping_table', $options);
        wp_send_json_success($options);
    }
    wp_die();
}


//Add distance

add_action('wp_ajax_add_distance', 'add_distance');

function add_distance()
{
    $options = get_option('wd_shipping_table');
    $distance = $_POST['distance'];
    if (intval($distance) < intval(max($options[0]))) {
        $error = new WP_Error('001', __('Please enter distance higher than current maximum'), 'Some information');
        wp_send_json_error($error);
    } else {
        wp_send_json_success(array_keys($options));
    }
    wp_die();
}
//Save distance

add_action('wp_ajax_save_distance', 'save_distance');

function save_distance()
{
    $options = get_option('wd_shipping_table');
    $distance = intval($_POST['distance']);
    $price = $_POST['price'];
    if (intval($distance) < intval(max($options[0]))) {
        $error = new WP_Error('001', __('Please enter weight higher than current maximum'), 'Some information');
        wp_send_json_error($error);
    } else {
        array_push($options[0], $distance);
        foreach ($options as $index => $weight) {
            if ($index != 0) {
                array_push($options[$index], $price[0]);
                array_shift($price);
            }
        }
        update_option('wd_shipping_table', $options);
        wp_send_json_success($options);
    }
    wp_die();
}
//AJAX URL
add_action('admin_head', 'wdshipping_ajaxurl');

function wdshipping_ajaxurl()
{
    echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         </script>';
}

//Crane actions
add_action('admin_post_wdshipping_crane_title', 'wdshipping_crane_title');

function wdshipping_crane_title()
{
    $text = $_POST['crane_title'];
    update_option('wd_shipping_crane_title', $text);
    wp_redirect(admin_url('admin.php?page=wdshipping-crane'));
}

add_action('admin_post_wdshipping_crane_text', 'wdshipping_crane_text');

function wdshipping_crane_text()
{
    $text = $_POST['crane_text'];
    update_option('wd_shipping_crane', $text);
    wp_redirect(admin_url('admin.php?page=wdshipping-crane'));
}

add_action('admin_post_wdshipping_crane_price', 'wdshipping_crane_price');

function wdshipping_crane_price()
{
    $price = $_POST['crane_price'];
    update_option('wd_shipping_crane_price', $price);
    wp_redirect(admin_url('admin.php?page=wdshipping-crane'));
}

add_action('admin_post_wdshipping_crane_price_min', 'wdshipping_crane_price_min');

function wdshipping_crane_price_min()
{
    $price = $_POST['crane_price_min'];
    update_option('wd_shipping_crane_price_min', $price);
    wp_redirect(admin_url('admin.php?page=wdshipping-crane'));
}

//Error text save
add_action('admin_post_wdshipping_error_text', 'wdshipping_error_text');

function wdshipping_error_text()
{
    $text = $_POST['error_text'];
    $link = $_POST['error_link'];
    update_option('wd_shipping_error_text', $text);
    update_option('wd_shipping_error_link', $link);
    wp_redirect(admin_url('admin.php?page=wdshipping-settings'));
}

//Save warehouse address

add_action('admin_post_wdshipping_warehouse', 'wdshipping_warehouse');

function wdshipping_warehouse()
{
    $address = $_POST['warehouse_address'];
    $store = urlencode($address);
    $response = wd_shipping_get_web_page("https://geocode.search.hereapi.com/v1/geocode?q=" . $store . "&apiKey=jHp51y6binH55FV-LI2sGyVUFAYKmdHhTemFPCD11TA");
    $result = json_decode($response);
    if (isset($result->items[0])) {
        $lat_store = $result->items[0]->position->lat;
        $lng_store = $result->items[0]->position->lng;
        update_option('wd_shipping_warehouse_address', $address);
        update_option('wd_shipping_warehouse_lat', $lat_store);
        update_option('wd_shipping_warehouse_lng', $lng_store);
        wp_redirect(admin_url('admin.php?page=wdshipping-warehouse'));
    } else {
        wp_redirect(admin_url('admin.php?page=wdshipping-warehouse&badAddress=1'));
    }
}

//Select categories

add_action('admin_post_wdshipping_categories', 'wdshipping_categories');

function wdshipping_categories()
{
    $categories = $_POST['categories'];
    update_option('wdshipping_categories', $categories);
    wp_redirect(admin_url('admin.php?page=wdshipping-categories'));
}
