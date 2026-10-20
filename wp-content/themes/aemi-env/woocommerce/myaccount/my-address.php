<?php
/**
 * My Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || exit;

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
    $get_addresses = apply_filters(
        'woocommerce_my_account_get_addresses',
        array(
            'billing'  => __( 'Billing address', AEMI_TEXT_DOMAIN ),
            'shipping' => __( 'Shipping address', AEMI_TEXT_DOMAIN ),
        ),
        $customer_id
    );
} else {
    $get_addresses = apply_filters(
        'woocommerce_my_account_get_addresses',
        array(
            'billing' => __( 'Billing address', AEMI_TEXT_DOMAIN ),
        ),
        $customer_id
    );
}

$oldcol = 1;
$col    = 1;
?>

    <legend class="title-section-account mt-0"><?= esc_html_e('Dirección', AEMI_TEXT_DOMAIN) ?></legend>

    <p>
        <?php echo apply_filters( 'woocommerce_my_account_my_address_description', esc_html__( 'The following addresses will be used on the checkout page by default.', AEMI_TEXT_DOMAIN ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </p>

<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
    <div class="u-columns woocommerce-Addresses col2-set addresses">
<?php endif; ?>

<?php foreach ( $get_addresses as $name => $address_title ) : ?>
    <?php
    $address = wc_get_account_formatted_address( $name );
    $col     = $col * -1;
    $oldcol  = $oldcol * -1;
    ?>

    <div class="u-column<?php echo $col < 0 ? 1 : 2; ?> col-<?php echo $oldcol < 0 ? 1 : 2; ?> woocommerce-Address">
        <header class="woocommerce-Address-title title">
            <h3><?php echo esc_html( __( $address_title, AEMI_TEXT_DOMAIN ) ); ?></h3>
            <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="edit edit-address-button"><?php echo $address ? esc_html__( 'Edit', AEMI_TEXT_DOMAIN ) : esc_html__( 'Add', AEMI_TEXT_DOMAIN ); ?></a>
        </header>
        <address>
            <?php
            echo $address ? wp_kses_post( $address ) : esc_html_e( 'You have not set up this type of address yet.', AEMI_TEXT_DOMAIN );
            ?>
        </address>
    </div>

<?php endforeach; ?>

<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
    </div>
<?php
endif;
