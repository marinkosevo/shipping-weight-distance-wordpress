<?php
add_action('woocommerce_after_checkout_billing_form', 'wdshipping_crane_box');
function wdshipping_crane_box($checkout)
{
    $text = get_option('wd_shipping_crane');
    echo '<div id="wdshipping_crane_box_form">';
    woocommerce_form_field('wdshipping_crane_box', array(
        'type'          => 'checkbox',
        'class'         => array('wdshipping_crane_box form-row-wide'),
        'label'         => $text,
        'placeholder'   => __(''),
    ), $checkout->get_value('wdshipping_crane_box'));
    echo '</div>';
}

add_action('wp_footer', 'woocommerce_wdshipping_crane_box');
function woocommerce_wdshipping_crane_box()
{
    if (is_checkout()) {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                if ($('input[name^="shipping_method"]:checked').val() != 'wdshipping' && $('#shipping_method_0_wdshipping').length != 1)
                    $('#wdshipping_crane_box_form').hide();
                $(document).on('click', 'input[name^="shipping_method"]', function() {
                    if ($('input[name^="shipping_method"]:checked').val() != 'wdshipping') {
                        $('#wdshipping_crane_box_form').hide();
                        $('#wdshipping_crane_box').prop('checked', false);
                    } else
                        $('#wdshipping_crane_box_form').show();
                });
                $('#wdshipping_crane_box').click(function() {
                    jQuery('body').trigger('update_checkout');
                });
            });
            jQuery('body').on('updated_checkout', function() {
                if (jQuery('#shipping_method_0_wdshipping').length) {
                    jQuery('#wd_shipping_error_text').hide();
                    jQuery('#wdshipping_crane_box_form').show();
                } else {
                    jQuery('#wd_shipping_error_text').show();
                    jQuery('#wdshipping_crane_box_form').hide();
                }
            });
        </script>
    <?php
    }
}

add_action('woocommerce_cart_calculate_fees', 'woo_add_cart_fee');
function woo_add_cart_fee($cart)
{
    if (!$_POST || (is_admin() && !is_ajax())) {
        return;
    }

    if (isset($_POST['post_data'])) {
        parse_str($_POST['post_data'], $post_data);
    } else {
        $post_data = $_POST; // fallback for final checkout (non-ajax)
    }

    if (isset($post_data['wdshipping_crane_box'])) {
        $weight = WC()->cart->get_cart_contents_weight();
        $min = intval(get_option('wd_shipping_crane_price_min'));
        $text = get_option('wd_shipping_crane_title');
        $extracost = ceil($weight / 1000) * get_option('wd_shipping_crane_price');
        if ($extracost < $min) {
            $extracost = $min;
        }
        WC()->cart->add_fee($text . ':', $extracost);
    }
}


function wdshipping_settings_crane()
{
    add_submenu_page(
        'wdshipping-settings',
        'Crane',
        'Crane',
        'manage_options',
        'wdshipping-crane',
        'wdshipping_render_crane_page',
    );
}
add_action('admin_menu', 'wdshipping_settings_crane');

function wdshipping_render_crane_page()
{
    ?>
    <h1>WDShipping Crane Settings</h1>

    <h3>Crane Title</h3>
    <form action="/wp-admin/admin-post.php" method="post">
        <input type="hidden" name="action" value="wdshipping_crane_title" />
        <input type="text" id="crane_title" name="crane_title" placeholder="<?= get_option('wd_shipping_crane_title'); ?>" />
        <input type="submit" id="submit_crane_title" value="Save">
    </form>

    <h3>Crane Checkbox</h3>
    <form action="/wp-admin/admin-post.php" method="post">
        <input type="hidden" name="action" value="wdshipping_crane_text" />
        <input type="text" id="crane_text" name="crane_text" placeholder="<?= get_option('wd_shipping_crane'); ?>" />
        <input type="submit" id="submit_crane_text" value="Save">
    </form>

    <h3>Crane Price (per 1 ton)</h3>
    <form action="/wp-admin/admin-post.php" method="post">
        <input type="hidden" name="action" value="wdshipping_crane_price" />
        <input type="number" id="crane_price" name="crane_price" placeholder="<?= get_option('wd_shipping_crane_price'); ?>" />
        <input type="submit" id="submit_crane_price" value="Save">
    </form>

    <h3>Crane Minimum Price</h3>
    <form action="/wp-admin/admin-post.php" method="post">
        <input type="hidden" name="action" value="wdshipping_crane_price_min" />
        <input type="number" id="crane_price_min" name="crane_price_min" placeholder="<?= get_option('wd_shipping_crane_price_min'); ?>" />
        <input type="submit" id="submit_crane_price_min" value="Save">
    </form>
<?php
}
