<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/pattihis/
 * @since      1.0.0
 *
 * @package    Helpful_Posts
 * @subpackage Helpful_Posts/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Helpful_Posts
 * @subpackage Helpful_Posts/public
 * @author     George Pattichis <gpattihis@gmail.com>
 */
class Helpful_Posts_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/helpful-posts-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/helpful-posts-public.js', array( 'jquery' ), $this->version, false );

		// Pass the ajax url, nonce and translated strings to our script.
		wp_localize_script(
			$this->plugin_name,
			'helpfulPosts',
			array(
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'nonce'        => wp_create_nonce( 'helpful-posts-nonce' ),
				'title'        => esc_html__( 'Was this article helpful?', 'helpful-posts' ),
				'yesTxt'       => esc_html__( 'Yes', 'helpful-posts' ),
				'noTxt'        => esc_html__( 'No', 'helpful-posts' ),
				'thankYou'     => esc_html__( 'Thank you for your feedback.', 'helpful-posts' ),
				'votedAlready' => esc_html__( 'You have already voted!', 'helpful-posts' ),
			)
		);
	}

	/**
	 * Add helpful post votes to the post content.
	 *
	 * @since    1.0.0
	 * @param    string $content    The content of the post.
	 */
	public function helpful_posts_render_votes( $content ) {

		// Only display the vote buttons on single posts.
		if ( is_single() ) {
			ob_start();
			include_once 'partials/helpful-posts-public-display.php';
			$content .= ob_get_clean();
		}

		return $content;
	}

	/**
	 * Ajax callback to check user's vote status. Busts cache.
	 *
	 * Most sites use caching systems to deliver static html pages to visitors
	 * bypassing our php code and returning the same output to all visitors. We handle
	 * this with an ajax request on load, to bypass the cache and return the correct output.
	 *
	 * @since   1.0.0
	 */
	public function helpful_posts_check_vote() {

		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'helpful-posts-nonce' ) ) {

			// Get the post id from the ajax request.
			$postid = ( isset( $_POST['postid'] ) && ! empty( $_POST['postid'] ) ) ? sanitize_key( $_POST['postid'] ) : false;

			if ( $postid ) {

				// Get the user IP.
				$user_ip = $this->helpful_get_user_ip();

				// Check if the user has already voted.
				$voted = $this->helpful_posts_has_voted( $postid, $user_ip );

				if ( $voted ) {

					$votes    = get_post_meta( $postid, 'helpful_posts', true );
					$yes_perc = 0;
					$no_perc  = 0;
					if ( $votes['yes'] > 0 || $votes['no'] > 0 ) {
						$yes_perc = round( $votes['yes'] / ( $votes['yes'] + $votes['no'] ) * 100 );
						$no_perc  = round( $votes['no'] / ( $votes['yes'] + $votes['no'] ) * 100 );
					}

					echo wp_json_encode(
						array(
							'status'    => 'voted',
							'vote'      => $voted,
							'yes_votes' => $votes['yes'],
							'no_votes'  => $votes['no'],
							'yes_perc'  => $yes_perc . '%',
							'no_perc'   => $no_perc . '%',
						)
					);
				} else {

					echo wp_json_encode( array( 'status' => 'no-vote' ) );
				}
			} else {

				// Return an error if the data is missing.
				wp_send_json_error( 'Missing data!', 403 );
			}
		} else {

			// Return an error if the nonce is invalid.
			wp_send_json_error( 'Nonce error!', 403 );
		}

		die();
	}


	/**
	 * Ajax callback to register a vote.
	 *
	 * This function is called when the user clicks on a vote button.
	 *
	 * @since   1.0.0
	 */
	public function helpful_posts_add_vote() {

		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'helpful-posts-nonce' ) ) {

			// Get the vote and post id from the ajax request.
			$vote   = ( isset( $_POST['vote'] ) && ! empty( $_POST['vote'] ) ) ? sanitize_key( $_POST['vote'] ) : false;
			$postid = ( isset( $_POST['postid'] ) && ! empty( $_POST['postid'] ) ) ? sanitize_key( $_POST['postid'] ) : false;

			if ( $vote && $postid ) {

				// Get the user IP, check if he hasn't voted already and save the vote in the database.
				$user_ip  = $this->helpful_get_user_ip();
				$new_vote = $this->helpful_posts_save_vote_ip( $vote, $postid, $user_ip );

				if ( $new_vote ) {

					// Check if post has votes already otherwise initialize the array.
					$helpful = get_post_meta( $postid, 'helpful_posts', true ) ? get_post_meta( $postid, 'helpful_posts', true ) : array(
						'yes' => 0,
						'no'  => 0,
					);

					// Increment the vote.
					$helpful[ $vote ]++;

					// Update the post meta.
					update_post_meta( $postid, 'helpful_posts', $helpful );

					// Calculate the percentages.
					$yes_perc = 0;
					$no_perc  = 0;
					if ( $helpful['yes'] > 0 || $helpful['no'] > 0 ) {
						$yes_perc = round( $helpful['yes'] / ( $helpful['yes'] + $helpful['no'] ) * 100 );
						$no_perc  = round( $helpful['no'] / ( $helpful['yes'] + $helpful['no'] ) * 100 );
					}

					// Return the updated votes.
					echo wp_json_encode(
						array(
							'yes_votes' => $helpful['yes'],
							'no_votes'  => $helpful['no'],
							'yes_perc'  => $yes_perc . '%',
							'no_perc'   => $no_perc . '%',
						)
					);
				} else {

					// Return an error if the user has already voted.
					wp_send_json_error( 'You have already voted!', 403 );
				}
			} else {

				// Return an error if the data is missing.
				wp_send_json_error( 'Missing data!', 403 );
			}
		} else {

			// Return an error if the nonce is invalid.
			wp_send_json_error( 'Nonce error!', 403 );
		}

		die();
	}

	/**
	 * Get the User IP Address.
	 *
	 * @since 1.0.0
	 */
	public function helpful_get_user_ip() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$user_ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$user_ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$user_ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		} else {
			$user_ip = 'unknown';
		}

		$user_ip = explode( ',', $user_ip )[0];

		return $user_ip;
	}

	/**
	 * Save the user IP and vote in the database.
	 *
	 * @since 1.0.0
	 * @param string $vote    The vote.
	 * @param string $postid  The post id.
	 * @param string $user_ip The user IP.
	 */
	public function helpful_posts_save_vote_ip( $vote, $postid, $user_ip ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'helpful_votes_ip';

		// phpcs:disable -- This is our own table, we can use it as we want.
		// Check if the user IP exists in the database.
		$ip_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE user_ip = %s", $user_ip));

		if ($ip_exists) {

			// Get user votes from the database.
			$votes = $wpdb->get_var($wpdb->prepare("SELECT votes FROM $table_name WHERE user_ip = %s", $user_ip));

			// Unserialize the votes.
			$votes = maybe_unserialize($votes);

			// Check if the user has already voted for this post.
			if (isset($votes[$postid])) {

				// Return false if the user has already voted.
				return false;
			}

			// Add the new vote to the array.
			$votes[$postid] = $vote;

			// Update the votes in the database.
			$wpdb->update(
				$table_name,
				array(
					'time'  => current_time('mysql'),
					'votes' => maybe_serialize($votes),
				),
				array(
					'user_ip' => $user_ip,
				)
			);
		} else {

			// Create a new entry in the database.
			$wpdb->insert(
				$table_name,
				array(
					'time'    => current_time('mysql'),
					'user_ip' => $user_ip,
					'votes'   => maybe_serialize(
						array(
							$postid => $vote,
						)
					),
				)
			);
		}

		// phpcs:enable
		return true;
	}

	/**
	 * Check if the user has already voted for this post.
	 *
	 * @since 1.0.0
	 * @param string $postid  The post id.
	 * @param string $user_ip The user IP.
	 */
	public function helpful_posts_has_voted( $postid, $user_ip ) {

		global $wpdb;

		$table_name = $wpdb->prefix . 'helpful_votes_ip';

		// phpcs:disable -- This is our own table, we can use it as we want.
		// Check if the user IP exists in the database.
		$ip_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE user_ip = %s", $user_ip));

		if ($ip_exists) {

			// Get user votes from the database.
			$votes = $wpdb->get_var($wpdb->prepare("SELECT votes FROM $table_name WHERE user_ip = %s", $user_ip));

			// Unserialize the votes.
			$votes = maybe_unserialize($votes);

			// Check if the user has already voted for this post.
			if (isset($votes[$postid])) {

				// Return the vote if the user has already voted.
				return $votes[$postid];
			}
		}

		// phpcs:enable
		return false;
	}
}
