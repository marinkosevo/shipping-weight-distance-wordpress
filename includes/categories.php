<?php
function wdshipping_settings_categories()
{
    add_submenu_page(
        'wdshipping-settings',
        'Categories',
        'Categories',
        'manage_options',
        'wdshipping-categories',
        'wdshipping_render_categories_page',
    );
}
add_action('admin_menu', 'wdshipping_settings_categories');

function wdshipping_render_categories_page()
{
    ?>
    <h1> Product categories for shipping option </h1>
<?php
    $orderby = 'name';
    $order = 'asc';
    $hide_empty = false;
    $cat_args = array(
        'orderby'    => $orderby,
        'order'      => $order,
        'hide_empty' => $hide_empty,
    );
    $current_categories = get_option('wdshipping_categories');
    $product_categories = get_terms('product_cat', $cat_args);
    $exists = 0;
    if (!empty($product_categories)) {
        echo '
        <form action="/wp-admin/admin-post.php" method="post">
        <input type="hidden" name="action" value="wdshipping_categories"/>
    <ul>';
        foreach ($product_categories as $key => $category) {
            if ($current_categories) {
                if (in_array($category->term_id, $current_categories)) {
                    $exists = 1;
                } else {
                    $exists = 0;
                }
            }

            echo '<li>';
            echo '<input type="checkbox" value="' . $category->term_id . '" name="categories[]"' . (($exists) ? "checked" : "") . '>';
            echo $category->name;
            echo '</li>';
        }
        echo '</ul>
        <input type="submit" id="submit_categories" value="Save">
    </form>
    
    ';
    }
}
