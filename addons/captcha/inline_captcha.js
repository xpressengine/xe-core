jQuery(function($){
	$('button.captchaPlay')
		.click(function(){
			var audioURL = current_url.setQuery('captcha_action','captchaAudio').setQuery('rand', (new Date).getTime());

			if(Modernizr.audio || Modernizr.audio.mp3) {
				var objAudio = new Audio(audioURL);
				objAudio.play();
			} else {
				var swf = document.querySelector('#captcha_audio_flash');
				if(!swf) return;

				if(swf.length > 1) swf = swf[0];

				$('input[type=text]#secret_text').focus();
				swf.setSoundTarget(audioURL, '1');
			}
		});

	$('button.captchaReload')
		.click(function(){
			$("#captcha_image").attr("src", current_url.setQuery('captcha_action','captchaImage').setQuery('rand', (new Date).getTime()).setQuery('renew',1));
		});
});
