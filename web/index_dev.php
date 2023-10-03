<?php

require '../bootstrap.php';
require '../reastApplication.php';

// 開発用エントリポイント
$app = new reastApplication(true);
$app->run();