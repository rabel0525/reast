<?php $this->setLayoutVar('title', 'パスワードリセット') ?>
<div class="wrap">
<div class="contents">
<div class="login_wrap">
<h3>パスワードリセット</h3>

<p>※セキュリティ対策のため変更用URLをメールアドレスに送信します。</p>
<form action="<?php echo $base_url; ?>/account/reset_check" method="post">
    <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>">

    <?php if (isset($errors) && count($errors) > 0): ?>
    <?php echo $this->render('errors', array('errors' => $errors)); ?>
    <?php endif; ?>

    <?php echo $this->render('account/temp_inputs', array(
        'email' => $email
    )); ?>

<div class="login_submit">
    <div>
        <input type="submit" value="送信" class="submit_blue_login">
</div>
</div>
</form>
</div>
    </div>
    </div>