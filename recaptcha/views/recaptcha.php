<?php /** @var EReCaptcha $this */ ?>

<div id="recaptcha_widget" style="display:none">

	<div id="recaptcha_image"></div>
	<div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect please try again</div>

	<span class="recaptcha_only_if_image"><?= Yii::t('view', 'Nhập lại chữ trên:'); ?></span>

	<input type="text" id="recaptcha_response_field" name="recaptcha_response_field" />

	<div><a href="javascript:Recaptcha.reload()"><i class="icon-refresh" title="<?= Yii::t('view', 'Lấy mã CAPTCHA khác'); ?>"></i></a></div>

	<div><a href="javascript:Recaptcha.showhelp()"><i class="icon-question-sign" title="<?= Yii::t('view', 'Trợ giúp'); ?>"></i></a></div>

</div>

<script type="text/javascript"
	 src="http://www.google.com/recaptcha/api/challenge?k=<?= $publicKey; ?>">
</script>
<noscript>
	<iframe src="http://www.google.com/recaptcha/api/noscript?k=<?= $publicKey; ?>"
		height="300" width="500" frameborder="0"></iframe><br>
	<textarea name="recaptcha_challenge_field" rows="3" cols="40">
	</textarea>
	<input type="hidden" name="recaptcha_response_field"
		value="manual_challenge">
</noscript>