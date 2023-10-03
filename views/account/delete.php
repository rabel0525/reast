<?php $this->setLayoutVar('title', 'アカウント削除') ?>
<div class="button"><span></span><span></span><span></span></div>

<div class="profile">
    <div class="p_user_profile">
            <div class="p_user_image">
                <img src="<?php echo $base_url; ?>/profileimages/<?php echo $this->escape($profile_image["0"]["profile_image"]); ?>"/>
            </div>
            <div style="margin: 10px;">
            <div class="p_screen_name" style="font-size: clamp(1.0em, 1.6vw, 1.3em);">
            <?php echo $this->escape($screen_name); ?>
            </div>
            <div class="p_user_name">
            ID: <span id="user_id"><?php echo $this->escape($user['user_name']); ?></span>
            </div>
            <div class="profile_detail">
                <p style="font-size: clamp(10px, 2.5vw, 16px);"><?php echo $this->escape($introduce); ?></p>
            </div>
            </div>
        </div> 
        <div class="p_user_detail">
                        <div class="p_user_detail_c">
                            <p>フォロー中</p><span><?php echo count($followings); ?><span>
                        </div>
                        <div class="p_user_detail_c">
                            <p>フォロワー</p><span><?php echo count($followed); ?><span>
                        </div>
                        <div class="p_user_detail_c">
                            <p>投稿数</p><span><?php  echo ($all_posts[0]["count"]); ?><span>
                        </div>
        </div>
</div>
<div>
</div>

<div class="wrap">
<div class="sidenav">
    <div class="user_menu">
    <ul>
        <li><a href="<?php echo $base_url; ?>/user/<?php echo $this->escape($user['user_name']); ?>">投稿一覧</a></li>
        <li><a href="<?php echo $base_url; ?>/user/<?php echo $this->escape($user['user_name']); ?>/following">フォロー中</a></li>
        <li><a href="<?php echo $base_url; ?>/user/<?php echo $this->escape($user['user_name']); ?>/followers">フォロワー</a></li>
    </ul>
    </div>
    <div class="user_menu">
    <ul>
        <li><a href="<?php echo $base_url; ?>/profile?editer=true">プロフィール編集</a></li>
        <li><a href="<?php echo $base_url; ?>/account/userid_setting">ユーザーID変更</a></li>
        <li><a href="<?php echo $base_url; ?>/account/password_setting">パスワード変更</a></li>
        <li><a href="<?php echo $base_url; ?>/account/email_setting">メールアドレス変更</a></li>
        <li><a href="<?php echo $base_url; ?>/account/delete">アカウントの削除</a></li>
        <li><a href="<?php echo $base_url; ?>/forms">ご要望</a></li>
    </ul>
    </div>
    <div class="footer">
        <p style="
    text-align: center;
    color: #b7b7b7;
">©2023 Reast Powered By Rabel0525</p>
    </div>
</div>

<div class="contents">
    <div id="statues">
        <div class="container-left">
            <div id="posts">
<form action="<?php echo $base_url; ?>/account/delete_account" method="post" enctype="multipart/form-data" style="
    border-bottom: solid 1px #eee;
">
    <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>">

    <?php if (isset($errors) && count($errors) > 0): ?>
    <?php echo $this->render('errors', array('errors' => $errors)); ?>
    <?php endif; ?>
    <h3>アカウント削除フォーム</h3>
<p>セキュリティ保持の為、削除するためにはログイン情報を再入力してください。</p>
    <div style="display:block;">
    <p>メールアドレス：<input type="text" name="user_name"></p>
    <p>パスワード：<input type="password" name="password"></p>
    </div>
    <div class="post_button">
    <label><input type="checkbox" name="scb3" value="on" onclick="connecttext(this.checked);">データが全て削除され復元できないことに同意します</label>
    <input type="submit" id="submit_button" value="削除" disabled>
</div>
</form>
<script>
function connecttext(ischecked) {
   // チェック状態に合わせて有効・無効を切り替える
   document.getElementById('submit_button').disabled = !ischecked;
}
</script>
            </div>
            <script src="/js/menu_button.js"></script>
        </div>
    </div>
</div>

