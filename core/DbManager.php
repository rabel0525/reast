<?php

/**
 * データベース接続情報を管理
 */
class DbManager
{
    /**
     * データベースへのPDOインスタンス
     */
    protected $connections = array();
    /**
     * リポジトリ毎の接続を保存
     */
    protected $repository_connection_map = array();
    /**
     * データ操作リポジトリ
     */
    protected $repositories = array();
    /**
     * データベースに接続
     * 
     * @param string 接続を特定する名前
     * @param array 接続するための情報
     */
    public function connect($name, $params)
    {
        // DB接続に必要なキー項目を設定
        $params = array_merge(array(
            'dsn'      => null,
            'user'     => '',
            'password' => '',
            'options'  => array(),
        ), $params);

        // PDOインスタンスを作成し接続
        $con = new PDO(
            $params['dsn'],
            $params['user'],
            $params['password'],
            $params['options']
        );

        // エラー発生時は例外で処理
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // 接続を保存
        $this->connections[$name] = $con;
    }

    /**
     * 名前でデータベース接続インスタンスを取得
     * 
     * @param string $name 接続を特定する名前
     * @return PDO インスタンス
     */
    public function getConnection($name = null)
    {
        if (is_null($name)) {
            // 名前が指定されながった場合、先頭を返す
            return current($this->connections);
        }

        return $this->connections[$name];
    }

    /**
     * リポジトリ毎の接続を設定

     * @param string $repository_name リポジトリ名
     * @param string $name 接続名
     */
    public function setRepositoryConnectionmap($repository_name, $name)
    {
        $this->repository_connection_map[$repository_name] = $name;
    }

    /**
     * リポジトリ名から接続を取得
     * @param string $repository_name リポジトリ名
     * @return PDO インスタンス
     */
    public function getConnectionForRepository($repository_name)
    {
        if (isset($this->repository_connection_map[$repository_name])) {
            $name = $this->repository_connection_map[$repository_name];
            $con = $this->getConnection($name);
        } else {
            $con = $this->getConnection();
        }

        return $con;
    }

    /**
     * リポジトリを取得
     * 
     * @param string $repository_name リポジトリ名
     * @return DbRepository リポジトリ（データ操作インスタンス）
     */
    public function get($repository_name)
    {
        if (!isset($this->repositories[$repository_name])) {
            // リポジトリのクラス名
            $repository_class = $repository_name . 'Repository';
            // 接続しているPDOインスタンス取得
            $con = $this->getConnectionForRepository($repository_name);

            // リポジトリクラスのインスタンス作成
            $repository = new $repository_class($con);

            // インスタンスを保存
            $this->repositories[$repository_name] = $repository;
        }

        return $this->repositories[$repository_name];
    }

    /**
     * デストラクタ
     * DBリポジトリとDB接続を破棄
     */
    public function __destruct()
    {
        // DBリポジトリを破棄
        foreach ($this->repositories as $repository) {
            unset($repository);
        }
        // PDOの接続を閉じる
        foreach ($this->connections as $son) {
            unset($con);
        }
    }
}