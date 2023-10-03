<div class="button"><span></span><span></span><span></span></div>
    <div class="sidenav">
    <div class="user_card">
        <div class="user_profile">
            <div class="user_image">
            <img src="<?php echo $base_url; ?>/profileimages/<?php echo $this->escape($profile_image["0"]["profile_image"]); ?>"/>
            </div>
            <div style="margin: 10px;">
            <div class="screen_name">
            <?php echo $this->escape($screen_name); ?>
            </div>
            <div class="user_name">
            ID: <span id="user_name" name="<?php echo $this->escape($user['user_name']); ?>"><?php echo $this->escape($user['user_name']); ?></span>
            </div>
            </div>
        </div> 
                <div class="user_detail">
                        <div class="user_detail_c">
                            <p>フォロー中</p><span><?php echo count($followings); ?><span>
                        </div>
                        <div class="user_detail_c">
                            <p>フォロワー</p><span><?php echo count($followed); ?><span>
                        </div>
                        <div class="user_detail_c">
                            <p>投稿数</p><span><?php  echo ($all_posts[0]["count"]); ?><span>
                        </div>
                </div>
</div>
<div class="todo">
<h3 style="text-align: center;">機能開発中 ver.1.29β+α(9/18)</h3>
<p>やるべきことリスト↓</p>
<ul>
    <li>いいね機能/コメント機能</li>
    <li>ミュート/ブロック機能</li>
    <li>多数決⇒話し合いによる自動BAN機能</li>
    <li>DM・メンションの通知</li>
    <li>youtubeのリンクが貼られたら動画再生のフィールドを追加する</li>
</ul>
<p>他欲しい機能とバグがあれば、Discordまたはプロフィールにあるご要望まで。</p>
</div>

<div class="footer">
        <p style="
    color: #b7b7b7;
    text-align: center;
">©2023 Reast Powered By Rabel0525</p>
    </div>

</div>