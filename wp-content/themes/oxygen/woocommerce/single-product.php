<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/woocommerce/single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

wp_enqueue_script(array('nivo-lightbox'));
wp_enqueue_style(array('nivo-lightbox', 'nivo-lightbox-default'));

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header( 'shop' ); ?>

	<?php
		/**
		 * woocommerce_before_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		do_action( 'woocommerce_before_main_content' );
	?>

		<div class="product-single">

		<?php while ( have_posts() ) : the_post(); ?>

			<div class="row<?php echo SHOPSINGLESIDEBAR && SHOPSINGLESIDEBARALIGN == 'left' ? ' shop-single-left-sidebar' : ''; ?>">

				<div class="col-md-<?php echo SHOPSINGLESIDEBAR ? 9 : 12; ?> product-info-env">

					<?php wc_get_template_part( 'content', 'single-product' ); ?>

				</div>

				<?php if(SHOPSINGLESIDEBAR): ?>
				<div class="col-md-3 sidebar-env">
					<div class="blog shop_sidebar">
						<?php dynamic_sidebar('shop_sidebar'); ?>
					</div>

				</div>
				<?php endif; ?>
			</div>

		<?php endwhile; // end of the loop. ?>

<!--	xulin	Load design Field Start	-->

			<?php
				global $wc_cpdf;
				$cutom_id = $wc_cpdf->get_value(get_the_ID(), '_product_id');
//				$default_main_width = get_option('default_width');
//				$default_main_height = get_option('default_height');
//				$default_main_left = get_option('default_left');
//				$default_main_top = get_option('default_top');
			$default_main_width = '186px';
			$default_main_height = '182px';
			$default_main_left = '150px';
			$default_main_top = '100px';
				if($product->get_attribute('is_diy_product')){
					echo '<iframe id="config_iframe" width="100%" height="650px" frameBorder="0" scrolling="no" 
					src="http://'.$_SERVER['SERVER_NAME'].'/tshirtecommerce/index.php?product='.$cutom_id.'&parent='.get_the_ID().
						'&width='.$default_main_width.
						'&height='.$default_main_height.
						'&left='.$default_main_left.
						'&top='.$default_main_top.'">
					</iframe>';
				}
			?>

<!--	xulin	Load design Field Ende	-->
		</div>

	<?php

		/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>

	<?php
		/**
		 * woocommerce_sidebar hook
		 *
		 * @hooked woocommerce_get_sidebar - 10
		 */
		#do_action( 'woocommerce_sidebar' );
	?>

<?php get_footer( 'shop' ); ?>
