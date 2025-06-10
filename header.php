<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title>
    <meta name="description" content="<?php bloginfo('description'); ?>">
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/favicon.ico">
    <link rel="apple-touch-icon" href="<?php echo get_template_directory_uri(); ?>/assets/images/apple-touch-icon.png">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                container: {
      center: true,
      padding: "2rem",
      screens: {
        "2xl": "1400px",
      },
    },
                extend: {
                    fontFamily: {
                        montserrat: ['Montserrat', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/styles.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <?php wp_head(); ?>
</head>
<body <?php body_class('font-montserrat'); ?>>
    <?php wp_body_open(); ?>
    
    <!-- Header -->
    <header class="sticky top-0 z-50 w-full bg-white border-b border-gray-200" x-data="{ mobileMenuOpen: false, activeMenu: null }">
        <!-- Announcement Bar -->
        <div class="bg-[#FF3A5E] text-white py-2 text-center text-sm">
            <p><?php echo get_theme_mod('announcement_text', 'Free shipping on orders over $100 | Use code WELCOME10 for 10% off your first order'); ?></p>
        </div>

        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <?php
                    if (has_custom_logo()) {
                        the_custom_logo();
                    } else {
                        echo '<a href="' . esc_url(home_url('/')) . '" class="text-2xl font-bold text-[#FF3A5E]">' . get_bloginfo('name') . '</a>';
                    }
                    ?>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-8">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'container' => false,
                        'menu_class' => 'flex items-center space-x-8',
                        'fallback_cb' => false,
                        'items_wrap' => '%3$s',
                        'walker' => new Hype_Pups_Nav_Walker()
                    ));
                    ?>
                </nav>

                <!-- Mobile Menu Button -->
                <button 
                    @click="mobileMenuOpen = !mobileMenuOpen" 
                    class="md:hidden p-2 rounded-md text-gray-700 hover:text-[#FF3A5E]"
                    aria-expanded="false"
                    aria-controls="mobile-menu"
                >
                    <svg x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                    <svg x-show="mobileMenuOpen" style="display: none;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>

                <!-- Right Side Icons -->
                <div class="flex items-center space-x-4">
                    <!-- Search Icon -->
                    <button class="text-gray-700 hover:text-[#FF3A5E]" aria-label="Search">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                    
                    <!-- Account Icon -->
                    <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" class="text-gray-700 hover:text-[#FF3A5E]" aria-label="Account">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </a>
                    
                    <!-- Cart Button -->
                    <button @click="$store.cart.isOpen = true" 
                            class="relative p-2 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <span class="sr-only">View cart</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span x-show="$store.cart.totalItems > 0"
                              x-text="$store.cart.totalItems"
                              class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary-600 text-xs font-medium text-white">
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div 
            x-show="mobileMenuOpen" 
            x-transition
            class="md:hidden bg-white border-t border-gray-200"
            id="mobile-menu"
            role="navigation"
            aria-label="Mobile navigation"
            style="display: none;"
        >
            <?php
            wp_nav_menu(array(
                'theme_location' => 'mobile',
                'container' => false,
                'menu_class' => 'px-2 pt-2 pb-3 space-y-1',
                'fallback_cb' => false,
                'walker' => new Hype_Pups_Mobile_Nav_Walker()
            ));
            ?>
        </div>
    </header>

    <!-- Cart Drawer -->
    <div x-data="{ isOpen: $store.cart.isOpen }" 
         x-show="isOpen" 
         @keydown.escape.window="isOpen = false"
         class="fixed inset-0 z-50 overflow-hidden" 
         x-cloak>
        <!-- Backdrop -->
        <div x-show="isOpen" 
             x-transition:enter="ease-in-out duration-500"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in-out duration-500"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-black bg-opacity-50 transition-opacity"
             @click="isOpen = false"></div>

        <!-- Drawer -->
        <div x-show="isOpen"
             x-transition:enter="transform transition ease-in-out duration-500"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-500"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="absolute inset-y-0 right-0 flex max-w-full pl-10">
            <div class="w-screen max-w-md">
                <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl dark:bg-gray-900">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-4 py-6 sm:px-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Shopping Cart</h2>
                        <button @click="isOpen = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <span class="sr-only">Close panel</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Cart Items -->
                    <div class="flex-1 overflow-y-auto px-4 sm:px-6">
                        <template x-if="$store.cart.items.length === 0">
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Your cart is empty</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start adding some items to your cart.</p>
                            </div>
                        </template>

                        <template x-if="$store.cart.items.length > 0">
                            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="(item, index) in $store.cart.items" :key="index">
                                    <li class="flex py-6">
                                        <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-md border border-gray-200 dark:border-gray-700">
                                            <img :src="item.image" :alt="item.name" class="h-full w-full object-cover object-center">
                                        </div>

                                        <div class="ml-4 flex flex-1 flex-col">
                                            <div>
                                                <div class="flex justify-between text-base font-medium text-gray-900 dark:text-white">
                                                    <h3 x-text="item.name"></h3>
                                                    <p class="ml-4" x-text="'$' + (item.price * item.quantity).toFixed(2)"></p>
                                                </div>
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="'Size: ' + item.size + ', Color: ' + item.color"></p>
                                            </div>
                                            <div class="flex flex-1 items-end justify-between text-sm">
                                                <div class="flex items-center space-x-2">
                                                    <button @click="$store.cart.updateQuantity(index, item.quantity - 1)" 
                                                            class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">-</button>
                                                    <span x-text="item.quantity" class="text-gray-500"></span>
                                                    <button @click="$store.cart.updateQuantity(index, item.quantity + 1)"
                                                            class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">+</button>
                                                </div>
                                                <button @click="$store.cart.removeItem(index)" 
                                                        class="font-medium text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300">
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </template>
                    </div>

                    <!-- Order Summary -->
                    <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-6 sm:px-6">
                        <div class="flex justify-between text-base font-medium text-gray-900 dark:text-white">
                            <p>Subtotal</p>
                            <p x-text="'$' + $store.cart.subtotal.toFixed(2)"></p>
                        </div>
                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Shipping and taxes calculated at checkout.</p>
                        <div class="mt-6">
                            <a href="<?php echo esc_url(wc_get_cart_url()); ?>" 
                               class="flex items-center justify-center rounded-md border border-transparent bg-primary-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-primary-700">
                                Checkout
                            </a>
                        </div>
                        <div class="mt-6 flex justify-center text-center text-sm text-gray-500 dark:text-gray-400">
                            <p>
                                or
                                <button @click="isOpen = false" class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300">
                                    Continue Shopping
                                    <span aria-hidden="true"> &rarr;</span>
                                </button>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 