<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta property="og:title" content="<?php if (isset($title_og)): echo $this->escape($title_og);endif; ?>">
    <meta property="og:description" content="<?php if (isset($user_post)): echo $this->escape($user_post) ;endif; ?>">
    <meta property="og:url" content="<?php if (isset($url)): echo 'https://reast.info/'. $this->escape($url);endif; ?>">
    <meta property="og:image" content="<?php if (isset($image_path)): echo $this->escape($image_path) ; else: echo 'https://reast.info/img/emb.png'; endif?>">
    <meta property="og:type" content="<?php if (isset($type)): echo $this->escape($type) ; endif; ?>">
    <meta property="og:site_name" content="Reast">
    <title><?php if (isset($title)): echo $this->escape($title) . ' - ';
        endif; ?>Reast</title>
    <link href="/css/normalize.css" rel="stylesheet" >
    <link href="/css/style.css?<?php echo date('YmdH'); ?>" rel="stylesheet" >
    <link href="https://fonts.googleapis.com/css?family=M+PLUS+Rounded+1c" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans+JP" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=BIZ+UDGothic&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body <?php 
        if($session->isAuthenticated()){
        $user = $session->get('user');
        $background_image="";
        if($user['bg_image']){
            $background_image="style='background-image: url(/images/". $user['bg_image'] .");'";
        }
        echo $background_image; 
    }?>>
<div class="wraper">
    <div id="header">
        <h1><a href="<?php echo $base_url; ?>/"><img src="/img/logo.png"/></a></h1>
    </div>

    <div id="nav">
        <p>
        <?php if($session->isAuthenticated()): ?>
    <a href="<?php echo $base_url ?>">ホーム</a>
    <a href="<?php echo $base_url ?>/profile">プロフィール</a>
    <a href="<?php echo $base_url ?>/users">ユーザー一覧</a>
    <a href="<?php echo $base_url ?>/updates">更新履歴</a>
    <a href="<?php echo $base_url ?>/account/signout">ログアウト</a>
            <?php else: ?>
                <a href="<?php echo $base_url; ?>/account/signin">ログイン</a>
                <a href="<?php echo $base_url; ?>/account/signup">アカウント登録</a>
            <?php endif; ?>
        </p>
    </div>

    <div id="main">
        <?php echo $_content; ?>
    </div>
</div>
</body>
</html>
