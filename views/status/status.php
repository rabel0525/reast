<div class="status-wrap">
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
                
                    <div class="status-content"><?php echo $this->escape_for_body($status['body']); ?></div>
                    <div class="status-media mt-3">
                    <?php if($status['image_path']):?>
                        <a target="_blank" href="<?php echo $base_url; ?>/images/<?php echo $this->escape($status['image_path']);?>">
                            <img src="<?php echo $base_url; ?>/images/<?php echo $this->escape($status['image_path']);?>" class="status-media-image">
                        </a>
                    <?php endif; ?>
                    </div>
                
            </div>
        </div>

        <a class="status-link-user" href="" target="_blank">
            <img class="status-image" src="<?php echo $base_url; ?>/profileimages/<?php echo $this->escape($status['profile_image']);?>">
        </a>