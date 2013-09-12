<?php
/*
Plugin Name: Easy Digital Downloads - External Products
Plugin URL: http://easydigitaldownloads.com
Description: Add an "external URL" to your Download post to redirect the purchase button to a different site. Handy both for affiliate-based product lists and referencing projects that are hosted elsewhere.
Version: 1.0.0
Author: WebDevStudios
Author URI: http://webdevstudios.com
*/

/**
 * External Product URL Field
 *
 * Adds field do the EDD Downloads meta box for specifying the "External Product URL"
 *
 * @since 1.0.0
 * @param integer $post_id Download (Post) ID
 */
function edd_render_external_product_row( $post_id ) {
	$edd_external_url = get_post_meta( $post_id, '_edd_external_url', true );
?>
	<p><strong><?php _e( 'External Product URL:', 'edd-external-product' ); ?></strong></p>
	<label for="edd_external_url">
		<input type="text" name="_edd_external_url" id="edd_external_url" value="<?php echo esc_attr( $edd_external_url ); ?>" size="80" placeholder="http://"/>
		<br/><?php _e( 'The external URL (including http://) to use for the purchase button. Leave blank for standard products.', 'edd-external-product' ); ?>
	</label>
<?php
}
add_action( 'edd_meta_box_fields', 'edd_render_external_product_row', 20 );

/**
 * Add the _edd_external_url field to the list of saved product fields
 *
 * @since  1.0.0
 *
 * @param  array $fields The default product fields list
 * @return array         The updated product fields list
 */
function edd_external_product_save( $fields ) {

	// Add our field
	$fields[] = '_edd_external_url';

	// Return the fields array
	return $fields;
}
add_filter( 'edd_metabox_fields_save', 'edd_external_product_save' );

/**
 * Override the default product purchase button with an external anchor
 *
 * Only affects products that have an external URL stored
 *
 * @since  1.0.0
 *
 * @param  string    $purchase_form The concatenated markup for the purchase area
 * @param  array    $args           Args passed from {@see edd_get_purchase_link()}
 * @return string                   The potentially modified purchase area markup
 */
function edd_external_product_link( $purchase_form, $args ) {

	// If the product has an external URL set
	if ( $external_url = get_post_meta( $args['download_id'], '_edd_external_url', true ) ) {

		// Open up the standard containers
		$output = '<div class="edd_download_purchase_form">';
		$output .= '<div class="edd_purchase_submit_wrapper">';

		// Output an anchor tag with the same classes as the product button
		$output .= sprintf(
			'<a class="%1$s" href="%2$s">%3$s</a>',
			implode( ' ', array( $args['style'], $args['color'], trim( $args['class'] ) ) ),
			esc_attr( $external_url ),
			esc_attr( $args['text'] )
		);

		// Close the containers
		$output .= '</div><!-- .edd_purchase_submit_wrapper -->';
		$output .= '</div><!-- .edd_download_purchase_form -->';

		// Replace the form output with our own output
		$purchase_form = $output;
	}

	// Return the possibly modified purchase form
	return $purchase_form;
}
add_filter( 'edd_purchase_download_form', 'edd_external_product_link', 10, 2 );
