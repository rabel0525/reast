<?php

/**
 * データベースへのアクセスを行う抽象クラス
 */
abstract class DbRepository
{
    /**
     * PDOインスタンス
     */
    protected $con;

    /**
     * コンストラクタ
     * 
     * @param PDO $con
     */
    public function __construct($con)
    {
        $this->setConnection($con);
    }

    /**
     * 接続を設定
     * 
     * @param PDO $con
     */
    public function setConnection($con)
    {
        $this->con = $con;
    }

    /**
     * クリエリを実行
     * 
     * @param string $sql 実行するクエリ
     * @param array $params クエリに渡すパラメーター
     * @return PDOStatement
     */
    public function execute($sql, $params = array())
    {
        $stmt = $this->con->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    /**
     * クリエリを実行し、1行の結果を取得
     * 
     * @param string $sql 実行するクエリ
     * @param array $params クエリに渡すパラメーター
     * @return array
     */
    public function fetch($sql, $params = array())
    {
        return $this->execute($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * クリエリを実行し、全ての結果を取得
     * 
     * @param string $sql 実行するクエリ
     * @param array $params クエリに渡すパラメーター
     * @return array
     */
    public function fetchAll($sql, $params = array())
    {
        return $this->execute($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
}