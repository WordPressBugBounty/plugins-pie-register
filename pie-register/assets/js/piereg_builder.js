/*
	* Pie Register: new form builder screen, step 2
	* Add fields (click or drag), reorder (drag or arrows), duplicate, delete, move between steps, add/remove steps, undo, save.
	* Every field carries its own hidden field[ID][...] inputs, so the form posts exactly like the classic builder.
	* Since v4.0.0.0
*/
(function($){
	"use strict";

	if( typeof pieBuilder === 'undefined' ){
		return;
	}
	var t = pieBuilder.i18n;
	var cfg = {}, current = null, $curField = null, tab = 'basic', step = 0, dirty = false, history = [];

	/* ---------- helpers ---------- */
	function esc(s){ return $('<div/>').text(s == null ? '' : String(s)).html(); }
	function name(type){ return (cfg.names && cfg.names[type]) || type; }
	function inArr(v, a){ return $.inArray(v, a || []) > -1; }

	function hidden(nameAttr, value){
		var out = [];
		if( $.isArray(value) || $.isPlainObject(value) ){
			$.each(value, function(k, v){ out = out.concat(hidden(nameAttr + '[' + k + ']', v)); });
			return out;
		}
		return [$('<input type="hidden" />').attr('name', nameAttr).val(value == null ? '' : String(value))];
	}

	function defaults(type, id){
		/* Same defaults as the classic builder (read from its settings form on the server) */
		if( cfg.defaults && cfg.defaults[type] ){
			var d = JSON.parse(JSON.stringify(cfg.defaults[type]).split('NBID').join(String(id)));
			if( d.label === undefined ){ d.label = ''; }
			return d;
		}
		var f = { id: String(id), type: type, label: name(type), desc: '', placeholder: '', css: '', show_in_profile: '1' };
		if( type === 'pagebreak' ){ f = { id: String(id), type: type, meta: '0', next_button: 'text', next_button_text: 'Next', next_button_url: '', prev_button: 'text', prev_button_text: 'Previous', prev_button_url: '' }; }
		return f;
	}

	function inputsFor(id, data){
		var $box = $('<div class="nb-inputs"></div>');
		$.each(data, function(k, v){ $box.append(hidden('field[' + id + '][' + k + ']', v)); });
		return $box;
	}

	/* Same boxes as the front end: Address Line 2, State and Country follow "Hide Address 2", "Hide State" and "List Type" */
	function addressHtml(d){
		var on = function(v){ return v !== undefined && v !== '' && v !== '0'; };
		var box = function(s, cls){ return '<div class="nb-in' + (cls ? ' ' + cls : '') + '">' + esc(s) + '</div>'; };
		var type = d.address_type || '', city = [box(t.city)], zip = [box(t.zip)];
		if( !on(d.hide_state) ){
			if( type === 'International' ){ city.push(box(t.stateIntl)); }
			else if( type === 'United States' ){ city.push(box(d.us_default_state || t.state, 'nb-select')); }
			else if( type === 'Canada' ){ city.push(box(d.canada_default_state || t.province, 'nb-select')); }
		}
		if( type === 'International' ){ zip.push(box(d.default_country || t.country, 'nb-select')); }
		var rows = ['<div class="nb-row">' + city.join('') + '</div>', '<div class="nb-row">' + zip.join('') + '</div>'];
		if( d.address_order === 'zip_city' ){ rows.reverse(); }
		return box(t.street) + (on(d.hide_address2) ? '' : '<div class="nb-row">' + box(t.address2) + '</div>') + rows.join('');
	}

	function previewHtml(type, d){
		var opts = d.display || [], ph = d.placeholder || '';
		switch( type ){
			case 'textarea': case 'description': return '<div class="nb-in nb-area">' + esc(ph) + '</div>';
			case 'dropdown': case 'multiselect': case 'custom_role': return '<div class="nb-in nb-select">' + esc(opts[0] || t.select) + '</div>';
			case 'checkbox': case 'radio':
				return '<div class="nb-opts">' + $.map(opts.length ? opts : [t.option], function(o){ return '<span class="nb-opt nb-' + type + '">' + esc(o) + '</span>'; }).join('') + '</div>';
			case 'password':
				/* Each box shows its placeholder when one is set, as on the front end */
				var dots = '\u2022\u2022\u2022\u2022\u2022\u2022\u2022\u2022';
				return '<div class="nb-in">' + (ph ? esc(ph) : dots) + '</div>' + (d.hide_confirm_password !== undefined ? '' :
					'<label class="nb-label nb-label2">' + esc(d.label2 || t.confirmPass) + '</label><div class="nb-in">' + (d.placeholder2 ? esc(d.placeholder2) : dots) + '</div>');
			case 'email':
				return '<div class="nb-in">' + esc(ph) + '&nbsp;</div>' + (d.confirm_email ? '<label class="nb-label nb-label2">' + esc(d.label2 || t.confirmEmail) + '</label><div class="nb-in">' + esc(d.placeholder2 || '') + '&nbsp;</div>' : '');
			case 'name':
				/* As on the front end: Placeholder in the first name box; Label2 and Placeholder 2 on the last name box */
				var first = '<div class="nb-in">' + esc(ph) + '&nbsp;</div>';
				if( d.hide_last_name !== undefined ){ return first; }
				return '<div class="nb-row nb-name-row"><div class="nb-col">' + first + '</div><div class="nb-col">' +
					(d.label2 ? '<label class="nb-label nb-label2">' + esc(d.label2) + '</label>' : '') + '<div class="nb-in">' + esc(d.placeholder2 || '') + '&nbsp;</div></div></div>';
			case 'address': return addressHtml(d);
			case 'upload': case 'profile_pic': return '<div class="nb-in nb-file">' + esc(t.chooseFile) + '</div>';
			case 'date': return '<div class="nb-in">' + esc(t.dateText) + '</div>';
			case 'time': return '<div class="nb-in">' + esc(t.timeText) + '</div>';
			case 'terms': return '<span class="nb-opt nb-checkbox">' + esc(t.agree) + '</span>';
			case 'captcha': case 'hcaptcha': return '<div class="nb-in nb-captcha">' + esc(t.robot) + '</div>';
			case 'math_captcha': return '<div class="nb-math">3 + 4 = ?</div><div class="nb-in nb-math-answer">' + esc(ph) + '&nbsp;</div>';
			case 'hidden': case 'honeypot': return '<div class="nb-in nb-ghost">' + esc(t.hiddenText) + '</div>';
			case 'html': return '<div class="nb-html">' + esc(t.htmlText) + '</div>';
			case 'sectionbreak': return '<hr class="nb-hr" />';
			case 'list': return '<div class="nb-in nb-ghost">' + esc(t.listText) + '</div>';
			case 'pricing': return '<div class="nb-in nb-select">' + esc(t.select) + '</div>';
			default: return '<div class="nb-in">' + esc(ph) + '&nbsp;</div>';
		}
	}

	function newField(type){
		var id = ++cfg.maxId, d = defaults(type, id);
		var info = { type: name(type), label: d.label || name(type), required: t.no, placeholder: d.placeholder || '', logic: t.no, desc: '', rule: '', message: '', css: '', profile: t.yes, show: t.show, rules: [], isNew: true };
		var $f = $('<div class="nb-field nb-new" tabindex="0"></div>').attr({ 'data-type': type, 'data-key': String(id), 'data-info': JSON.stringify(info) });
		$f.append($('<label class="nb-label"></label>').text(d.label || name(type)));
		$f.append($('<div class="nb-pv"></div>').html(previewHtml(type, d)));
		$f.append(inputsFor(id, d));
		return $f;
	}

	/* ---------- state: steps, tiles, status ---------- */
	function pages(){ return $('#nb-pages > .nb-page'); }

	function presentTypes(){
		return $('#nb-pages .nb-field').map(function(){ return $(this).attr('data-type'); }).get();
	}

	function refresh(){
		var $tabs = $('#nb-steps').empty(), n = pages().length;
		if( step > n - 1 ){ step = n - 1; }
		pages().each(function(i){
			var count = $(this).children('.nb-field').length;
			$(this).attr('data-step', i).toggle(i === step);
			$(this).children('.nb-empty').toggle(count === 0);
			var $b = $('<button type="button" class="nb-step" role="tab"></button>').attr('data-step', i).toggleClass('on', i === step)
				.append(document.createTextNode(t.step + ' ' + (i + 1) + ' '))
				.append($('<small></small>').text('(' + count + ')'));
			if( i > 0 ){
				$b.append($('<span class="nb-step-cog dashicons dashicons-admin-generic" role="button" tabindex="0"></span>').attr('title', t.stepSettings));
			}
			if( i > 0 && count === 0 ){
				$b.append($('<span class="nb-step-x" role="button" tabindex="0"></span>').attr('title', t.removeStep).text('×'));
			}
			if( $curField && $curField.hasClass('nb-break') && $curField.closest('.nb-page')[0] === this ){ $b.addClass('nb-step-sel'); }
			$tabs.append($b);
		});
		$tabs.append($('<button type="button" class="nb-step nb-addstep"></button>').text('+ ' + t.step));

		/* Next / Previous texts come from the page break settings, as on the front end */
		var $cur = pages().eq(step), $nextBreak = pages().eq(step + 1).children('.nb-break'), $curBreak = $cur.children('.nb-break');
		var nd = $nextBreak.length ? fieldData($nextBreak) : {}, pd = $curBreak.length ? fieldData($curBreak) : {};
		$('.nb-next').text(nd.next_button === 'url' ? t.next + ' (' + t.imageButton + ')' : (nd.next_button_text || t.next));
		$('.nb-prev').text(pd.prev_button === 'url' ? t.previous + ' (' + t.imageButton + ')' : (pd.prev_button_text || t.previous));
		$('.nb-prev').toggle(step > 0);
		$('.nb-next').toggle(step < n - 1);
		$('.nb-submit').toggle(step === n - 1);

		var present = presentTypes();
		$('.nb-tile').each(function(){
			var type = $(this).attr('data-type'), why = '';
			if( cfg.unavail && cfg.unavail[type] ){ why = cfg.unavail[type]; }
			else if( !inArr(type, cfg.addable) ){ why = t.notYet; }
			else if( inArr(type, cfg.single) && inArr(type, present) ){ why = t.onlyOnce; }
			$(this).toggleClass('nb-off', why !== '').attr('title', why || t.addHint).attr('aria-disabled', why ? 'true' : 'false');
		});
		$('#nb-undo').prop('disabled', history.length === 0);
		updateProgress();
		initSortable();
	}

	function setDirty(){
		dirty = true;
		$('#nb-status').removeClass('nb-good nb-bad').text(t.unsaved);
	}

	/* Copy of the canvas for the undo history. The open settings editor is copied into its field, so the live editor is not moved (typing keeps focus). */
	function capture(){
		var $live = $('#nb-pages'), $ed = $('#nb-insp .nb-editor');
		syncAttrs($live);
		syncAttrs($ed);
		/* A palette tile being dropped is still on the canvas at this moment: never keep it in the undo history */
		var $copy = $live.clone();
		$copy.find('.nb-tile, .ui-sortable-placeholder').remove();
		/* A field being dragged carries temporary drag styles (absolute position, z-index, size); keep them out of the history too */
		$copy.children('.nb-page').children('.nb-field').removeAttr('style').removeClass('ui-sortable-helper');
		if( $ed.length && $curField ){
			var idx = $live.find('.nb-field, .nb-break').index($curField);
			if( idx > -1 ){ $copy.find('.nb-field, .nb-break').eq(idx).children('.nb-edpark').append($ed.clone()); }
		}
		return { html: $copy.html(), maxId: cfg.maxId, step: step };
	}

	function pushHistory(state){
		history.push(state);
		if( history.length > 40 ){ history.shift(); }
		$('#nb-undo').prop('disabled', false);
	}

	function snapshot(){
		parkEditor();
		pushHistory(capture());
	}

	function undo(){
		if( !history.length ){ return; }
		var s = history.pop();
		destroySortable();
		$('#nb-pages').html(s.html);
		$('#nb-pages .nb-field, #nb-pages .nb-break').removeData('nbEd');
		$('#nb-pages .nb-edpark').each(function(){ var $ed = $(this).children('.nb-editor'); var $f = $(this).closest('.nb-field, .nb-break'); if( $ed.length ){ $f.data('nbEd', $ed); } });
		cfg.maxId = s.maxId;
		step = s.step;
		clearSelection();
		setDirty();
		refresh();
	}

	function showStep(i){
		step = Math.max(0, Math.min(parseInt(i, 10) || 0, pages().length - 1));
		clearSelection();
		refresh();
	}

	/* ---------- dialog (replaces the browser's confirm/alert) ---------- */
	function dialog(opts){
		$('.nb-modal').remove();
		var $m = $('<div class="nb-modal" role="dialog" aria-modal="true"></div>');
		var $box = $('<div class="nb-modal-box"></div>').appendTo($m);
		$('<div class="nb-modal-head"></div>').text(opts.title || '').appendTo($box);
		$('<div class="nb-modal-body"></div>').text(opts.text || '').appendTo($box);
		var $foot = $('<div class="nb-modal-foot"></div>').appendTo($box);
		if( opts.cancel ){ $('<button type="button" class="nb-btn nb-ghostbtn nb-m-cancel"></button>').text(opts.cancel).appendTo($foot); }
		$('<button type="button" class="nb-btn nb-save nb-m-ok"></button>').text(opts.ok || t.ok).appendTo($foot);
		var last = document.activeElement;
		function close(){ $m.remove(); $(document).off('keydown.nbmodal'); if( last && last.focus ){ last.focus(); } }
		$m.on('click', '.nb-m-cancel', close);
		$m.on('click', function(e){ if( e.target === this ){ close(); } });
		$m.on('click', '.nb-m-ok', function(){ close(); if( opts.onOk ){ opts.onOk(); } });
		$(document).on('keydown.nbmodal', function(e){ if( e.key === 'Escape' ){ e.stopImmediatePropagation(); close(); } });
		$('body').append($m);
		$m.find(opts.cancel ? '.nb-m-cancel' : '.nb-m-ok').trigger('focus');
	}

	/* ---------- editing actions ---------- */
	function addField(type, $at){
		if( type === 'pagebreak' ){ addPageBreak(); return; }
		snapshot();
		var $f = newField(type), $page = pages().eq(step);
		if( $at && $at.length ){ $at.replaceWith($f); } else { $page.children('.nb-empty').before($f); }
		setDirty();
		refresh();
		select($f);
		$f[0].scrollIntoView({ block: 'nearest' });
	}

	function newStepPage(){
		var id = ++cfg.maxId;
		return $('<div class="nb-page"></div>')
			.append($('<div class="nb-break" data-type="pagebreak"></div>').attr('data-key', String(id)).append(inputsFor(id, defaults('pagebreak', id))))
			.append($('<div class="nb-empty"></div>').text(t.dropHere));
	}

	function addStep(){
		snapshot();
		var $p = newStepPage();
		$('#nb-pages').append($p);
		step = pages().length - 1;
		setDirty();
		clearSelection();
		refresh();
	}

	/* Page break inside a step, like the classic builder: $first and every field after it move to a new step placed right after this one */
	function splitStep($page, $first){
		snapshot();
		var $p = newStepPage();
		if( $first && $first.length ){
			$p.children('.nb-empty').before($first.nextAll('.nb-field').addBack());
		}
		$page.after($p);
		step = pages().index($p);
		setDirty();
		clearSelection();
		refresh();
	}

	/* Page Break tile clicked: split after the selected field, otherwise add an empty step at the end */
	function addPageBreak(){
		if( $curField && $curField.hasClass('nb-field') && $curField.closest('#nb-pages').length ){
			splitStep($curField.closest('.nb-page'), $curField.nextAll('.nb-field').first());
			return;
		}
		addStep();
	}

	function removeStep(i){
		var $p = pages().eq(i);
		if( i < 1 || $p.children('.nb-field').length ){ return; }
		snapshot();
		$p.remove();
		if( step >= i ){ step = Math.max(0, step - 1); }
		setDirty();
		refresh();
	}

	function duplicate($f){
		var type = $f.attr('data-type');
		if( !inArr(type, cfg.addable) || inArr(type, cfg.single) ){ return; }
		snapshot();
		var oldId = $f.attr('data-key'), id = ++cfg.maxId, $c = $f.clone();
		$c.find('[name]').each(function(){ var n = $(this).attr('name') || ''; $(this).attr('name', n.replace('field[' + oldId + ']', 'field[' + id + ']')); });
		$c.find('[id]').each(function(){ var i = $(this).attr('id') || ''; $(this).attr('id', i.split('_' + oldId).join('_' + id)); });
		$c.removeData('nbEd');
		$c.removeClass('sel').attr('data-key', String(id)).find('.nb-tools').remove();
		$c.find('input[type="hidden"]').each(function(){
			var n = $(this).attr('name') || '';
			$(this).attr('name', n.replace('field[' + oldId + ']', 'field[' + id + ']'));
			if( $(this).attr('name') === 'field[' + id + '][id]' ){ $(this).val(String(id)); }
		});
		$f.after($c);
		setDirty();
		refresh();
		select($c);
	}

	function remove($f){
		if( inArr($f.attr('data-type'), cfg.locked) ){ dialog({ title: t.cannotRemove, text: t.locked, ok: t.ok }); return; }
		var label = $.trim($f.children('.nb-label').first().text().replace('*', ''));
		dialog({ title: t.removeTitle + (label ? ': ' + label : ''), text: t.confirmDelete, ok: t.remove, cancel: t.cancel, onOk: function(){
			snapshot();
			$f.remove();
			clearSelection();
			setDirty();
			refresh();
		} });
	}

	function move($f, dir){
		var $sib = dir < 0 ? $f.prevAll('.nb-field').first() : $f.nextAll('.nb-field').first();
		if( !$sib.length ){ return; }
		snapshot();
		if( dir < 0 ){ $sib.before($f); } else { $sib.after($f); }
		setDirty();
		refresh();
		select($f);
	}

	function moveToStep($f, i){
		i = parseInt(i, 10);
		if( isNaN(i) || i === step ){ return; }
		snapshot();
		pages().eq(i).children('.nb-empty').before($f);
		step = i;
		setDirty();
		refresh();
		select($f);
	}

	/* ---------- drag and drop ---------- */
	function destroySortable(){
		pages().each(function(){ if( $(this).data('ui-sortable') ){ $(this).sortable('destroy'); } });
	}

	function initSortable(){
		if( !$.fn.sortable ){ return; }
		pages().each(function(){
			if( $(this).data('ui-sortable') ){ return; }
			$(this).sortable({
				items: '> .nb-field',
				placeholder: 'nb-placeholder',
				tolerance: 'pointer',
				cancel: '.nb-tools, .nb-tools *',
				start: function(){ snapshot(); },
				receive: function(e, ui){ /* handled in update */ },
				update: function(e, ui){
					var $tiles = $(this).children('.nb-tile');
					if( $tiles.length ){
						/* Only the tile just dropped becomes a field; anything else left behind is removed */
						var $tile = ui && ui.item && ui.item.is('.nb-tile') ? ui.item : $tiles.last();
						$tiles.not($tile).remove();
						var type = $tile.attr('data-type');
						history.pop(); // addField takes its own snapshot
						if( type === 'pagebreak' ){
							/* Split the step where the break was dropped */
							var $page = $tile.closest('.nb-page'), $first = $tile.nextAll('.nb-field').first();
							$tile.remove();
							splitStep($page, $first);
							return;
						}
						addField(type, $tile);
						return;
					}
					setDirty();
					refresh();
				}
			});
		});
		if( $.fn.draggable ){
			$('.nb-tile').each(function(){
				var $tile = $(this);
				if( $tile.data('ui-draggable') ){ $tile.draggable('destroy'); }
				if( $tile.hasClass('nb-off') ){ return; }
				$tile.draggable({ connectToSortable: '#nb-pages > .nb-page:visible', helper: 'clone', revert: 'invalid', appendTo: 'body', zIndex: 10000 });
			});
		}
	}

	/* ---------- selection and settings panel ---------- */
	function kv(label, value){
		return $('<div class="nb-kv"></div>').append($('<b></b>').text(label)).append($('<div></div>').text(value === '' ? '—' : value));
	}

	function toolsFor($f){
		var type = $f.attr('data-type');
		var $tools = $('<div class="nb-tools"></div>');
		$('<button type="button" data-a="up"></button>').attr('title', t.up).text('↑').appendTo($tools);
		$('<button type="button" data-a="down"></button>').attr('title', t.down).text('↓').appendTo($tools);
		if( inArr(type, cfg.addable) && !inArr(type, cfg.single) ){
			$('<button type="button" data-a="dup"></button>').attr('title', t.duplicate).text('⧉').appendTo($tools);
		}
		if( !inArr(type, cfg.locked) ){
			$('<button type="button" data-a="del"></button>').attr('title', t.remove).text('✕').appendTo($tools);
		}
		if( pages().length > 1 ){
			var $sel = $('<select data-a="step"></select>').attr('aria-label', t.moveTo).append($('<option value=""></option>').text(t.moveTo));
			pages().each(function(i){ if( i !== step ){ $sel.append($('<option></option>').val(i).text(t.step + ' ' + (i + 1))); } });
			$tools.append($sel);
		}
		return $tools;
	}

	/* ---------- settings editor (step 3) ----------
		The classic settings form of the field type is filled with the field's saved values and shown in the panel.
		Its inputs keep their field[ID][...] names, so saving posts them like the classic builder did. */
	var LOGIC_RX = /\[(conditional_logic|field_status|selected_field|field_rule_operator|conditional_value|conditional)\]/;
	var RULES_RX = /\[(required|validation_rule|validation_message|length|min|max|css|show_in_profile|show_in_bbpress|unique|restrict|regex|file_types|file_size|max_uploads|file_upload_field_message|show_meter|pass_[a-z_]+|min_strength|strength_message|password_generator|hide_confirm_password|confirm_email|is_search|date_format|date_type|startingDate|endingDate|calendar_icon[a-z_]*|allowed_codes|code_length)\]/;
	var OPERATORS = ['==', '!=', 'empty', 'not_empty', '>', '<', 'contains', 'not_contains', 'starts_with', 'not_starts_with', 'ends_with', 'not_ends_with', 'range'];

	/* Make attributes match live values, so html()/clone() keep what was typed */
	function syncAttrs($scope){
		$scope.find('input, textarea, select').each(function(){
			var $i = $(this), tp = ($i.attr('type') || '').toLowerCase();
			if( this.tagName === 'TEXTAREA' ){ $i.text($i.val()); }
			else if( this.tagName === 'SELECT' ){ $i.find('option').each(function(){ if( this.selected ){ $(this).attr('selected', 'selected'); } else { $(this).removeAttr('selected'); } }); }
			else if( tp === 'checkbox' || tp === 'radio' ){ if( this.checked ){ $i.attr('checked', 'checked'); } else { $i.removeAttr('checked'); } }
			else { $i.attr('value', $i.val()); }
		});
	}

	/* Read named inputs into a nested object, the way PHP reads a POST */
	function parseInputs($scope){
		var out = {};
		$scope.find('input[name], select[name], textarea[name]').each(function(){
			var $i = $(this), tp = ($i.attr('type') || '').toLowerCase(), vals = [];
			if( $i.prop('disabled') ){ return; }
			if( (tp === 'checkbox' || tp === 'radio') && !this.checked ){ return; }
			if( this.tagName === 'SELECT' ){ var v = $i.val(); vals = $.isArray(v) ? v : (v === null ? [] : [v]); } else { vals = [$i.val()]; }
			var parts = [], m, rx = /\[([^\]]*)\]/g, n = $i.attr('name');
			parts.push(n.split('[')[0]);
			while( (m = rx.exec(n)) ){ parts.push(m[1]); }
			$.each(vals, function(_, val){
				var o = out;
				for( var k = 0; k < parts.length; k++ ){
					var key = parts[k], last = k === parts.length - 1;
					if( key === '' ){ key = String(Object.keys(o).length); }
					if( last ){ o[key] = val; } else { if( typeof o[key] !== 'object' || o[key] === null ){ o[key] = {}; } o = o[key]; }
				}
			});
		});
		return out;
	}
	function toList(v){ if( v === undefined || v === null ){ return []; } if( $.isArray(v) ){ return v; } if( typeof v === 'object' ){ return $.map(Object.keys(v).sort(function(a, b){ return a - b; }), function(k){ return v[k]; }); } return [v]; }

	function fieldData($f){
		var key = $f.attr('data-key'), o = parseInputs($f), e = $f.data('nbEd');
		var d = (o.field && o.field[key]) ? o.field[key] : {};
		if( e ){ var x = parseInputs(e); if( x.field && x.field[key] ){ d = $.extend(true, d, x.field[key]); } }
		return d;
	}

	function otherFields($f){
		var list = [];
		$('#nb-pages .nb-field').each(function(){
			if( this === $f[0] ){ return; }
			list.push({ key: $(this).attr('data-key'), label: $.trim($(this).children('.nb-label').first().clone().children().remove().end().text()) || name($(this).attr('data-type')) });
		});
		return list;
	}

	/* Conditional rules: Show/Hide this field when (A and B) or (C) ... saved as conditional[group][rule][...]
		Buttons are handled by page-level listeners (see wiring), so they keep working after Undo restores the markup. */
	function fieldOptions($sel, key, value){
		var $f = $('#nb-pages .nb-field').filter(function(){ return $(this).attr('data-key') === key; });
		$sel.find('option').not(':first').remove();
		$.each(otherFields($f), function(_, f){ $sel.append($('<option></option>').val(f.key).text(f.label)); });
		$sel.val(value || '');
	}

	function ruleRow(key, r){
		var $r = $('<div class="nb-lrule"></div>');
		var $sf = $('<select class="nb-l-field"></select>').append($('<option value=""></option>').text(t.pickField));
		fieldOptions($sf, key, r.selected_field);
		var $op = $('<select class="nb-l-op"></select>');
		$.each(OPERATORS, function(_, o){ $op.append($('<option></option>').val(o).text(t['op_' + o] || o)); });
		$op.val(r.field_rule_operator || '==');
		var $val = $('<input type="text" class="nb-l-val" />').attr('placeholder', t.valueText).val(r.conditional_value || '');
		var $x = $('<button type="button" class="nb-l-x"></button>').attr('title', t.remove).text('\u00d7');
		return $r.append($sf, $op, $val, $x);
	}

	function groupBox(key, rules){
		var $g = $('<div class="nb-lgroup"></div>');
		$.each(rules.length ? rules : [{}], function(_, r){ $g.append(ruleRow(key, r)); });
		$g.append($('<button type="button" class="nb-l-and"></button>').text('+ ' + t.andText));
		return $g;
	}

	function renumberLogic($box){
		var key = $box.attr('data-key'), $groups = $box.find('.nb-lgroups');
		$groups.children('.nb-lgroup').each(function(g){
			$(this).children('.nb-lrule').each(function(r){
				var base = 'field[' + key + '][conditional][' + g + '][' + r + ']';
				$(this).find('.nb-l-field').attr('name', base + '[selected_field]');
				$(this).find('.nb-l-op').attr('name', base + '[field_rule_operator]');
				$(this).find('.nb-l-val').attr('name', base + '[conditional_value]');
			});
		});
		$groups.children('.nb-or').remove();
		$groups.children('.nb-lgroup').not(':first').before($('<div class="nb-or"></div>').text(t.orText));
	}

	function logicEditor($f, key, saved){
		var $box = $('<div class="advance_fields nb-logic pr-g pr-g-logic"></div>').attr('data-key', key);
		var on = String(saved.conditional_logic) === '1';
		var $on = $('<select class="nb-l-on"></select>').attr('name', 'field[' + key + '][conditional_logic]')
			.append($('<option value="0"></option>').text(t.logicOff)).append($('<option value="1"></option>').text(t.logicOn)).val(on ? '1' : '0');
		$box.append($('<label></label>').text(t.logic)).append($on);
		var $body = $('<div class="nb-logic-body"></div>').toggle(on).appendTo($box);
		var $status = $('<select></select>').attr('name', 'field[' + key + '][field_status]')
			.append($('<option value="1"></option>').text(t.showField)).append($('<option value="0"></option>').text(t.hideField)).val(String(saved.field_status) === '0' ? '0' : '1');
		$body.append($('<div class="nb-logic-head"></div>').append($status).append($('<span></span>').text(' ' + t.whenText)));
		var $groups = $('<div class="nb-lgroups"></div>').appendTo($body);
		var groups = toList(saved.conditional);
		if( !groups.length ){ groups = [[{}]]; }
		$.each(groups, function(_, g){ $groups.append(groupBox(key, toList(g))); });
		$body.append($('<button type="button" class="nb-l-or"></button>').text('+ ' + t.orGroup));
		$body.append($('<p class="nb-note"></p>').text(t.logicHelp));
		renumberLogic($box);
		return $box;
	}

	/* Option rows (drop down, radio, checkbox, multi select, pricing, custom role): repeat the classic row.
		A disabled copy of the empty row is kept inside the editor (never posted) to add new rows, also after Undo. */
	function reindexOptions($box){
		$box.children('.nb-orow').each(function(i){ $(this).find('input[name$="[selected][]"]').val(String(i)); });
	}

	function newOptionRow($box){
		var $r = $box.children('.nb-otpl').children().first().clone();
		$r.find(':input').prop('disabled', false);
		$box.children('.nb-otpl').before($r);
		return $r;
	}

	function optionRows($ed, key, saved){
		var $rows = $ed.find('.multi_options');
		if( !$rows.length ){ return; }
		var $tpl = $rows.first().clone(), $box = $('<div class="advance_fields nb-options pr-g pr-g-basic"></div>');
		$rows.first().before($box);
		$rows.remove();
		$tpl.find('.pie_add_options_icon').remove();
		var $row = $tpl.removeClass('advance_fields').addClass('nb-orow').append($('<button type="button" class="nb-o-x"></button>').attr('title', t.remove).text('\u00d7'));
		$row.find('input[type="text"]').val('');
		$row.find('input[type="radio"], input[type="checkbox"]').prop('checked', false).removeAttr('checked');
		var $holder = $('<div class="nb-otpl" hidden></div>').append($row.clone());
		$holder.find(':input').prop('disabled', true).attr('disabled', 'disabled');
		$box.append($holder);

		var display = toList(saved.display), count = Math.max(display.length, 1), selected = $.map(toList(saved.selected), String);
		for( var i = 0; i < count; i++ ){
			var $r = newOptionRow($box);
			$r.find('[name]').each(function(){
				var n = $(this).attr('name'), m = n.match(/\[([a-z_]+)\]\[\]$/i);
				if( !m ){ return; }
				var list = toList(saved[m[1]]), tp = ($(this).attr('type') || '').toLowerCase();
				if( m[1] === 'selected' ){ $(this).prop('checked', $.inArray(String(i), selected) > -1); return; }
				if( display.length && list[i] !== undefined && tp !== 'checkbox' && tp !== 'radio' ){ $(this).val(list[i]); }
			});
		}
		$box.after($('<div class="advance_fields pr-g pr-g-basic"></div>').append($('<button type="button" class="nb-o-add"></button>').text('+ ' + t.addOption)));
		reindexOptions($box);
	}

	function pathGet(obj, parts){
		var o = obj;
		for( var k = 0; k < parts.length; k++ ){
			if( o === undefined || o === null ){ return undefined; }
			if( parts[k] === '' ){ return toList(o); }
			o = o[parts[k]];
		}
		return o;
	}

	function buildEditor($f){
		var key = $f.attr('data-key'), type = $f.attr('data-type');
		var html = (cfg.meta || {})[type];
		if( !html ){ return null; }
		if( window.pie_pr_backend_dec_vars && pie_pr_backend_dec_vars.payment_gateways_list ){ html = html.split('%payment_gateways_list_box%').join(pie_pr_backend_dec_vars.payment_gateways_list); }
		html = html.split('%payment_gateways_list_box%').join('').split('%d%').join(key);
		var $ed = $('<div class="nb-editor"></div>').html(html);
		$ed.find('script').remove();
		$ed.find('*').each(function(){ var el = this; $.each($.makeArray(el.attributes), function(_, at){ if( /^on/i.test(at.name) ){ el.removeAttribute(at.name); } }); });
		$ed.find('.pie_add_options_icon, .add_conditional_value_fields, .fields_hint').remove();
		$ed.find('[name="field[' + key + '][type]"]').remove();

		var savedAll = parseInputs($f.children('.nb-inputs')), saved = (savedAll.field && savedAll.field[key]) ? savedAll.field[key] : {};

		/* Logic: replace the classic block (it needed the classic scripts) */
		var $logicEls = $ed.find('[name]').filter(function(){ return LOGIC_RX.test($(this).attr('name')); });
		var hasLogic = $logicEls.length > 0;
		$logicEls.closest('.advance_fields').remove();
		$ed.find('.enabel_conditional_logic_area, [id^="conditional_area_"]').remove();

		optionRows($ed, key, saved);

		/* Fill every other setting with the saved value */
		$ed.find('[name]').each(function(){
			var $i = $(this), n = $i.attr('name'), tp = ($i.attr('type') || '').toLowerCase();
			if( $i.closest('.nb-orow').length ){ return; }
			var parts = [], m, rx = /\[([^\]]*)\]/g;
			while( (m = rx.exec(n)) ){ parts.push(m[1]); }
			parts.shift();
			var has = saved.hasOwnProperty(parts[0]), v = pathGet(saved, parts);
			if( tp === 'checkbox' ){ var list = $.map(toList(v), String); $i.prop('checked', has && $.inArray(String($i.val()), list) > -1); return; }
			if( !has ){ return; }
			if( tp === 'radio' ){ $i.prop('checked', String(v) === String($i.val())); return; }
			if( this.tagName === 'SELECT' ){ $i.val($.isArray(v) ? $.map(v, String) : String(v)); return; }
			$i.val($.isArray(v) ? (v[0] || '') : v);
		});

		var $wrap = $ed.find('.advance_options_fields').first();
		if( !$wrap.length ){ $wrap = $ed; }
		/* Only fields whose classic settings had conditional logic get the Logic tab */
		if( hasLogic ){ $wrap.append(logicEditor($f, key, saved)); }

		/* Tabs: sort each settings block into Basic, Rules or Logic */
		/* Settings tab: every setting of the classic builder, in its order. Logic has its own tab. */
		$wrap.children().not('.pr-g').addClass('pr-g pr-g-basic');
		applyDeps($ed);

		/* The editor now owns these settings: drop their hidden copies (keep id first, and anything it does not show) */
		var owned = {};
		$ed.find('[name]').each(function(){ var mm = ($(this).attr('name') || '').match(/^field\[[^\]]+\]\[([^\]]+)\]/); if( mm ){ owned[mm[1]] = 1; } });
		owned.display = owned.display || !!$ed.find('.nb-orow').length;
		$f.children('.nb-inputs').find('input[type="hidden"]').each(function(){
			var mm = ($(this).attr('name') || '').match(/^field\[[^\]]+\]\[([^\]]+)\]/);
			if( mm && mm[1] !== 'id' && owned[mm[1]] ){ $(this).remove(); }
		});

		$f.data('nbEd', $ed);
		$f.append($('<div class="nb-edpark" hidden></div>').append($ed));
		return $ed;
	}

	/* Show or hide dependent settings like the classic builder:
		Label2 / Placeholder 2 of E-mail only with "Confirm Email"; of Password only without "Hide Confirm Password" */
	function applyDeps($ed){
		var $ce = $ed.find('input[name$="[confirm_email]"]'), $hp = $ed.find('input[name$="[hide_confirm_password]"]');
		var show = true;
		if( $ce.length ){ show = $ce.is(':checked'); }
		if( $hp.length ){ show = !$hp.is(':checked'); }
		if( $ce.length || $hp.length ){
			$ed.find('[name$="[label2]"], [name$="[placeholder2]"]').closest('.advance_fields').toggleClass('nb-dep-off', !show);
		}
	}

	/* Keep the canvas in step with the settings */
	function updateCanvas($f){
		if( $f.hasClass('nb-break') ){
			var nb = fieldData(pages().eq(step + 1).children('.nb-break')), pb = fieldData(pages().eq(step).children('.nb-break'));
			$('.nb-next').text(nb.next_button === 'url' ? t.next + ' (' + t.imageButton + ')' : (nb.next_button_text || t.next));
			$('.nb-prev').text(pb.prev_button === 'url' ? t.previous + ' (' + t.imageButton + ')' : (pb.prev_button_text || t.previous));
			return;
		}
		var d = fieldData($f), type = $f.attr('data-type');
		var label = d.label !== undefined && d.label !== '' ? d.label : name(type);
		var $l = $f.children('.nb-label').first().empty().text(label);
		if( d.required ){ $l.append(' ').append($('<span class="nb-req">*</span>')); }
		if( String(d.conditional_logic) === '1' ){ $l.append($('<span class="nb-chip"></span>').text(t.logicChip)); }
		$f.children('.nb-pv').html(previewHtml(type, d));
		$f.children('.nb-help').remove();
		if( d.desc ){ $f.children('.nb-pv').after($('<div class="nb-help"></div>').text(d.desc)); }
		$('#nb-insp .nb-ptitle').text(label);
	}

	function parkEditor(){
		$('#nb-insp .nb-editor').each(function(){
			var $ed = $(this), $f = $('#nb-pages .nb-field, #nb-pages .nb-break').filter(function(){ return $(this).data('nbEd') && $(this).data('nbEd')[0] === $ed[0]; });
			if( $f.length ){ $f.children('.nb-edpark').append($ed); } else { $ed.remove(); }
		});
	}

	/* ---------- form settings and submit button panels (real inputs, outside the canvas so Undo leaves them alone) ---------- */
	function showPanel(id){
		clearSelection();
		$('#nb-insp, .nb-rtabs').hide();
		$('#' + id).prop('hidden', false);
		if( id === 'nb-form-panel' ){ $('#nb-formhead').addClass('sel'); fillRuleFields(); } else { $('.nb-submit').addClass('sel'); }
	}

	function panelDeps(){
		$('#nb-form-panel .nb-dep-resume').toggleClass('nb-dep-off', $('#nb-save-resume').val() !== '1');
		$('#nb-vrules').prop('hidden', $('#nb-form-logic').val() !== '1');
		$('#nb-submit-panel .nb-dep-reset').toggleClass('nb-dep-off', $('#nb-submit-reset').val() !== '1');
		var conf = $('#nb-submit-panel input[name="field[submit][confirmation]"]:checked').val() || 'text';
		$('#nb-submit-panel .nb-dep-conf').each(function(){ $(this).toggleClass('nb-dep-off', $(this).attr('data-conf') !== conf); });
	}

	function panelCanvas(){
		$('#nb-formhead').toggleClass('nb-title-off', !$('#nb-display-name').is(':checked'));
		var desc = $.trim($('#nb-form-desc').val() || '');
		$('#nb-canvas-desc').text($('<div/>').html(desc).text()).toggle(desc !== '');
		$('.nb-submit').text($.trim($('#nb-submit-text').val() || '') || t.submit);
		$('.nb-reset').text($.trim($('#nb-submit-reset-text').val() || '') || t.reset).toggle($('#nb-submit-reset').val() === '1');
		$('#nb-progress').attr('data-label', $('#nb-progress-label').val() || '');
		updateProgress();
	}

	/* Progress bar preview, as the front end shows it (assets/js/prVariablesDeclarationFooter.js) */
	function updateProgress(){
		var n = pages().length, $p = $('#nb-progress'), pct = Math.round((step + 1) / n * 100), mode = $p.attr('data-label') || '', txt = '';
		$p.toggle(n > 1);
		$p.children('.nb-progress-bar').css('width', pct + '%');
		if( mode === 'percent' ){ txt = pct + '%'; }
		else if( mode === 'steps' ){ txt = t.stepOf.replace('%1$s', step + 1).replace('%2$s', n); }
		else if( mode === 'both' ){ txt = t.stepOf.replace('%1$s', step + 1).replace('%2$s', n) + ' - ' + pct + '%'; }
		$p.children('.nb-progress-text').text(txt);
	}

	/* Fields a verification rule can test, by the name they are posted with (same as getPieregFieldName() on the server) */
	function postNames(){
		var list = [], skip = ['pagebreak', 'sectionbreak', 'html', 'captcha', 'hcaptcha', 'math_captcha', 'honeypot', 'password', 'upload', 'profile_pic', 'list'];
		$('#nb-pages .nb-field').each(function(){
			var d = fieldData($(this)), type = d.type || $(this).attr('data-type'), label = d.label || name($(this).attr('data-type'));
			if( inArr(type, skip) ){ return; }
			if( type === 'name' ){ list.push({ v: 'first_name', l: label }); list.push({ v: 'last_name', l: d.label2 || t.last }); return; }
			var pn = type === 'email' ? 'e_mail' : (inArr(type, ['username', 'custom_role', 'pricing', 'invitation']) ? type : (type === 'default' ? (d.field_name || '') : type + '_' + d.id));
			if( pn ){ list.push({ v: pn, l: label }); }
		});
		return list;
	}

	function fillRuleFields(){
		var list = postNames();
		$('#nb-vrules .nb-v-field').each(function(){
			var $s = $(this), v = $s.val() || $s.attr('data-value') || '', found = false;
			$s.find('option').not(':first').remove();
			$.each(list, function(_, f){ $s.append($('<option></option>').val(f.v).text(f.l)); if( f.v === v ){ found = true; } });
			if( v && !found ){ $s.append($('<option></option>').val(v).text(v)); }
			$s.val(v);
		});
	}

	function render(){
		parkEditor();
		$('.nb-panel').prop('hidden', true);
		$('#nb-insp, .nb-rtabs').show();
		$('#nb-formhead, .nb-submit').removeClass('sel');
		var $p = $('#nb-insp').empty();
		if( !$curField || !$curField.closest('#nb-pages').length ){
			$p.append($('<p class="nb-none"></p>').text(t.pick));
			return;
		}
		var $ed = $curField.data('nbEd') || buildEditor($curField);
		var ptitle = $curField.hasClass('nb-break') ? t.stepSettings + ': ' + t.step + ' ' + (step + 1) : ($.trim($curField.children('.nb-label').first().clone().children().remove().end().text()) || name($curField.attr('data-type')));
		$p.append($('<div class="nb-ptitle"></div>').text(ptitle));
		if( !$ed ){
			$p.append($('<p class="nb-note"></p>').text(t.noSettings));
			return;
		}
		$p.append($ed);
		$ed.attr('data-tab', tab);
		var counts = { basic: $ed.find('.pr-g-basic').length, logic: $ed.find('.pr-g-logic').length };
		if( tab === 'logic' && !counts.logic ){ tab = 'basic'; }
		$('.nb-rtabs button').each(function(){ $(this).prop('disabled', !counts[$(this).attr('data-tab')]); });
	}

	function setTab(n){
		tab = n;
		$('.nb-rtabs button').removeClass('on').attr('aria-selected', 'false').filter('[data-tab="' + n + '"]').addClass('on').attr('aria-selected', 'true');
		$('#nb-insp .nb-editor').attr('data-tab', n);
	}

	function clearSelection(){
		parkEditor();
		$('.nb-field').removeClass('sel').children('.nb-tools').remove();
		$('.nb-break').removeClass('sel');
		$('.nb-step').removeClass('nb-step-sel');
		current = null;
		$curField = null;
		render();
	}

	function select($f){
		$('.nb-field').not($f).removeClass('sel').children('.nb-tools').remove();
		$('.nb-step').removeClass('nb-step-sel');
		$f.addClass('sel');
		$f.children('.nb-tools').remove();
		if( !$f.hasClass('nb-break') ){ $f.append(toolsFor($f)); }
		else { $('.nb-step[data-step="' + step + '"]').addClass('nb-step-sel'); }
		$curField = $f;
		render();
		setTab(tab);
	}

	/* ---------- wiring ---------- */
	$(function(){
		var $root = $('#piereg-nb');
		if( !$root.length ){ return; }
		try{ cfg = JSON.parse($root.attr('data-cfg')) || {}; }catch(e){ cfg = {}; }
		cfg.maxId = parseInt(cfg.maxId, 10) || 0;

		$root.on('click', '.nb-step', function(e){
			if( $(e.target).hasClass('nb-step-x') ){ removeStep(parseInt($(this).attr('data-step'), 10)); return; }
			if( $(e.target).hasClass('nb-step-cog') ){
				var i = parseInt($(this).attr('data-step'), 10);
				if( i !== step ){ step = i; refresh(); }
				select(pages().eq(i).children('.nb-break'));
				return;
			}
			if( $(this).hasClass('nb-addstep') ){ addStep(); return; }
			showStep($(this).attr('data-step'));
		});
		$root.on('click', '.nb-prev', function(){ showStep(step - 1); });
		$root.on('click', '.nb-next', function(){ showStep(step + 1); });

		$root.on('click', '.nb-dev button', function(){
			$('.nb-dev button').removeClass('on');
			$(this).addClass('on');
			$('#nb-canvas').toggleClass('nb-mobile', $(this).attr('data-dev') === 'mobile');
		});

		$root.on('click', '.nb-tile', function(){
			if( $(this).hasClass('nb-off') || $(this).hasClass('ui-draggable-dragging') ){ return; }
			addField($(this).attr('data-type'));
		});
		$root.on('keydown', '.nb-tile', function(e){
			if( (e.key === 'Enter' || e.key === ' ') && !$(this).hasClass('nb-off') ){ e.preventDefault(); addField($(this).attr('data-type')); }
		});

		$root.on('click', '.nb-field', function(e){
			if( $(e.target).closest('.nb-tools').length ){ return; }
			select($(this));
		});
		$root.on('keydown', '.nb-field', function(e){
			if( e.target !== this ){ return; }
			if( e.key === 'Enter' || e.key === ' ' ){ e.preventDefault(); select($(this)); }
		});
		$root.on('click', '.nb-tools button', function(e){
			e.stopPropagation();
			var $f = $(this).closest('.nb-field'), a = $(this).attr('data-a');
			if( a === 'up' ){ move($f, -1); } else if( a === 'down' ){ move($f, 1); } else if( a === 'dup' ){ duplicate($f); } else if( a === 'del' ){ remove($f); }
		});
		$root.on('change', '.nb-tools select', function(){ moveToStep($(this).closest('.nb-field'), $(this).val()); });

		$root.on('click', '.nb-rtabs button', function(){ setTab($(this).attr('data-tab')); });

		/* Settings editor buttons (delegated, so they survive Undo) */
		$root.on('click', '.nb-o-add', function(){
			var $box = $(this).closest('.advance_fields').prev('.nb-options');
			var $r = newOptionRow($box); reindexOptions($box);
			$r.find('input[type="text"]').first().trigger('focus');
			setDirty(); if( $curField ){ updateCanvas($curField); }
		});
		$root.on('click', '.nb-o-x', function(){
			var $box = $(this).closest('.nb-options');
			if( $box.children('.nb-orow').length > 1 ){ $(this).closest('.nb-orow').remove(); reindexOptions($box); setDirty(); if( $curField ){ updateCanvas($curField); } }
		});
		$root.on('change', '.nb-l-on', function(){ $(this).closest('.nb-logic').children('.nb-logic-body').toggle($(this).val() === '1'); });
		$root.on('click', '.nb-l-and', function(){ var $box = $(this).closest('.nb-logic'); $(this).before(ruleRow($box.attr('data-key'), {})); renumberLogic($box); setDirty(); });
		$root.on('click', '.nb-l-or', function(){ var $box = $(this).closest('.nb-logic'); $box.find('.nb-lgroups').append(groupBox($box.attr('data-key'), [])); renumberLogic($box); setDirty(); });
		$root.on('click', '.nb-l-x', function(){
			var $box = $(this).closest('.nb-logic'), $g = $(this).closest('.nb-lgroup'), $groups = $box.find('.nb-lgroups');
			$(this).closest('.nb-lrule').remove();
			if( !$g.children('.nb-lrule').length ){ $g.remove(); }
			if( !$groups.children('.nb-lgroup').length ){ $groups.append(groupBox($box.attr('data-key'), [])); }
			renumberLogic($box); setDirty();
		});
		$root.on('focus', '.nb-l-field', function(){ fieldOptions($(this), $(this).closest('.nb-logic').attr('data-key'), $(this).val()); });
		/* Each setting edited is one undo step: keep the state from before the edit, and add it to the history on the first change */
		var preEdit = null, preEditEl = null;
		$root.on('focusin', '.nb-editor :input', function(){ preEdit = capture(); preEditEl = this; });
		$root.on('input change', '.nb-editor :input', function(){
			if( preEdit && preEditEl === this ){ pushHistory(preEdit); preEdit = null; }
			applyDeps($(this).closest('.nb-editor')); setDirty(); if( $curField ){ updateCanvas($curField); }
		});
		$('#nb-undo').on('click', undo);

		/* Form settings and submit button panels */
		$('#nb-formset').on('click', function(){ showPanel('nb-form-panel'); });
		$root.on('click', '#nb-formhead', function(){ showPanel('nb-form-panel'); });
		$root.on('click', '.nb-nav .nb-submit', function(){ showPanel('nb-submit-panel'); });
		$root.on('keydown', '#nb-formhead, .nb-nav .nb-submit', function(e){
			if( e.key === 'Enter' || e.key === ' ' ){ e.preventDefault(); $(this).trigger('click'); }
		});
		$root.on('input change', '.nb-panel :input', function(){ panelDeps(); panelCanvas(); setDirty(); });
		$root.on('click', '.nb-v-add', function(){
			var $r = $('#nb-vrules .nb-vrule').first().clone();
			$r.find('select').each(function(){ this.selectedIndex = 0; });
			$r.find('.nb-v-field').attr('data-value', '');
			$r.find('.nb-v-val').val('');
			$(this).before($r);
			setDirty();
		});
		$root.on('click', '.nb-v-x', function(){
			var $r = $(this).closest('.nb-vrule');
			if( $('#nb-vrules .nb-vrule').length > 1 ){ $r.remove(); }
			else { $r.find('select').each(function(){ this.selectedIndex = 0; }); $r.find('.nb-v-val').val(''); }
			setDirty();
		});
		panelDeps();

		$(document).on('keydown', function(e){
			if( e.key === 'Escape' ){ clearSelection(); }
			if( (e.ctrlKey || e.metaKey) && e.key === 'z' && !$(e.target).is('input, textarea') ){ e.preventDefault(); undo(); }
			/* Delete (or Backspace, the Mac "delete" key) removes the selected field, with the usual confirmation. Never while typing or in a dialog. */
			if( ( e.key === 'Delete' || e.key === 'Backspace' ) && !e.ctrlKey && !e.metaKey && !e.altKey ){
				if( $(e.target).is('input, textarea, select, [contenteditable="true"]') || $('.nb-modal').length ){ return; }
				var $del = $(e.target).closest('#nb-pages .nb-field');
				if( !$del.length && $curField && $curField.hasClass('nb-field') && $curField.closest('#nb-pages').length ){ $del = $curField; }
				if( $del.length ){ e.preventDefault(); remove($del); }
			}
		});

		$root.on('click keydown', '.nb-gh', function(e){
			if( e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ' ){ return; }
			e.preventDefault();
			$(this).closest('.nb-group').toggleClass('nb-collapsed');
		});

		$('#nb-q').on('input', function(){
			var q = $.trim($(this).val()).toLowerCase(), any = false;
			$('.nb-group').each(function(){
				var $g = $(this), hits = 0;
				$g.find('.nb-tile').each(function(){
					var m = q === '' || $(this).text().toLowerCase().indexOf(q) > -1;
					$(this).toggle(m);
					if( m ){ hits++; }
				});
				$g.toggle(hits > 0);
				if( hits ){ any = true; }
			});
			$('.nb-group.nb-collapsed .nb-grid').css('display', q !== '' ? 'grid' : '');
			$('.nb-nomatch').toggle(!any);
		});

		$('#nb-title').on('input', function(){
			$('#nb-canvas-title').text($.trim($(this).val()) || t.untitled);
			setDirty();
		});

		$('#nb-form').on('submit', function(e){
			if( $.trim($('#nb-title').val()) === '' ){
				e.preventDefault();
				dialog({ title: t.titleNeeded, text: t.titleText, ok: t.ok });
				$('#nb-title').trigger('focus');
				return;
			}
			parkEditor();
			dirty = false;
			$('#nb-save').prop('disabled', true).text(t.saving);
		});
		/* Leaving through a link on the page: our own dialog. Closing the tab or reloading still needs the browser's box (it cannot be styled). */
		var leaving = false;
		$(document).on('click', 'a[href]', function(e){
			var href = $(this).attr('href') || '', target = ($(this).attr('target') || '').toLowerCase();
			if( !dirty || leaving || e.isDefaultPrevented() || e.ctrlKey || e.metaKey || e.shiftKey || e.button === 1 ){ return; }
			if( target === '_blank' || href === '' || href.charAt(0) === '#' || /^javascript:/i.test(href) ){ return; }
			e.preventDefault();
			dialog({ title: t.leaveTitle, text: t.leaveText, ok: t.leave, cancel: t.stay, onOk: function(){
				leaving = true;
				window.location.href = href;
			} });
		});
		/* Reload from the keyboard (F5, Ctrl+R, Ctrl+Shift+R, Ctrl+F5): our own dialog as well.
			The browser's reload button, closing the tab or typing a new address still show the browser's box. */
		$(document).on('keydown', function(e){
			var k = (e.key || '').toLowerCase();
			var isReload = k === 'f5' || ((e.ctrlKey || e.metaKey) && k === 'r');
			if( !isReload || !dirty || leaving ){ return; }
			e.preventDefault();
			if( $('.nb-modal').length ){ return; }
			dialog({ title: t.reloadTitle, text: t.leaveText, ok: t.reload, cancel: t.stay, onOk: function(){
				leaving = true;
				window.location.reload();
			} });
		});
		window.addEventListener('beforeunload', function(e){
			if( dirty && !leaving ){ e.preventDefault(); e.returnValue = ''; }
		});

		refresh();
		clearSelection();
	});
})(jQuery);
