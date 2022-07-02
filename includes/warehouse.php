<?php

function wdshipping_settings_warehouse()
{
    add_submenu_page(
        'wdshipping-settings',
        'Warehouse',
        'Warehouse',
        'manage_options',
        'wdshipping-warehouse',
        'wdshipping_render_warehouse_page',
    );
}
add_action('admin_menu', 'wdshipping_settings_warehouse');

function wdshipping_render_warehouse_page()
{
    ?>
    <h1>WDShipping Warehouse Settings</h1>
    <?php if (isset($_GET['badAddress'])) {
        echo '<p>Please enter valid address!';
    } ?>

    <h3>Warehouse Address</h3>
    <form action="/wp-admin/admin-post.php" method="post">
        <input type="hidden" name="action" value="wdshipping_warehouse" />
        <input type="text" id="warehouse_address" name="warehouse_address" placeholder="<?= get_option('wd_shipping_warehouse_address'); ?>" />
        <input type="submit" id="submit_warehouse_address" value="Save">
    </form>
<?php
}
