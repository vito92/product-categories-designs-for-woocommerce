<?php
/**
 * Shortcode
 * 
 * @package Product Categories Designs for WooCommerce 
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

	function pcdfwoo_product_categories( $atts ) {
		global $woocommerce_loop;

		$atts = shortcode_atts( array(
			'number'     => null,
			'orderby'    => 'name',
			'order'      => 'ASC',
			'design'     => 'design-1',
			'columns'    => '4',
			'hide_empty' => 1,
			'parent'     => '',
			'ids'        => ''
		), $atts, 'pcdfwoo_product_categories' );

		if ( isset( $atts['ids'] ) ) {
			$ids = explode( ',', $atts['ids'] );
			$ids = array_map( 'trim', $ids );
		} else {
			$ids = array();
		}

		$hide_empty = ( $atts['hide_empty'] == true || $atts['hide_empty'] == 1 ) ? 1 : 0;

		// get terms and workaround WP bug with parents/pad counts
		$args = array(
			'orderby'    => $atts['orderby'],
			'order'      => $atts['order'],
			'hide_empty' => $hide_empty,
			'include'    => $ids,
			'pad_counts' => true,			
			'parent'     => 0
		);

		$product_categories = get_terms( 'product_cat', $args );		
	
		
		if ( '' !== $atts['parent'] ) {
			$product_categories = wp_list_filter( $product_categories, array( 'parent' => $atts['parent'] ) );
		}

		if ( $hide_empty ) {
			foreach ( $product_categories as $key => $category ) {
				if ( $category->count == 0 ) {
					unset( $product_categories[ $key ] );
				}
			}
		}

		if ( $atts['number'] ) {
			$product_categories = array_slice( $product_categories, 0, $atts['number'] );
		}
		
		$design = $atts['design'] ;
		$columns = absint( $atts['columns'] );
		$woocommerce_loop['columns'] = $columns;

		ob_start();

		if ( $product_categories ) { ?>
			
			<div class="pcdfwoo-product-cat <?php echo $design; ?>">
				<?php foreach ( $product_categories as $category ) {
					 $cat_thumb_id = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true );
					 $cat_thumb_url = wp_get_attachment_image_src( $cat_thumb_id, 'shop_catalog' );
					 $term_link = get_term_link( $category, 'product_cat' );
					$cat_thumb_link = $cat_thumb_url[0];	?>
				
				<div class="pcdfwoo-medium-<?php echo $columns; ?> pcdfwoo-columns">
					<div class="pcdfwoo-product-cat_inner">
						<a href="<?php echo $term_link; ?>">
							<?php if(!empty($cat_thumb_link)) { ?>
							<img  src="<?php echo $cat_thumb_link; ?>"  alt="<?php echo $category->name; ?>" />
							<?php } else { 
							echo wc_placeholder_img();
							 } ?>
							<div class="pcdfwoo_title"><?php echo $category->name; ?> <span class="pcdfwoo_count"><?php echo $category->count; ?> </span></div>
						</a>
					</div>
				</div>
					
				<?php } ?>
			</div>
		<?php	
		}

		woocommerce_reset_loop();

		return '<div class="pcdfwoo_woocommerce">' . ob_get_clean() . '</div>';
}

add_shortcode('wpos_product_categories', 'pcdfwoo_product_categories');