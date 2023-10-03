<?php $this->setLayoutVar('title', $status['user_name']) ?>
<?php $this->setLayoutVar('title_og', $status['screen_name']. '(ID:'. $status['user_name'] .') | Reast ') ?>
<?php $this->setLayoutVar('user_post', $status['body']) ?>
<?php $this->setLayoutVar('url', 'user/' . $status['user_name'] . '/status/' . $status['id']) ?>
<?php $this->setLayoutVar('type', 'article') ?>
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
                             <p>Reastは超軽量で動作するSNSです。</p>
                             <p>是非、Reastに参加しましょう！！</p>
                             <p>登録は下からできます。</p>
                             <p>
    <a href="/account/signup">新規ユーザ登録</a>
</p>
                        </div>
            <?php endif; ?>

<div class="contents">
<?php echo $this->render('status/status', array('status' => $status, 'user' => $user)); ?>
</div>
<script src="/js/menu_button.js"></script>
            </div>