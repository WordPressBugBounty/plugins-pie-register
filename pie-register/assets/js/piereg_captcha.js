/*
	* Pie Register: invisible reCAPTCHA v2
	* Each .piereg-recaptcha-invisible becomes an invisible widget. On submit (after the plugin's own validation passed)
	* the challenge runs; when Google returns a token the form is submitted, including the button that was clicked.
	* Since v4.0.0.0
*/
window.pieregInvisibleInit = function(){
	var $ = jQuery;
	$('.piereg-recaptcha-invisible').each(function(){
		var el = this, $form = $(el).closest('form');
		if( !$form.length || $(el).data('pieregReady') || typeof grecaptcha === 'undefined' ){ return; }
		$(el).data('pieregReady', 1);
		var submitter = null, widget;

		$form.on('click', '[type="submit"], input[type="image"]', function(){ submitter = this; });

		widget = grecaptcha.render(el, {
			sitekey: el.getAttribute('data-sitekey'),
			size: 'invisible',
			badge: 'bottomright',
			callback: function(){
				$form.data('pieregCaptchaOk', 1);
				if( submitter && submitter.name && !$form.find('input[type="hidden"][name="' + submitter.name + '"]').length ){
					$('<input type="hidden" />').attr('name', submitter.name).val(submitter.value || '1').appendTo($form);
				}
				$form.trigger('piereg_before_native_submit');
				$form[0].submit();
			},
			'expired-callback': function(){ $form.removeData('pieregCaptchaOk'); }
		});

		$form.on('submit', function(e){
			if( e.isDefaultPrevented() || $form.data('pieregCaptchaOk') ){ return; }
			e.preventDefault();
			grecaptcha.execute(widget);
		});
	});
};
