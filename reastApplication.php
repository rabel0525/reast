<?php

class reastApplication extends Application
{
    protected $login_action = array('account', 'signin');

    /**
     * ルートディレクトリへのパスを取得
     * 
     * @return string
     */
    public function getRootDir()
    {
        return dirname(__FILE__);
    }

    /**
     * ルーティング定義配列を返す
     * 
     * @return array
     */
    protected function registerRoutes()
    {
        return array(
            '/'
                => array('controller' => 'status', 'action' => 'index'),
            '/timelines'
                => array('controller' => 'api', 'action' => 'more'),
            '/user_posts'
                => array('controller' => 'api', 'action' => 'user_posts'),
            '/get_following_user'
                => array('controller' => 'api', 'action' => 'get_following_user'),
            '/get_followers_user'
                => array('controller' => 'api', 'action' => 'get_followers_user'),
            '/get_all_user'
                => array('controller' => 'api', 'action' => 'get_all_user'),
            '/status/likes'
                => array('controller' => 'api', 'action' => 'likes'),
            '/status/post'
                => array('controller' => 'status', 'action' => 'post'),
            '/user/:user_name/status/:id/editer'
                => array('controller' => 'status', 'action' => 'edit'),
            '/status/edit_complete'
                => array('controller' => 'status', 'action' => 'edited'),
            '/user/:user_name'
                => array('controller' => 'status', 'action' => 'user'),
            '/user/:user_name/following'
                => array('controller' => 'status', 'action' => 'list_following'),
            '/user/:user_name/followers'
                => array('controller' => 'status', 'action' => 'list_follower'),
            '/user/:user_name/status/:id'
                => array('controller' => 'status', 'action' => 'show'),
            '/updates'
            => array('controller' => 'status', 'action' => 'updates'),
            '/profile'
                => array('controller' => 'account', 'action' => 'index'),
            '/account/:action'
                => array('controller' => 'account'),
            '/follow'
                => array('controller' => 'account', 'action' => 'follow'),
            '/unfollow'
                => array('controller' => 'account', 'action' => 'unfollow'),
            '/users'
                => array('controller' => 'account', 'action' => 'list'),
            '/forms'
                => array('controller' => 'account', 'action' => 'forms'),
        );
    }

    /**
     * アプリケーションを設定
     */
    protected function configure()
    {
        // データベースの接続設定
        $this->db_manager->connect('master', array(
            'dsn'      => 'sqlite:../db/blog.db',
            'user'     => null,
            'password' => null
        ));
    }
}