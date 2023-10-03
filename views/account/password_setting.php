<?php $this->setLayoutVar('title', 'パスワード再設定') ?>
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
<form action="<?php echo $base_url; ?>/account/change_password" method="post" enctype="multipart/form-data" style="
    border-bottom: solid 1px #eee;
">
    <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>">

    <?php if (isset($errors) && count($errors) > 0): ?>
    <?php echo $this->render('errors', array('errors' => $errors)); ?>
    <?php endif; ?>
    <h3>パスワード変更フォーム</h3>
<p>変更に関する注意事項は利用規約をご覧ください。</p>
    <div style="display:block;">
    <p>現在のパスワード：<input type="password" name="old_password"></p>
    <p>変更後のパスワード：<input type="password" name="new_password"></p>
    </div>
    <div class="post_button">
    <input type="submit" value="変更" style="
    padding: 0.25rem 0.6rem;
    background-color: rgb(91 146 228);
    color: rgb(255 255 255);
    border-radius: 0.25rem;
    cursor: pointer;
    border: 1px solid rgb(117 124 135);
">
</div>
</form>
            </div>
            <script src="/js/menu_button.js"></script>
        </div>
    </div>
</div>

