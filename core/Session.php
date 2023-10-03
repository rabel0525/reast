<?php

/**
 * セッション・クッキー管理
 */
class Session
{
    /**
     * セッションの開始状態
     */
    protected static $sessionStarted = false;
    /**
     * セッションIDの再発行状態
     */
    protected static $sessionIdRegenerated = false;

    /**
     * コンストラクタ
     * セッションを開始
     */
    public function __construct()
    {
        if (!self::$sessionStarted) {
            session_start();

            self::$sessionStarted = true;
        }
    }

    /**
     * セッションに値を設定
     * 
     * @param string $name 設定する名前
     * @param mixed $value 設定する値
     */
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * セッションから値を取得
     * 
     * @param string $name 取得する名前
     * @param mixed $default セッションにない場合の取得値
     * @return mixed 取得値
     */
    public function get($name, $default = null)
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }

        return $default;
    }

    /**
     * セッションから値を削除
     * 
     * @param string $name 削除する名前
     */
    public function remove($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * セッションを空にする
     */
    public function clear()
    {
        $_SESSION = array();
    }

    /**
     * セッションIDを再生成
     * ※1度のリクエスト中に複数回呼び出されないように状態を管理している
     * 
     * @param boolean $destroy true : 古いセッションを削除
     */
    public function regenerate($destroy = true)
    {
        if (!self::$sessionIdRegenerated) {
            session_regenerate_id($destroy);

            self::$sessionIdRegenerated = true;
        }
    }

    /**
     * 認証状態を設定
     * 
     * @param boolean $bool 設定する認証状態
     */
    public function setAuthenticated($bool)
    {
        $this->set('_authenticated', (bool)$bool);

        $this->regenerate();
    }

    /**
     * 認証済みか判定
     * 
     * @return boolean
     */
    public function isAuthenticated()
    {
        return $this->get('_authenticated', false);
    }
}