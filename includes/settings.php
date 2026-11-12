<?php
/**
 * Pengaturan: Settings > GCO WordPress Care.
 *
 * @package GCO_WP_Care
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gcc_fields() {
	return array(
		'email'      => array( 'Email penerima permintaan', 'email', 'gcosupport@globalcentreone.com', 'Permintaan dari formulir dikirim ke sini dan juga tersimpan di menu Care Requests.' ),
		'contact_url' => array( 'Halaman kontak', 'url', 'https://globalcentreone.com/contact/', 'Ditampilkan sebagai pilihan konsultasi di halaman dan formulir Ask GCO.' ),
		'company'    => array( 'Nama perusahaan', 'text', 'Global Faith Connection LLC', '' ),
		'location'   => array( 'Lokasi', 'text', 'Wyoming, USA', '' ),
		'response'   => array( 'Waktu balas', 'text', 'within 1 business day', 'Dipakai di kalimat: "We reply ..."' ),
		'currency'   => array( 'Simbol mata uang', 'text', '$', '' ),
		'packages'   => array(
			'Paket',
			'textarea',
			"Safe Setup | 149 | one-time | Automatic backups to your own Google Drive; A full backup, checked and complete; A site health check: versions, plugins, security basics; A written report with next steps |\n" .
			"Safe Update | 299 | one-time | Full backup before anything changes; WordPress and plugins updated one at a time; A test of checkout, donations or forms after each update; Stop and restore if anything breaks; A written log of every change and how to undo it |\n" .
			"Care Basic | 99 | /month | Monthly updates, one at a time; Backup checks every month; Security scan and admin user review; A short written monthly report | \n" .
			"Care Store | 199 | /month | Everything in Care Basic; A real test order after updates; Checks on subscriptions, renewals and donations; Up to 2 hours of small fixes each month | featured",
			'Satu paket per baris:  Nama | Harga | Periode | Fitur; fitur; fitur | featured (opsional, untuk paket yang ditonjolkan)',
		),
		'services'   => array(
			'Layanan lainnya',
			'textarea',
			"Custom WordPress themes | A site that looks like your brand and is easy to edit | Designed from your logo, colors or Figma file; Built as a fast block theme your team can edit; Mobile-first and accessible; Handover guide for your editors | Churches, nonprofits and businesses that have outgrown a template\n" .
			"Custom plugins | The exact feature your site is missing | Planned with you in writing first; Clean, secure code with settings you can change; No yearly license fee to us; Documented so any developer can maintain it | Replacing several paid plugins with one simple tool\n" .
			"Online stores & checkout | Sell products, courses or tickets with WooCommerce | Product pages, cart and checkout; Card and PayPal payments; Shipping, taxes and emails set up; Test orders before launch | Small shops, ministries and authors\n" .
			"Donations, memberships & events | Give, join and register on one site | Donation forms and recurring giving; Member areas and subscriptions; Event pages with registration; Tested so payments keep working | Churches, ministries and nonprofits\n" .
			"Speed & mobile fixes | A site that loads fast on any phone | Speed test before and after; Image and cache optimization; Layout fixes on phones and tablets; Core Web Vitals report | Sites that feel slow or look broken on mobile\n" .
			"Page builder cleanup & migration | Move off a heavy builder without losing your pages | Content moved from Divi, Elementor or similar; Rebuilt as clean blocks or patterns; URLs kept the same to protect search ranking; Page-by-page check before launch | Sites stuck on an old or slow builder\n" .
			"Multilingual sites | Reach people in their own language | Language switcher and translated pages; Search-friendly language versions; Easy for your team to update | Global ministries and organizations\n" .
			"AI features | Helpful AI on your own site | Assistants that answer questions from your content; Writing and translation helpers; Clear rules on privacy and data | Teams that want to save time with care",
			'Satu layanan per baris:  Judul | Kalimat singkat | Yang didapat; yang didapat; ... | Cocok untuk',
		),
		'work'       => array(
			'Karya GCO (portofolio)',
			'textarea',
			"GCO Digital | https://globalcentreone.com/gco-digital/ | A store for WordPress plugins, themes and templates: 83 ready now, 2 coming soon, across 14 categories, with a custom-work order flow. | 1-gco-digital.jpg | Plugins & themes\n" .
			"Global Centre One | https://globalcentreone.com | The home of the GCO ecosystem: one account across every service, multilingual pages and dark mode. | 2-global-centre-one.jpg | Platform\n" .
			"GCO Card | https://card.globalcentreone.com | A smart safety and membership card: SafeCircle with SOS, emergency ID, pet tags and Lost & Found, verified online by QR code, with 1–5 year plans. | 3-gco-card.jpg | Payments & memberships\n" .
			"GCO AI | https://globalcentreone.com/ai/ | A multilingual AI assistant that writes, translates and reads photos and documents, with voice input and a credit system. | 5-gco-ai.jpg | AI\n" .
			"GCO Books | https://globalcentreone.com/books/ | A write-and-publish platform for authors, in English and Indonesian, with an earnings calculator. | 6-gco-books.jpg | Publishing\n" .
			"GCO Audio | https://globalcentreone.com/audio/ | An audio shelf of motivation, devotions and stories, with topic filters and a recording studio for members. | 7-gco-audio.jpg | Audio\n" .
			"GCO Food | https://globalcentreone.com/food/ | A mobile-first local food ordering app with live courier tracking, launching in Rock Springs, WY. | 8-gco-food.jpg | Launching soon",
			'Satu karya per baris:  Judul | Link | Deskripsi | Gambar (nama file bawaan atau URL) | Label',
		),
		'packages_note' => array( 'Catatan di bawah paket', 'text', 'Starting prices. Large stores, many sites or urgent work are quoted after a free look at your site.', '' ),
		'sample_pdf' => array( 'Contoh laporan (PDF)', 'url', '', 'Kosongkan untuk memakai contoh bawaan plugin.' ),
		'ask_button' => array( 'Tampilan "Ask GCO"', 'select', 'banner', 'Banner: menyatu di bagian bawah setiap halaman, tepat di atas footer, tidak menutupi tombol lain. Mengambang: tombol bulat di pojok layar.', array( 'banner' => 'Banner di bawah halaman (disarankan)', 'float-left' => 'Tombol mengambang di kiri bawah', 'float-right' => 'Tombol mengambang di kanan bawah', 'off' => 'Mati (hanya di halaman WordPress Care)' ) ),
		'banner_focus' => array( 'Pesan banner', 'select', 'rotate', 'Bergantian: setiap kunjungan menampilkan salah satu dari dua pesan secara acak.', array( 'rotate' => 'Bergantian: Care dan Own your tools', 'care' => 'Hanya GCO Care (backup & update)', 'own' => 'Hanya Own your tools (bebas dari pihak ketiga)' ) ),
		'own_title'    => array( 'Judul banner "Own your tools"', 'text', 'Still renting your website, one plugin at a time?', '' ),
		'own_text'     => array( 'Teks banner "Own your tools"', 'text', 'GCO builds themes, plugins and features in your organization’s name. One project price, no yearly license to us, and the code is yours to keep.', '' ),
		'banner_title' => array( 'Judul banner', 'text', 'Is your WordPress site safe to update?', '' ),
		'banner_text'  => array( 'Teks banner', 'text', 'GCO Care backs up first, updates one at a time and tests your checkout or donations after every change. Ask us anything about your website.', '' ),
		'ask_label'  => array( 'Teks tombol', 'text', 'Ask GCO', '' ),
		'accent'     => array( 'Warna hijau GCO', 'color', '#0d5c3f', '' ),
		'gold'       => array( 'Warna emas GCO', 'color', '#d9a93b', '' ),
	);
}

function gcc_get( $key ) {
	$opts   = get_option( 'gcc_settings', array() );
	$fields = gcc_fields();
	if ( is_array( $opts ) && isset( $opts[ $key ] ) && '' !== $opts[ $key ] ) {
		return $opts[ $key ];
	}
	return isset( $fields[ $key ] ) ? $fields[ $key ][2] : '';
}

/**
 * Paket dalam bentuk array.
 */
function gcc_packages() {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) gcc_get( 'packages' ) ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$p = array_pad( array_map( 'trim', explode( '|', $line ) ), 5, '' );
		if ( '' === $p[0] ) {
			continue;
		}
		$out[] = array(
			'name'     => $p[0],
			'price'    => $p[1],
			'period'   => $p[2],
			'features' => array_values( array_filter( array_map( 'trim', explode( ';', $p[3] ) ) ) ),
			'featured' => 'featured' === strtolower( $p[4] ),
		);
	}
	return $out;
}

/**
 * Layanan lainnya dalam bentuk array.
 */
function gcc_services() {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) gcc_get( 'services' ) ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$s = array_pad( array_map( 'trim', explode( '|', $line ) ), 4, '' );
		if ( '' === $s[0] ) {
			continue;
		}
		$out[] = array(
			'title' => $s[0],
			'short' => $s[1],
			'items' => array_values( array_filter( array_map( 'trim', explode( ';', $s[2] ) ) ) ),
			'for'   => $s[3],
		);
	}
	return $out;
}

/**
 * Karya GCO dalam bentuk array.
 */
function gcc_work() {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) gcc_get( 'work' ) ) as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$w = array_pad( array_map( 'trim', explode( '|', $line ) ), 5, '' );
		if ( '' === $w[0] ) {
			continue;
		}
		$img = $w[3];
		if ( $img && ! preg_match( '#^https?://#i', $img ) ) {
			$img = GCC_URL . 'assets/work/' . rawurlencode( $img );
		}
		$out[] = array( 'title' => $w[0], 'url' => $w[1], 'desc' => $w[2], 'img' => $img, 'label' => $w[4] );
	}
	return $out;
}

add_action( 'admin_init', function () {
	register_setting( 'gcc_group', 'gcc_settings', array(
		'sanitize_callback' => function ( $in ) {
			$in    = is_array( $in ) ? $in : array();
			$clean = array();
			foreach ( gcc_fields() as $k => $f ) {
				$v = isset( $in[ $k ] ) ? wp_unslash( $in[ $k ] ) : '';
				switch ( $f[1] ) {
					case 'email':
						$clean[ $k ] = sanitize_email( $v );
						break;
					case 'url':
						$clean[ $k ] = esc_url_raw( trim( $v ) );
						break;
					case 'textarea':
						$clean[ $k ] = sanitize_textarea_field( $v );
						break;
					case 'checkbox':
						$clean[ $k ] = $v ? '1' : '0';
						break;
					case 'select':
						$clean[ $k ] = isset( $f[4][ $v ] ) ? $v : $f[2];
						break;
					case 'color':
						$clean[ $k ] = sanitize_hex_color( $v ) ? sanitize_hex_color( $v ) : $f[2];
						break;
					default:
						$clean[ $k ] = sanitize_text_field( $v );
				}
			}
			return $clean;
		},
	) );
} );

add_action( 'admin_menu', function () {
	add_options_page( 'GCO WordPress Care', 'GCO WordPress Care', 'manage_options', 'gco-wp-care', 'gcc_settings_page' );
} );

function gcc_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$page = (int) get_option( 'gcc_page_id' );
	?>
	<div class="wrap">
		<h1>GCO WordPress Care</h1>
		<p>
			Halaman layanan tampil di mana pun shortcode <code>[gco_care]</code> dipasang.
			<?php if ( $page && get_post( $page ) ) : ?>
				Halaman otomatis: <a href="<?php echo esc_url( get_permalink( $page ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( get_permalink( $page ) ); ?></a>
			<?php endif; ?>
			· Permintaan masuk ada di menu <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=gcc_request' ) ); ?>">Care Requests</a>.
		</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'gcc_group' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( gcc_fields() as $k => $f ) :
					$id  = 'gcc_' . $k;
					$val = gcc_get( $k );
					if ( 'sample_pdf' === $k ) {
						$opts = get_option( 'gcc_settings', array() );
						$val  = isset( $opts[ $k ] ) ? $opts[ $k ] : '';
					}
					?>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $f[0] ); ?></label></th>
						<td>
							<?php if ( 'select' === $f[1] ) : ?>
								<select id="<?php echo esc_attr( $id ); ?>" name="gcc_settings[<?php echo esc_attr( $k ); ?>]">
									<?php foreach ( $f[4] as $ov => $ol ) : ?><option value="<?php echo esc_attr( $ov ); ?>" <?php selected( $val, $ov ); ?>><?php echo esc_html( $ol ); ?></option><?php endforeach; ?>
								</select>
							<?php elseif ( 'checkbox' === $f[1] ) : ?>
								<label><input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="gcc_settings[<?php echo esc_attr( $k ); ?>]" value="1" <?php checked( '1', $val ); ?>> Aktif</label>
							<?php elseif ( 'textarea' === $f[1] ) : ?>
								<textarea id="<?php echo esc_attr( $id ); ?>" name="gcc_settings[<?php echo esc_attr( $k ); ?>]" rows="7" class="large-text code"><?php echo esc_textarea( $val ); ?></textarea>
							<?php else : ?>
								<input type="<?php echo esc_attr( 'color' === $f[1] ? 'color' : ( 'email' === $f[1] ? 'email' : ( 'url' === $f[1] ? 'url' : 'text' ) ) ); ?>" id="<?php echo esc_attr( $id ); ?>" name="gcc_settings[<?php echo esc_attr( $k ); ?>]" value="<?php echo esc_attr( $val ); ?>" class="<?php echo 'color' === $f[1] ? '' : 'regular-text'; ?>">
							<?php endif; ?>
							<?php if ( $f[3] ) : ?><p class="description"><?php echo esc_html( $f[3] ); ?></p><?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button( 'Simpan' ); ?>
		</form>
	</div>
	<?php
}
