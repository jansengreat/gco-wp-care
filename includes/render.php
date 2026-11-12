<?php
/**
 * Tampilan halaman layanan: shortcode [gco_care].
 *
 * @package GCO_WP_Care
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function gcc_faq() {
	return array(
		array( 'Do you need my hosting or Stripe passwords?', 'No. You create an admin login for me in WordPress. I never need your payment-processor account, and I never share your login with anyone. Anything that must be checked inside Stripe or PayPal, I flag for you to confirm.' ),
		array( 'What happens if an update breaks something?', 'I stop right away, roll back that one update or restore the backup taken before work began, and confirm the site works again. You get a written note of what happened and the safe way forward before anything else changes.' ),
		array( 'Will my site go offline during updates?', 'Normally no. Updates are done one at a time at a low-traffic time you choose, and each one is tested before the next.' ),
		array( 'Where are my backups kept?', 'In your own Google Drive account, so you own them and they live off your server. Your host’s backups stay as a second layer.' ),
		array( 'Do you delete plugins or change things on your own?', 'Never. Nothing is deleted and nothing is changed without your approval. Recommendations come in writing first.' ),
		array( 'Do you use AI tools?', 'Yes, to research and double-check work. Your login details, customer data and order information are never shared with them.' ),
		array( 'Which sites do you work on?', 'WordPress sites, including WooCommerce stores, donation forms, subscriptions, memberships, events and page builders. Churches, ministries, nonprofits and small businesses are especially welcome.' ),
	);
}

function gcc_sample_log() {
	return array(
		array( 'Example security plugin', 'x.y.1 → x.y.4', 'Test order with 100%-off coupon; login; admin screens', 'pass', 'Roll back to x.y.1, or restore the backup' ),
		array( 'Example subscriptions add-on', 'x.y.0 → x.y.2', 'Test order; subscription created; next payment date correct; renewal in Scheduled Actions', 'pass', 'Roll back to x.y.0' ),
		array( 'Example events plugin', 'x.y.3 → x.y.5', 'Event pages; event checkout; page-builder layouts', 'stop', 'Rolled back to x.y.3; site confirmed working' ),
	);
}

function gcc_icon( $name ) {
	$p = array(
		'shield'  => '<path d="M12 3l7 3v5c0 4.5-3 8.3-7 10-4-1.7-7-5.5-7-10V6l7-3z"/><path d="M9 12l2 2 4-4"/>',
		'cloud'   => '<path d="M7 18h10a4 4 0 0 0 .5-7.97A6 6 0 0 0 6 9.5 4.25 4.25 0 0 0 7 18z"/><path d="M12 11v5m-2-2l2 2 2-2"/>',
		'layers'  => '<path d="M12 3l9 5-9 5-9-5 9-5z"/><path d="M3 13l9 5 9-5"/>',
		'cart'    => '<circle cx="9" cy="20" r="1.4"/><circle cx="17" cy="20" r="1.4"/><path d="M3 4h2l2.4 11h10.2L20 8H6.2"/>',
		'undo'    => '<path d="M9 14L4 9l5-5"/><path d="M4 9h10a6 6 0 0 1 0 12h-3"/>',
		'doc'     => '<path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5M10 13h6M10 17h6"/>',
		'lock'    => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>',
		'user'    => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
		'flag'    => '<path d="M5 21V4m0 0h11l-2 4 2 4H5"/>',
		'check'   => '<path d="M5 12.5l4.5 4.5L19 7"/>',
		'pause'   => '<path d="M9 6v12M15 6v12"/>',
		'down'    => '<path d="M12 4v12m-5-5l5 5 5-5M5 20h14"/>',
	);
	return '<svg class="gcc-i" viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . ( isset( $p[ $name ] ) ? $p[ $name ] : '' ) . '</svg>';
}

function gcc_sample_pdf_url() {
	$custom = gcc_get( 'sample_pdf' );
	return $custom ? $custom : GCC_URL . 'assets/sample-update-backup-log.pdf';
}

add_action( 'wp_enqueue_scripts', function () {
	wp_register_style( 'gcc', GCC_URL . 'assets/care.css', array(), GCC_VERSION );
	wp_register_script( 'gcc', GCC_URL . 'assets/care.js', array(), GCC_VERSION, true );
} );

function gcc_render() {
	wp_enqueue_style( 'gcc' );
	wp_enqueue_script( 'gcc' );

	$green    = sanitize_hex_color( gcc_get( 'accent' ) );
	$gold     = sanitize_hex_color( gcc_get( 'gold' ) );
	$cur      = gcc_get( 'currency' );
	$packages = gcc_packages();
	$status   = isset( $_GET['gcc'] ) ? sanitize_key( $_GET['gcc'] ) : ''; // phpcs:ignore
	$style    = '--gcc-green:' . $green . ';--gcc-gold:' . $gold . ';';

	ob_start();
	?>
<div class="gcc" style="<?php echo esc_attr( $style ); ?>">

	<section class="gcc-hero" aria-labelledby="gcc-title">
		<div class="gcc-hero__text">
			<p class="gcc-kicker"><?php echo gcc_icon( 'shield' ); // phpcs:ignore ?> GCO Digital · WordPress Care</p>
			<h1 id="gcc-title" class="gcc-title">Update your WordPress site without breaking what people pay with.</h1>
			<p class="gcc-lead">Backups first. One update at a time. A real test of your checkout, donations or forms after every change. And a written note of exactly what changed and how to undo it.</p>
			<div class="gcc-actions">
				<a class="gcc-btn gcc-btn--gold" href="#gcc-request">Request a free site check</a>
				<a class="gcc-btn gcc-btn--ghost" href="#gcc-sample">See a sample report</a>
				<button type="button" class="gcc-btn gcc-btn--ghost" data-gcc-ask-open>Ask a question</button>
			</div>
			<p class="gcc-meta">By <?php echo esc_html( gcc_get( 'company' ) ); ?> · <?php echo esc_html( gcc_get( 'location' ) ); ?> · Written updates during US business hours</p>
		</div>

		<div class="gcc-console" aria-label="Example of an update session">
			<div class="gcc-console__bar"><span></span><span></span><span></span><b>Update session · example</b></div>
			<ol class="gcc-console__log" data-gcc-log>
				<li class="is-ok"><?php echo gcc_icon( 'cloud' ); // phpcs:ignore ?><span>Full backup saved to Google Drive</span><em>verified</em></li>
				<li class="is-ok"><?php echo gcc_icon( 'layers' ); // phpcs:ignore ?><span>WordPress core updated</span><em>1 of 4</em></li>
				<li class="is-ok"><?php echo gcc_icon( 'cart' ); // phpcs:ignore ?><span>Test order placed · payment OK</span><em>pass</em></li>
				<li class="is-ok"><?php echo gcc_icon( 'layers' ); // phpcs:ignore ?><span>Subscriptions add-on updated</span><em>2 of 4</em></li>
				<li class="is-ok"><?php echo gcc_icon( 'check' ); // phpcs:ignore ?><span>Renewal scheduled · next date correct</span><em>pass</em></li>
				<li class="is-stop"><?php echo gcc_icon( 'undo' ); // phpcs:ignore ?><span>Events plugin broke a layout → rolled back</span><em>paused</em></li>
				<li class="is-ok"><?php echo gcc_icon( 'doc' ); // phpcs:ignore ?><span>Written report sent to you</span><em>done</em></li>
			</ol>
		</div>
	</section>

	<section class="gcc-sec gcc-problems" aria-labelledby="gcc-problems-title">
		<h2 id="gcc-problems-title" class="gcc-h2">Sound familiar?</h2>
		<ul class="gcc-problems__list">
			<li>“We have dozens of updates waiting and we’re afraid to click.”</li>
			<li>“The last update broke our checkout and we lost a weekend.”</li>
			<li>“We’re not sure we even have a backup that works.”</li>
			<li>“We were hacked once. We can’t let it happen again.”</li>
		</ul>
	</section>

	<section class="gcc-sec" aria-labelledby="gcc-process-title">
		<h2 id="gcc-process-title" class="gcc-h2">How every job is done</h2>
		<p class="gcc-sub">The same careful order, every time. Nothing is skipped to save time.</p>
		<ol class="gcc-steps">
			<li><span class="gcc-steps__n">1</span><div><h3>Backup first</h3><p>Automatic backups to your own Google Drive, plus a full backup checked complete before anything changes.</p></div></li>
			<li><span class="gcc-steps__n">2</span><div><h3>One update at a time</h3><p>WordPress and plugins updated in a written order you approve, never all at once.</p></div></li>
			<li><span class="gcc-steps__n">3</span><div><h3>Test what matters</h3><p>After each update: a real test order, donation or form, subscription renewals, events and key pages on desktop and mobile.</p></div></li>
			<li><span class="gcc-steps__n">4</span><div><h3>Stop and restore</h3><p>If anything breaks, work stops. That one change is rolled back and you’re told exactly what caused it.</p></div></li>
			<li><span class="gcc-steps__n">5</span><div><h3>Written report</h3><p>What was updated, what was tested, what to watch, and how to undo every change.</p></div></li>
		</ol>
	</section>

	<section class="gcc-sec gcc-sample" id="gcc-sample" aria-labelledby="gcc-sample-title">
		<div class="gcc-sample__head">
			<div>
				<h2 id="gcc-sample-title" class="gcc-h2">This is the report you get</h2>
				<p class="gcc-sub">After every session. Example entries shown, not a real client.</p>
			</div>
			<a class="gcc-btn gcc-btn--line" href="<?php echo esc_url( gcc_sample_pdf_url() ); ?>" target="_blank" rel="noopener" download><?php echo gcc_icon( 'down' ); // phpcs:ignore ?> Download sample (PDF)</a>
		</div>

		<article class="gcc-report">
			<header class="gcc-report__head">
				<div>
					<p class="gcc-report__label">WordPress Update &amp; Backup Log</p>
					<p class="gcc-report__title">Example session · low-traffic window</p>
				</div>
				<span class="gcc-tag">Sample</span>
			</header>

			<dl class="gcc-report__summary">
				<div><dt>Backup before work</dt><dd>Full files + database to Google Drive, all parts complete</dd></div>
				<div><dt>Result</dt><dd><b class="gcc-ok">2 of 3 updates done</b>, checkout and renewals verified. 1 update paused.</dd></div>
				<div><dt>Approvals</dt><dd>Followed the approved update order. Nothing deleted.</dd></div>
			</dl>

			<div class="gcc-table" role="region" aria-label="Sample update table" tabindex="0">
				<table>
					<thead><tr><th>#</th><th>Item</th><th>Version</th><th>Tested after update</th><th>Result</th><th>How to undo</th></tr></thead>
					<tbody>
					<?php foreach ( gcc_sample_log() as $i => $r ) : ?>
						<tr>
							<td><?php echo (int) $i + 1; ?></td>
							<td><?php echo esc_html( $r[0] ); ?></td>
							<td class="gcc-mono"><?php echo esc_html( $r[1] ); ?></td>
							<td><?php echo esc_html( $r[2] ); ?></td>
							<td><?php if ( 'pass' === $r[3] ) : ?><span class="gcc-pill gcc-pill--ok"><?php echo gcc_icon( 'check' ); // phpcs:ignore ?>Pass</span><?php else : ?><span class="gcc-pill gcc-pill--stop"><?php echo gcc_icon( 'pause' ); // phpcs:ignore ?>Stopped</span><?php endif; ?></td>
							<td><?php echo esc_html( $r[4] ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<div class="gcc-report__note">
				<?php echo gcc_icon( 'flag' ); // phpcs:ignore ?>
				<p><b>Issue:</b> update #3 broke an event-page layout. Work stopped, the update was rolled back, the site was confirmed working, and the update is paused until the vendor fixes it or you decide.</p>
			</div>
		</article>
	</section>

	<?php $work = gcc_work(); if ( $work ) : ?>
	<section class="gcc-sec" id="gcc-work" aria-labelledby="gcc-work-title">
		<h2 id="gcc-work-title" class="gcc-h2">Built and run by GCO</h2>
		<p class="gcc-sub">Our own platforms, live and in daily use. The same care goes into your site.</p>
		<div class="gcc-work">
			<?php foreach ( $work as $i => $w ) : ?>
				<a class="gcc-work__item<?php echo 0 === $i ? ' gcc-work__item--lead' : ''; ?>" href="<?php echo esc_url( $w['url'] ); ?>" target="_blank" rel="noopener">
					<?php if ( $w['img'] ) : ?>
						<span class="gcc-work__media"><img src="<?php echo esc_url( $w['img'] ); ?>" alt="<?php echo esc_attr( $w['title'] . ' homepage' ); ?>" loading="lazy" decoding="async" width="1200" height="750"></span>
					<?php endif; ?>
					<span class="gcc-work__body">
						<?php if ( $w['label'] ) : ?><span class="gcc-work__label"><?php echo esc_html( $w['label'] ); ?></span><?php endif; ?>
						<span class="gcc-work__title"><?php echo esc_html( $w['title'] ); ?></span>
						<span class="gcc-work__desc"><?php echo esc_html( $w['desc'] ); ?></span>
						<span class="gcc-work__link">Visit <?php echo esc_html( preg_replace( '#^https?://(www\.)?#', '', untrailingslashit( $w['url'] ) ) ); ?></span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( $packages ) : ?>
	<section class="gcc-sec" id="gcc-plans" aria-labelledby="gcc-plans-title">
		<h2 id="gcc-plans-title" class="gcc-h2">Plans</h2>
		<p class="gcc-sub">Start with a one-time job, or keep your site cared for every month.</p>
		<div class="gcc-plans">
			<?php foreach ( $packages as $p ) : ?>
				<div class="gcc-plan<?php echo $p['featured'] ? ' gcc-plan--featured' : ''; ?>">
					<?php if ( $p['featured'] ) : ?><span class="gcc-plan__badge">For stores &amp; donations</span><?php endif; ?>
					<h3 class="gcc-plan__name"><?php echo esc_html( $p['name'] ); ?></h3>
					<p class="gcc-plan__price">
						<?php if ( is_numeric( $p['price'] ) ) : ?><small>from</small> <?php echo esc_html( $cur . $p['price'] ); ?><?php else : ?><?php echo esc_html( $p['price'] ); ?><?php endif; ?>
						<span><?php echo esc_html( $p['period'] ); ?></span>
					</p>
					<ul>
						<?php foreach ( $p['features'] as $feat ) : ?><li><?php echo gcc_icon( 'check' ); // phpcs:ignore ?><?php echo esc_html( $feat ); ?></li><?php endforeach; ?>
					</ul>
					<a class="gcc-btn <?php echo $p['featured'] ? 'gcc-btn--gold' : 'gcc-btn--line'; ?>" href="#gcc-request" data-gcc-pick="<?php echo esc_attr( $p['name'] ); ?>">Choose <?php echo esc_html( $p['name'] ); ?></a>
				</div>
			<?php endforeach; ?>
		</div>
		<?php if ( gcc_get( 'packages_note' ) ) : ?><p class="gcc-note"><?php echo esc_html( gcc_get( 'packages_note' ) ); ?></p><?php endif; ?>
	</section>
	<?php endif; ?>

	<?php $services = gcc_services(); if ( $services ) : ?>
	<section class="gcc-sec" id="gcc-services" aria-labelledby="gcc-services-title">
		<h2 id="gcc-services-title" class="gcc-h2">More we build at GCO</h2>
		<p class="gcc-sub">Care is where most sites start. When you need more, it’s the same careful, written way of working. Tap a service to see what’s included.</p>
		<div class="gcc-services">
			<?php foreach ( $services as $s ) : ?>
				<details class="gcc-service">
					<summary>
						<span class="gcc-service__title"><?php echo esc_html( $s['title'] ); ?></span>
						<span class="gcc-service__short"><?php echo esc_html( $s['short'] ); ?></span>
						<span class="gcc-service__toggle" aria-hidden="true"></span>
					</summary>
					<div class="gcc-service__body">
						<?php if ( $s['items'] ) : ?>
							<p class="gcc-service__label">What you get</p>
							<ul>
								<?php foreach ( $s['items'] as $it ) : ?><li><?php echo gcc_icon( 'check' ); // phpcs:ignore ?><?php echo esc_html( $it ); ?></li><?php endforeach; ?>
							</ul>
						<?php endif; ?>
						<?php if ( $s['for'] ) : ?>
							<p class="gcc-service__for"><b>Good for:</b> <?php echo esc_html( $s['for'] ); ?></p>
						<?php endif; ?>
						<a class="gcc-btn gcc-btn--line" href="#gcc-request" data-gcc-pick="<?php echo esc_attr( $s['title'] ); ?>">Ask about <?php echo esc_html( strtolower( $s['title'] ) ); ?></a>
					</div>
				</details>
			<?php endforeach; ?>
		</div>
		<div class="gcc-askbar">
			<p><b>Need something that isn’t listed?</b> Tell us what you need, and we’ll reply in writing <?php echo esc_html( gcc_get( 'response' ) ); ?>.</p>
			<button type="button" class="gcc-btn gcc-btn--gold" data-gcc-ask-open>Ask GCO</button>
		</div>
	</section>
	<?php endif; ?>

	<section class="gcc-sec gcc-own" id="gcc-own" aria-labelledby="gcc-own-title">
		<div class="gcc-own__head">
			<p class="gcc-own__kicker"><?php echo gcc_icon( 'lock' ); // phpcs:ignore ?> Own your tools</p>
			<h2 id="gcc-own-title" class="gcc-own__title">Still renting your website, one plugin at a time?</h2>
			<p class="gcc-own__lead">Many organizations pay the same theme and plugin renewals every year, for features they barely use, and lose them the day they stop paying. GCO builds what you need in your own name, and you keep it.</p>
		</div>

		<ul class="gcc-own__questions">
			<li>Are you paying yearly renewals for plugins you only use a small part of?</li>
			<li>Would your site break if one vendor raised prices, changed direction or shut down?</li>
			<li>Do you want themes and tools that carry your own name, not someone else’s?</li>
		</ul>

		<div class="gcc-own__compare" role="table" aria-label="Rented tools compared with tools you own">
			<div class="gcc-own__row gcc-own__row--head" role="row">
				<span role="columnheader">Renting from third parties</span>
				<span role="columnheader">Owning it, built by GCO</span>
			</div>
			<?php
			$rows = array(
				array( 'A license fee every year, for every plugin', 'One project price, with no yearly license fee to GCO' ),
				array( 'The vendor’s name, roadmap and price changes', 'Your organization’s name, built around how you work' ),
				array( 'Heavy plugins full of features you never use', 'Only what you need, so your site stays lighter and faster' ),
				array( 'Locked code you can’t change or take with you', 'Full source code and rights handed over to you' ),
				array( 'Help depends on the vendor’s support queue', 'Clear documentation, so any developer can maintain it' ),
			);
			foreach ( $rows as $r ) :
				?>
				<div class="gcc-own__row" role="row">
					<span role="cell" class="gcc-own__rent"><?php echo gcc_icon( 'undo' ); // phpcs:ignore ?><?php echo esc_html( $r[0] ); ?></span>
					<span role="cell" class="gcc-own__mine"><?php echo gcc_icon( 'check' ); // phpcs:ignore ?><?php echo esc_html( $r[1] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>

		<ol class="gcc-own__steps">
			<li><b>Tell us what you depend on.</b> List the paid themes and plugins you renew today, or just describe what your site needs to do.</li>
			<li><b>Get an honest map.</b> We show what can be replaced, what can be combined, and what is better to keep.</li>
			<li><b>We build it in your name.</b> Branded for your organization, planned with you in writing first.</li>
			<li><b>It’s yours.</b> Source code, rights and documentation are handed over when the project is paid.</li>
		</ol>

		<p class="gcc-own__honest"><b>Our honest rule:</b> some tools are worth keeping, like WooCommerce itself or your payment gateway, and we’ll say so. Code you own still needs normal WordPress updates over time, which our Care plans cover. Payment and hosting fees from other providers stay as they are.</p>

		<div class="gcc-own__cta">
			<p>What are you still renting today?<small>Consultation is free. Ask us anything, with no obligation.</small></p>
			<div class="gcc-own__cta-actions">
				<button type="button" class="gcc-btn gcc-btn--gold" data-gcc-ask-open data-gcc-ask-topic="Replace paid plugins with our own">Tell us what you depend on</button>
				<a class="gcc-btn gcc-btn--line" href="#gcc-work">See what GCO has built</a>
			</div>
		</div>
	</section>

	<section class="gcc-sec gcc-trust" aria-labelledby="gcc-trust-title">
		<h2 id="gcc-trust-title" class="gcc-h2">Rules we work by</h2>
		<div class="gcc-trust__grid">
			<div><?php echo gcc_icon( 'user' ); // phpcs:ignore ?><h3>One person, your own login</h3><p>No agency hand-offs. I use only the admin login you create, and no one else touches your site.</p></div>
			<div><?php echo gcc_icon( 'lock' ); // phpcs:ignore ?><h3>No payment access needed</h3><p>I never need your Stripe or PayPal account. Your customers’ data stays yours.</p></div>
			<div><?php echo gcc_icon( 'shield' ); // phpcs:ignore ?><h3>Nothing deleted, ever</h3><p>No plugin, page or user is removed without your written approval.</p></div>
			<div><?php echo gcc_icon( 'doc' ); // phpcs:ignore ?><h3>Everything in writing</h3><p>Async, written updates you can keep, forward to your board, or check later.</p></div>
		</div>
	</section>

	<section class="gcc-sec gcc-faq" aria-labelledby="gcc-faq-title">
		<h2 id="gcc-faq-title" class="gcc-h2">Questions</h2>
		<div class="gcc-faq__list">
			<?php foreach ( gcc_faq() as $q ) : ?>
				<details>
					<summary><?php echo esc_html( $q[0] ); ?></summary>
					<p><?php echo esc_html( $q[1] ); ?></p>
				</details>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="gcc-request" id="gcc-request" aria-labelledby="gcc-request-title">
		<div class="gcc-request__intro">
			<h2 id="gcc-request-title">Request a free site check</h2>
			<p>Tell me about your site. I’ll look at it from the outside and reply <?php echo esc_html( gcc_get( 'response' ) ); ?> with what needs attention and which plan fits. No login needed yet.</p>
			<ul>
				<li><?php echo gcc_icon( 'check' ); // phpcs:ignore ?>Free consultation on anything about your website</li>
				<li><?php echo gcc_icon( 'check' ); // phpcs:ignore ?>No obligation</li>
				<li><?php echo gcc_icon( 'check' ); // phpcs:ignore ?>No passwords in this form, ever</li>
				<li><?php echo gcc_icon( 'check' ); // phpcs:ignore ?>Written reply you can share with your team</li>
			</ul>
			<div class="gcc-contact">
				<p class="gcc-contact__label">Prefer to reach us directly?</p>
				<?php $cmail = gcc_get( 'email' ); if ( $cmail ) : ?>
					<a class="gcc-contact__item" href="mailto:<?php echo esc_attr( $cmail ); ?>?subject=<?php echo rawurlencode( 'Consultation request' ); ?>"><?php echo gcc_icon( 'doc' ); // phpcs:ignore ?><span><b>Email</b><?php echo esc_html( $cmail ); ?></span></a>
				<?php endif; ?>
				<?php if ( gcc_get( 'contact_url' ) ) : ?>
					<a class="gcc-contact__item" href="<?php echo esc_url( gcc_get( 'contact_url' ) ); ?>"><?php echo gcc_icon( 'user' ); // phpcs:ignore ?><span><b>Contact page</b><?php echo esc_html( preg_replace( '#^https?://(www\.)?#', '', untrailingslashit( gcc_get( 'contact_url' ) ) ) ); ?></span></a>
				<?php endif; ?>
			</div>
		</div>

		<form class="gcc-form" data-gcc-form="care" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php if ( 'sent' === $status ) : ?>
				<p class="gcc-alert gcc-alert--ok" role="status">Request sent. You’ll get a written reply <?php echo esc_html( gcc_get( 'response' ) ); ?>.</p>
			<?php elseif ( 'invalid' === $status ) : ?>
				<p class="gcc-alert gcc-alert--err" role="alert">Request not sent. Add your name, a valid email and your website address, then send again.</p>
			<?php elseif ( 'rate' === $status ) : ?>
				<p class="gcc-alert gcc-alert--err" role="alert">Request not sent. Too many requests from this connection; try again in 10 minutes.</p>
			<?php endif; ?>

			<input type="hidden" name="action" value="gcc_request">
			<input type="hidden" name="gcc_e" value="" data-gcc-e>
			<div class="gcc-hp" aria-hidden="true"><label>Leave this empty <input type="text" name="gcc_hp_x7" value="" tabindex="-1" autocomplete="one-time-code" data-lpignore="true" data-1p-ignore></label></div>

			<div class="gcc-form__row">
				<p class="gcc-field"><label for="gcc-name">Your name</label><input id="gcc-name" name="gcc_name" type="text" required autocomplete="name"></p>
				<p class="gcc-field"><label for="gcc-email">Email</label><input id="gcc-email" name="gcc_email" type="email" required autocomplete="email"></p>
			</div>
			<div class="gcc-form__row">
				<p class="gcc-field"><label for="gcc-org">Organization <small>optional</small></label><input id="gcc-org" name="gcc_org" type="text" autocomplete="organization"></p>
				<p class="gcc-field"><label for="gcc-site">Website address</label><input id="gcc-site" name="gcc_site" type="text" inputmode="url" placeholder="example.org" required></p>
			</div>

			<fieldset class="gcc-field gcc-checks">
				<legend>Your site uses</legend>
				<?php foreach ( array( 'Online store', 'Donations', 'Subscriptions or memberships', 'Events', 'Page builder' ) as $feat ) : ?>
					<label><input type="checkbox" name="gcc_features[]" value="<?php echo esc_attr( $feat ); ?>"><span><?php echo esc_html( $feat ); ?></span></label>
				<?php endforeach; ?>
			</fieldset>

			<div class="gcc-form__row">
				<p class="gcc-field">
					<label for="gcc-plugins">About how many plugins?</label>
					<select id="gcc-plugins" name="gcc_plugins">
						<option value="Not sure">Not sure</option>
						<option>Under 20</option>
						<option>20–50</option>
						<option>50–100</option>
						<option>100+</option>
					</select>
				</p>
				<p class="gcc-field">
					<label for="gcc-package">Interested in</label>
					<select id="gcc-package" name="gcc_package">
						<option value="Not sure yet">Not sure yet</option>
						<?php if ( $packages ) : ?>
							<optgroup label="Care plans">
								<?php foreach ( $packages as $p ) : ?><option value="<?php echo esc_attr( $p['name'] ); ?>"><?php echo esc_html( $p['name'] ); ?></option><?php endforeach; ?>
							</optgroup>
						<?php endif; ?>
						<?php if ( $services ) : ?>
							<optgroup label="Other services">
								<?php foreach ( $services as $s ) : ?><option value="<?php echo esc_attr( $s['title'] ); ?>"><?php echo esc_html( $s['title'] ); ?></option><?php endforeach; ?>
							</optgroup>
						<?php endif; ?>
					</select>
				</p>
			</div>
			<p class="gcc-field"><label for="gcc-host">Hosting company <small>optional</small></label><input id="gcc-host" name="gcc_host" type="text" placeholder="e.g. WP Engine, SiteGround, DreamHost"></p>
			<p class="gcc-field"><label for="gcc-message">What’s worrying you about your site, or what would you like built? <small>optional</small></label><textarea id="gcc-message" name="gcc_message" rows="4"></textarea></p>

			<button type="submit" class="gcc-btn gcc-btn--gold gcc-btn--lg">Send request</button>
		</form>
	</section>
</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'gco_care', 'gcc_render' );

/* Data terstruktur: Service + FAQPage, hanya di halaman yang memakai shortcode. */
add_action( 'wp_head', function () {
	$post = get_post();
	if ( ! is_singular() || ! $post || ! has_shortcode( $post->post_content, 'gco_care' ) ) {
		return;
	}
	$faq = array();
	foreach ( gcc_faq() as $q ) {
		$faq[] = array( '@type' => 'Question', 'name' => $q[0], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $q[1] ) );
	}
	$offers = array();
	foreach ( gcc_packages() as $p ) {
		if ( is_numeric( $p['price'] ) ) {
			$offers[] = array( '@type' => 'Offer', 'name' => $p['name'], 'price' => $p['price'], 'priceCurrency' => 'USD' );
		}
	}
	$data = array(
		array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Service',
			'name'        => 'WordPress Care & Security',
			'serviceType' => 'WordPress maintenance, backups and security',
			'provider'    => array( '@type' => 'Organization', 'name' => gcc_get( 'company' ), 'url' => home_url( '/' ) ),
			'areaServed'  => 'Worldwide',
			'offers'      => $offers,
		),
		array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faq ),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
} );
