<?php $this->setLayoutVar('title', 'ホーム') ?>
<div class="wrap">
<?php echo $this->render('sidenav', array('user' => $user, 
                                        'screen_name' => $screen_name, 
                                        'profile_image' => $profile_image, 
                                        'followings' => $followings,
                                        'followed' => $followed,
                                        'all_posts' => $all_posts)); ?>

<div class="contents">
<form action="<?php echo $base_url; ?>/status/post" method="post" enctype="multipart/form-data" style="
    border-bottom: solid 1px #eee;
">
    <input type="hidden" name="_token" value="<?php echo $this->escape($_token); ?>">

    <?php if (isset($errors) && count($errors) > 0): ?>
    <?php echo $this->render('errors', array('errors' => $errors)); ?>
    <?php endif; ?>

    <div style="display:block;"><textarea name="body" rows="5" cols="60" placeholder="今、なにしてる？" class="post_area"><?php echo $this->escape_edit($body); ?></textarea></div>
    <div class="post_button">
    UPする画像:
    <input
  type="file"
  id="fileElem"
  multiple
  accept="image/*"
  style="display:none" name="upload_image[]" />
<button id="fileSelect" type="button">選択</button>
    <input type="submit" value="つぶやく" style="
    padding: 0.46rem 1.6rem;
    background-color: #3396e8;
    color: rgb(255 255 255);
    border-radius: 0.25rem;
    cursor: pointer;
    border: 1px solid rgb(117 124 135);
    line-height: 1;
">
</div>
<ul id="uplist">
</ul>
</form>

<h2>ホーム</h2>
<div id="statues">

<div class="container-left">

<div id="posts"></div>
<div class="loading">
<img id="loadingAnimation" src="/images/loading.gif">
<span id ="loaded"></span>
</div>
<div style="margin-bottom:60px;"></div>
<div id="postBaseElemWrap" style="display: none;">
    <div class="status-wrap">
        <div class="status-main">
            <a class="status-screen-name" href="">
                <div class="status-user-name">
                    <span class="status-user-name-value">-</span>
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
<script src="/js/script.js?<?php echo date('YmdH'); ?>"></script>
<script src="https://cdn.jsdelivr.net/gh/fengyuanchen/compressorjs/dist/compressor.min.js"></script>
<script>
        $(function() {
            $("#refreshButton").on("click", function() {
                react.Post.getTimeline(true);
            });
            react.Post.getTimeline(false);
        });
        document.getElementById('fileElem').addEventListener('change', (e) => {
  const file = e.target.files[0];
  // ボタンクリックで画像変換
  document.getElementById('changeBtn').addEventListener('click', () => {
  // ファイルが存在するかどうか
  if (!file) {
    return;
  }
  console.log('start', file)
  console.time()
  new Compressor(file, {
    // Compressorのオプション
    maxHeight: 1080,
    convertSize: Infinity,
    success(result) {
      console.timeEnd()
      console.log(result)
      const url = URL.createObjectURL(result)
      // imgタグに出力
      document.getElementById('preview').src = url
      console.log(url)
    },
    error(err) {
      // エラー時のメッセージ
      console.log(err.message);
    },
  });
  // imgmタグに「show」のclassを付けて表示
  const fileImg = document.getElementById('preview');
  fileImg.classList.add('show');
});

});
    </script>
                </div>
</div>
</div>
</div>