<?php
/**
 * Fired during plugin activation
 *
 * @link       https://profiles.wordpress.org/pattihis/
 * @since      1.0.0
 *
 * @package    Helpful_Posts
 * @subpackage Helpful_Posts/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Helpful_Posts
 * @subpackage Helpful_Posts/includes
 * @author     George Pattichis <gpattihis@gmail.com>
 */
class Helpful_Posts_Activator {

	/**
	 * Create our Database Table to store IPs of voters.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;

		$table_name      = $wpdb->prefix . 'helpful_votes_ip';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			user_ip tinytext NOT NULL,
			votes text NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		// dbDelta() is used to create the table if it doesn't exist.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}
