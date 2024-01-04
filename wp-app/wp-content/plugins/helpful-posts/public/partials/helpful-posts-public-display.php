<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://profiles.wordpress.org/pattihis/
 * @since      1.0.0
 *
 * @package    Helpful_Posts
 * @subpackage Helpful_Posts/public/partials
 */

$votes = get_post_meta( get_the_ID(), 'helpful_posts', true );
$front = new Helpful_Posts_Public( 'helpful-posts', HELPFUL_POSTS_VERSION );
$voted = $front->helpful_posts_has_voted( get_the_ID(), $front->helpful_get_user_ip() );

// Check if the user has voted to display the results.
if ( $voted ) :
	$yes_perc = 0;
	$no_perc  = 0;
	if ( $votes['yes'] > 0 || $votes['no'] > 0 ) {
		$yes_perc = round( $votes['yes'] / ( $votes['yes'] + $votes['no'] ) * 100 );
		$no_perc  = round( $votes['no'] / ( $votes['yes'] + $votes['no'] ) * 100 );
	}
	?>
	<div class="helpful-posts-wrap" data-post="<?php echo absint( get_the_ID() ); ?>">
		<div class="helpful-posts">
			<div class="helpful-posts__title">
				<?php esc_html_e( 'Thank you for your feedback.', 'helpful-posts' ); ?>
			</div>
			<div id="helpful-posts__loader" class="is-hidden"></div>
			<div class="helpful-posts__buttons">
				<div class="helpful-posts__buttons--yes <?php echo 'yes' === $voted ? 'selected' : ''; ?>">
					<span><?php echo esc_html( $yes_perc ); ?>%</span>
				</div>
				<div class="helpful-posts__buttons--no <?php echo 'no' === $voted ? 'selected' : ''; ?>">
					<span><?php echo esc_html( $no_perc ); ?>%</span>
				</div>
			</div>
		</div>
	</div>
	<?php
	// If user hasn't voted yet, we display the vote buttons.
else :
	?>
	<div class="helpful-posts-wrap" data-post="<?php echo absint( get_the_ID() ); ?>">
		<div class="helpful-posts">
			<div class="helpful-posts__title">
				<?php esc_html_e( 'Was this article helpful?', 'helpful-posts' ); ?>
			</div>
			<div id="helpful-posts__loader" class="is-hidden"></div>
			<div class="helpful-posts__buttons">
				<div class="helpful-posts__buttons--yes">
					<button data-vote="yes"><?php esc_html_e( 'Yes', 'helpful-posts' ); ?></button>
				</div>
				<div class="helpful-posts__buttons--no">
					<button data-vote="no"><?php esc_html_e( 'No', 'helpful-posts' ); ?></button>
				</div>
			</div>
		</div>
	</div>
	<?php
endif;
