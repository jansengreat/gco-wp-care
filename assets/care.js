(function () {
	'use strict';

	/* ---------- Formulir: waktu mulai & simpan isian sementara ---------- */
	var KEY = 'gcc_form_draft';
	var loadedAt = Date.now();

	function saveDraft(form) {
		var data = {};
		Array.prototype.forEach.call(form.elements, function (el) {
			if (!el.name || el.type === 'hidden' || el.name === 'gcc_hp_x7') return;
			if (el.type === 'checkbox' || el.type === 'radio') {
				if (el.checked) { (data[el.name] = data[el.name] || []).push(el.value); }
			} else {
				data[el.name] = el.value;
			}
		});
		try { sessionStorage.setItem(KEY, JSON.stringify({ kind: form.getAttribute('data-gcc-form'), data: data })); } catch (e) {}
	}
	function restoreDraft(kind) {
		var raw = null;
		try { raw = sessionStorage.getItem(KEY); } catch (e) {}
		if (!raw) return;
		var d; try { d = JSON.parse(raw); } catch (e) { return; }
		if (!d || d.kind !== kind) return;
		var form = document.querySelector('[data-gcc-form="' + kind + '"]');
		if (!form) return;
		Array.prototype.forEach.call(form.elements, function (el) {
			if (!el.name || !(el.name in d.data)) return;
			var v = d.data[el.name];
			if (el.type === 'checkbox' || el.type === 'radio') { el.checked = v.indexOf(el.value) !== -1; }
			else if (el.type !== 'hidden') { el.value = v; }
		});
	}
	function clearDraft() { try { sessionStorage.removeItem(KEY); } catch (e) {} }

	Array.prototype.forEach.call(document.querySelectorAll('[data-gcc-form]'), function (form) {
		form.addEventListener('submit', function () {
			var e = form.querySelector('[data-gcc-e]');
			if (e) e.value = Math.round((Date.now() - loadedAt) / 1000);
			// Kolom jebakan selalu dikosongkan sebelum kirim, jika sempat terisi autofill browser.
			var hp = form.querySelector('[name="gcc_hp_x7"]');
			if (hp && document.activeElement !== hp) hp.value = '';
			saveDraft(form);
		});
	});
	var q = window.location.search;
	var askStatus = (q.match(/[?&]gcc_ask=([a-z]+)/) || [])[1];
	var careStatus = (q.match(/[?&]gcc=([a-z]+)/) || [])[1];
	if (askStatus === 'sent' || careStatus === 'sent') clearDraft();
	if (askStatus && askStatus !== 'sent') restoreDraft('ask');
	if (careStatus && careStatus !== 'sent') restoreDraft('care');

	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* ---------- Halaman layanan ---------- */
	var root = document.querySelector('.gcc');
	if (root) {
		// Satu momen gerak: baris log di hero muncul satu per satu, sekali saja.
		var log = root.querySelector('[data-gcc-log]');
		if (log && !reduce) {
			root.classList.add('gcc-js');
			var items = log.querySelectorAll('li');
			var start = function () {
				Array.prototype.forEach.call(items, function (li, i) {
					setTimeout(function () { li.classList.add('is-in'); }, 250 + i * 420);
				});
			};
			if ('IntersectionObserver' in window) {
				var io = new IntersectionObserver(function (entries) {
					if (entries[0].isIntersecting) { start(); io.disconnect(); }
				}, { threshold: 0.3 });
				io.observe(log);
			} else {
				start();
			}
		}

		// Tombol "Choose ..." / "Ask about ..." memilih isian di formulir.
		var select = root.querySelector('#gcc-package');
		Array.prototype.forEach.call(root.querySelectorAll('[data-gcc-pick]'), function (a) {
			a.addEventListener('click', function () {
				if (!select) return;
				var name = a.getAttribute('data-gcc-pick');
				for (var i = 0; i < select.options.length; i++) {
					if (select.options[i].value === name) { select.selectedIndex = i; break; }
				}
			});
		});
	}

	/* ---------- Ask GCO ---------- */
	var ask = document.querySelector('.gcc-ask');
	var dialog = document.getElementById('gcc-ask-dialog');
	if (!ask || !dialog) return;

	// Pindahkan banner ke tepat di atas footer tema, supaya menyatu dengan halaman.
	var band = ask.querySelector('[data-gcc-band]');
	if (band) {
		var footer = document.querySelector('body > footer, .site-footer, #colophon, footer.wp-block-template-part, .wp-site-blocks > footer, footer');
		if (footer && footer.parentNode && !footer.closest('.gcc') && !footer.closest('dialog')) {
			footer.parentNode.insertBefore(band, footer);
		}
	}

	// Banner bergantian: pilih satu pesan secara acak di setiap kunjungan.
	if (band) {
		var panels = band.querySelectorAll('[data-gcc-band-panel]');
		if (panels.length > 1) {
			var pick = Math.floor(Math.random() * panels.length);
			Array.prototype.forEach.call(panels, function (pn, i) { pn.hidden = (i !== pick); });
		}
	}

	var lastFocus = null;
	function open() {
		lastFocus = document.activeElement;
		if (typeof dialog.showModal === 'function') {
			if (!dialog.open) dialog.showModal();
		} else {
			dialog.setAttribute('open', '');
		}
		var first = dialog.querySelector('textarea');
		if (first && !ask.getAttribute('data-gcc-ask-status')) setTimeout(function () { first.focus(); }, 50);
	}
	function close() {
		if (typeof dialog.close === 'function' && dialog.open) dialog.close();
		else dialog.removeAttribute('open');
		if (lastFocus && lastFocus.focus) lastFocus.focus();
	}

	document.addEventListener('click', function (e) {
		var t = e.target;
		if (!t || !t.closest) return;
		var opener = t.closest('[data-gcc-ask-open]');
		if (opener) {
			e.preventDefault();
			var topic = opener.getAttribute('data-gcc-ask-topic');
			if (topic) {
				Array.prototype.forEach.call(dialog.querySelectorAll('input[name="gcc_package"]'), function (r) { r.checked = (r.value === topic); });
			}
			open();
		}
		else if (t.closest('[data-gcc-ask-close]')) { e.preventDefault(); close(); }
	});
	dialog.addEventListener('click', function (e) { if (e.target === dialog) close(); });

	// Setelah mengirim, buka lagi dialog untuk menampilkan hasilnya.
	if (ask.getAttribute('data-gcc-ask-status')) {
		open();
		if (window.history && history.replaceState) {
			var url = window.location.href.replace(/([?&])gcc_ask=[^&#]*&?/, '$1').replace(/[?&](#|$)/, '$1');
			history.replaceState(null, '', url);
		}
	}
})();
