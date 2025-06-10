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
            do_action( 'woocommerce_before_main_content' );
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
                    
                    <div class="product-main-content">
                        <div class="row">
                            <!-- Product Images Column -->
                            <div class="col-md-6 product-images">
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
                            <div class="col-md-6 product-summary">
                                <div class="summary entry-summary">
                                    
                                    <!-- Product Title -->
                                    <h1 class="product_title entry-title"><?php the_title(); ?></h1>
                                    
                                    <!-- Product Rating -->
                                    <?php if ( wc_review_ratings_enabled() ) : ?>
                                        <div class="woocommerce-product-rating">
                                            <?php echo wc_get_rating_html( $product->get_average_rating() ); ?>
                                            <a href="#reviews" class="woocommerce-review-link" rel="nofollow">
                                                (<?php printf( _n( '%s customer review', '%s customer reviews', $product->get_review_count(), 'woocommerce' ), '<span class="count">' . esc_html( $product->get_review_count() ) . '</span>' ); ?>)
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Product Price -->
                                    <p class="price"><?php echo $product->get_price_html(); ?></p>

                                    <!-- Product Short Description -->
                                    <div class="woocommerce-product-details__short-description">
                                        <?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ); ?>
                                    </div>

                                    <!-- Variable Product Form -->
                                    <?php if ( $product->is_type( 'variable' ) ) : ?>
                                        <form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo htmlspecialchars( wp_json_encode( $available_variations ) ) ?>">
                                            
                                            <?php wp_nonce_field( 'woocommerce-cart' ); ?>
                                            
                                            <!-- Debug: Show available variations -->
                                            <?php if (WP_DEBUG): ?>
                                            <div style="background: #f0f0f0; padding: 10px; margin: 10px 0; font-size: 12px;">
                                                <strong>Debug - Available Variations:</strong><br>
                                                <?php foreach($available_variations as $variation): ?>
                                                    ID: <?php echo $variation['variation_id']; ?> - 
                                                    <?php foreach($variation['attributes'] as $attr => $value): ?>
                                                        <?php echo $attr; ?>: <?php echo $value; ?> 
                                                    <?php endforeach; ?><br>
                                                <?php endforeach; ?>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <?php do_action( 'woocommerce_before_variations_form' ); ?>

                                            <?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
                                                <p class="stock out-of-stock"><?php esc_html_e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>
                                            <?php else : ?>
                                                
                                                <!-- Variations Table -->
                                                <table class="variations" cellspacing="0">
                                                    <tbody>
                                                        <?php foreach ( $attributes as $attribute_name => $options ) : ?>
                                                            <tr>
                                                                <td class="label">
                                                                    <label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>">
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

                                                <!-- Selected Variation Details -->
                                                <div class="single_variation_wrap">
                                                    <div class="woocommerce-variation single_variation"></div>
                                                    
                                                    <!-- Add to Cart Button -->
                                                    <div class="woocommerce-variation-add-to-cart variations_button">
                                                        <?php
                                                        woocommerce_quantity_input(
                                                            array(
                                                                'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
                                                                'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                                                                'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(),
                                                            )
                                                        );
                                                        ?>
                                                        <button type="submit" class="single_add_to_cart_button button alt disabled wc-variation-selection-needed" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>">
                                                            <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
                                                        </button>
                                                        
                                                        <input type="hidden" name="product_id" value="<?php echo esc_attr( $product->get_id() ); ?>" />
                                                        <input type="hidden" name="variation_id" class="variation_id" value="0" />
                                                    </div>
                                                </div>

                                            <?php endif; ?>

                                            <?php do_action( 'woocommerce_after_variations_form' ); ?>
                                        </form>

                                    <?php else : ?>
                                        <!-- Simple Product Add to Cart -->
                                        <form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
                                            <?php
                                            do_action( 'woocommerce_before_add_to_cart_button' );

                                            do_action( 'woocommerce_before_add_to_cart_quantity' );

                                            woocommerce_quantity_input(
                                                array(
                                                    'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
                                                    'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                                                    'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(),
                                                )
                                            );

                                            do_action( 'woocommerce_after_add_to_cart_quantity' );
                                            ?>

                                            <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt">
                                                <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
                                            </button>

                                            <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
                                        </form>
                                    <?php endif; ?>

                                    <!-- Product Meta -->
                                    <div class="product_meta">
                                        <?php do_action( 'woocommerce_product_meta_start' ); ?>

                                        <?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
                                            <span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'woocommerce' ); ?> <span class="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span></span>
                                        <?php endif; ?>

                                        <?php echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>

                                        <?php echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>

                                        <?php do_action( 'woocommerce_product_meta_end' ); ?>
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
.single-product-wrapper {
    margin: 20px 0;
}

.product-images {
    margin-bottom: 30px;
}

.product-summary {
    padding: 0 15px;
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
</style>

<script>
jQuery(document).ready(function($) {
    // Initialize WooCommerce variation form
    $('.variations_form').wc_variation_form();
    
    // Handle variation found event
    $('.variations_form').on('found_variation', function(event, variation) {
        console.log('Variation found:', variation);
        $('.variation_id').val(variation.variation_id);
        $('.single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed');
        
        // Update price if needed
        if (variation.price_html) {
            $('.single_variation .price').html(variation.price_html);
        }
    });
    
    // Handle variation reset/clear
    $('.variations_form').on('reset_data', function() {
        $('.variation_id').val('0');
        $('.single_add_to_cart_button').addClass('disabled wc-variation-selection-needed');
    });
    
    // Handle variation selection change
    $('.variations_form').on('change', 'select', function() {
        var form = $(this).closest('.variations_form');
        var allSelected = true;
        
        form.find('select').each(function() {
            if ($(this).val() === '') {
                allSelected = false;
                return false;
            }
        });
        
        if (!allSelected) {
            $('.variation_id').val('0');
            $('.single_add_to_cart_button').addClass('disabled wc-variation-selection-needed');
        }
    });
    
    // Prevent form submission if no variation selected
    $('.variations_form').on('submit', function(e) {
        var variationId = $('.variation_id').val();
        if (!variationId || variationId === '0') {
            e.preventDefault();
            alert('Please select all product options before adding to cart.');
            return false;
        }
    });
});
</script>

<?php get_footer( 'shop' ); ?>