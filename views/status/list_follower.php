<?php $this->setLayoutVar('title', $this->escape($user_info['user_name']) . 'さんがフォロー中のアカウント') ?>
<div class="button"><span></span><span></span><span></span></div>

<div style="display: flex;margin-bottom: 10px; border-radius: 5px;padding: 5px; background-color: #ffffffc7; border-bottom: solid 1px #eee; flex-wrap: nowrap;">
<div class="p_user_profile">
            <div class="p_user_image">
                <img src="<?php echo $base_url; ?>/profileimages/<?php echo $this->escape($profile_image["0"]["profile_image"]); ?>"/>
            </div>
            <div style="margin: 10px;">
            <div class="p_screen_name">
            <?php echo $this->escape($screen_name); ?>
            </div>
            <div class="p_user_name">
            ID: <span id="u_id" name="<?php echo $this->escape($user_info['id']); ?>"><?php echo $this->escape($user_info['user_name']); ?></span>
            </div>
            <div class="profile_detail">
                <p style="font-size: clamp(5px, 2.9vw, 16px);"><?php echo $this->escape($introduce); ?></p>
                <div class="follow">

                <?php if ($isfollower): ?>
                    <p>フォローされています</p>
                <?php endif; ?>
                    
                    <?php if (!is_null($following)): ?>
                    <?php if ($following): ?>
                        <form action="<?php echo $base_url; ?>/unfollow" method="post">
                            <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>">
                            <input type="hidden" name="following_name" value="<?php echo $this->escape($user_info['user_name']); ?>">
                    
                            <input type="submit" value="フォロー解除" class="submit_blue">
                        </form>
                    <?php else: ?>
                        <form action="<?php echo $base_url; ?>/follow" method="post">
                            <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>">
                            <input type="hidden" name="following_name" value="<?php echo $this->escape($user_info['user_name']); ?>">
                    
                            <input type="submit" value="フォローする" class="submit_blue">
                        </form>
                    <?php endif; ?>
                    <?php endif; ?>
                    
                </div>
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
        <li><a href="<?php echo $base_url; ?>/user/<?php echo $this->escape($user_info['user_name']); ?>">投稿一覧</a></li>
        <li><a href="<?php echo $base_url; ?>/user/<?php echo $this->escape($user_info['user_name']); ?>/following">フォロー中</a></li>
        <li><a href="<?php echo $base_url; ?>/user/<?php echo $this->escape($user_info['user_name']); ?>/followers">フォロワー</a></li>
    </ul>
    </div>
    <?php if ($user['id'] == $user_info['id']): ?>
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
    <?php endif; ?>
    <div class="header" style="
    margin: 10px;
">
        <p style="
    text-align: center;
    color: #b7b7b7;
">©2023 Reast Powered By Rabel0525</p>
    </div>
</div>

<div class="contents">
    <div id="statues">
        <div class="container-left">
        <div id="posts"></div>
        <div class="loading">
<img id="loadingAnimation" src="/images/loading.gif">
<span id ="loaded"></span>
<div style="margin-bottom:60px;"></div>

<div id="postBaseElemWrap" style="display: none;">
<div class="following_wrap">
<a href="" class="user_link" style="display: block; text-decoration: none; color: #41474b;">
<div style="display: flex;margin-bottom: 10px; border-radius: 5px;padding: 5px; background-color: #ffffffc7; border-bottom: solid 1px #eee; flex-wrap: nowrap;">
<div class="p_user_profile">
            <div class="p_user_image">
                <img src="" class="user_profileimage"/>
            </div>
            <div style="margin: 10px;">
            <div class="p_screen_name">-</div>
            <div class="p_user_name">
            ID: <span id="user_id">-</span>
            </div>
            <div class="profile_detail">
                <p style="font-size: clamp(0.6em, 1.3vw, 1.0em);" class="introduce">-</p>
            </div>
            </div>
        </div> 
        <div class="p_user_detail">
                        <div class="p_user_detail_c">
                            <p>フォロー中</p><span class="followings">-<span>
                        </div>
                        <div class="p_user_detail_c">
                            <p>フォロワー</p><span class="follower">-<span>
                        </div>
                        <div class="p_user_detail_c">
                            <p>投稿数</p><span class="posts_count">-<span>
                        </div>
        </div>
</div>
    </a>
</div>
</div>
<script src="/js/followers.js"></script>
<script>
        $(function() {
            $("#refreshButton").on("click", function() {
                react.Post.getfollowers(true);
            });
            react.Post.getfollowers(false);
        });
    </script>
        </div>
    </div>
</div>

