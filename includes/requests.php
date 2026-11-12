<?php
/**
 * Permintaan layanan: disimpan di dashboard (Care Requests) dan dikirim ke email.
 *
 * @package GCO_WP_Care
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gcc_register_requests() {
	register_post_type( 'gcc_request', array(
		'labels'          => array(
			'name'          => 'Care Requests',
			'singular_name' => 'Care Request',
			'menu_name'     => 'Care Requests',
			'edit_item'     => 'Care Request',
			'all_items'     => 'Semua permintaan',
			'not_found'     => 'Belum ada permintaan.',
		),
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'menu_icon'       => 'dashicons-shield',
		'menu_position'   => 26,
		'supports'        => array( 'title', 'editor' ),
		'capability_type' => 'post',
		'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
		'map_meta_cap'    => true,
	) );
}
add_action( 'init', 'gcc_register_requests' );

function gcc_request_meta_keys() {
	return array(
		'gcc_email'    => 'Email',
		'gcc_org'      => 'Organisasi',
		'gcc_site'     => 'Situs',
		'gcc_package'  => 'Paket / layanan',
		'gcc_features' => 'Situs memakai',
		'gcc_plugins'  => 'Jumlah plugin',
		'gcc_host'     => 'Hosting',
	);
}

add_filter( 'manage_gcc_request_posts_columns', function ( $cols ) {
	return array(
		'cb'          => $cols['cb'],
		'title'       => 'Dari',
		'gcc_status'  => 'Status',
		'gcc_site'    => 'Situs',
		'gcc_package' => 'Paket',
		'gcc_email'   => 'Email',
		'date'        => 'Tanggal',
	);
} );

add_action( 'manage_gcc_request_posts_custom_column', function ( $col, $id ) {
	$v = get_post_meta( $id, $col, true );
	if ( 'gcc_email' === $col && $v ) {
		printf( '<a href="mailto:%1$s">%1$s</a>', esc_attr( $v ) );
	} elseif ( 'gcc_site' === $col && $v ) {
		printf( '<a href="%1$s" target="_blank" rel="noopener">%2$s</a>', esc_url( $v ), esc_html( preg_replace( '#^https?://#', '', $v ) ) );
	} elseif ( 'gcc_status' === $col ) {
		$v      = $v ? $v : 'New';
		$labels = array( 'Replied' => array( 'Dibalas', '#17714b' ), 'Check' => array( 'Cek: mungkin spam', '#8c8f94' ), 'New' => array( 'Baru', '#b45309' ) );
		$l      = isset( $labels[ $v ] ) ? $labels[ $v ] : $labels['New'];
		printf( '<strong style="color:%s">%s</strong>', esc_attr( $l[1] ), esc_html( $l[0] ) );
	} else {
		echo esc_html( $v ? $v : '—' );
	}
}, 10, 2 );

add_action( 'add_meta_boxes_gcc_request', function () {
	add_meta_box( 'gcc_meta', 'Detail permintaan', function ( $post ) {
		echo '<table class="widefat striped"><tbody>';
		foreach ( gcc_request_meta_keys() as $k => $label ) {
			$v = get_post_meta( $post->ID, $k, true );
			echo '<tr><th style="width:120px">' . esc_html( $label ) . '</th><td>' . esc_html( $v ? $v : '—' ) . '</td></tr>';
		}
		$sus = get_post_meta( $post->ID, 'gcc_suspect', true );
		if ( $sus ) {
			echo '<tr><th>Catatan</th><td style="color:#b45309">Ditandai mungkin spam (' . esc_html( $sus ) . '). Jika ini pesan asli, balas seperti biasa.</td></tr>';
		}
		echo '</tbody></table>';
		$email = get_post_meta( $post->ID, 'gcc_email', true );
		if ( $email ) {
			printf(
				'<p><a class="button button-primary" href="mailto:%s?subject=%s">Balas lewat email</a></p>',
				esc_attr( $email ),
				rawurlencode( 'Re: WordPress Care request for ' . get_post_meta( $post->ID, 'gcc_site', true ) )
			);
		}
	}, 'gcc_request', 'side', 'high' );
} );

/* Jumlah permintaan baru di menu admin. */
add_action( 'admin_menu', function () {
	global $menu;
	$n = (int) get_option( 'gcc_unread', 0 );
	if ( $n < 1 || ! is_array( $menu ) ) {
		return;
	}
	foreach ( $menu as $i => $item ) {
		if ( isset( $item[2] ) && 'edit.php?post_type=gcc_request' === $item[2] ) {
			$menu[ $i ][0] .= ' <span class="awaiting-mod"><span class="pending-count">' . $n . '</span></span>'; // phpcs:ignore
		}
	}
}, 99 );

add_action( 'load-edit.php', function () {
	if ( isset( $_GET['post_type'] ) && 'gcc_request' === $_GET['post_type'] ) { // phpcs:ignore
		update_option( 'gcc_unread', 0, false );
	}
} );

function gcc_redirect( $status, $kind = 'care' ) {
	$back = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$back = remove_query_arg( array( 'gcc', 'gcc_ask' ), $back );
	if ( 'ask' === $kind ) {
		wp_safe_redirect( add_query_arg( 'gcc_ask', $status, $back ) );
	} else {
		wp_safe_redirect( add_query_arg( 'gcc', $status, $back ) . '#gcc-request' );
	}
	exit;
}

function gcc_handle_request() {
	$kind = ( isset( $_POST['gcc_kind'] ) && 'ask' === $_POST['gcc_kind'] ) ? 'ask' : 'care'; // phpcs:ignore
	// Formulir publik: tidak memakai nonce (bisa kedaluwarsa karena cache halaman).
	// Perlindungan spam: jebakan bot (honeypot), batas kirim per koneksi, dan waktu minimal mengisi.
	// Tanda kecurigaan spam. Pesan tetap disimpan (tidak pernah dibuang), tapi diberi label dan tanpa email.
	$suspect = array();
	if ( ! empty( $_POST['gcc_hp_x7'] ) ) { // phpcs:ignore -- kolom jebakan bot.
		$suspect[] = 'hidden field filled';
	}
	// Lama mengisi dihitung di browser (kebal terhadap perbedaan jam komputer dan server).
	$elapsed = ( isset( $_POST['gcc_e'] ) && '' !== $_POST['gcc_e'] ) ? absint( $_POST['gcc_e'] ) : -1; // phpcs:ignore
	if ( $elapsed >= 0 && $elapsed < 2 ) {
		$suspect[] = 'sent in under 2 seconds';
	}

	$ip_key = 'gcc_rate_' . md5( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'x' );
	$tries  = (int) get_transient( $ip_key );
	if ( $tries >= 3 ) {
		gcc_redirect( 'rate', $kind );
	}

	$f = function ( $k, $type = 'text' ) {
		if ( ! isset( $_POST[ $k ] ) ) { // phpcs:ignore
			return '';
		}
		$v = wp_unslash( $_POST[ $k ] ); // phpcs:ignore
		if ( 'email' === $type ) {
			return sanitize_email( $v );
		}
		if ( 'url' === $type ) {
			$v = trim( $v );
			if ( $v && ! preg_match( '#^https?://#i', $v ) ) {
				$v = 'https://' . $v;
			}
			return esc_url_raw( $v );
		}
		if ( 'textarea' === $type ) {
			return sanitize_textarea_field( $v );
		}
		return sanitize_text_field( $v );
	};

	$name    = $f( 'gcc_name' );
	$email   = $f( 'gcc_email', 'email' );
	$org     = $f( 'gcc_org' );
	$site    = $f( 'gcc_site', 'url' );
	$package = $f( 'gcc_package' );
	$plugins = $f( 'gcc_plugins' );
	$host    = $f( 'gcc_host' );
	$message = $f( 'gcc_message', 'textarea' );

	$allowed  = array( 'Online store', 'Donations', 'Subscriptions or memberships', 'Events', 'Page builder' );
	$features = array();
	if ( isset( $_POST['gcc_features'] ) && is_array( $_POST['gcc_features'] ) ) {
		foreach ( wp_unslash( $_POST['gcc_features'] ) as $feat ) { // phpcs:ignore
			$feat = sanitize_text_field( $feat );
			if ( in_array( $feat, $allowed, true ) ) {
				$features[] = $feat;
			}
		}
	}

	if ( 'ask' === $kind ) {
		if ( '' === $name || ! is_email( $email ) || '' === trim( $message ) ) {
			gcc_redirect( 'invalid', 'ask' );
		}
		$package = $package ? $package : 'Question';
	} elseif ( '' === $name || ! is_email( $email ) || '' === $site ) {
		gcc_redirect( 'invalid' );
	}

	set_transient( $ip_key, $tries + 1, 10 * MINUTE_IN_SECONDS );

	$title = $org ? $name . ' — ' . $org : $name;
	$id    = wp_insert_post( array(
		'post_type'    => 'gcc_request',
		'post_status'  => 'publish',
		'post_title'   => $title,
		'post_content' => $message,
	) );

	$meta = array(
		'gcc_email'    => $email,
		'gcc_org'      => $org,
		'gcc_site'     => $site,
		'gcc_package'  => $package,
		'gcc_features' => implode( ', ', $features ),
		'gcc_plugins'  => $plugins,
		'gcc_host'     => $host,
	);
	if ( $id && ! is_wp_error( $id ) ) {
		foreach ( $meta as $k => $v ) {
			update_post_meta( $id, $k, $v );
		}
		update_post_meta( $id, 'gcc_kind', $kind );
		update_post_meta( $id, 'gcc_status', $suspect ? 'Check' : 'New' );
		if ( $suspect ) {
			update_post_meta( $id, 'gcc_suspect', implode( ', ', $suspect ) );
		} else {
			update_option( 'gcc_unread', (int) get_option( 'gcc_unread', 0 ) + 1, false );
		}
	}

	if ( $suspect ) {
		gcc_redirect( 'sent', $kind ); // Tersimpan untuk dicek; tanpa email agar spam tidak membanjiri inbox.
	}

	$to   = gcc_get( 'email' ) ? gcc_get( 'email' ) : get_option( 'admin_email' );
	$body = ( 'ask' === $kind ? "New question from Ask GCO" : "New WordPress Care request" ) . "\n\n";
	foreach ( gcc_request_meta_keys() as $k => $label ) {
		$body .= $label . ': ' . ( $meta[ $k ] ? $meta[ $k ] : '-' ) . "\n";
	}
	$body .= "\nMessage:\n" . ( $message ? $message : '-' ) . "\n\nAll requests: " . admin_url( 'edit.php?post_type=gcc_request' );
	$subject = ( 'ask' === $kind ? '[Ask GCO] ' : '[GCO Care] Request from ' ) . $title;
	$body   .= "\nReply from your dashboard: " . ( $id && ! is_wp_error( $id ) ? admin_url( 'post.php?post=' . $id . '&action=edit' ) : '' );
	wp_mail( $to, $subject, $body, array( 'Reply-To: ' . $name . ' <' . $email . '>' ) );

	// Konfirmasi otomatis ke pengirim, supaya mereka tahu pesannya diterima.
	$company = gcc_get( 'company' );
	$confirm = "Hi {$name},\n\nThank you for contacting Global Centre One. We received your message and will reply in writing " . gcc_get( 'response' ) . ".\n\nYour message:\n" . ( $message ? $message : '-' ) . "\n\n— GCO Digital\n{$company}\n" . home_url( '/' );
	$reply_to = gcc_get( 'email' ) ? gcc_get( 'email' ) : get_option( 'admin_email' );
	wp_mail( $email, 'We received your message — Global Centre One', $confirm, array( 'Reply-To: GCO <' . $reply_to . '>' ) );

	gcc_redirect( 'sent', $kind );
}
add_action( 'admin_post_nopriv_gcc_request', 'gcc_handle_request' );
add_action( 'admin_post_gcc_request', 'gcc_handle_request' );


/* Balas langsung dari dashboard: email terkirim ke pengirim dan tersimpan sebagai riwayat. */
add_action( 'add_meta_boxes_gcc_request', function () {
	add_meta_box( 'gcc_reply', 'Balas ke pengirim', function ( $post ) {
		wp_nonce_field( 'gcc_reply', 'gcc_reply_nonce' );
		$email   = get_post_meta( $post->ID, 'gcc_email', true );
		$history = get_post_meta( $post->ID, 'gcc_replies', true );
		$history = is_array( $history ) ? $history : array();
		$sent    = get_transient( 'gcc_reply_sent_' . get_current_user_id() );
		if ( $sent ) {
			delete_transient( 'gcc_reply_sent_' . get_current_user_id() );
			echo '<div class="notice notice-success inline"><p>Balasan terkirim ke ' . esc_html( $email ) . '.</p></div>';
		}
		if ( $history ) {
			echo '<h4 style="margin:8px 0">Riwayat balasan</h4>';
			foreach ( $history as $h ) {
				echo '<div style="background:#f6f7f7;border-left:3px solid #0d5c3f;padding:8px 12px;margin-bottom:8px">';
				echo '<p style="margin:0 0 4px;color:#646970;font-size:12px">' . esc_html( $h['date'] ) . '</p>';
				echo '<p style="margin:0;white-space:pre-wrap">' . esc_html( $h['text'] ) . '</p></div>';
			}
		}
		if ( ! $email ) {
			echo '<p>Tidak ada email pengirim.</p>';
			return;
		}
		echo '<p><label for="gcc_reply_text"><strong>Tulis balasan</strong> (dikirim ke ' . esc_html( $email ) . ')</label></p>';
		echo '<textarea id="gcc_reply_text" name="gcc_reply_text" rows="8" class="large-text" placeholder="Hi ' . esc_attr( strtok( get_the_title( $post ), ' ' ) ) . ', thank you for reaching out..."></textarea>';
		echo '<p><button type="submit" name="gcc_send_reply" value="1" class="button button-primary">Kirim balasan</button> <span class="description">Email terkirim dari situs ini; balasan mereka masuk ke ' . esc_html( gcc_get( 'email' ) ? gcc_get( 'email' ) : get_option( 'admin_email' ) ) . '.</span></p>';
	}, 'gcc_request', 'normal', 'high' );
} );

add_action( 'save_post_gcc_request', function ( $post_id ) {
	if ( empty( $_POST['gcc_send_reply'] ) || ! isset( $_POST['gcc_reply_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['gcc_reply_nonce'] ), 'gcc_reply' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$text  = isset( $_POST['gcc_reply_text'] ) ? sanitize_textarea_field( wp_unslash( $_POST['gcc_reply_text'] ) ) : '';
	$email = get_post_meta( $post_id, 'gcc_email', true );
	if ( '' === trim( $text ) || ! is_email( $email ) ) {
		return;
	}
	$from     = gcc_get( 'email' ) ? gcc_get( 'email' ) : get_option( 'admin_email' );
	$original = get_post_field( 'post_content', $post_id );
	$body     = $text . "\n\n— GCO Digital\n" . gcc_get( 'company' ) . "\n" . home_url( '/' );
	if ( $original ) {
		$body .= "\n\n----\nYour message:\n" . $original;
	}
	$ok = wp_mail( $email, 'Re: your message to Global Centre One', $body, array( 'Reply-To: GCO <' . $from . '>' ) );
	if ( $ok ) {
		$history   = get_post_meta( $post_id, 'gcc_replies', true );
		$history   = is_array( $history ) ? $history : array();
		$history[] = array( 'date' => wp_date( 'M j, Y g:i a' ), 'text' => $text );
		update_post_meta( $post_id, 'gcc_replies', $history );
		update_post_meta( $post_id, 'gcc_status', 'Replied' );
		set_transient( 'gcc_reply_sent_' . get_current_user_id(), 1, 60 );
	}
} );
