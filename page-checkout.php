<?php
/* Template Name: Hype Pups Checkout */

get_header();
?>

<div class="container mx-auto px-4 py-8" id="main-content">
  <div class="mb-4">
    <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="inline-flex items-center text-sm text-gray-600 hover:text-[#FF3A5E]">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>
      <?php esc_html_e( 'Back to Cart', 'woocommerce' ); ?>
    </a>
  </div>

  <?php
  if ( have_posts() ) :
    while ( have_posts() ) : the_post();
      the_content();
    endwhile;
  endif;
  ?>
</div>

<?php get_footer(); ?> 