<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://profiles.wordpress.org/pattihis/
 * @since      1.0.0
 *
 * @package    Helpful_Posts
 * @subpackage Helpful_Posts/admin/partials
 */

$helpful = get_post_meta( get_the_ID(), 'helpful_posts', true );

// If there are no votes yet, set the default values.
$yes_votes = isset( $helpful['yes'] ) ? $helpful['yes'] : 0;
$no_votes  = isset( $helpful['no'] ) ? $helpful['no'] : 0;

$yes_perc = 0;
$no_perc  = 0;
if ( $yes_votes > 0 || $no_votes > 0 ) {
	$yes_perc = round( $yes_votes / ( $yes_votes + $no_votes ) * 100 );
	$no_perc  = round( $no_votes / ( $yes_votes + $no_votes ) * 100 );
}

?>

<h4><?php echo esc_html__( 'Total Visitor Votes:', 'helpful-posts' ) . ' ' . intval( $yes_votes + $no_votes ); ?> </h4>
<div class="helpful-posts-metabox-wrap">
	<div class="helpful-posts-metabox-left">
		<div class="helpful-posts-metabox yes">
			<span><?php esc_html_e( 'Yes', 'helpful-posts' ); ?></span>
			<span><?php echo absint( $yes_perc ); ?>%</span>
			<span><?php echo esc_attr( '(' . $yes_votes . '&nbsp;' . __( 'Votes', 'helpful-posts' ) . ')' ); ?></span>
		</div>
		<div class="helpful-posts-metabox no">
			<span><?php esc_html_e( 'No', 'helpful-posts' ); ?></span>
			<span><?php echo absint( $no_perc ); ?>%</span>
			<span><?php echo esc_attr( '(' . $no_votes . '&nbsp;' . __( 'Votes', 'helpful-posts' ) . ')' ); ?></span>
		</div>
	</div>
	<div class="helpful-posts-metabox-right">
		<button id="deleteHelpfulVotes" title="<?php esc_html_e( 'Delete all votes for this Post.', 'helpful-posts' ); ?>" data-post="<?php echo (int) get_the_ID(); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'helpful-posts-nonce' ) ); ?>">
			<?php esc_html_e( 'Reset Votes', 'helpful-posts' ); ?>
		</button>
	</div>
</div>
