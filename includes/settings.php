<?php

//Add settings page
function wdshipping_settings_add()
{
    add_menu_page(
        'wdshipping Settings',
        'WDShipping',
        'manage_options',
        'wdshipping-settings',
        'wdshipping_render_settings_page',
        'dashicons-schedule',
        3
    );
}
add_action('admin_menu', 'wdshipping_settings_add');
function wdshipping_render_settings_page()
{
    $options = get_option('wd_shipping_table');
    $max_w = intval(max(array_keys($options)));
    $max_d = intval(max($options[0])); ?>
    <h1>WDShipping Plugin Settings</h1>

    <h3>Edit price</h3>
    <form>
        <select id="weight_select">
            <option selected="selected" disabled>Choose weight</option>
            <?php
            foreach ($options as $index => $item) {
                if ($index > 0) {
                    echo "<option value='$index'>Up to $index tons</option>";
                }
            } ?>
        </select>
        <select id="distance_select">
            <option selected="selected" disabled>Choose distance</option>
            <?php
            foreach ($options[0] as $index => $item) {
                echo "<option value='$index'>Up to $item km</option>";
            } ?>
        </select>
        <input type="number" id="price" />
        <input type="submit" id="submit_btn" value="Save price">
    </form>

    <h3>Add weight</h3>
    <p>Current maximum weight: up to <?= max(array_keys($options)); ?> tons</p>
    <form>
        <input type="number" id="weight_new" min="<?= ($max_w + 1); ?>" placeholder="<?= ($max_w + 1); ?>" />
        <input type="submit" id="add_weight" value="Add weight">
    </form>

    <h3>Add Distance</h3>
    <p>Current maximum distance: up to <?= max($options[0]); ?>km</p>
    <form>
        <input type="number" id="distance_new" min="<?= ($max_d + 1); ?>" placeholder="<?= ($max_d + 1); ?>" />
        <input type="submit" id="add_distance" value="Add distance">
    </form>

    <h3>Not Available Text</h3>
    <form action="/wp-admin/admin-post.php" method="post">
        <input type="hidden" name="action" value="wdshipping_error_text" />
        <input type="text" id="crane_text" name="error_text" placeholder="<?= get_option('wd_shipping_error_text'); ?>" />
        <input type="text" id="crane_text" name="error_link" placeholder="<?= get_option('wd_shipping_error_link'); ?>" />
        <input type="submit" id="submit_error_text" value="Save">
    </form>


    <script type="text/javascript">
        //Get price on each change
        jQuery("select").on('change', function(event) {
            if (jQuery('#weight_select').val() != null && jQuery('#distance_select').val() != null)
                jQuery.post(
                    ajaxurl, {
                        'action': 'get_price',
                        'weight': jQuery('#weight_select').val(),
                        'distance': jQuery('#distance_select').val(),
                    },
                    function(response) {
                        jQuery('#price').val(response);
                    }
                );
        });

        //Save price

        jQuery("#submit_btn").on('click', function(event) {
            event.preventDefault();

            if (jQuery('#weight_select').val() != null && jQuery('#distance_select').val() != null)
                jQuery.post(
                    ajaxurl, {
                        'action': 'save_price',
                        'weight': jQuery('#weight_select').val(),
                        'distance': jQuery('#distance_select').val(),
                        'price': jQuery('#price').val(),
                    },
                    function(response) {
                        alert(response);
                    }
                );
        });

        //Add weight

        jQuery("#add_weight").on('click', function(event) {
            event.preventDefault();

            if (jQuery('#weight_new').val() != '')
                jQuery.post(
                    ajaxurl, {
                        'action': 'add_weight',
                        'weight': jQuery('#weight_new').val(),
                    },
                    function(response) {
                        if (response.success == false)
                            alert(response.data[0].message);
                        else {
                            var distance = '';
                            response.data.forEach(function(item) {
                                distance += '<br/>Price for distance up to ' + item + 'km: <input type="number" name="price[]"/>';
                            });
                            distance += '<br/><input type="submit" id="submit_weight" value="Save weight and prices">';
                            jQuery('#add_weight').parent().append(distance);
                            jQuery('#add_weight').remove();
                        }
                    }
                );
            else
                alert('Please enter weight higher than current maximum');
        });

        //Save weight

        jQuery(document).on('click', '#submit_weight', function(event) {
            event.preventDefault();
            var prices = new Array();
            var error = 0;
            jQuery('input[name^="price"]').each(function() {
                if (jQuery(this).val() != '')
                    prices.push(jQuery(this).val());
                else
                    error = 1;
            });
            if (jQuery('#weight').val() != '' && error != 1)
                jQuery.post(
                    ajaxurl, {
                        'action': 'save_weight',
                        'weight': jQuery('#weight_new').val(),
                        'price': prices
                    },
                    function(response) {
                        if (response.success == false)
                            alert(response.data[0].message);
                        else {
                            if (!alert('Succesfully added new weight and prices!')) {
                                window.location.reload();
                            }
                        }
                    }
                );
            else
                alert('Please enter weight and all prices fields!');
        });

        //Add Distance

        jQuery("#add_distance").on('click', function(event) {
            event.preventDefault();
            if (jQuery('#distance_new').val() != '')
                jQuery.post(
                    ajaxurl, {
                        'action': 'add_distance',
                        'distance': jQuery('#distance_new').val(),
                    },
                    function(response) {
                        console.log(response);
                        if (response.success == false)
                            alert(response.data[0].message);
                        else {
                            var distance = '';
                            response.data.forEach(function(item) {
                                if (item != 0)
                                    distance += '<br/>Price for weight up to ' + item + 'tons: <input type="number" name="price[]"/>';
                            });
                            distance += '<br/><input type="submit" id="submit_distance" value="Save distance and prices">';
                            jQuery('#add_distance').parent().append(distance);
                            jQuery('#add_distance').remove();
                        }
                    }
                );
            else
                alert('Please enter distance higher than current maximum');
        });

        //Save distance

        jQuery(document).on('click', '#submit_distance', function(event) {
            event.preventDefault();
            var prices = new Array();
            var error = 0;
            jQuery('input[name^="price"]').each(function() {
                if (jQuery(this).val() != '')
                    prices.push(jQuery(this).val());
                else
                    error = 1;
            });
            if (jQuery('#distance_new').val() != '' && error != 1)
                jQuery.post(
                    ajaxurl, {
                        'action': 'save_distance',
                        'distance': jQuery('#distance_new').val(),
                        'price': prices
                    },
                    function(response) {
                        if (response.success == false)
                            alert(response.data[0].message);
                        else {
                            if (!alert('Succesfully added new distance and prices!')) {
                                window.location.reload();
                            }
                        }
                    }
                );
            else
                alert('Please enter distance and all prices fields!');
        });
    </script>
<?php
}
