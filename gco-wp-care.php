<?php
/**
 * Plugin Name:       GCO WordPress Care
 * Description:       Halaman layanan "WordPress Care & Security" untuk Global Centre One: cara kerja, paket harga, contoh laporan, FAQ, dan formulir permintaan yang tersimpan di dashboard. Pasang dengan shortcode [gco_care].
 * Version:           1.2.3
 * Author:            Global Faith Connection LLC
 * License:           GPL-2.0-or-later
 * Text Domain:       gco-wp-care
 * Requires at least: 6.0
 * Requires PHP:      7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GCC_VERSION', '1.2.3' );
define( 'GCC_FILE', __FILE__ );
define( 'GCC_URL', plugin_dir_url( __FILE__ ) );
define( 'GCC_DIR', plugin_dir_path( __FILE__ ) );

require GCC_DIR . 'includes/settings.php';
require GCC_DIR . 'includes/requests.php';
require GCC_DIR . 'includes/render.php';
require GCC_DIR . 'includes/ask.php';

/* Saat diaktifkan: daftarkan jenis konten, lalu buat halaman "WordPress Care" bila belum ada. */
register_activation_hook( __FILE__, function () {
	gcc_register_requests();
	if ( ! get_option( 'gcc_page_id' ) || ! get_post( (int) get_option( 'gcc_page_id' ) ) ) {
		$page_id = wp_insert_post( array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => 'WordPress Care & Security',
			'post_name'    => 'wordpress-care',
			'post_content' => '<!-- wp:shortcode -->[gco_care]<!-- /wp:shortcode -->',
		) );
		if ( $page_id && ! is_wp_error( $page_id ) ) {
			update_option( 'gcc_page_id', (int) $page_id, false );
		}
	}
	flush_rewrite_rules();
} );

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $links ) {
	array_unshift(
		$links,
		'<a href="' . esc_url( admin_url( 'options-general.php?page=gco-wp-care' ) ) . '">Pengaturan</a>'
	);
	$page = (int) get_option( 'gcc_page_id' );
	if ( $page && get_post( $page ) ) {
		$links[] = '<a href="' . esc_url( get_permalink( $page ) ) . '" target="_blank" rel="noopener">Lihat halaman</a>';
	}
	return $links;
} );
