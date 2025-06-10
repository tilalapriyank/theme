<?php
/**
 * The template for displaying product content within loops
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

// WordPress functions are always available in this context, so no need to require them manually.
// Remove previous manual requires to fix linter errors.

global $product;

// Ensure visibility
if (empty($product) || !$product->is_visible()) {
    return;
}

// Get product data
$product_id = $product->get_id();
$product_name = $product->get_name();
$product_price = $product->get_price();
$product_image = wp_get_attachment_image_url($product->get_image_id(), 'woocommerce_thumbnail');
$product_rating = $product->get_average_rating();
$product_review_count = $product->get_review_count();
$product_badge = '';

// Check if product is on sale
if ($product->is_on_sale()) {
    $product_badge = 'Sale';
} elseif ($product->is_featured()) {
    $product_badge = 'Featured';
}

// Get product categories
$categories = get_the_terms($product_id, 'product_cat');
$category_name = $categories ? $categories[0]->name : '';

// Get product attributes
$attributes = $product->get_attributes();
$sizes = [];
$colors = [];

if (isset($attributes['pa_size'])) {
    $sizes = $attributes['pa_size']->get_options();
}

if (isset($attributes['pa_color'])) {
    $colors = $attributes['pa_color']->get_options();
}

// Prepare product data for cart
$product_data = [
    'id' => $product_id,
    'name' => $product_name,
    'price' => (float) $product_price,
    'image' => $product_image,
    'url' => get_permalink($product_id),
    'quantity' => 1
];
?>

<div class="bg-white rounded-2xl shadow-md overflow-hidden group relative flex flex-col justify-between h-full">
    <div class="relative">
        <?php if ($product_badge) : ?>
            <span class="absolute top-4 left-4 z-20 bg-[#FF3A5E] text-white text-xs font-semibold px-3 py-1 rounded-full">
                <?php echo esc_html($product_badge); ?>
            </span>
        <?php endif; ?>
        <div class="absolute top-4 right-4 z-20">
            <?php if (function_exists('yith_wcwl_add_to_wishlist')) {
                echo do_shortcode('[yith_wcwl_add_to_wishlist product_id="' . esc_attr($product_id) . '"]');
            } else { ?>
                <button class="text-gray-400 hover:text-[#FF3A5E] focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            <?php } ?>
        </div>
        <a href="<?php echo esc_url($product->get_permalink()); ?>" class="block aspect-square w-full overflow-hidden bg-gray-100">
            <img src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product_name); ?>" class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105" />
        </a>
        <div class="absolute inset-0 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/40 z-10">
            <button class="mb-2 px-6 py-2 rounded-full bg-white/90 text-gray-900 font-medium text-base shadow hover:bg-white">Quick View</button>
            <?php
            echo apply_filters('woocommerce_loop_add_to_cart_link',
                sprintf('<button class="px-8 py-2 rounded-full bg-[#FF3A5E] text-white font-semibold text-base shadow hover:bg-[#E02E50] flex items-center gap-2">%s</button>',
                    esc_html__('Add to Cart', 'woocommerce')
                ),
                $product
            );
            ?>
        </div>
    </div>
    <div class="flex-1 flex flex-col justify-between p-6">
        <div>
            <h3 class="font-semibold text-lg text-gray-900 mb-1">
                <a href="<?php echo esc_url($product->get_permalink()); ?>" class="hover:text-[#FF3A5E] transition-colors"><?php echo esc_html($product_name); ?></a>
            </h3>
            <div class="flex items-center gap-1 mb-2">
                <?php
                $rating = $product_rating;
                for ($i = 1; $i <= 5; $i++) {
                    $fill_class = $i <= floor($rating) ? 'text-[#FFD100]' : 'text-gray-300';
                    echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ' . $fill_class . '" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>';
                }
                ?>
                <span class="text-xs text-gray-500 ml-1">(<?php echo esc_html($product_review_count); ?>)</span>
            </div>
            <div class="flex items-center gap-2 mt-2">
                <?php if ($product->is_on_sale()) : ?>
                    <span class="text-gray-400 text-lg font-semibold line-through"><?php echo wc_price($product->get_regular_price()); ?></span>
                    <span class="text-[#FF3A5E] text-lg font-bold"><?php echo wc_price($product->get_sale_price()); ?></span>
                <?php else : ?>
                    <span class="text-gray-900 text-lg font-bold"><?php echo wc_price($product_price); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div> 