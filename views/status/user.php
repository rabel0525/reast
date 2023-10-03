<?php $this->setLayoutVar('title', $user_info['user_name'] .'さんのプロフィール') ?>
<?php $this->setLayoutVar('title_og', $user_info['screen_name']. ' (ID:'. $user_info['user_name'] .') | Reast ') ?>
<?php $this->setLayoutVar('user_post', $user_info['introduce']) ?>
<?php $this->setLayoutVar('url', 'user/' . $user_info['user_name']) ?>
<?php $this->setLayoutVar('image_path', 'https://reast.info/profileimages/' .$profile_image["0"]["profile_image"]) ?>
<?php $this->setLayoutVar('type', 'profile') ?>
<div class="button"><span></span><span></span><span></span></div>

<div class="profile">
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
                <div class="follow" style="display: flex; align-items: center;">

                <?php if ($isfollower): ?>
                    <span style="font-size: clamp(5px, 2.9vw, 12px);margin-left: clamp(8px, 1.9vw, 32px);;margin-right: clamp(8px, 1.9vw, 32px);;">フォローされています</span>
                <?php endif; ?>

<?php if (!is_null($following)): ?>
<?php if ($following): ?>
    <form action="<?php echo $base_url; ?>/unfollow" method="post" style="padding:0;">
        <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>">
        <input type="hidden" name="following_name" value="<?php echo $this->escape($user_info['user_name']); ?>">

        <input type="submit" value="フォロー解除" class="submit_blue">
    </form>
<?php else: ?>
    <form action="<?php echo $base_url; ?>/follow" method="post" style="padding:0;">
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


<div class="wrap">
<div class="sidenav">
    <div class="user_menu">
    <ul>
        <li><a href="<?php echo $base_url; ?>/user/<?php echo $this->escape($user_info['user_name']); ?>">投稿一覧</a></li>
        <li><a href="<?php echo $base_url; ?>/user/<?php echo $this->escape($user_info['user_name']); ?>/following">フォロー中</a></li>
        <li><a href="<?php echo $base_url; ?>/user/<?php echo $this->escape($user_info['user_name']); ?>/followers">フォロワー</a></li>
    </ul>
    </div>
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
    <div class="status-wrap">
        <div class="status-main">
            <a class="status-screen-name" href="">
                <div class="status-user-name">
                    <span class="status-user-name-value">-</span>
                    <span class="status-user-badge"><i class="fas fa-check-circle"></i></span>
                    <span class="status-user-locked"><i class="fas fa-lock"></i></span>
                </div>
            </a>
            <div class="status-infos">
                <a class="status-link-user" href="">
                    ID:<span class="status-user_name">-</span>
                </a>
                ·
                <a class="status-link status-link-post" href="">
                    <span class="status-time">-</span>
                </a>
        <div class="edited" style="display: inline;">
        <a class="status-edited">（編集済み）</a>
        </div>
        <div class="editer" style="display: inline;">
        <a class="status-editer" href="">[編集]</a>
        </div>
            </div>

            <div class="status-content-wrapper">
                <a class="status-link status-link-post" href="" draggable="false">
                    <div class="status-content">-</div>
                    <div class="status-media mt-3"></div>
                </a>
                <div class="post-buttons" style="text-align: right;">
                    <button type="button" class="status-button status-button-off status-like-button" data-post-id="" onclick="react.Post.like(this);"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M458.4 64.3C400.6 15.7 311.3 23 256 79.3 200.7 23 111.4 15.6 53.6 64.3-21.6 127.6-10.6 230.8 43 285.5l175.4 178.7c10 10.2 23.4 15.9 37.6 15.9 14.3 0 27.6-5.6 37.6-15.8L469 285.6c53.5-54.7 64.7-157.9-10.6-221.3zm-23.6 187.5L259.4 430.5c-2.4 2.4-4.4 2.4-6.8 0L77.2 251.8c-36.5-37.2-43.9-107.6 7.3-150.7 38.9-32.7 98.9-27.8 136.5 10.5l35 35.7 35-35.7c37.8-38.5 97.8-43.2 136.5-10.6 51.1 43.1 43.5 113.9 7.3 150.8z"/></svg><span class="status-like-button-value">-</span></button>
                </div>
            </div>
        </div>

        <a class="status-link-user" href="" target="_blank">
            <img class="status-image" src="<?php echo $base_url; ?>/images/blank.png">
        </a>
    </div>
</div>
</div>
</div>
<script src="/js/user_posts.js?<?php echo date('YmdH'); ?>"></script>
<script>
        $(function() {
            $("#refreshButton").on("click", function() {
                react.Post.getUserPosts(true);
            });
            react.Post.getUserPosts(false);
        });
    </script>
                </div>
</div>
</div>
