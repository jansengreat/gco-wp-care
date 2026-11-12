<?php
/**
 * Tombol mengambang "Ask GCO" + dialog: pengunjung menulis apa yang mereka butuhkan.
 *
 * @package GCO_WP_Care
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gcc_is_care_page() {
	$post = get_post();
	return is_singular() && $post && has_shortcode( $post->post_content, 'gco_care' );
}

function gcc_ask_mode() {
	$m = (string) gcc_get( 'ask_button' );
	if ( '1' === $m ) {
		$m = 'banner'; // Nilai lama dari versi sebelumnya.
	}
	if ( '0' === $m ) {
		$m = 'off';
	}
	return in_array( $m, array( 'banner', 'float-left', 'float-right', 'off' ), true ) ? $m : 'banner';
}

function gcc_ask_enabled() {
	if ( is_admin() ) {
		return false;
	}
	return 'off' !== gcc_ask_mode() || gcc_is_care_page();
}

function gcc_care_url() {
	$page = (int) get_option( 'gcc_page_id' );
	return ( $page && get_post( $page ) ) ? get_permalink( $page ) : home_url( '/wordpress-care/' );
}

add_action( 'wp_enqueue_scripts', function () {
	if ( gcc_ask_enabled() ) {
		wp_enqueue_style( 'gcc' );
		wp_enqueue_script( 'gcc' );
	}
}, 20 );

function gcc_ask_topics() {
	return array(
		'Website care, updates or backups',
		'A new website or theme',
		'A custom plugin or feature',
		'Replace paid plugins with our own',
		'Online store, donations or memberships',
		'Speed, mobile or something broken',
		'AI features',
		'Something else',
	);
}

add_action( 'wp_footer', function () {
	if ( ! gcc_ask_enabled() ) {
		return;
	}
	$status = isset( $_GET['gcc_ask'] ) ? sanitize_key( $_GET['gcc_ask'] ) : ''; // phpcs:ignore
	$style  = '--gcc-green:' . sanitize_hex_color( gcc_get( 'accent' ) ) . ';--gcc-gold:' . sanitize_hex_color( gcc_get( 'gold' ) ) . ';';
	?>
<?php
	$mode     = gcc_ask_mode();
	$on_care  = gcc_is_care_page();
	$show_bar = 'banner' === $mode && ! $on_care;
	$show_fab = in_array( $mode, array( 'float-left', 'float-right' ), true );
	?>
<div class="gcc-ask gcc-ask--<?php echo esc_attr( $mode ); ?>" style="<?php echo esc_attr( $style ); ?>" data-gcc-ask-status="<?php echo esc_attr( $status ); ?>">
	<?php if ( $show_bar ) : ?>
	<?php
	$focus  = (string) gcc_get( 'banner_focus' );
	$focus  = in_array( $focus, array( 'care', 'own', 'rotate' ), true ) ? $focus : 'rotate';
	$panels = array();
	if ( 'own' !== $focus ) {
		$panels['care'] = array(
			'kicker' => 'GCO Care · WordPress care & security',
			'title'  => gcc_get( 'banner_title' ),
			'text'   => gcc_get( 'banner_text' ),
			'icon'   => '<path d="M12 3l7 3v5c0 4.5-3 8.3-7 10-4-1.7-7-5.5-7-10V6l7-3z"/><path d="M9 12l2 2 4-4"/>',
			'cta'    => gcc_get( 'ask_label' ),
			'topic'  => 'Website care, updates or backups',
			'link'   => array( 'See GCO Care plans', gcc_care_url() ),
		);
	}
	if ( 'care' !== $focus ) {
		$panels['own'] = array(
			'kicker' => 'GCO Digital · Own your tools',
			'title'  => gcc_get( 'own_title' ),
			'text'   => gcc_get( 'own_text' ),
			'icon'   => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 7.5-2"/>',
			'cta'    => 'Tell us what you depend on',
			'topic'  => 'Replace paid plugins with our own',
			'link'   => array( 'How ownership works', gcc_care_url() . '#gcc-own' ),
		);
	}
	?>
	<aside class="gcc-band" data-gcc-band aria-label="GCO" style="<?php echo esc_attr( $style ); ?>">
		<?php foreach ( $panels as $key => $pn ) : ?>
		<div class="gcc-band__inner" data-gcc-band-panel="<?php echo esc_attr( $key ); ?>"<?php echo ( count( $panels ) > 1 && 'own' === $key ) ? ' hidden' : ''; ?>>
			<span class="gcc-band__badge" aria-hidden="true"><svg class="gcc-i" viewBox="0 0 24 24"><?php echo $pn['icon']; // phpcs:ignore ?></svg></span>
			<div class="gcc-band__text">
				<p class="gcc-band__kicker"><?php echo esc_html( $pn['kicker'] ); ?></p>
				<p class="gcc-band__title"><?php echo esc_html( $pn['title'] ); ?></p>
				<p class="gcc-band__sub"><?php echo esc_html( $pn['text'] ); ?></p>
			</div>
			<div class="gcc-band__actions">
				<button type="button" class="gcc-btn gcc-btn--gold" data-gcc-ask-open data-gcc-ask-topic="<?php echo esc_attr( $pn['topic'] ); ?>" aria-haspopup="dialog">
					<svg class="gcc-i" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v11H9l-5 4V5z"/><path d="M8 9h8M8 12h5"/></svg>
					<?php echo esc_html( $pn['cta'] ); ?>
				</button>
				<a class="gcc-btn gcc-btn--ghost" href="<?php echo esc_url( $pn['link'][1] ); ?>"><?php echo esc_html( $pn['link'][0] ); ?></a>
			</div>
		</div>
		<?php endforeach; ?>
	</aside>
	<?php endif; ?>
	<?php if ( $show_fab ) : ?>
	<button type="button" class="gcc-ask__fab" data-gcc-ask-open aria-haspopup="dialog">
		<svg class="gcc-i" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v11H9l-5 4V5z"/><path d="M8 9h8M8 12h5"/></svg>
		<span><?php echo esc_html( gcc_get( 'ask_label' ) ); ?></span>
	</button>
	<?php endif; ?>

	<dialog class="gcc-ask__dialog" id="gcc-ask-dialog" aria-labelledby="gcc-ask-title">
		<form class="gcc-ask__panel" data-gcc-form="ask" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<button type="button" class="gcc-ask__close" data-gcc-ask-close aria-label="Close">
				<svg class="gcc-i" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
			</button>
			<p class="gcc-ask__kicker">Global Centre One · GCO Digital</p>
			<h2 id="gcc-ask-title" class="gcc-ask__title">What do you need?</h2>
			<p class="gcc-ask__intro">Free consultation on anything about your website: a fix, a new feature, a whole new site. A real person reads every message and replies in writing <?php echo esc_html( gcc_get( 'response' ) ); ?>.</p>

			<?php if ( 'sent' === $status ) : ?>
				<p class="gcc-alert gcc-alert--ok" role="status">Message sent. We’ve emailed you a confirmation, and you’ll get a written reply <?php echo esc_html( gcc_get( 'response' ) ); ?>.</p>
			<?php elseif ( 'invalid' === $status ) : ?>
				<p class="gcc-alert gcc-alert--err" role="alert">Message not sent. Add your name, a valid email and what you need, then send again.</p>
			<?php elseif ( 'rate' === $status ) : ?>
				<p class="gcc-alert gcc-alert--err" role="alert">Message not sent. Too many messages from this connection; try again in 10 minutes.</p>
			<?php endif; ?>

			<input type="hidden" name="action" value="gcc_request">
			<input type="hidden" name="gcc_kind" value="ask">
			<input type="hidden" name="gcc_e" value="" data-gcc-e>
			<div class="gcc-hp" aria-hidden="true"><label>Leave this empty <input type="text" name="gcc_hp_x7" value="" tabindex="-1" autocomplete="one-time-code" data-lpignore="true" data-1p-ignore></label></div>

			<fieldset class="gcc-field gcc-checks gcc-ask__topics">
				<legend>It’s about</legend>
				<?php foreach ( gcc_ask_topics() as $i => $t ) : ?>
					<label><input type="radio" name="gcc_package" value="<?php echo esc_attr( $t ); ?>" <?php checked( 0, $i ); ?>><span><?php echo esc_html( $t ); ?></span></label>
				<?php endforeach; ?>
			</fieldset>

			<p class="gcc-field">
				<label for="gcc-ask-message">Tell us what you need</label>
				<textarea id="gcc-ask-message" name="gcc_message" rows="4" required placeholder="e.g. Our donation form stopped working after an update, or: we need an event registration page."></textarea>
			</p>
			<div class="gcc-form__row">
				<p class="gcc-field"><label for="gcc-ask-name">Your name</label><input id="gcc-ask-name" name="gcc_name" type="text" required autocomplete="name"></p>
				<p class="gcc-field"><label for="gcc-ask-email">Email</label><input id="gcc-ask-email" name="gcc_email" type="email" required autocomplete="email"></p>
			</div>
			<p class="gcc-field"><label for="gcc-ask-site">Website <small>optional</small></label><input id="gcc-ask-site" name="gcc_site" type="text" inputmode="url" placeholder="example.org"></p>

			<div class="gcc-ask__foot">
				<button type="submit" class="gcc-btn gcc-btn--gold">Send message</button>
				<p>Never send passwords here.</p>
			</div>
			<p class="gcc-ask__alt">Free consultation on anything. You can also email
				<?php $cmail = gcc_get( 'email' ); if ( $cmail ) : ?><a href="mailto:<?php echo esc_attr( $cmail ); ?>"><?php echo esc_html( $cmail ); ?></a><?php endif; ?>
				<?php if ( gcc_get( 'contact_url' ) ) : ?>or use our <a href="<?php echo esc_url( gcc_get( 'contact_url' ) ); ?>">contact page</a><?php endif; ?>.</p>
		</form>
	</dialog>
</div>
	<?php
} );
