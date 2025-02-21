function ensure_woocommerce_session() {
    if (class_exists('WooCommerce') && WC()->session) {
        WC()->session->set_customer_session_cookie(true);
    }
}
add_action('wp', 'ensure_woocommerce_session');
