/*
	* Pie Register: registration form tracker
	* Sends anonymous step events (view, start, field, error, page, submit).
	* It never reads or sends what a visitor types.
	* Since v4.0.0.0
*/
(function($){
	"use strict";

	if( typeof pieAnalytics === 'undefined' ){
		return;
	}

	var cfg			= pieAnalytics;
	// wp_localize_script() turns 0/1 into "0"/"1" strings, and "0" is truthy: read them explicitly
	function flag(v){ return v === true || v === 1 || v === '1'; }
	var queue		= {};	// form id => events
	var started		= {};	// form id => true
	var seenField	= {};	// "form|field" => true
	var timer		= null;
	var skipName	= /^(piereg_|form_id|page_no|_wp|g-recaptcha|h-captcha|pie_|honeypot|math|select_payment|error_stripe)/i;

	function randomKey(){
		var a = new Uint8Array(16), i;
		if( window.crypto && window.crypto.getRandomValues ){
			window.crypto.getRandomValues(a);
		}else{
			for( i = 0; i < 16; i++ ){ a[i] = Math.floor(Math.random() * 256); }
		}
		return Array.prototype.map.call(a, function(b){ return ('0' + b.toString(16)).slice(-2); }).join('');
	}

	function sessionKey(){
		try{
			var k = window.sessionStorage.getItem('pieregAnalyticsKey');
			if( k && /^[a-f0-9]{32}$/.test(k) ){ return k; }
			k = randomKey();
			window.sessionStorage.setItem('pieregAnalyticsKey', k);
			return k;
		}catch(e){
			window.pieregAnalyticsMem = window.pieregAnalyticsMem || randomKey();
			return window.pieregAnalyticsMem;
		}
	}

	function optedOut(){
		if( !flag(cfg.respectDnt) ){ return false; }
		return navigator.doNotTrack === '1' || window.doNotTrack === '1' || navigator.msDoNotTrack === '1' || navigator.globalPrivacyControl === true;
	}

	function consentGiven(){
		if( !flag(cfg.requireConsent) ){ return true; }
		if( window.pieregAnalyticsConsent === true ){ return true; }
		if( typeof window.wp_has_consent === 'function' ){
			try{ return !!window.wp_has_consent('statistics'); }catch(e){ return false; }
		}
		return false;
	}

	function pageOf($el){
		var m = ($el.closest('li.fields').attr('class') || '').match(/pageFields_(\d+)/);
		return m ? parseInt(m[1], 10) : 0;
	}

	function currentPage($form){
		var $vis = $form.find('li.fields:visible').first();
		return pageOf($vis);
	}

	function push(formId, e, f, p){
		if( optedOut() ){ return; }
		queue[formId] = queue[formId] || [];
		if( queue[formId].length >= 50 ){ return; }
		var ev = { e: e };
		if( f ){ ev.f = f; }
		if( p ){ ev.p = p; }
		queue[formId].push(ev);
		if( !timer ){ timer = window.setTimeout(flush, 10000); }
	}

	function flush(){
		window.clearTimeout(timer);
		timer = null;
		if( !consentGiven() || optedOut() ){ return; }
		var id, data, body;
		for( id in queue ){
			if( !queue.hasOwnProperty(id) || !queue[id].length ){ continue; }
			body = new FormData();
			body.append('action', 'piereg_track_events');
			body.append('nonce', cfg.nonce);
			body.append('form_id', id);
			body.append('session_key', sessionKey());
			body.append('events', JSON.stringify(queue[id]));
			body.append('utm', JSON.stringify(utmTags()));
			queue[id] = [];
			if( navigator.sendBeacon ){
				navigator.sendBeacon(cfg.ajaxURL, body);
			}else{
				$.ajax({ url: cfg.ajaxURL, type: 'POST', data: body, processData: false, contentType: false });
			}
		}
	}

	/* Campaign tags from the address (utm_source, utm_medium, utm_campaign); remembered for this tab */
	function utmTags(){
		var keep = null;
		try{ keep = JSON.parse(window.sessionStorage.getItem('pieregUtm') || 'null'); }catch(e){}
		if( keep ){ return keep; }
		var q = {}, m, rx = /[?&](utm_source|utm_medium|utm_campaign)=([^&#]*)/g, s = window.location.search || '';
		while( (m = rx.exec(s)) ){ try{ q[m[1].replace('utm_', '')] = decodeURIComponent(m[2].replace(/\+/g, ' ')).slice(0, 80); }catch(e){} }
		if( q.source || q.medium || q.campaign ){
			try{ window.sessionStorage.setItem('pieregUtm', JSON.stringify(q)); }catch(e){}
		}
		return q;
	}

	function formId($form){
		return parseInt($form.attr('data-form'), 10) || 0;
	}

	function cleanName(name){
		return String(name || '').replace(/\[.*$/, '');
	}

	/*
		* Pie Register marks an invalid field by adding "error" to its div.fieldset (piereg_validation.js),
		* not to the input. Report each invalid field once per check.
	*/
	function recordErrors($form, id){
		var seen = {};
		$form.find('.fieldset.error, :input.error').each(function(){
			var $box = $(this), $in = $box.is(':input') ? $box : $box.find(':input').filter(function(){
				var n = cleanName(this.name), t = (this.type || '').toLowerCase();
				return n && !skipName.test(n) && t !== 'hidden' && t !== 'submit' && t !== 'button';
			}).first();
			var name = cleanName($in.attr('name'));
			if( !name && $box.find('.prPass2').length ){ name = 'password'; $in = $box.find('.prPass2').first(); }
			if( !name || seen[name] || skipName.test(name) ){ return; }
			seen[name] = true;
			push(id, 'error', name, pageOf($in));
		});
	}

	function allowed(){
		return consentGiven() && !optedOut();
	}

	/* The hidden visit key ties a registration to its visit. Only fill it when this visit may be tracked. */
	function stampKey($form){
		$form.find('input[name="piereg_ask"]').val(allowed() ? sessionKey() : '');
	}

	function markStart(id){
		if( !started[id] ){
			started[id] = true;
			push(id, 'start');
		}
	}

	$(function(){
		var $forms = $('form.pie_register_reg_form[data-form]');
		if( !$forms.length ){ return; }

		$forms.each(function(){
			var $form = $(this), id = formId($form);
			if( !id ){ return; }
			stampKey($form);
			push(id, 'view');
		});

		// First touch of each field
		$forms.on('focusin change', ':input', function(){
			var $in = $(this), $form = $in.closest('form'), id = formId($form), name = cleanName($in.attr('name')), type = ($in.attr('type') || '').toLowerCase();
			if( !id || !name || skipName.test(name) || type === 'hidden' || type === 'submit' || type === 'button' ){ return; }
			markStart(id);
			var key = id + '|' + name;
			if( !seenField[key] ){
				seenField[key] = true;
				push(id, 'field', name, pageOf($in));
			}
		});

		// Page changes in multi-step forms
		$forms.on('click', '.pie_next, .pie_prev', function(){
			var $form = $(this).closest('form'), id = formId($form);
			window.setTimeout(function(){
				push(id, 'page', '', currentPage($form));
				// Next with invalid fields shows errors on the page that stays visible
				recordErrors($form, id);
			}, 250);
		});
	});

	/*
		* Submit. Listen in the capture phase: the form's own validation handler stops the event with
		* "return false", so a bubbling listener never saw failed submits. Decide once those handlers have
		* run: prevented means validation failed (or a captcha is still pending).
	*/
	document.addEventListener('submit', function(ev){
		var $form = $(ev.target);
		if( !$form.is('form.pie_register_reg_form[data-form]') ){ return; }
		var id = formId($form);
		if( !id ){ return; }
		stampKey($form);
		markStart(id);
		window.setTimeout(function(){
			if( ev.defaultPrevented ){
				recordErrors($form, id);
			}else{
				push(id, 'submit');
			}
			flush();
		}, 0);
	}, true);

	// The invisible captcha submits with form.submit(), which fires no submit event
	$(document).on('piereg_before_native_submit', 'form.pie_register_reg_form[data-form]', function(){
		var id = formId($(this));
		if( id ){
			push(id, 'submit');
			flush();
		}
	});

	window.addEventListener('pagehide', flush);
	document.addEventListener('visibilitychange', function(){
		if( document.visibilityState === 'hidden' ){ flush(); }
	});
	window.addEventListener('piereg_analytics_consent', function(){
		window.pieregAnalyticsConsent = true;
		$('form.pie_register_reg_form[data-form]').each(function(){ stampKey($(this)); });
		flush();
	});
})(jQuery);
