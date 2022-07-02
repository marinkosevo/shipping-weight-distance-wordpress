<?php
//Add script
function add_checkout_script()
{ ?>

  <script type="text/javascript">
    jQuery('form.woocommerce-checkout input').on('change', function() {
      jQuery(document.body).trigger('update_checkout');
    });
  </script>

<?php
}
add_action('woocommerce_after_checkout_form', 'add_checkout_script');
