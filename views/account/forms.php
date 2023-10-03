<?php $this->setLayoutVar('title', 'ご要望フォーム') ?>
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
<form action="<?php echo $base_url; ?>/forms?post=true" method="post" enctype="multipart/form-data" style="
    border-bottom: solid 1px #eee;
">
    <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>">

    <?php if (isset($errors) && count($errors) > 0): ?>
    <?php echo $this->render('errors', array('errors' => $errors)); ?>
    <?php endif; ?>
<h2>ご要望フォーム</h2>
<p>500文字以内で入力してください。※ユーザー名が送信されます。</p>
    <div style="display:block;"><textarea name="contents" rows="5" cols="60" placeholder="発生した問題または、改善点をお書きください。" class="post_area"><?php echo $this->escape_edit($contents); ?></textarea></div>
    <div class="post_button">
    <input type="submit" value="送信" style="
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

