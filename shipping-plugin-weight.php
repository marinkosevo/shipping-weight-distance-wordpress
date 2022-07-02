<?php
/*
Plugin Name: WD Shipping Plugin
Description: Plugin for calculating shipping price based on weight and distance.
Version: 1.0
Author: Marinko Sevo
Author URI: https://www.upwork.com/freelancers/~0192cdf6c40b5e060a
Text Domain: wd-shipping
*/

// include only file

if (!defined('ABSPATH')) {
    die('Do not open this file directly.');
}
include(plugin_dir_path(__FILE__) . 'includes/shipping.php');
include(plugin_dir_path(__FILE__) . 'includes/script.php');
include(plugin_dir_path(__FILE__) . 'includes/settings.php');
include(plugin_dir_path(__FILE__) . 'includes/crane.php');
include(plugin_dir_path(__FILE__) . 'includes/categories.php');
include(plugin_dir_path(__FILE__) . 'includes/actions.php');
include(plugin_dir_path(__FILE__) . 'includes/warehouse.php');

/**
 * Activate the plugin.
 */
function wdshipping_activate()
{
    $options = array(
        0 => array(30, 45, 60, 80, 100, 120, 140, 160, 180, 200, 230, 260, 300, 350, 400),
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
    if (!get_option('wd_shipping_table')) {
        update_option('wd_shipping_table', $options);
    }

    if (!get_option('wd_shipping_error_text')) {
        update_option('wd_shipping_error_text', 'Contact us');
    }

    if (!get_option('wd_shipping_error_link')) {
        update_option('wd_shipping_error_link', 'https://google.com');
    }

    if (!get_option('wd_shipping_crane_title')) {
        update_option('wd_shipping_crane_title', 'Crane delivery');
    }

    if (!get_option('wd_shipping_crane')) {
        update_option('wd_shipping_crane', 'Crane delivery (5e/t, min.25e)');
    }

    if (!get_option('wd_shipping_crane_price')) {
        update_option('wd_shipping_crane_price', '5');
    }

    if (!get_option('wd_shipping_crane_price_min')) {
        update_option('wd_shipping_crane_price_min', '25');
    }
}
register_activation_hook(__FILE__, 'wdshipping_activate');
