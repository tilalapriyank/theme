<?php
/**
 * The Template for displaying all single products
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Include WordPress core
require_once(ABSPATH . 'wp-load.php');

// Ensure WooCommerce is active
if (!class_exists('WooCommerce')) {
    return;
}

// Get the product
global $product;

// Ensure $product is a valid product object
if (!is_a($product, 'WC_Product')) {
    $product = wc_get_product(get_the_ID());
}

// Ensure we have a valid product
if (!$product || !$product->is_visible()) {
    return;
}

get_header('shop');
?>

<main class="py-12 bg-white text-gray-900 font-montserrat">
  <div class="max-w-7xl mx-auto px-4">
    <div class="grid md:grid-cols-2 gap-8 lg:gap-12">
      <!-- Product Images -->
      <div class="space-y-4">
        <div id="main-image" class="aspect-square relative rounded-lg overflow-hidden border border-gray-200">
          <?php if ($product->get_image_id()) : ?>
            <?php echo wp_get_attachment_image($product->get_image_id(), 'large', false, ['class' => 'object-cover w-full h-full']); ?>
          <?php else : ?>
            <img src="<?php echo wc_placeholder_img_src(); ?>" alt="<?php the_title_attribute(); ?>" class="object-cover w-full h-full" />
          <?php endif; ?>
        </div>
        <div class="grid grid-cols-4 gap-2">
          <?php
          $main_id = $product->get_image_id();
          $gallery_ids = $product->get_gallery_image_ids();
          if ($main_id) {
            echo '<button class="aspect-square relative rounded-md overflow-hidden border-2 border-[#FF3A5E]" data-image="' . esc_url(wp_get_attachment_url($main_id)) . '">' . wp_get_attachment_image($main_id, 'thumbnail', false, ['class' => 'object-cover w-full h-full']) . '</button>';
          }
          if ($gallery_ids) {
            foreach ($gallery_ids as $i => $img_id) {
              echo '<button class="aspect-square relative rounded-md overflow-hidden border-2 border-gray-200" data-image="' . esc_url(wp_get_attachment_url($img_id)) . '">' . wp_get_attachment_image($img_id, 'thumbnail', false, ['class' => 'object-cover w-full h-full']) . '</button>';
            }
          }
          ?>
        </div>
      </div>

      <!-- Product Info -->
      <div class="space-y-6">
        <div>
          <h1 class="text-3xl font-bold"><?php the_title(); ?></h1>
          <div class="flex items-center mt-2 space-x-2">
            <div class="flex">
              <?php
              $rating = $product->get_average_rating();
              for ($i = 1; $i <= 5; $i++) {
                if ($i <= round($rating)) {
                  echo '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#FFD100" stroke="#FFD100" class="h-5 w-5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>';
                } else {
                  echo '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#D1D5DB" class="h-5 w-5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>';
                }
              }
              ?>
            </div>
            <span class="text-sm text-gray-600"><?php echo esc_html($rating); ?> (<?php echo esc_html($product->get_review_count()); ?> reviews)</span>
          </div>
          <div class="mt-4">
            <div class="flex items-center space-x-2">
              <?php 
              $price = $product->get_price();
              $regular_price = $product->get_regular_price();
              $sale_price = $product->get_sale_price();
              
              if ($product->is_on_sale() && $sale_price) {
                echo '<span class="text-3xl font-bold text-[#FF3A5E]">' . wc_price($sale_price) . '</span>';
                echo '<span class="text-sm text-gray-500 line-through">' . wc_price($regular_price) . '</span>';
              } else {
                echo '<span class="text-3xl font-bold text-[#FF3A5E]">' . wc_price($price) . '</span>';
              }
              ?>
            </div>
          </div>
        </div>
        <p class="text-gray-600"><?php echo $product->get_short_description(); ?></p>

        <!-- Key Features -->
        <?php
        $features = get_post_meta($product->get_id(), '_product_key_features', true);
        if ($features && is_array($features) && !empty($features)) :
        ?>
        <div class="space-y-3">
          <h3 class="font-medium">Key Features</h3>
          <ul class="space-y-2">
            <?php foreach ($features as $feature) : ?>
              <li class="flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FF3A5E" class="h-5 w-5 mr-2 flex-shrink-0 mt-0.5">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span class="text-gray-700"><?php echo esc_html(trim($feature)); ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>

        <!-- Color Selection (example: use attribute 'pa_color') -->
        <?php if ($product->is_type('variable') && $product->get_attribute('pa_color')) : ?>
        <div>
          <h3 class="font-medium mb-3">Color</h3>
          <div class="flex space-x-2">
            <?php
            $colors = wc_get_product_terms($product->get_id(), 'pa_color', ['fields' => 'all']);
            foreach ($colors as $i => $color) {
              $color_val = strtolower($color->name);
              $border = $i === 0 ? 'border-[#FF3A5E]' : 'border-gray-200';
              echo '<button class="w-10 h-10 rounded-full border-2 ' . $border . '" style="background-color:' . esc_attr($color_val) . '" data-color="' . esc_attr($color_val) . '" aria-label="Select ' . esc_attr($color->name) . ' color"></button>';
            }
            ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Size Selection (example: use attribute 'pa_size') -->
        <?php if ($product->is_type('variable') && $product->get_attribute('pa_size')) : ?>
        <div>
          <div class="flex justify-between items-center mb-3">
            <h3 class="font-medium">Size</h3>
            <button id="size-guide-button" type="button" class="text-sm text-[#FF3A5E] hover:underline">Size Guide</button>
          </div>
          <div class="flex flex-wrap gap-2" id="size-selector">
            <?php
            $sizes = wc_get_product_terms($product->get_id(), 'pa_size', ['fields' => 'all']);
            foreach ($sizes as $i => $size) {
              $checked = $i === 0 ? 'checked' : '';
              $border = $i === 0 ? 'border-[#FF3A5E] text-[#FF3A5E]' : 'border-gray-200';
              echo '<div><input type="radio" name="size" id="size-' . esc_attr($size->slug) . '" value="' . esc_attr($size->slug) . '" class="peer sr-only" aria-label="' . esc_attr($size->name) . '" ' . $checked . '><label for="size-' . esc_attr($size->slug) . '" class="flex h-10 w-10 items-center justify-center rounded-full border-2 ' . $border . ' bg-white text-center cursor-pointer">' . esc_html($size->name) . '</label></div>';
            }
            ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Quantity -->
        <div>
          <h3 class="font-medium mb-3">Quantity</h3>
          <div class="flex items-center space-x-2">
            <button id="decrease-quantity" type="button" class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-100" aria-label="Decrease quantity">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            </button>
            <span id="quantity" class="w-10 text-center">1</span>
            <button id="increase-quantity" type="button" class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center hover:bg-gray-100" aria-label="Increase quantity">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            </button>
          </div>
        </div>

        <!-- Add to Cart, Wishlist, Share -->
        <div class="flex flex-wrap gap-4">
          <?php if ($product->is_in_stock()) : ?>
            <form class="cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype="multipart/form-data">
              <?php do_action('woocommerce_before_add_to_cart_button'); ?>
              
              <?php if ($product->is_type('variable')) : ?>
                <?php woocommerce_variable_add_to_cart(); ?>
              <?php else : ?>
                <?php wp_nonce_field('woocommerce-add-to-cart', 'woocommerce-add-to-cart-nonce'); ?>
                <input type="hidden" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" />
                <input type="hidden" name="quantity" id="quantity-input" value="1" />
                <input type="hidden" name="variation_id" value="0" />
                <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="single_add_to_cart_button button alt bg-[#FF3A5E] hover:bg-[#E02E50] text-white font-medium py-3 px-8 rounded-md transition-colors flex items-center justify-center gap-2 w-full md:w-auto" data-product_id="<?php echo esc_attr($product->get_id()); ?>" data-quantity="1">
                  <span class="button-text"><?php echo esc_html($product->single_add_to_cart_text()); ?></span>
                </button>
              <?php endif; ?>
              
              <?php do_action('woocommerce_after_add_to_cart_button'); ?>
            </form>
          <?php else : ?>
            <p class="stock out-of-stock"><?php echo esc_html(apply_filters('woocommerce_out_of_stock_message', __('This product is currently out of stock and unavailable.', 'woocommerce'))); ?></p>
          <?php endif; ?>
          <?php if (function_exists('YITH_WCWL')) : ?>
            <?php echo do_shortcode('[yith_wcwl_add_to_wishlist product_id="' . esc_attr($product->get_id()) . '" icon="custom" label="Add to Wishlist" already_in_wishslist_text="Added to Wishlist" browse_wishlist_text="Browse Wishlist" product_added_text="Product added to wishlist" link_classes="border-2 border-[#FF3A5E] text-[#FF3A5E] font-medium text-base min-w-[220px] h-[56px] px-8 rounded-[10px] bg-white flex items-center justify-center gap-2 transition-colors hover:bg-[#FF3A5E]/10"]'); ?>
          <?php else : ?>
            <button id="add-to-wishlist" class="border-2 border-[#FF3A5E] text-[#FF3A5E] font-medium text-base min-w-[220px] h-[56px] px-8 rounded-[10px] bg-white flex items-center justify-center gap-2 transition-colors hover:bg-[#FF3A5E]/10">
              Add to Wishlist
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#FF3A5E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 h-5 w-5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
            </button>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Tabs: Description, Additional Information, Reviews -->
    <div class="mt-16">
      <div class="border-b">
        <div class="flex">
          <button class="tab-button py-3 px-4 text-base font-medium border-b-2 border-[#FF3A5E] text-[#FF3A5E]" data-tab="description" aria-selected="true">Description</button>
          <button class="tab-button py-3 px-4 text-base font-medium border-b-2 border-transparent hover:text-[#FF3A5E]" data-tab="reviews" aria-selected="false">Reviews</button>
        </div>
      </div>
      <div id="description-tab" class="tab-content mt-6">
        <?php echo $product->get_description(); ?>
      </div>
      <div id="additional-tab" class="tab-content mt-6 hidden">
        <?php
        // WooCommerce attributes table
        wc_display_product_attributes($product);
        ?>
      </div>
      <div id="reviews-tab" class="tab-content mt-6 hidden">
        <?php comments_template(); ?>
      </div>
    </div>

    <!-- You May Also Like (Related Products) -->
    <div class="mt-20">
      <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-bold">You May Also Like</h2>
        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="text-[#FF3A5E] hover:underline flex items-center text-sm font-medium">View All Products</a>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php
        $related_products = wc_get_related_products($product->get_id(), 4);
        if ($related_products) :
          foreach ($related_products as $related_product_id) :
            $related_product = wc_get_product($related_product_id);
            if (!$related_product || !$related_product->is_visible()) continue;
            
            $product_link = get_permalink($related_product_id);
            $product_img = get_the_post_thumbnail_url($related_product_id, 'woocommerce_thumbnail');
            $product_title = $related_product->get_name();
            $product_price = $related_product->get_price_html();
            $review_count = $related_product->get_review_count();
            $average = $related_product->get_average_rating();
            $badge = '';

            // Check if product is on sale
            if ($related_product->is_on_sale()) {
              $badge = 'Sale';
            }
            // Check if product is new (less than 30 days old)
            elseif ((time() - strtotime($related_product->get_date_created())) < (30 * 24 * 60 * 60)) {
              $badge = 'New';
            }
            // Check if product is bestseller
            elseif ($related_product->get_total_sales() > 10) {
              $badge = 'Bestseller';
            }
        ?>
            <div class="rounded-lg overflow-hidden border-none shadow-md group bg-white">
              <a href="<?php echo esc_url($product_link); ?>" class="block">
                <div class="relative">
                  <div class="aspect-square relative overflow-hidden">
                    <?php if ($product_img) : ?>
                      <img
                        src="<?php echo esc_url($product_img); ?>"
                        alt="<?php echo esc_attr($product_title); ?>"
                        class="object-cover w-full h-full transition-transform group-hover:scale-105 duration-500">
                    <?php else : ?>
                      <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400">No Image</span>
                      </div>
                    <?php endif; ?>
                  </div>
                  <?php if ($badge) : ?>
                    <div class="absolute top-2 left-2 bg-[#FF3A5E] text-white text-xs font-semibold px-2 py-1 rounded-full">
                      <?php echo esc_html($badge); ?>
                    </div>
                  <?php endif; ?>
                  <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                    <div class="flex flex-col gap-2 w-full max-w-[200px] px-4">
                      <a
                        href="<?php echo esc_url($product_link); ?>"
                        class="bg-white text-black hover:bg-white/90 w-full py-2 px-4 rounded-full text-sm font-medium flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Quick View
                      </a>
                      <?php if ($related_product->is_purchasable() && $related_product->is_in_stock()) : ?>
                        <button
                          class="add_to_cart_button ajax_add_to_cart bg-[#FF3A5E] text-white hover:bg-[#FF3A5E]/90 w-full py-2 px-4 rounded-full text-sm font-medium flex items-center justify-center"
                          data-quantity="1"
                          data-product_id="<?php echo esc_attr($related_product_id); ?>"
                          data-product_sku="<?php echo esc_attr($related_product->get_sku()); ?>"
                          rel="nofollow">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                          </svg>
                          Add to Cart
                        </button>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
                <div class="p-4">
                  <div class="flex justify-between items-start mb-2">
                    <h3 class="font-medium text-lg font-montserrat hover:text-[#FF3A5E] transition-colors">
                      <?php echo esc_html($product_title); ?>
                    </h3>
                    <?php if (function_exists('YITH_WCWL')) : ?>
                      <?php echo do_shortcode('[yith_wcwl_add_to_wishlist product_id="' . esc_attr($related_product_id) . '" icon="custom" label="" already_in_wishslist_text="" browse_wishlist_text="" product_added_text="" link_classes="text-gray-700 hover:text-[#FF3A5E] p-1 rounded-full"]'); ?>
                    <?php else : ?>
                      <button
                        class="text-gray-700 hover:text-[#FF3A5E] p-1 rounded-full"
                        onclick="event.preventDefault(); addToWishlist(<?php echo esc_attr($related_product_id); ?>)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                      </button>
                    <?php endif; ?>
                  </div>
                  <div class="flex items-center gap-1 mb-2">
                    <?php
                    for ($i = 1; $i <= 5; $i++) {
                      if ($i <= round($average)) {
                        echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-[#FFD100] text-[#FFD100]" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>';
                      } else {
                        echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>';
                      }
                    }
                    ?>
                    <span class="text-xs text-gray-500 ml-1">(<?php echo esc_html($review_count); ?>)</span>
                  </div>
                  <p class="font-bold text-lg"><?php echo $product_price; ?></p>
                </div>
              </a>
            </div>
        <?php
          endforeach;
        endif;
        ?>
      </div>
    </div>
  </div>
</main>

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-md opacity-0 transition-opacity duration-300 z-50">
    Product added to cart
</div>

<script>
    function showToast(message) {
        const toast = document.getElementById('toast');
        if (toast) {
            toast.textContent = message;
            toast.classList.remove('opacity-0');
            toast.classList.add('opacity-100');
            setTimeout(() => {
                toast.classList.remove('opacity-100');
                toast.classList.add('opacity-0');
            }, 3000);
        }
    }

    // Handle quantity controls
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('quantity-input');
        const quantityDisplay = document.getElementById('quantity');
        const decreaseBtn = document.getElementById('decrease-quantity');
        const increaseBtn = document.getElementById('increase-quantity');

        function updateQuantity(value) {
            const newValue = Math.max(1, value);
            quantityInput.value = newValue;
            quantityDisplay.textContent = newValue;
        }

        decreaseBtn.addEventListener('click', function() {
            updateQuantity(parseInt(quantityInput.value) - 1);
        });

        increaseBtn.addEventListener('click', function() {
            updateQuantity(parseInt(quantityInput.value) + 1);
        });
    });

    // Handle gallery images
    document.addEventListener('DOMContentLoaded', function() {
        const mainImage = document.getElementById('main-image');
        const galleryButtons = document.querySelectorAll('[data-image]');

        galleryButtons.forEach(button => {
            button.addEventListener('click', function() {
                const imageUrl = this.getAttribute('data-image');
                const mainImageImg = mainImage.querySelector('img');
                
                // Update main image
                if (mainImageImg) {
                    mainImageImg.src = imageUrl;
                }

                // Update active state of gallery buttons
                galleryButtons.forEach(btn => {
                    btn.classList.remove('border-[#FF3A5E]');
                    btn.classList.add('border-gray-200');
                });
                this.classList.remove('border-gray-200');
                this.classList.add('border-[#FF3A5E]');
            });
        });
    });

    // Handle add to cart
    jQuery(document).ready(function($) {
        $('.single_add_to_cart_button').on('click', function(e) {
            e.preventDefault();

            var $button = $(this);
            var $form = $button.closest('form');
            var product_id = $button.data('product_id');
            var quantity = $('#quantity-input').val() || 1;

            // Add loading state
            $button.addClass('loading').text('Adding...');

            // Get form data
            var formData = new FormData($form[0]);
            formData.append('action', 'woocommerce_add_to_cart');

            $.ajax({
                url: wc_add_to_cart_params.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.error) {
                        showToast('Error adding to cart');
                    } else {
                        showToast('Product added to cart');
                        // Update cart count if needed
                        $(document.body).trigger('wc_fragment_refresh');
                    }
                },
                error: function() {
                    showToast('Error adding to cart');
                },
                complete: function() {
                    // Reset button
                    $button.removeClass('loading').html('<span class="button-text">Add to Cart</span>');
                }
            });
        });

        // Handle wishlist functionality
        function addToWishlist(productId) {
            if (typeof yith_wcwl_lists === 'undefined') {
                showToast('Wishlist functionality not available');
                return;
            }

            $.ajax({
                url: yith_wcwl_lists.ajax_url,
                type: 'POST',
                data: {
                    action: 'add_to_wishlist',
                    product_id: productId,
                    wishlist_id: yith_wcwl_lists.default_wishlist,
                    add_to_wishlist: productId,
                    _wpnonce: yith_wcwl_lists.nonce
                },
                success: function(response) {
                    if (response.success) {
                        showToast('Product added to wishlist');
                        // Update wishlist count if needed
                        if (response.fragments) {
                            $.each(response.fragments, function(key, value) {
                                $(key).replaceWith(value);
                            });
                        }
                    } else {
                        showToast(response.message || 'Error adding to wishlist');
                    }
                },
                error: function() {
                    showToast('Error adding to wishlist');
                }
            });
        }

        // Make addToWishlist function globally available
        window.addToWishlist = addToWishlist;

        // Handle wishlist button clicks
        $('.add_to_wishlist').on('click', function(e) {
            e.preventDefault();
            const productId = $(this).data('product-id');
            addToWishlist(productId);
        });
    });
</script>

<style>
    /* YITH Wishlist Button Styling */
    .yith-wcwl-add-to-wishlist {
        margin: 0 !important;
    }
    .yith-wcwl-add-to-wishlist a {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0.5rem !important;
        text-decoration: none !important;
    }
    .yith-wcwl-add-to-wishlist a i {
        margin: 0 !important;
        font-size: 1.25rem !important;
    }
    .yith-wcwl-add-to-wishlist a:hover {
        text-decoration: none !important;
    }
    .yith-wcwl-add-to-wishlist .feedback {
        display: none !important;
    }
    .yith-wcwl-add-to-wishlist .yith-wcwl-icon {
        margin: 0 !important;
    }
    .yith-wcwl-add-to-wishlist .yith-wcwl-icon i {
        font-size: 1.25rem !important;
    }
    .yith-wcwl-add-to-wishlist .yith-wcwl-icon i:before {
        content: "" !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='22' height='22' viewBox='0 0 24 24' fill='none' stroke='%23FF3A5E' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z'%3E%3C/path%3E%3C/svg%3E");
        width: 1.25rem;
        height: 1.25rem;
        display: block;
    }
    .yith-wcwl-add-to-wishlist .yith-wcwl-icon i.fa-heart:before {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='22' height='22' viewBox='0 0 24 24' fill='%23FF3A5E' stroke='%23FF3A5E' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z'%3E%3C/path%3E%3C/svg%3E");
    }
</style>

<?php get_footer('shop'); ?>
