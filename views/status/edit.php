<?php $this->setLayoutVar('title', '投稿の編集') ?>
<div class="wrap">
<?php echo $this->render('sidenav', array('user' => $user, 
                                        'screen_name' => $screen_name, 
                                        'profile_image' => $profile_image, 
                                        'followings' => $followings,
                                        'followed' => $followed,
                                        'all_posts' => $all_posts)); ?>

<div class="contents">
<?php if (isset($errors) && count($errors) > 0): ?>
    <?php echo $this->render('errors', array('errors' => $errors)); ?>
<?php endif; ?>
<?php $user2 = $this->escape($status['user_id']); if($user['id'] == $user2):?>
    <form action="<?php echo $base_url; ?>/status/edit_complete" method="post" enctype="multipart/form-data">
<div class="status-wrap" style="border-bottom:none;">
        <div class="status-main">
            <a class="status-screen-name" href="<?php echo $base_url; ?>/user/<?php echo $this->escape($status['user_name']); ?>">
                <div class="status-user-name">
                    <span class="status-user-name-value"><?php echo $this->escape($status['screen_name']); ?></span>
                    <span class="status-user-badge"><i class="fas fa-check-circle"></i></span>
                    <span class="status-user-locked"><i class="fas fa-lock"></i></span>
                </div>
            </a>
            <div class="status-infos">
                <a class="status-link-user" href="<?php echo $base_url; ?>/user/<?php echo $this->escape($status['user_name']); ?>">
                    ID:<span class="status-user_name"><?php echo $this->escape($status['user_name']); ?></span>
                </a>
                ·
                <a class="status-link status-link-post" href="#">
                    <span class="status-time"><?php echo $this->escape($status['created_at']); ?></span>
                </a>
                <?php if($status['edited'] == 'TRUE'): ?>
        <div class="edited" style="display: inline;">
        <a class="status-edited">（編集済み）</a>
        </div>
        <?php endif; ?>
        <?php 
    $user2 = $this->escape($status['user_id']);
    if($user['id'] == $user2):?>
        <div class="editer" style="display: inline;">
        <a class="status-editer" href="<?php echo $base_url; ?>/user/<?php echo $this->escape($status['user_name']);?>/status/<?php echo $this->escape($status['id']); ?>/editer">[編集]</a>
        </div>
        <?php endif; ?>
            </div>

            <div class="status-content-wrapper">
                <a class="status-link status-link-post" href="#" draggable="false">
                    <div class="status-content"> <textarea id="comment" name="body" cols="30" rows="10" class="post_area"><?php echo $this->escape_edit($status['body']); ?></textarea></div>
                    <?php if($status['image_path']): ?>
                    <div class="status-media mt-3" style="text-align: center;">
                    <?php $image_array= explode(PHP_EOL, $status['image_path']);
            foreach($image_array as $no => $img): ?>
            <img src="<?php echo $base_url; ?>/images/<?php echo $this->escape($img); ?>" class="status-media-image" style="width: 47%;">
            <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </a>
            </div>
        </div>

        <a class="status-link-user" href="" target="_blank">
            <img class="status-image" src="<?php echo $base_url; ?>/profileimages/<?php echo $this->escape($status['profile_image']);?>">
        </a>
        <div class="edit_submit">
    <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>">
    <input type="hidden" name="status_id" value="<?php echo $this->escape($status['id']); ?>">
    <input type="hidden" name="user_name" value="<?php echo $this->escape($status['user_name']); ?>">
    <input type="submit" value="完了" class="submit_blue">
        </div>
    </form>
        <?php endif; ?>
        <script src="/js/menu_button.js"></script>

</div>