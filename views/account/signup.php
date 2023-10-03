<?php $this->setLayoutVar('title', 'アカウント登録') ?>

<div class="wrap">
<div class="contents">
<div class="login_wrap">
<h3>アカウント仮登録</h3>

<p>※セキュリティ対策のため登録用URLをメールアドレスに送信します。</p>
<form action="<?php echo $base_url; ?>/account/tmp_register" method="post">
    <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>">

    <?php if (isset($errors) && count($errors) > 0): ?>
    <?php echo $this->render('errors', array('errors' => $errors)); ?>
    <?php endif; ?>

    <?php echo $this->render('account/temp_inputs', array(
        'email' => $email
    )); ?>

<div class="login_submit">
    <div>
        <input type="submit" value="登録" class="submit_blue_login">
</div>
</div>
</form>
</div>
</div>
<div>