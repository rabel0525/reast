<?php $this->setLayoutVar('title', 'パスワードリセット') ?>
<div class="wrap">
<div class="contents">
<div class="login_wrap">
<h3>パスワードリセット</h3>

<p>※リセットするためには、登録時でのメールアドレスが必要です。</p>
<form action="<?php echo $base_url; ?>/account/reset_password_check" method="post">
    <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>">

    <?php if (isset($errors) && count($errors) > 0): ?>
    <?php echo $this->render('errors', array('errors' => $errors)); ?>
    <?php endif; ?>


<div>
<div class="login_1">
<span>メールアドレス</span>
<input type="text" name="user_name"
                     class="input_login">
</div>
<div class="login_1">
<span>新しいパスワード</span>
<input type="password" name="password_2"
                     class="input_login">
</div>
</div>

<div class="login_submit">
    <div>
        <input type="submit" value="変更" class="submit_blue_login">
</div>
</div>
</form>
</div>

    </div>
    </div>