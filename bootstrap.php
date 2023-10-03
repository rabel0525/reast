<?php

require 'core/ClassLoader.php';

$loader = new ClassLoader();
// オートロードの対象ディレクトリの登録
$loader->registerDir(dirname(__FILE__).'/core');
$loader->registerDir(dirname(__FILE__).'/models');
// オートローダーの起動
$loader->register();