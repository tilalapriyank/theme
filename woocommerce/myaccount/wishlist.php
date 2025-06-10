<?php
// Example using TI WooCommerce Wishlist plugin. Adjust if using another plugin.
if (function_exists('tinv_wishlist')) {
    echo do_shortcode('[ti_wishlistsview]');
} else {
    // Fallback: show a message or custom implementation
    echo '<div class="p-6 text-center text-gray-500">No wishlist plugin found. Please install a WooCommerce wishlist plugin for full functionality.</div>';
} 