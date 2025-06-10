<?php
/**
 * Theme functions and definitions
 */

// Ensure WooCommerce is loaded
if (!class_exists('WooCommerce')) {
    return;
}

// Add WooCommerce support
function hype_pups_add_woocommerce_support() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'hype_pups_add_woocommerce_support');

// Include ACF fields
if (file_exists(get_template_directory() . '/inc/acf-fields.php')) {
    require_once get_template_directory() . '/inc/acf-fields.php';
}

// Include tag functionality
if (file_exists(get_template_directory() . '/inc/tags.php')) {
    require_once get_template_directory() . '/inc/tags.php';
}

// Include navigation walkers
if (file_exists(get_template_directory() . '/inc/nav-walkers.php')) {
    require_once get_template_directory() . '/inc/nav-walkers.php';
}

// Add WooCommerce support
function hype_pups_woocommerce_template_path() {
    return 'woocommerce/';
}
add_filter('woocommerce_template_path', 'hype_pups_woocommerce_template_path');

// Include WordPress core functions
require_once(ABSPATH . 'wp-includes/pluggable.php');
require_once(ABSPATH . 'wp-includes/formatting.php');
require_once(ABSPATH . 'wp-includes/link-template.php');

// Include WooCommerce functions
require_once(WC()->plugin_path() . '/includes/wc-template-functions.php');
require_once(WC()->plugin_path() . '/includes/wc-account-functions.php');

add_filter('use_block_editor_for_post_type', '__return_false', 10, 2);

function hype_pups_theme_setup() {
    // Add theme support features
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'hype-pups'),
        'mobile'  => __('Mobile Menu', 'hype-pups'),
        'footer_shop' => __('Footer Shop Menu', 'hype-pups'),
        'footer_company' => __('Footer Company Menu', 'hype-pups'),
        'footer_orders' => __('Footer Orders Menu', 'hype-pups'),
        'footer_bottom' => __('Footer Bottom Menu', 'hype-pups'),
    ));

    // Add customizer settings
    add_action('customize_register', 'hype_pups_customize_register');
}
add_action('after_setup_theme', 'hype_pups_theme_setup');

// Register additional menu locations
function hype_pups_register_menus() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'hype-pups'),
        'mobile'  => __('Mobile Menu', 'hype-pups'),
        'footer_shop' => __('Footer Shop Menu', 'hype-pups'),
        'footer_company' => __('Footer Company Menu', 'hype-pups'),
        'footer_orders' => __('Footer Orders Menu', 'hype-pups'),
        'footer_bottom' => __('Footer Bottom Menu', 'hype-pups'),
        'top_bar' => __('Top Bar Menu', 'hype-pups'),
        'account_menu' => __('Account Menu', 'hype-pups'),
    ));
}
add_action('init', 'hype_pups_register_menus');

// Enqueue scripts and styles
function hype_pups_scripts() {
    // Enqueue custom CSS
    wp_enqueue_style('hype-pups-style', get_stylesheet_uri());
    
    // Enqueue custom JS
    wp_enqueue_script('hype-pups-script', get_template_directory_uri() . '/assets/js/main.js', array(), '1.0', true);
    
    // Enqueue shop JS
    if (is_shop() || is_product_category() || is_product_tag()) {
        wp_enqueue_script('hype-pups-shop', get_template_directory_uri() . '/assets/js/shop.js', array('jquery'), '1.0', true);
        wp_localize_script('hype-pups-shop', 'wc_add_to_cart_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'cart_url' => wc_get_cart_url(),
        ));
    }

    // Enqueue product details JS
    if (is_product() || is_shop() || is_product_category()) {
        wp_enqueue_script('hype-pups-product-details', get_template_directory_uri() . '/assets/js/product-details.js', array('jquery'), '1.0', true);
        wp_localize_script('hype-pups-product-details', 'wc_add_to_cart_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'cart_url' => wc_get_cart_url(),
        ));
    }
}
add_action('wp_enqueue_scripts', 'hype_pups_scripts');

function enqueue_swiper_assets() {
    // Swiper CSS
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
    // Swiper JS
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_swiper_assets');

// FIXED: Variable Product AJAX Handlers - ADD THESE TO YOUR FUNCTIONS.PHP

// AJAX handler for finding variation ID based on attributes
add_action('wp_ajax_find_variation_id', 'find_variation_id_ajax');
add_action('wp_ajax_nopriv_find_variation_id', 'find_variation_id_ajax');

function find_variation_id_ajax() {
    if (!isset($_POST['product_id']) || !isset($_POST['attributes'])) {
        wp_send_json_error('Missing required parameters');
    }
    
    $product_id = intval($_POST['product_id']);
    $attributes_input = $_POST['attributes'];
    
    // Handle both JSON string and array input
    if (is_string($attributes_input)) {
        $attributes = json_decode(stripslashes($attributes_input), true);
    } else {
        $attributes = $attributes_input;
    }
    
    if (!$attributes) {
        wp_send_json_error('Invalid attributes format');
    }
    
    $product = wc_get_product($product_id);
    
    if (!$product || !$product->is_type('variable')) {
        wp_send_json_error('Invalid variable product');
    }
    
    // Get available variations
    $available_variations = $product->get_available_variations();
    
    // Find matching variation
    foreach ($available_variations as $variation) {
        $variation_attributes = $variation['attributes'];
        $match = true;
        
        foreach ($attributes as $attribute_name => $attribute_value) {
            // Ensure proper attribute name format
            if (!str_starts_with($attribute_name, 'attribute_')) {
                $variation_attribute_name = 'attribute_' . $attribute_name;
            } else {
                $variation_attribute_name = $attribute_name;
            }
            
            // Check if this variation has the required attribute value
            if (!isset($variation_attributes[$variation_attribute_name])) {
                $match = false;
                break;
            }
            
            $variation_value = $variation_attributes[$variation_attribute_name];
            
            // Handle empty variation attributes (means "any")
            if ($variation_value !== '' && $variation_value !== $attribute_value) {
                $match = false;
                break;
            }
        }
        
        if ($match) {
            wp_send_json_success([
                'variation_id' => $variation['variation_id'],
                'is_purchasable' => $variation['is_purchasable'],
                'is_in_stock' => $variation['is_in_stock'],
                'price_html' => $variation['price_html'],
                'matched_attributes' => $variation_attributes
            ]);
        }
    }
    
    wp_send_json_error('No matching variation found for the selected attributes');
}

// Enhanced AJAX add to cart handler for variable products
add_action('wp_ajax_add_variable_to_cart', 'add_variable_to_cart_ajax');
add_action('wp_ajax_nopriv_add_variable_to_cart', 'add_variable_to_cart_ajax');

function add_variable_to_cart_ajax() {
    if (!isset($_POST['product_id']) || !isset($_POST['variation_id'])) {
        wp_send_json_error('Missing required parameters');
    }
    
    $product_id = intval($_POST['product_id']);
    $variation_id = intval($_POST['variation_id']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $attributes = isset($_POST['attributes']) ? $_POST['attributes'] : array();
    
    // Validate the variation
    $variation = wc_get_product($variation_id);
    if (!$variation || !$variation->is_purchasable()) {
        wp_send_json_error('Product variation is not available');
    }
    
    // Format attributes for cart
    $variation_data = array();
    foreach ($attributes as $key => $value) {
        $variation_data['attribute_' . $key] = $value;
    }
    
    // Add to cart
    $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation_data);
    
    if ($cart_item_key) {
        // Get updated cart fragments
        WC_AJAX::get_refreshed_fragments();
    } else {
        wp_send_json_error('Failed to add product to cart');
    }
}

// AJAX handler to get product variations
add_action('wp_ajax_get_product_variations', 'get_product_variations_ajax');
add_action('wp_ajax_nopriv_get_product_variations', 'get_product_variations_ajax');

function get_product_variations_ajax() {
    if (!isset($_POST['product_id'])) {
        wp_send_json_error('Missing product ID');
    }
    
    $product_id = intval($_POST['product_id']);
    $product = wc_get_product($product_id);
    
    if (!$product || !$product->is_type('variable')) {
        wp_send_json_error('Invalid variable product');
    }
    
    $variations = $product->get_available_variations();
    
    // Filter out unnecessary data to reduce payload size
    $filtered_variations = array();
    foreach ($variations as $variation) {
        $filtered_variations[] = array(
            'variation_id' => $variation['variation_id'],
            'attributes' => $variation['attributes'],
            'is_purchasable' => $variation['is_purchasable'],
            'is_in_stock' => $variation['is_in_stock'],
            'price_html' => $variation['price_html']
        );
    }
    
    wp_send_json_success($filtered_variations);
}

// Debug function to check variations
function debug_product_variations($product_id) {
    $product = wc_get_product($product_id);
    
    if (!$product || !$product->is_type('variable')) {
        return 'Not a variable product';
    }
    
    $variations = $product->get_available_variations();
    
    echo '<pre>';
    echo "Product ID: $product_id\n";
    echo "Total Variations: " . count($variations) . "\n\n";
    
    foreach ($variations as $variation) {
        echo "Variation ID: " . $variation['variation_id'] . "\n";
        echo "Attributes: " . print_r($variation['attributes'], true) . "\n";
        echo "In Stock: " . ($variation['is_in_stock'] ? 'Yes' : 'No') . "\n";
        echo "Purchasable: " . ($variation['is_purchasable'] ? 'Yes' : 'No') . "\n";
        echo "---\n";
    }
    echo '</pre>';
}

// Shortcode to debug variations (use [debug_variations id="105"])
add_shortcode('debug_variations', function($atts) {
    $atts = shortcode_atts(['id' => 0], $atts);
    if ($atts['id']) {
        ob_start();
        debug_product_variations($atts['id']);
        return ob_get_clean();
    }
    return 'Please provide product ID';
});

// Fix for single product page variations
add_action('wp_footer', 'add_variable_product_scripts');

function add_variable_product_scripts() {
    if (is_product()) {
        global $product;
        if ($product && $product->is_type('variable')) {
            ?>
            <script>
            jQuery(document).ready(function($) {
                // Override the default variation form behavior
                $('form.variations_form').on('woocommerce_variation_has_changed', function() {
                    var $form = $(this);
                    var product_id = $form.find('input[name="product_id"]').val();
                    var $variations = $form.find('select[name^="attribute_"]');
                    var attributes = {};
                    var allSelected = true;
                    
                    $variations.each(function() {
                        var attribute_name = $(this).attr('name');
                        var attribute_value = $(this).val();
                        
                        if (attribute_value === '') {
                            allSelected = false;
                        } else {
                            attributes[attribute_name] = attribute_value;
                        }
                    });
                    
                    if (allSelected) {
                        // Find the variation ID
                        $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                            action: 'find_variation_id',
                            product_id: product_id,
                            attributes: attributes
                        }, function(response) {
                            if (response.success) {
                                $form.find('input[name="variation_id"]').val(response.data.variation_id);
                                
                                // Update add to cart button
                                var $button = $form.find('.single_add_to_cart_button');
                                if (response.data.is_purchasable && response.data.is_in_stock) {
                                    $button.removeClass('disabled wc-variation-is-unavailable')
                                           .addClass('wc-variation-selection-needed');
                                } else {
                                    $button.addClass('disabled wc-variation-is-unavailable');
                                }
                            }
                        });
                    }
                });
                
                // Enhanced add to cart for variable products
                $('form.variations_form').on('submit', function(e) {
                    var $form = $(this);
                    var $button = $form.find('.single_add_to_cart_button');
                    
                    // Check if it's an AJAX add to cart
                    if ($button.hasClass('ajax_add_to_cart')) {
                        e.preventDefault();
                        
                        var product_id = $form.find('input[name="product_id"]').val();
                        var variation_id = $form.find('input[name="variation_id"]').val();
                        var quantity = $form.find('input[name="quantity"]').val();
                        var attributes = {};
                        
                        $form.find('select[name^="attribute_"]').each(function() {
                            var name = $(this).attr('name').replace('attribute_', '');
                            attributes[name] = $(this).val();
                        });
                        
                        if (!variation_id || variation_id === '0') {
                            alert('Please select all product options');
                            return false;
                        }
                        
                        // Show loading
                        $button.addClass('loading').text('Adding...');
                        
                        $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                            action: 'add_variable_to_cart',
                            product_id: product_id,
                            variation_id: variation_id,
                            quantity: quantity,
                            attributes: attributes
                        }, function(response) {
                            if (response.success) {
                                // Update cart fragments
                                $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
                                
                                // Show success
                                $button.removeClass('loading').addClass('added').text('Added!');
                                
                                setTimeout(function() {
                                    $button.removeClass('added').text('Add to cart');
                                }, 2000);
                            } else {
                                $button.removeClass('loading');
                                alert('Error: ' + (response.data || 'Failed to add to cart'));
                            }
                        }).fail(function() {
                            $button.removeClass('loading');
                            alert('Error adding product to cart');
                        });
                    }
                });
            });
            </script>
            <?php
        }
    }
}

// Register Blog Post Type
function register_blog_post_type() {
    $labels = array(
        'name'               => 'Blog Posts',
        'singular_name'      => 'Blog Post',
        'menu_name'          => 'Blog Posts',
        'add_new'           => 'Add New',
        'add_new_item'      => 'Add New Blog Post',
        'edit_item'         => 'Edit Blog Post',
        'new_item'          => 'New Blog Post',
        'view_item'         => 'View Blog Post',
        'search_items'      => 'Search Blog Posts',
        'not_found'         => 'No blog posts found',
        'not_found_in_trash'=> 'No blog posts found in Trash'
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'publicly_queryable'  => true,
        'query_var'           => true,
        'rewrite'            => array('slug' => 'blog'),
        'capability_type'     => 'post',
        'hierarchical'        => false,
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
        'menu_position'       => 5,
        'menu_icon'          => 'dashicons-admin-post',
        'show_in_rest'       => true
    );

    register_post_type('blog_post', $args);

    // Register Blog Categories Taxonomy
    register_taxonomy('blog_category', 'blog_post', array(
        'label' => 'Blog Categories',
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'blog-category'),
    ));
}
add_action('init', 'register_blog_post_type');

// Add ACF fields for blog posts
if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
        'key' => 'group_blog_fields',
        'title' => 'Blog Post Details',
        'fields' => array(
            array(
                'key' => 'field_author_name',
                'label' => 'Author Name',
                'name' => 'author_name',
                'type' => 'text',
                'required' => 1,
            ),
            array(
                'key' => 'field_author_avatar',
                'label' => 'Author Avatar',
                'name' => 'author_avatar',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'thumbnail',
                'required' => 1,
            ),
            array(
                'key' => 'field_featured_post',
                'label' => 'Featured Post',
                'name' => 'featured_post',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'blog_post',
                ),
            ),
        ),
    ));
}

// Add ACF fields for blog page settings
if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
        'key' => 'group_blog_page_settings',
        'title' => 'Blog Page Settings',
        'fields' => array(
            array(
                'key' => 'field_blog_hero_image',
                'label' => 'Hero Background Image',
                'name' => 'blog_hero_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'required' => 1,
            ),
            array(
                'key' => 'field_blog_description',
                'label' => 'Blog Description',
                'name' => 'blog_description',
                'type' => 'textarea',
                'required' => 1,
                'default_value' => 'Insights, stories, and guides from the world of premium dog streetwear. Stay updated with the latest trends, behind-the-scenes content, and community stories.',
            ),
            array(
                'key' => 'field_newsletter_title',
                'label' => 'Newsletter Title',
                'name' => 'newsletter_title',
                'type' => 'text',
                'required' => 1,
                'default_value' => 'Join Our Newsletter',
            ),
            array(
                'key' => 'field_newsletter_description',
                'label' => 'Newsletter Description',
                'name' => 'newsletter_description',
                'type' => 'textarea',
                'required' => 1,
                'default_value' => 'Get the latest articles, style guides, and exclusive offers delivered directly to your inbox. No spam, just the content you want.',
            ),
            array(
                'key' => 'field_newsletter_image',
                'label' => 'Newsletter Image',
                'name' => 'newsletter_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'required' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'page-blog.php',
                ),
            ),
        ),
    ));
}

/**
 * Calculate reading time for blog posts
 * @return int Reading time in minutes
 */
function reading_time() {
    $content = get_post_field('post_content', get_the_ID());
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Assuming 200 words per minute reading speed
    return max(1, $reading_time); // Return at least 1 minute
}

/**
 * Track post views
 */
function track_post_views() {
    if (is_single()) {
        $post_id = get_the_ID();
        $count = get_post_meta($post_id, 'post_views_count', true);
        if ($count == '') {
            delete_post_meta($post_id, 'post_views_count');
            add_post_meta($post_id, 'post_views_count', 1);
        } else {
            update_post_meta($post_id, 'post_views_count', $count + 1);
        }
    }
}
add_action('wp_head', 'track_post_views');

/**
 * Custom comment callback function
 */
function hype_pups_comment_callback($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    $comment_id = get_comment_ID();
    $comment_author = get_comment_author();
    $comment_date = get_comment_date('F j, Y');
    $comment_time = get_comment_time();
    $comment_content = get_comment_text();
    $comment_avatar = get_avatar($comment, 50);
    $comment_reply_link = get_comment_reply_link(array(
        'reply_text' => 'Reply',
        'depth' => $depth,
        'max_depth' => $args['max_depth'],
        'before' => '<span class="text-[#FF3A5E] hover:underline text-sm font-medium">',
        'after' => '</span>'
    ));
    ?>
    <div id="comment-<?php echo $comment_id; ?>" class="comment">
        <div class="flex gap-4">
            <div class="flex-shrink-0">
                <?php echo $comment_avatar; ?>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h4 class="font-medium"><?php echo $comment_author; ?></h4>
                        <p class="text-sm text-gray-500">
                            <?php echo $comment_date; ?> at <?php echo $comment_time; ?>
                        </p>
                    </div>
                    <?php if ($comment_reply_link) : ?>
                        <div>
                            <?php echo $comment_reply_link; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="prose prose-sm max-w-none">
                    <?php echo $comment_content; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Comment end callback function
 */
function hype_pups_comment_end_callback($comment, $args, $depth) {
    echo '</div>'; // Close the comment div
}

/**
 * Add comment form fields
 */
function hype_pups_comment_form_fields($fields) {
    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $aria_req = ($req ? " aria-required='true'" : '');
    
    $fields['author'] = '<div class="flex flex-col sm:flex-row gap-4 mb-4">
        <input 
            type="text" 
            name="author" 
            placeholder="Name' . ($req ? ' *' : '') . '" 
            value="' . esc_attr($commenter['comment_author']) . '" 
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#FF3A5E]"
            ' . $aria_req . '
        >';
    
    $fields['email'] = '<input 
        type="email" 
        name="email" 
        placeholder="Email' . ($req ? ' *' : '') . '" 
        value="' . esc_attr($commenter['comment_author_email']) . '" 
        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#FF3A5E]"
        ' . $aria_req . '
    >
    </div>';
    
    $fields['url'] = '<input 
        type="url" 
        name="url" 
        placeholder="Website" 
        value="' . esc_attr($commenter['comment_author_url']) . '" 
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#FF3A5E] mb-4"
    >';
    
    return $fields;
}
add_filter('comment_form_default_fields', 'hype_pups_comment_form_fields');

/**
 * Add comment form comment field
 */
function hype_pups_comment_form_comment_field($comment_field) {
    $comment_field = '<div class="mb-4">
        <textarea 
            name="comment" 
            placeholder="Join the discussion..." 
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-[#FF3A5E]"
            rows="4"
            required
        ></textarea>
    </div>';
    return $comment_field;
}
add_filter('comment_form_field_comment', 'hype_pups_comment_form_comment_field');

/**
 * Add comment form submit button
 */
function hype_pups_comment_form_submit_button($submit_button) {
    return '<button type="submit" class="bg-[#FF3A5E] hover:bg-[#E02E50] text-white font-medium py-2 px-6 rounded-lg transition-colors">Post Comment</button>';
}
add_filter('comment_form_submit_button', 'hype_pups_comment_form_submit_button');

/**
 * Add comment form cookie consent
 */
function hype_pups_comment_form_cookie_consent($fields) {
    $fields['cookies'] = '<div class="flex items-center mb-4">
        <input type="checkbox" id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" value="yes" class="mr-2">
        <label for="wp-comment-cookies-consent" class="text-sm text-gray-600">Save my name and email for the next time I comment</label>
    </div>';
    return $fields;
}
add_filter('comment_form_default_fields', 'hype_pups_comment_form_cookie_consent');

/**
 * Handle comment submission via AJAX
 */
function hype_pups_handle_comment_submission() {
    check_ajax_referer('comment_nonce', 'nonce');
    
    $comment_data = array(
        'comment_post_ID' => intval($_POST['post_id']),
        'comment_author' => sanitize_text_field($_POST['author']),
        'comment_author_email' => sanitize_email($_POST['email']),
        'comment_author_url' => esc_url_raw($_POST['url']),
        'comment_content' => wp_kses_post($_POST['comment']),
        'comment_type' => 'comment',
        'comment_parent' => 0,
        'user_id' => get_current_user_id(),
        'comment_approved' => 1
    );
    
    $comment_id = wp_insert_comment($comment_data);
    
    if ($comment_id) {
        wp_send_json_success(array(
            'message' => 'Comment posted successfully!',
            'comment_id' => $comment_id
        ));
    } else {
        wp_send_json_error(array(
            'message' => 'Failed to post comment. Please try again.'
        ));
    }
}
add_action('wp_ajax_submit_comment', 'hype_pups_handle_comment_submission');
add_action('wp_ajax_nopriv_submit_comment', 'hype_pups_handle_comment_submission');

function hype_pups_enqueue_product_tabs_js() {
    if (is_product()) {
        wp_enqueue_script('hype-pups-product-tabs', get_template_directory_uri() . '/assets/js/product-tabs.js', array(), '1.0', true);
    }
}
add_action('wp_enqueue_scripts', 'hype_pups_enqueue_product_tabs_js');

// Add support for ACF
function hype_pups_acf_init() {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page(array(
            'page_title' => 'Theme Settings',
            'menu_title' => 'Theme Settings',
            'menu_slug' => 'theme-settings',
            'capability' => 'edit_posts',
            'redirect' => false
        ));
    }
}
add_action('acf/init', 'hype_pups_acf_init');

// Add ACF fields support
function hype_pups_acf_fields() {
    if (function_exists('acf_add_local_field_group')) {
        // Fields are defined in inc/acf-fields.php
    }
}
add_action('acf/init', 'hype_pups_acf_fields');

// Add custom meta box for product key features
function hype_pups_add_product_key_features_meta_box() {
    add_meta_box(
        'product_key_features',
        'Key Features',
        'hype_pups_product_key_features_callback',
        'product',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'hype_pups_add_product_key_features_meta_box');

// Callback function to display the meta box
function hype_pups_product_key_features_callback($post) {
    $features = get_post_meta($post->ID, '_product_key_features', true);
    if (!is_array($features)) {
        $features = array();
    }
    ?>
    <div class="key-features-container">
        <div id="key-features-list">
            <?php foreach ($features as $index => $feature) : ?>
                <div class="key-feature-item">
                    <input type="text" name="product_key_features[]" value="<?php echo esc_attr($feature); ?>" class="widefat">
                    <button type="button" class="button remove-feature" style="color: #dc2626;">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="button add-feature" style="margin-top: 10px;">Add Feature</button>
    </div>
    <style>
        .key-feature-item {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        .key-feature-item input {
            flex: 1;
        }
        .remove-feature {
            color: #dc2626;
        }
    </style>
    <script>
    jQuery(document).ready(function($) {
        // Add new feature
        $('.add-feature').on('click', function() {
            var newFeature = `
                <div class="key-feature-item">
                    <input type="text" name="product_key_features[]" value="" class="widefat">
                    <button type="button" class="button remove-feature" style="color: #dc2626;">Remove</button>
                </div>
            `;
            $('#key-features-list').append(newFeature);
        });

        // Remove feature
        $(document).on('click', '.remove-feature', function() {
            $(this).closest('.key-feature-item').remove();
        });
    });
    </script>
    <?php
}

// Save the meta box data
function hype_pups_save_product_key_features($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    if (isset($_POST['product_key_features'])) {
        $features = array_map('sanitize_text_field', $_POST['product_key_features']);
        $features = array_filter($features); // Remove empty values
        update_post_meta($post_id, '_product_key_features', $features);
    }
}
add_action('save_post_product', 'hype_pups_save_product_key_features');

add_action('wp_ajax_save_account_address', function() {
    if (!is_user_logged_in()) {
        wp_send_json_error('Not logged in');
    }
    $user_id = get_current_user_id();
    $type = $_POST['address_type'] ?? 'shipping';
    $fields = [
        'first_name', 'last_name', 'address_1', 'address_2', 'city', 'state', 'postcode', 'country'
    ];
    $meta_prefix = $type . '_';
    foreach ($fields as $field) {
        update_user_meta($user_id, $meta_prefix . $field, sanitize_text_field($_POST[$field] ?? ''));
    }
    wp_send_json_success('Address saved');
});

add_action('woocommerce_save_account_details', function($user_id) {
    if (isset($_POST['account_phone'])) {
        update_user_meta($user_id, 'billing_phone', sanitize_text_field($_POST['account_phone']));
    }
});

// Add AJAX handler for getting cart contents
add_action('wp_ajax_get_cart_contents', 'hype_pups_get_cart_contents');
add_action('wp_ajax_nopriv_get_cart_contents', 'hype_pups_get_cart_contents');

function hype_pups_get_cart_contents() {
    $cart_items = array();
    
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
        
        if ($_product && $_product->exists() && $cart_item['quantity'] > 0) {
            $cart_items[] = array(
                'product_id' => $_product->get_id(),
                'name' => $_product->get_name(),
                'price' => $_product->get_price(),
                'quantity' => $cart_item['quantity'],
                'image' => wp_get_attachment_image_url($_product->get_image_id(), 'thumbnail'),
                'url' => get_permalink($_product->get_id()),
                'size' => isset($cart_item['variation']['attribute_pa_size']) ? $cart_item['variation']['attribute_pa_size'] : '',
                'color' => isset($cart_item['variation']['attribute_pa_color']) ? $cart_item['variation']['attribute_pa_color'] : ''
            );
        }
    }
    
    wp_send_json_success($cart_items);
}

// Enqueue WooCommerce scripts and styles
function hype_pups_woocommerce_scripts() {
    if (is_shop() || is_product_category() || is_product_tag()) {
        // Only enqueue custom AJAX for shop/archive pages
        wp_enqueue_script('hype-pups-ajax-add-to-cart', get_template_directory_uri() . '/assets/js/ajax-add-to-cart.js', array('jquery'), '1.0', true);
        wp_localize_script('hype-pups-ajax-add-to-cart', 'hype_pups_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('hype-pups-ajax-nonce')
        ));
    }
    // DO NOT enqueue custom AJAX on single product page!
}
add_action('wp_enqueue_scripts', 'hype_pups_woocommerce_scripts');

function hype_pups_enqueue_checkout_assets() {
    if (is_checkout()) {
        wp_enqueue_style('tailwindcss', 'https://cdn.tailwindcss.com');
        wp_enqueue_style('montserrat-font', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap', false);
    }
}
add_action('wp_enqueue_scripts', 'hype_pups_enqueue_checkout_assets');

// Register Custom Product Widget
class HypePups_Product_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'hypepups_product_widget',
            __('HypePups Product Widget', 'hype-pups'),
            array('description' => __('Displays a grid of WooCommerce products with theme styling.', 'hype-pups'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        include get_template_directory() . '/woocommerce/widgets/product-widget.php';
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Products', 'woocommerce');
        $count = isset($instance['count']) ? (int)$instance['count'] : 6;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Number of products to show:'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($count); ?>" size="3" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['count'] = (!empty($new_instance['count'])) ? (int)$new_instance['count'] : 6;
        return $instance;
    }
}

function hypepups_register_product_widget() {
    register_widget('HypePups_Product_Widget');
}
add_action('widgets_init', 'hypepups_register_product_widget');

// Add theme styling to WooCommerce loop add-to-cart button
add_filter('woocommerce_loop_add_to_cart_link', function($button, $product) {
    if ($product && $product->is_type('simple')) {
        $button = sprintf(
            '<a href="%s" data-quantity="1" data-product_id="%s" data-product_sku="%s" class="button product_type_simple add_to_cart_button ajax_add_to_cart bg-[#FF3A5E] text-white hover:bg-[#FF3A5E]/90 w-full py-2 px-4 rounded-full text-sm font-medium flex items-center justify-center gap-2" rel="nofollow">'
            . '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>'
            . '%s'
            . '</a>',
            esc_url($product->add_to_cart_url()),
            esc_attr($product->get_id()),
            esc_attr($product->get_sku()),
            esc_html($product->add_to_cart_text())
        );
    }
    return $button;
}, 10, 2);

// Force enqueue WooCommerce add-to-cart script on shop/archive pages
add_action('wp_enqueue_scripts', function() {
    if (is_shop() || is_product_category() || is_product_tag()) {
        wp_enqueue_script('wc-add-to-cart');
        wp_enqueue_script('wc-cart-fragments');
    }
});


// Add to your theme's functions.php - REMOVE after debugging
add_action('wp_ajax_woocommerce_add_to_cart_variable_product', 'debug_ajax_add_to_cart');
add_action('wp_ajax_nopriv_woocommerce_add_to_cart_variable_product', 'debug_ajax_add_to_cart');

function debug_ajax_add_to_cart() {
    error_log('AJAX Add to Cart Debug:');
    error_log('POST data: ' . print_r($_POST, true));
    
    if (isset($_POST['product_id'])) {
        $product = wc_get_product($_POST['product_id']);
        error_log('Product type: ' . $product->get_type());
        error_log('Available variations: ' . print_r($product->get_available_variations(), true));
    }
}

// Ensure WooCommerce variation scripts are loaded on single product pages
function load_wc_variation_scripts() {
    if (is_product()) {
        wp_enqueue_script('wc-add-to-cart-variation');
    }
}
add_action('wp_enqueue_scripts', 'load_wc_variation_scripts');
