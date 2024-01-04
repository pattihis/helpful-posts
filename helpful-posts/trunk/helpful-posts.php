<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://profiles.wordpress.org/pattihis/
 * @since             1.0.0
 * @package           Helpful_Posts
 *
 * @wordpress-plugin
 * Plugin Name:       Helpful Posts
 * Plugin URI:        https://github.com/pattihis/helpful-post
 * Description:       This is a simple plugin to allow website visitors to vote if a Post is helpful or not.
 * Version:           1.0.0
 * Author:            George Pattichis
 * Author URI:        https://profiles.wordpress.org/pattihis//
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       helpful-posts
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 *
 * @since 1.0.0
 */
define( 'HELPFUL_POSTS_VERSION', '1.0.0' );

/**
 * Plugin's basename.
 *
 * @since 1.0.0
 */
define( 'HELPFUL_POSTS_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-helpful-posts-activator.php
 */
function activate_helpful_posts() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-helpful-posts-activator.php';
	Helpful_Posts_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-helpful-posts-deactivator.php
 */
function deactivate_helpful_posts() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-helpful-posts-deactivator.php';
	Helpful_Posts_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_helpful_posts' );
register_deactivation_hook( __FILE__, 'deactivate_helpful_posts' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-helpful-posts.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_helpful_posts() {

	$plugin = new Helpful_Posts();
	$plugin->run();

}
run_helpful_posts();
