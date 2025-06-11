<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' ); ?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php
            /**
             * woocommerce_before_main_content hook.
             */
            // do_action( 'woocommerce_before_main_content' );
            ?>

            <?php while ( have_posts() ) : ?>
                <?php the_post(); ?>
                <?php 
                global $product;
                // Get available variations for variable products
                $available_variations = array();
                $attributes = array();
                if ( $product->is_type( 'variable' ) ) {
                    $available_variations = $product->get_available_variations();
                    $attributes = $product->get_variation_attributes();
                }
                ?>

                <div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'single-product-wrapper', $product ); ?>>
                    
                    <div class="product-main-content grid grid-cols-1 md:grid-cols-2 py-8">
                        <!-- Product Images Column -->
                        <div class="product-images flex flex-col">
                            <?php
                            /**
                             * Hook: woocommerce_before_single_product_summary.
                             *
                             * @hooked woocommerce_show_product_sale_flash - 10
                             * @hooked woocommerce_show_product_images - 20
                             */
                            do_action( 'woocommerce_before_single_product_summary' );
                            ?>
                        </div>

                        <!-- Product Summary Column -->
                        <div class="product-summary">
                            <div class="summary entry-summary flex flex-col gap-4">
                                <!-- Product Title -->
                                <h1 class="product_title entry-title text-3xl font-bold text-gray-900"><?php the_title(); ?></h1>

                                <!-- Product Rating -->
                                <?php if ( wc_review_ratings_enabled() ) : ?>
                                    <div class="flex items-center gap-2">
                                        <?php echo wc_get_rating_html( $product->get_average_rating() ); ?>
                                        <a href="#reviews" class="text-sm text-gray-500 hover:underline" rel="nofollow">
                                            (<?php printf( _n( '%s review', '%s reviews', $product->get_review_count(), 'woocommerce' ), '<span class="count">' . esc_html( $product->get_review_count() ) . '</span>' ); ?>)
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <!-- Product Price -->
                                <p class="price text-2xl font-bold text-pink-600"><?php echo $product->get_price_html(); ?></p>

                                <!-- Product Short Description -->
                                <div class="woocommerce-product-details__short-description text-gray-700">
                                    <?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ); ?>
                                </div>

                                <!-- Key Features (Dynamic from custom field, fallback to static) -->
                                <div class="mb-4">
                                    <h3 class="font-semibold text-lg mb-2">Key Features</h3>
                                    <ul class="list-none space-y-1">
                                        <?php 
                                        $features = get_post_meta(get_the_ID(), 'product_features', true);
                                        if ($features) {
                                            $features_arr = array_filter(array_map('trim', explode("\n", $features)));
                                            foreach ($features_arr as $feature) {
                                                echo '<li class="flex items-center text-sm text-gray-700"><span class="text-pink-500 mr-2">&#10003;</span> ' . esc_html($feature) . '</li>';
                                            }
                                        } else {
                                        ?>
                                            <li class="flex items-center text-sm text-gray-700"><span class="text-pink-500 mr-2">&#10003;</span> Water-resistant outer shell protects from light rain and snow</li>
                                            <li class="flex items-center text-sm text-gray-700"><span class="text-pink-500 mr-2">&#10003;</span> Premium insulation keeps your dog warm in cold weather</li>
                                            <li class="flex items-center text-sm text-gray-700"><span class="text-pink-500 mr-2">&#10003;</span> Reflective details for visibility during evening walks</li>
                                            <li class="flex items-center text-sm text-gray-700"><span class="text-pink-500 mr-2">&#10003;</span> Full-length zipper for easy on/off</li>
                                            <li class="flex items-center text-sm text-gray-700"><span class="text-pink-500 mr-2">&#10003;</span> Adjustable straps for a perfect fit</li>
                                        <?php } ?>
                                    </ul>
                                </div>

                                <!-- Improved spacing for Quantity and Add to Cart -->
                                <div class="flex flex-col gap-4 mb-4">
                                    <!-- Buttons Row -->
                                    <div class="flex flex-row gap-4 w-full">
                                        <?php if ( $product->is_type( 'variable' ) ) : ?>
                                            <form class="variations_form cart w-full" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo htmlspecialchars( wp_json_encode( $available_variations ) ) ?>">
                                                <?php wp_nonce_field( 'woocommerce-cart' ); ?>
                                                <?php do_action( 'woocommerce_before_variations_form' ); ?>
                                                <?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
                                                    <p class="stock out-of-stock text-red-500 text-sm"><?php esc_html_e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>
                                                <?php else : ?>
                                                    <table class="variations w-full mb-2">
                                                        <tbody>
                                                            <?php foreach ( $attributes as $attribute_name => $options ) : ?>
                                                                <tr>
                                                                    <td class="label pr-2">
                                                                        <label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>" class="font-semibold text-sm">
                                                                            <?php echo wc_attribute_label( $attribute_name ); ?>
                                                                        </label>
                                                                    </td>
                                                                    <td class="value">
                                                                        <?php
                                                                        wc_dropdown_variation_attribute_options(
                                                                            array(
                                                                                'options'   => $options,
                                                                                'attribute' => $attribute_name,
                                                                                'product'   => $product,
                                                                            )
                                                                        );
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                    <div class="single_variation_wrap">
                                                        <div class="woocommerce-variation single_variation"></div>
                                                        <div class="woocommerce-variation-add-to-cart variations_button flex items-center gap-2 mt-2">
                                                            <?php
                                                            woocommerce_quantity_input(
                                                                array(
                                                                    'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
                                                                    'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                                                                    'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(),
                                                                )
                                                            );
                                                            ?>
                                                            <button type="submit" class="single_add_to_cart_button button alt disabled wc-variation-selection-needed bg-pink-500 hover:bg-pink-600 text-white px-8 py-3 rounded transition flex items-center gap-2 flex-1 justify-center text-lg font-medium" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>">
                                                                Add to Cart xx<span style="font-size: 22px; vertical-align: middle; margin-left: 6px;">&#128722;</span>
                                                            </button>
                                                            <input type="hidden" name="product_id" value="<?php echo esc_attr( $product->get_id() ); ?>" />
                                                            <input type="hidden" name="variation_id" class="variation_id" value="0" />
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <?php do_action( 'woocommerce_after_variations_form' ); ?>
                                                <button class="add-to-wishlist flex-1 px-8 py-3 border border-pink-500 text-pink-500 rounded hover:bg-pink-50 transition text-lg font-medium flex items-center justify-center gap-2 bg-white" style="border-width:2px; margin-top: 16px;">
                                                    Add to Wishlist <span style="font-size: 22px; vertical-align: middle; margin-left: 6px;">&#9825;</span>
                                                </button>
                                            </form>
                                        <?php else : ?>
                                            <form class="cart flex-1" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
                                                <?php
                                                do_action( 'woocommerce_before_add_to_cart_button' );
                                                do_action( 'woocommerce_before_add_to_cart_quantity' );
                                                // Quantity input already rendered above
                                                do_action( 'woocommerce_after_add_to_cart_quantity' );
                                                ?>
                                                <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt bg-pink-500 hover:bg-pink-600 text-white px-8 py-3 rounded transition flex items-center gap-2 flex-1 justify-center text-lg font-medium">
                                                    Add to Cart xx2<span style="font-size: 22px; vertical-align: middle; margin-left: 6px;">&#128722;</span>
                                                </button>
                                                <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Tabs -->
                    <div class="woocommerce-tabs wc-tabs-wrapper">
                        <?php
                        /**
                         * Hook: woocommerce_output_product_data_tabs.
                         *
                         * @hooked woocommerce_output_product_data_tabs - 10
                         */
                        do_action( 'woocommerce_output_product_data_tabs' );
                        ?>
                    </div>

                    <!-- Related Products -->
                    <?php
                    /**
                     * Hook: woocommerce_after_single_product_summary.
                     *
                     * @hooked woocommerce_output_product_data_tabs - 10
                     * @hooked woocommerce_upsell_display - 15
                     * @hooked woocommerce_output_related_products - 20
                     */
                    do_action( 'woocommerce_after_single_product_summary' );
                    ?>

                </div>

            <?php endwhile; ?>

            <?php
            /**
             * woocommerce_after_main_content hook.
             */
            do_action( 'woocommerce_after_main_content' );
            ?>

        </div>
    </div>
</div>

<style>
    .add-to-wishlist{
        background-color: #fff !important;
        color: #ff3a5e !important;
        border: 2px solid #ff3a5e !important;
        padding:0px !important;
        height: 40px !important;
        margin-top: 20px !important;
    }
.single-product-wrapper {
    margin: 20px 0;
}

.product-images {
    margin-bottom: 30px;
}

.product_title {
    font-size: 2em;
    margin-bottom: 15px;
    color: #333;
}

.price {
    font-size: 1.5em;
    font-weight: bold;
    color: #77a464;
    margin: 15px 0;
}

.variations {
    width: 100%;
    margin-bottom: 20px;
}

.variations td {
    padding: 8px 0;
    vertical-align: middle;
}

.variations .label {
    font-weight: bold;
    width: 30%;
}

.variations .value {
    width: 70%;
}

.variations select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.woocommerce-variation-add-to-cart {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-top: 20px;
}
.woocommerce-variation-add-to-cart button {
    background-color: #ff3a5e !important;
    color: #fff !important;
    border: none !important;
    border-radius: 4px !important;
    cursor: pointer !important;
    font-size: 16px !important;
    transition: background-color 0.3s !important;
}

.quantity input {
    width: 80px;
    padding: 8px;
    text-align: center;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.single_add_to_cart_button {
    background-color: #77a464;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
}

.single_add_to_cart_button:hover {
    background-color: #5a7c4a;
}

.single_add_to_cart_button.disabled {
    background-color: #ccc;
    cursor: not-allowed;
}

.product_meta {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.product_meta span {
    display: block;
    margin-bottom: 8px;
}

@media (max-width: 768px) {
    .woocommerce-variation-add-to-cart {
        flex-direction: column;
        align-items: stretch;
    }
    
    .single_add_to_cart_button {
        width: 100%;
        margin-top: 10px;
    }
}

/* Custom styles for color swatches and size buttons */
.product-summary .w-7.h-7 {
    display: inline-block;
    box-shadow: 0 0 0 2px #fff, 0 0 0 3px #e5e7eb;
    transition: box-shadow 0.2s;
}
.product-summary .w-7.h-7.ring-2 {
    box-shadow: 0 0 0 2px #fff, 0 0 0 3px #ec4899;
}
.product-summary .w-9.h-9 {
    transition: border-color 0.2s, color 0.2s;
}
.product-summary .w-9.h-9.border-2 {
    border-width: 2px;
}

.woocommerce #content div.product div.images, .woocommerce div.product div.images, .woocommerce-page #content div.product div.images, .woocommerce-page div.product div.images{
    width: 100%;
    object-fit: cover;
}
.woocommerce div.product div.summary{
    width: 100%;
    padding: 0 30px;
}

/* Main product image */
.woocommerce div.product div.images img,
.woocommerce-page div.product div.images img {
    width: 100%;           /* Makes image responsive */
    max-width: 500px;      /* Set your desired max width */
    height: 500px;         /* Fixed height */
    object-fit: cover;     /* Ensures image covers the area without distortion */
    border-radius: 8px;    /* Optional: rounded corners */
    margin: 0 auto 16px auto !important;
    display: block;
}

/* Gallery thumbnails */
.woocommerce div.product div.images .thumbnails img,
.woocommerce-page div.product div.images .thumbnails img {
    width: 100px;          /* Fixed width for thumbnails */
    min-height: 100px !important;         /* Fixed height for thumbnails */
    object-fit: cover;     /* Ensures thumbnails are not distorted */
    border-radius: 6px;    /* Optional: rounded corners */
    margin-right: 10px;
    border: 1px solid #eee;
    transition: border 0.2s;
    cursor: pointer;
}
.woocommerce-product-gallery__trigger{
    right: 6.5rem !important;
}

.woocommerce div.product div.images .thumbnails img:hover,
.woocommerce-page div.product div.images .thumbnails img:hover {
    border: 1px solid #ff3a5e;
}
img.flex-active{
    border: 2px solid #ff3a5e !important;
}
.flex-control-nav.flex-control-thumbs li img{
    width: 150px !important;
    height: 150px !important;
    /* padding-bottom: 10px; */
}

/* Custom WooCommerce Product Tabs */
.woocommerce-tabs .wc-tabs {
    display: flex;
    border-bottom: 2px solid #f3f4f6 !important;
    margin-bottom: 0;
    padding-left: 0;
    gap: 2rem;
    background: none;
    box-shadow: none;
}

.woocommerce-tabs .wc-tabs li {
    margin: 0;
    padding: 0;
    border: none !important;
    background: none !important;
    list-style: none;
}

.woocommerce-tabs .wc-tabs li a {
    display: inline-block;
    padding: 0 0 8px 0;
    font-size: 1.25rem;
    color: #64748b;
    font-weight: 500;
    border: none;
    background: none;
    text-decoration: none;
    transition: color 0.2s;
    position: relative;
}

.woocommerce-tabs .wc-tabs li.active a,
.woocommerce-tabs .wc-tabs li a:focus,
.woocommerce-tabs .wc-tabs li a:hover {
    color: #ff3a5e !important;
    font-weight: 600 !important;
}

.woocommerce-tabs .wc-tabs li.active a::after {
    content: "";
    display: block;
    height: 5px;
    width: 100%;
    background: #ff3a5e;
    border-radius: 2px;
    position: absolute;
    left: 0;
    bottom: -2px;
}

.woocommerce-tabs .wc-tab {
    padding: 2rem 0 0 0;
    border: none;
    background: none;
}
.woocommerce-Tabs-panel h2{
    display: none;
}

.quantity-input-wrapper input.qty {
    width: 48px;
    text-align: center;
    border: none;
    background: transparent;
    font-size: 1.1rem;
    font-weight: 500;
    outline: none;
}
.quantity-minus, .quantity-plus {
    min-width: 44px;
    min-height: 44px;
    font-size: 1.5rem;
    line-height: 1;
    background: #fff;
}
@media (max-width: 640px) {
    .flex-row.gap-4.w-full {
        flex-direction: column !important;
        gap: 0.75rem !important;
    }
    .flex-1 {
        width: 100% !important;
    }
}
</style>
g9git
<script>
jQuery(document).ready(function($) {
    // Quantity plus/minus
    $(document).on('click', '.quantity-plus', function() {
        var $input = $(this).siblings('.quantity-input-wrapper').find('input.qty');
        var val = parseInt($input.val()) || 1;
        var max = parseInt($input.attr('max')) || 9999;
        if(val < max) $input.val(val + 1).trigger('change');
    });
    $(document).on('click', '.quantity-minus', function() {
        var $input = $(this).siblings('.quantity-input-wrapper').find('input.qty');
        var val = parseInt($input.val()) || 1;
        var min = parseInt($input.attr('min')) || 1;
        if(val > min) $input.val(val - 1).trigger('change');
    });

    // Enable Add to Cart when all radios are selected
    function checkVariationRadios() {
        var allSelected = true;
        $('.variations_form input[type=radio][name^=attribute_]').each(function() {
            var name = $(this).attr('name');
            if ($('.variations_form input[type=radio][name="' + name + '"]:checked').length === 0) {
                allSelected = false;
                return false;
            }
        });
        if (allSelected) {
            $('.single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed');
        } else {
            $('.single_add_to_cart_button').addClass('disabled wc-variation-selection-needed');
        }
    }
    $(document).on('change', '.variations_form input[type=radio][name^=attribute_]', checkVariationRadios);
    checkVariationRadios();
});
</script>

<?php get_footer( 'shop' ); ?>