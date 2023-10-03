<?php $this->setLayoutVar('title', 'ユーザー一覧') ?>
<div class="wrap">
<?php if($session->isAuthenticated()): ?>
    <?php echo $this->render('sidenav', array('user' => $user, 
                                        'screen_name' => $screen_name, 
                                        'profile_image' => $profile_image, 
                                        'followings' => $followings,
                                        'followed' => $followed,
                                        'all_posts' => $all_posts)); ?>
            <?php else: ?>
                <div class="button"><span></span><span></span><span></span></div>
                        <div class="sidenav">
                        <div class="user_card">
                             <p>Reastは超軽量で動作するSNSです。</p>
                             <p>是非、Reastに参加しましょう！！</p>
                             <p>登録は下からできます。</p>
                             <p>
    <a href="/account/signup">新規ユーザ登録</a>
</p>
            </div>
                        </div>
            <?php endif; ?>

<div class="contents">
<h2>ユーザー一覧</h2>
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
<div class="profile">
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
<script src="/js/get_all_users.js"></script>
<script>
        $(function() {
            $("#refreshButton").on("click", function() {
                react.Post.get_all_users(true);
            });
            react.Post.get_all_users(false);
        });
    </script>
                </div>
</div>
</div>
</div>