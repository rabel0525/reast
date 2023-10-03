<?php $this->setLayoutVar('title', 'アカウント登録') ?>

<div class="wrap">
<div class="contents">
<div class="login_wrap">
<h3>アカウント本登録</h3>

<p>以下の入力項目は必須です。登録後、プロフィールから多くの変更を加えることができます。</p>
<form action="<?php echo $base_url; ?>/account/check_register" method="post">
    <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>">
    <input type="hidden" name="register_token" value="<?php echo $this->escape($register_token); ?>">

    <?php if (isset($errors) && count($errors) > 0): ?>
    <?php echo $this->render('errors', array('errors' => $errors)); ?>
    <?php endif; ?>

    <?php echo $this->render('account/inputs', array(
        'screen_name' => $screen_name, 'user_name' => $user_name, 'password' => $password,
    )); ?>

<div class="login_submit">
    <div>
        <input type="submit" value="登録" class="submit_blue_login">
</div>
</div>
</form>
</div>
</div>
</div>