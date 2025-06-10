    <!-- Footer -->
    <footer class="border-t bg-white" role="contentinfo">
        <div class="container mx-auto px-4 py-12 md:py-16">
            <div class="grid grid-cols-2 gap-8 md:grid-cols-2 lg:grid-cols-4">
                <div class="space-y-4 col-span-2 md:col-span-1">
                    <?php
                    if (has_custom_logo()) {
                        the_custom_logo();
                    } else {
                        echo '<h3 class="text-xl font-bold text-[#FF3A5E] font-montserrat">' . get_bloginfo('name') . '</h3>';
                    }
                    ?>
                    <p class="text-sm text-gray-600">
                        <?php echo get_theme_mod('footer_description', 'Premium streetwear-inspired fashion for dogs. Elevating your pup\'s style game since 2023.'); ?>
                    </p>
                    <div class="flex space-x-4">
                        <?php
                        $social_links = array(
                            'instagram' => array(
                                'url' => get_theme_mod('social_instagram', 'https://instagram.com/hype_pups'),
                                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>',
                                'label' => 'Instagram'
                            ),
                            'facebook' => array(
                                'url' => get_theme_mod('social_facebook', 'https://facebook.com/hypepups'),
                                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>',
                                'label' => 'Facebook'
                            ),
                            'twitter' => array(
                                'url' => get_theme_mod('social_twitter', 'https://twitter.com/hypepups'),
                                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>',
                                'label' => 'Twitter'
                            )
                        );

                        foreach ($social_links as $platform => $data) {
                            if (!empty($data['url'])) {
                                printf(
                                    '<a href="%s" target="_blank" rel="noopener noreferrer" aria-label="Follow us on %s" class="text-gray-600 hover:text-[#FF3A5E] transition-colors">%s<span class="sr-only">%s</span></a>',
                                    esc_url($data['url']),
                                    esc_attr($data['label']),
                                    $data['icon'],
                                    esc_html($data['label'])
                                );
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- Shop Menu -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold font-montserrat" id="footer-shop">
                        <?php echo esc_html__('Shop', 'hype-pups'); ?>
                    </h3>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer_shop',
                        'container' => false,
                        'menu_class' => 'space-y-2 text-sm',
                        'fallback_cb' => false,
                        'items_wrap' => '<ul class="%2$s" aria-labelledby="footer-shop">%3$s</ul>',
                        'walker' => new Hype_Pups_Footer_Menu_Walker()
                    ));
                    ?>
                </div>

                <!-- Company Menu -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold font-montserrat" id="footer-company">
                        <?php echo esc_html__('Company', 'hype-pups'); ?>
                    </h3>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer_company',
                        'container' => false,
                        'menu_class' => 'space-y-2 text-sm',
                        'fallback_cb' => false,
                        'items_wrap' => '<ul class="%2$s" aria-labelledby="footer-company">%3$s</ul>',
                        'walker' => new Hype_Pups_Footer_Menu_Walker()
                    ));
                    ?>
                </div>

                <!-- Orders & Shipping Menu -->
                <div class="space-y-4 col-span-2 md:col-span-1">
                    <h3 class="text-lg font-semibold font-montserrat" id="footer-orders">
                        <?php echo esc_html__('Orders & Shipping', 'hype-pups'); ?>
                    </h3>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer_orders',
                        'container' => false,
                        'menu_class' => 'space-y-2 text-sm',
                        'fallback_cb' => false,
                        'items_wrap' => '<ul class="%2$s" aria-labelledby="footer-orders">%3$s</ul>',
                        'walker' => new Hype_Pups_Footer_Menu_Walker()
                    ));
                    ?>
                </div>

                <!-- Newsletter -->
                <div class="space-y-4 col-span-2">
                    <h3 class="text-lg font-semibold font-montserrat" id="newsletter-signup">
                        <?php echo esc_html__('Stay Updated', 'hype-pups'); ?>
                    </h3>
                    <p class="text-sm text-gray-600">
                        <?php echo get_theme_mod('newsletter_text', 'Subscribe to get special offers, free giveaways, and new drop announcements.'); ?>
                    </p>
                    <form class="space-y-2" action="<?php echo esc_url(home_url('/')); ?>" method="post">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <div class="flex-1">
                                <input type="email" name="email" placeholder="<?php echo esc_attr__('Your email', 'hype-pups'); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#FF3A5E] focus:border-transparent" aria-label="<?php echo esc_attr__('Email address', 'hype-pups'); ?>" required>
                            </div>
                            <button type="submit" class="whitespace-nowrap px-6 py-2 bg-[#FF3A5E] hover:bg-[#E02E50] text-white font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-[#FF3A5E] focus:ring-offset-2">
                                <span><?php echo esc_html__('Subscribe', 'hype-pups'); ?></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Copyright -->
            <div class="mt-12 border-t pt-6 text-center text-sm text-gray-600">
                <p>&copy; <?php echo date('Y'); ?> <?php echo get_bloginfo('name'); ?>. <?php echo esc_html__('All rights reserved.', 'hype-pups'); ?></p>
                <div class="mt-2 flex flex-wrap justify-center gap-4">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer_bottom',
                        'container' => false,
                        'menu_class' => 'flex flex-wrap justify-center gap-4',
                        'fallback_cb' => false,
                        'items_wrap' => '%3$s',
                        'walker' => new Hype_Pups_Footer_Bottom_Menu_Walker()
                    ));
                    ?>
                </div>
            </div>
        </div>
    </footer>

    <!-- Cart Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('cart', {
                items: [],
                isOpen: false,
                
                init() {
                    // Load cart from localStorage
                    const savedCart = localStorage.getItem('cart');
                    if (savedCart) {
                        try {
                            this.items = JSON.parse(savedCart);
                        } catch (error) {
                            console.error('Failed to parse cart from localStorage:', error);
                            this.items = [];
                        }
                    }

                    // Listen for cart updates
                    window.addEventListener('storage', (e) => {
                        if (e.key === 'cart') {
                            this.items = JSON.parse(e.newValue);
                        }
                    });
                },
                
                // Save cart to localStorage
                saveCart() {
                    localStorage.setItem('cart', JSON.stringify(this.items));
                    // Dispatch event for other tabs/windows
                    window.dispatchEvent(new StorageEvent('storage', {
                        key: 'cart',
                        newValue: JSON.stringify(this.items)
                    }));
                },
                
                // Add item to cart
                addItem(item) {
                    // Check if item already exists in cart
                    const existingItemIndex = this.items.findIndex(
                        i => i.id === item.id && i.size === item.size && i.color === item.color
                    );
                    
                    if (existingItemIndex > -1) {
                        // Update quantity if item exists
                        this.items[existingItemIndex].quantity += item.quantity;
                    } else {
                        // Add new item if it doesn't exist
                        this.items.push(item);
                    }
                    
                    this.saveCart();
                    this.isOpen = true; // Open cart drawer when item is added
                },
                
                // Remove item from cart
                removeItem(index) {
                    this.items.splice(index, 1);
                    this.saveCart();
                },
                
                // Update item quantity
                updateQuantity(index, quantity) {
                    if (quantity < 1) return;
                    
                    this.items[index].quantity = quantity;
                    this.saveCart();
                },
                
                // Clear cart
                clearCart() {
                    this.items = [];
                    this.saveCart();
                },
                
                // Get total items count
                get totalItems() {
                    return this.items.reduce((total, item) => total + item.quantity, 0);
                },
                
                // Get subtotal
                get subtotal() {
                    return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
                }
            });
        });
    </script>

    <?php wp_footer(); ?>
    <div id="quick-view-modal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
        <div id="quick-view-modal-content" style="background:#fff; max-width:800px; width:95vw; margin:auto; border-radius:12px; padding:32px; position:relative;"></div>
        <button id="quick-view-close" style="position:absolute; top:24px; right:24px; background:#fff; border:2px solid #FF3A5E; color:#FF3A5E; border-radius:50%; width:36px; height:36px; font-size:24px; display:flex; align-items:center; justify-content:center;">&times;</button>
    </div>
</body>
</html> 