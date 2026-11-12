<?php
/*
	* Pie Register > Analytics
	* Numbers are loaded with AJAX by assets/js/piereg_analytics_admin.js
	* Uses its own "an-" classes so the plugin's generic admin styles (settings, fields, input_fields ...) do not apply.
	* Variables from PieReg_Analytics::render_page(): $options
*/
if( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="pieregister-admin piereg-an">
	<div class="an-head">
		<div>
			<h1 class="an-title"><?php esc_html_e( 'Registration Analytics', 'pie-register' ); ?></h1>
			<p class="an-sub"><?php esc_html_e( 'Where visitors come from, how far they get, and which field makes them leave. No personal data is stored.', 'pie-register' ); ?></p>
		</div>
		<a href="#" class="an-btn an-btn-light" id="piereg_an_export"><span class="dashicons dashicons-download" aria-hidden="true"></span> <?php esc_html_e( 'Export CSV', 'pie-register' ); ?></a>
	</div>

	<div class="an-toolbar">
		<div class="an-filter">
			<label for="piereg_an_form"><?php esc_html_e( 'Form', 'pie-register' ); ?></label>
			<select id="piereg_an_form">
			</select>
		</div>
		<div class="an-filter">
			<label for="piereg_an_range"><?php esc_html_e( 'Date range', 'pie-register' ); ?></label>
			<select id="piereg_an_range">
				<option value="7"><?php esc_html_e( 'Last 7 days', 'pie-register' ); ?></option>
				<option value="30" selected="selected"><?php esc_html_e( 'Last 30 days', 'pie-register' ); ?></option>
				<option value="90"><?php esc_html_e( 'Last 90 days', 'pie-register' ); ?></option>
				<option value="custom"><?php esc_html_e( 'Custom…', 'pie-register' ); ?></option>
			</select>
		</div>
		<div class="an-filter piereg-an-custom" style="display:none;">
			<label for="piereg_an_from"><?php esc_html_e( 'From', 'pie-register' ); ?></label>
			<input type="date" id="piereg_an_from" />
		</div>
		<div class="an-filter piereg-an-custom" style="display:none;">
			<label for="piereg_an_to"><?php esc_html_e( 'To', 'pie-register' ); ?></label>
			<input type="date" id="piereg_an_to" />
		</div>
		<div class="an-filter an-filter-btn">
			<button type="button" class="an-btn an-btn-red" id="piereg_an_apply"><?php esc_html_e( 'Apply', 'pie-register' ); ?></button>
		</div>
	</div>

	<div id="piereg_an_msg" class="an-msg" style="display:none;" role="status"></div>

	<div class="an-kpis" id="piereg_an_kpis"></div>

	<div class="an-grid">
		<section class="an-card">
			<h2 class="an-card-title"><?php esc_html_e( 'Funnel', 'pie-register' ); ?></h2>
			<div id="piereg_an_funnel" class="an-funnel"></div>
			<p class="an-note"><?php esc_html_e( 'Views, Started and Submitted count unique visits (one per browser tab). The Registered and Activated tiles count every account; the funnel and Conversion use only accounts from tracked visits. Conversion is tracked registrations divided by Views.', 'pie-register' ); ?></p>
		</section>
		<section class="an-card">
			<h2 class="an-card-title"><?php esc_html_e( 'Daily trend', 'pie-register' ); ?></h2>
			<div id="piereg_an_trend" class="an-trend"></div>
			<div class="an-legend"><span class="an-l-views"><?php esc_html_e( 'Views', 'pie-register' ); ?></span><span class="an-l-reg"><?php esc_html_e( 'Registrations', 'pie-register' ); ?></span></div>
		</section>
	</div>

	<section class="an-card">
		<h2 class="an-card-title"><?php esc_html_e( 'Field drop-off', 'pie-register' ); ?></h2>
		<p class="an-note an-note-top"><?php esc_html_e( 'The field highlighted in red is where most visitors stopped.', 'pie-register' ); ?></p>
		<div class="an-table-wrap">
			<table class="an-table" id="piereg_an_fields">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Field', 'pie-register' ); ?></th>
						<th><?php esc_html_e( 'Interacted', 'pie-register' ); ?></th>
						<th><?php esc_html_e( 'Abandoned here', 'pie-register' ); ?></th>
						<th><?php esc_html_e( 'Errors', 'pie-register' ); ?></th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</section>

	<section class="an-card">
		<h2 class="an-card-title"><?php esc_html_e( 'Traffic sources', 'pie-register' ); ?></h2>
		<p class="an-note an-note-top"><?php esc_html_e( 'From the campaign tags in your links (utm_source, utm_medium, utm_campaign).', 'pie-register' ); ?></p>
		<div class="an-table-wrap">
			<table class="an-table" id="piereg_an_sources">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Source / medium', 'pie-register' ); ?></th>
						<th><?php esc_html_e( 'Campaign', 'pie-register' ); ?></th>
						<th><?php esc_html_e( 'Views', 'pie-register' ); ?></th>
						<th><?php esc_html_e( 'Registrations', 'pie-register' ); ?></th>
						<th><?php esc_html_e( 'Conversion', 'pie-register' ); ?></th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</section>

	<details class="an-card an-settings">
		<summary class="an-card-title"><?php esc_html_e( 'Tracking settings', 'pie-register' ); ?></summary>
		<form id="piereg_an_settings">
			<div class="an-checks">
				<label class="an-check"><input type="checkbox" name="enable" value="1" <?php checked( ! empty( $options['enable'] ) ); ?> /> <span><?php esc_html_e( 'Enable tracking', 'pie-register' ); ?></span></label>
				<label class="an-check"><input type="checkbox" name="exclude_logged_in" value="1" <?php checked( ! empty( $options['exclude_logged_in'] ) ); ?> /> <span><?php esc_html_e( 'Exclude logged-in users', 'pie-register' ); ?></span></label>
				<label class="an-check"><input type="checkbox" name="respect_dnt" value="1" <?php checked( ! empty( $options['respect_dnt'] ) ); ?> /> <span><?php esc_html_e( 'Respect Do-Not-Track and Global Privacy Control', 'pie-register' ); ?></span></label>
				<label class="an-check"><input type="checkbox" name="require_consent" value="1" <?php checked( ! empty( $options['require_consent'] ) ); ?> /> <span><?php esc_html_e( 'Require consent before tracking', 'pie-register' ); ?></span></label>
			</div>
			<p class="an-note"><?php esc_html_e( 'With consent required, tracking starts only after your consent tool sets window.pieregAnalyticsConsent = true (or the WP Consent API allows "statistics").', 'pie-register' ); ?></p>
			<div class="an-toolbar an-toolbar-plain">
				<div class="an-filter">
					<label for="piereg_an_raw"><?php esc_html_e( 'Keep raw events for (days, 7 to 90)', 'pie-register' ); ?></label>
					<input type="number" id="piereg_an_raw" name="raw_retention" min="7" max="90" value="<?php echo esc_attr( (int) $options['raw_retention'] ); ?>" />
				</div>
				<div class="an-filter">
					<label for="piereg_an_daily"><?php esc_html_e( 'Keep daily totals for (days, 30 to 1095)', 'pie-register' ); ?></label>
					<input type="number" id="piereg_an_daily" name="daily_retention" min="30" max="1095" value="<?php echo esc_attr( (int) $options['daily_retention'] ); ?>" />
				</div>
			</div>
			<div class="an-actions">
				<button type="submit" class="an-btn an-btn-red"><?php esc_html_e( 'Save settings', 'pie-register' ); ?></button>
				<button type="button" class="an-btn an-btn-danger" id="piereg_an_purge"><?php esc_html_e( 'Delete all analytics data', 'pie-register' ); ?></button>
			</div>
		</form>
	</details>
</div>
