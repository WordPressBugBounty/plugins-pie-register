/*
	* Pie Register > Analytics admin screen
	* Since v4.0.0.0
*/
(function($){
	"use strict";

	if( typeof pieAnalyticsAdmin === 'undefined' ){
		return;
	}
	var cfg = pieAnalyticsAdmin, i18n = cfg.i18n;

	function pad(n){ return (n < 10 ? '0' : '') + n; }
	function ymd(d){ return d.getUTCFullYear() + '-' + pad(d.getUTCMonth() + 1) + '-' + pad(d.getUTCDate()); }
	function num(n){ return Number(n || 0).toLocaleString(); }

	function range(){
		var v = $('#piereg_an_range').val(), to = new Date(cfg.today + 'T00:00:00Z'), from;
		if( v === 'custom' ){
			return { from: $('#piereg_an_from').val() || cfg.today, to: $('#piereg_an_to').val() || cfg.today };
		}
		from = new Date(to.getTime() - (parseInt(v, 10) - 1) * 86400000);
		return { from: ymd(from), to: ymd(to) };
	}

	function message(text, isError){
		$('#piereg_an_msg').text(text).toggleClass('error', !!isError).show();
		window.setTimeout(function(){ $('#piereg_an_msg').fadeOut(); }, 3500);
	}

	// Placeholder tiles shaped like the KPI cards while the numbers load
	function kpiSkeleton(){
		var $wrap = $('<div/>').append($('<span class="screen-reader-text"/>').text(i18n.loading));
		for( var i = 0; i < 6; i++ ){
			$wrap.append('<div class="piereg-an-kpi an-skel" aria-hidden="true"><span></span><b></b></div>');
		}
		return $wrap.children();
	}

	function rate(v){
		return ( v === null || typeof v === 'undefined' || !isFinite(v) ) ? '\u2014' : v + '%';
	}

	function drawKpis(t, conversion, accounts){
		accounts = accounts || { registered: t.registered, activated: t.activated };
		var items = [
			[i18n.views, t.view], [i18n.started, t.start], [i18n.submitted, t.submit],
			[i18n.registered, accounts.registered, t.registered], [i18n.activated, accounts.activated, t.activated]
		], $k = $('#piereg_an_kpis').empty();
		$.each(items, function(_, it){
			var $tile = $('<div class="piereg-an-kpi"/>').append($('<span/>').text(it[0])).append($('<b/>').text(num(it[1])));
			// Accounts include visits the tracker skipped; say how many are in the funnel
			if( it.length > 2 && it[2] !== it[1] ){
				$('<small class="an-kpi-note"/>').text(i18n.fromTracked.replace('%s', num(it[2]))).appendTo($tile);
			}
			$tile.appendTo($k);
		});
		// No tracked views means there is nothing to divide by: show a dash, not 0%
		$('<div class="piereg-an-kpi piereg-an-conv"/>').append($('<span/>').text(i18n.conversion)).append($('<b/>').text(rate(conversion))).appendTo($k);
	}

	function drawFunnel(t){
		var steps = [[i18n.views, t.view], [i18n.started, t.start], [i18n.submitted, t.submit], [i18n.registered, t.registered], [i18n.activated, t.activated]],
			top = t.view || 0, $f = $('#piereg_an_funnel').empty();
		if( !top ){ $f.append($('<p class="piereg-an-empty"/>').text(i18n.noData)); return; }
		$.each(steps, function(idx, s){
			var pct = Math.min(100, Math.round((s[1] / top) * 1000) / 10);
			var $row = $('<div class="piereg-an-frow"/>').attr('title', s[0] + ': ' + num(s[1]));
			$('<span class="n"/>').text(s[0]).appendTo($row);
			$('<div class="an-track"/>').append($('<div class="b"/>').addClass('s' + idx).css('width', Math.max(pct, 1) + '%')).appendTo($row);
			$('<span class="v"/>').text(pct + '%').appendTo($row);
			$f.append($row);
		});
	}

	var lastTrend = null, resizeTimer;
	function drawTrend(trend){
		lastTrend = trend;
		var $c = $('#piereg_an_trend').empty(), w = Math.max(320, Math.round($c.width()) || 520), h = 190, pl = 34, pb = 22, max = 1, n = trend.length;
		$.each(trend, function(_, d){ max = Math.max(max, d.views, d.registered); });
		if( !n ){ $c.append($('<p class="piereg-an-empty"/>').text(i18n.noData)); return; }
		var ns = 'http://www.w3.org/2000/svg', svg = document.createElementNS(ns, 'svg');
		svg.setAttribute('viewBox', '0 0 ' + w + ' ' + h);
		svg.setAttribute('class', 'piereg-an-svg');
		function y(v){ return (h - pb) - (v / max) * (h - pb - 10); }
		function x(i){ return pl + (n === 1 ? (w - pl) / 2 : i * ((w - pl - 6) / (n - 1))); }
		function line(key, cls){
			var pts = [];
			$.each(trend, function(i, d){ pts.push(x(i).toFixed(1) + ',' + y(d[key]).toFixed(1)); });
			var p = document.createElementNS(ns, 'polyline');
			p.setAttribute('points', pts.join(' ')); p.setAttribute('class', cls); svg.appendChild(p);
		}
		$.each([0, 0.5, 1], function(_, g){
			var gy = y(max * g), l = document.createElementNS(ns, 'line'), t = document.createElementNS(ns, 'text');
			l.setAttribute('x1', pl); l.setAttribute('x2', w); l.setAttribute('y1', gy); l.setAttribute('y2', gy); l.setAttribute('class', 'grid');
			t.setAttribute('x', 0); t.setAttribute('y', gy + 4); t.setAttribute('class', 'tick'); t.textContent = Math.round(max * g);
			svg.appendChild(l); svg.appendChild(t);
		});
		line('views', 'lv'); line('registered', 'lr');
		var a = document.createElementNS(ns, 'text'), b = document.createElementNS(ns, 'text');
		a.setAttribute('x', pl); a.setAttribute('y', h - 4); a.setAttribute('class', 'tick'); a.textContent = trend[0].day;
		b.setAttribute('x', w); b.setAttribute('y', h - 4); b.setAttribute('class', 'tick'); b.setAttribute('text-anchor', 'end'); b.textContent = trend[n - 1].day;
		svg.appendChild(a); svg.appendChild(b);
		$c.append(svg);
	}

	function drawFields(fields, formId){
		var $b = $('#piereg_an_fields tbody').empty();
		if( !formId ){ $b.append($('<tr/>').append($('<td colspan="4" class="an-empty-row"/>').text(i18n.pickForm))); return; }
		if( !fields.length ){ $b.append($('<tr/>').append($('<td colspan="4" class="an-empty-row"/>').text(i18n.noData))); return; }
		// Rows stay in form order (as sent by the server); the field most visitors left at is highlighted
		var worst = 0;
		$.each(fields, function(_, f){ if( f.abandoned > worst ){ worst = f.abandoned; } });
		$.each(fields, function(_, f){
			var $tr = $('<tr/>');
			if( worst > 0 && f.abandoned === worst ){ $tr.addClass('worst'); }
			$('<td/>').text(f.label).appendTo($tr);
			$('<td/>').text(num(f.interacted)).appendTo($tr);
			$('<td/>').text(num(f.abandoned)).appendTo($tr);
			$('<td/>').text(num(f.errors)).appendTo($tr);
			$b.append($tr);
		});
	}

	function drawSources(rows){
		var $b = $('#piereg_an_sources tbody').empty();
		if( !rows || !rows.length ){ $b.append($('<tr/>').append($('<td colspan="5" class="an-empty-row"/>').text(i18n.noSources))); return; }
		var best = 0;
		$.each(rows, function(_, r){ var c = r.views ? Math.min(1, r.registered / r.views) : 0; if( c > best ){ best = c; } });
		$.each(rows, function(_, r){
			// A row without views (or untracked accounts) has no rate: show a dash instead of 0%
			var c = r.views ? Math.min(100, Math.round(r.registered / r.views * 1000) / 10) : null;
			var $tr = $('<tr/>');
			var $src = $('<td/>').text(r.source).appendTo($tr);
			if( r.untracked ){ $tr.addClass('an-untracked'); $src.attr('title', i18n.notTracked); }
			$('<td/>').text(r.campaign || '\u2014').appendTo($tr);
			$('<td/>').text(r.views === null || typeof r.views === 'undefined' ? '\u2014' : num(r.views)).appendTo($tr);
			$('<td/>').text(num(r.registered)).appendTo($tr);
			$('<td/>').append($('<span class="an-rate"/>').toggleClass('hi', best > 0 && c !== null && Math.min(1, r.registered / r.views) === best).text(rate(c))).appendTo($tr);
			$b.append($tr);
		});
	}

	function load(){
		var r = range(), formId = parseInt($('#piereg_an_form').val(), 10) || 0;
		$('#piereg_an_apply').prop('disabled', true);
		$('.piereg-an').addClass('is-loading').attr('aria-busy', 'true');
		$('#piereg_an_kpis').html(kpiSkeleton());
		$.post(cfg.ajaxURL, { action: 'piereg_analytics_data', nonce: cfg.nonce, form_id: formId, from: r.from, to: r.to }).done(function(res){
			if( !res || !res.success ){ message(i18n.error, true); return; }
			var d = res.data;
			drawKpis(d.totals, d.conversion, d.accounts);
			drawFunnel(d.totals);
			drawTrend(d.trend);
			drawFields(d.fields, formId);
			drawSources(d.sources);
		}).fail(function(){ $('#piereg_an_kpis').empty(); message(i18n.error, true); }).always(function(){ $('.piereg-an').removeClass('is-loading').removeAttr('aria-busy'); $('#piereg_an_apply').prop('disabled', false); });
	}

	$(function(){
		var $form = $('#piereg_an_form');
		$.each(cfg.forms, function(_, f){ $('<option/>').val(f.id).text(f.title).appendTo($form); });
		var qsForm = parseInt(new URLSearchParams(window.location.search).get('form_id'), 10);
		if( qsForm && $form.find('option[value="' + qsForm + '"]').length ){ $form.val(String(qsForm)); }

		$('#piereg_an_range').on('change', function(){
			$('.piereg-an-custom').toggle($(this).val() === 'custom');
		});
		$('#piereg_an_apply').on('click', load);

		$('#piereg_an_export').on('click', function(e){
			e.preventDefault();
			var r = range();
			window.location.href = cfg.exportURL + '&form_id=' + (parseInt($('#piereg_an_form').val(), 10) || 0) + '&from=' + encodeURIComponent(r.from) + '&to=' + encodeURIComponent(r.to);
		});

		$('#piereg_an_settings').on('submit', function(e){
			e.preventDefault();
			var data = $(this).serializeArray();
			data.push({ name: 'action', value: 'piereg_analytics_save' }, { name: 'nonce', value: cfg.nonce });
			$.post(cfg.ajaxURL, data).done(function(res){
				message(res && res.success ? i18n.saved : i18n.error, !(res && res.success));
			}).fail(function(){ message(i18n.error, true); });
		});

		$('#piereg_an_purge').on('click', function(){
			if( !window.confirm(i18n.confirm) ){ return; }
			$.post(cfg.ajaxURL, { action: 'piereg_analytics_purge', nonce: cfg.nonce }).done(function(res){
				if( res && res.success ){ message(i18n.purged); load(); }else{ message(i18n.error, true); }
			}).fail(function(){ message(i18n.error, true); });
		});

		$(window).on('resize', function(){
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(function(){ if( lastTrend ){ drawTrend(lastTrend); } }, 150);
		});

		load();
	});
})(jQuery);
