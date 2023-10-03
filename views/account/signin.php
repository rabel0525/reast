<?php $this->setLayoutVar('title', 'ログイン') ?>
<?php $this->setLayoutVar('title_og', 'Reast | 軽量SNS') ?>
<?php $this->setLayoutVar('user_post', 'Reastはどんなインターネット状況下でも軽量に動作するSNSです。いつでもどこでも気軽に投稿することができます。') ?>
<?php $this->setLayoutVar('url', 'signin') ?>
<?php $this->setLayoutVar('type', 'website') ?>

<div class="wrap">
<div class="contents">
    <div class="login_wrap">
<h3 class="login_submit">ログイン</h3>

<p class="login_menu">
    <a href="<?php echo $base_url; ?>/account/signup">新規ユーザ登録</a>
    <a href="<?php echo $base_url; ?>/account/reset">パスワードをお忘れですか？</a>
</P>

<form action="<?php echo $base_url; ?>/account/authenticate" method="post">
    <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>">

    <?php if (isset($errors) && count($errors) > 0): ?>
    <?php echo $this->render('errors', array('errors' => $errors)); ?>
    <?php endif; ?>

    <?php echo $this->render('account/inputs_for_login', array(
        'user_name' => $user_name, 'password' => $password,
    )); ?>

<div class="login_submit">
    <div>
     <input type="checkbox" name="delete_bg">
     <label>ログイン情報を保持する</label>
    </div>
    <div>
        <input type="submit" value="ログイン" class="submit_blue_login">
</div>
</div>
</form>
</div>
</div>
<div>