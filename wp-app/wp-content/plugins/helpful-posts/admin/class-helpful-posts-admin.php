<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://profiles.wordpress.org/pattihis/
 * @since      1.0.0
 *
 * @package    Helpful_Posts
 * @subpackage Helpful_Posts/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and any required hooks to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Helpful_Posts
 * @subpackage Helpful_Posts/admin
 * @author     George Pattichis <gpattihis@gmail.com>
 */
class Helpful_Posts_Admin {

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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/helpful-posts-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/helpful-posts-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			$this->plugin_name,
			'helpfulPosts',
			array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'clearText' => esc_html__(
					'Total Visitor Votes:',
					'helpful-posts'
				),
			)
		);
	}

	/**
	 * Register our custom metabox to display voting results in the admin area.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function helpful_posts_register_metabox() {
		add_meta_box( 'helpful-posts-votes', 'Was this article helpful?', array( $this, 'helpful_posts_render_metabox' ), 'post', 'normal', 'default' );
	}

	/**
	 * Render our custom metabox to display voting results in the admin area.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function helpful_posts_render_metabox() {

		// We separate the display markup to a template.
		include_once 'partials/helpful-posts-admin-display.php';
	}

	/**
	 * Show custom links in Plugins Page
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array $links Default Links.
	 * @param  array $file Plugin's root filepath.
	 * @return array Links list to display in plugins page.
	 */
	public function helpful_posts_plugin_links( $links, $file ) {

		if ( HELPFUL_POSTS_BASENAME === $file ) {
			$options_links = '<a href="' . get_admin_url() . 'options-general.php?page=helpful-posts" title="Plugin Options">' . __( 'Settings', 'helpful-posts' ) . '</a>';
			array_unshift( $links, $options_links );
		}

		return $links;
	}

	/**
	 * Ajax callback to clear post votes.
	 *
	 * It deletes the post meta and removes the post id from all votes in the database.
	 *
	 * @since   1.0.0
	 */
	public function helpful_posts_clear_votes() {

		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'helpful-posts-nonce' ) ) {

			// Get the post id from the ajax request.
			$postid = ( isset( $_POST['postid'] ) && ! empty( $_POST['postid'] ) ) ? sanitize_key( $_POST['postid'] ) : false;

			if ( $postid ) {

				// Delete the post meta.
				delete_post_meta( $postid, 'helpful_posts' );

				global $wpdb;
				$table = $wpdb->prefix . 'helpful_votes_ip';
				$match = '%i:' . $postid . ';%';

				// phpcs:disable -- This is our own table, we can use it as we want.
				// Find all entries in our database that have this post id in their past votes.
				$entries = $wpdb->get_results($wpdb->prepare("SELECT id FROM {$table} WHERE votes LIKE %s", $match), ARRAY_A);

				foreach ($entries as $vote) {
					//Get the votes array for each entry.
					$vote_arr = $wpdb->get_var($wpdb->prepare("SELECT votes FROM {$table} WHERE id = %d", $vote['id']));
					$vote_arr = maybe_unserialize($vote_arr);

					// Remove the post id from the votes array.
					if (isset($vote_arr[$postid])) {
						unset($vote_arr[$postid]);
					}

					// Update the votes array in the database.
					$wpdb->update($table, array('votes' => maybe_serialize($vote_arr)), array('id' => $vote['id']));
					// phpcs:enable
				}

				echo wp_json_encode( array( 'success' => true ) );
			}
		}

		wp_die();
	}
}
