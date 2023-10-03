<?php

/**
 * データベースを新規作成
 */
function createDatabase() {
    try {
        // データベースを作成
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];
        $pdo = new PDO('sqlite:./blog.db', null, null, $options);

        // ユーザテーブル
        $createUserTable = 'CREATE TABLE user (
            id INTEGER PRIMARY KEY,
            user_name VARCHAR(20) NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TEXT
            );';
        $pdo->exec($createUserTable);

        // フォローテーブル
        $createFollowingTable = 'CREATE TABLE following (
            user_id INTEGER,
            following_id INTEGER,
            created_at TEXT,
            FOREIGN KEY(user_id) REFERENCES user(id),
            FOREIGN KEY(following_id) REFERENCES user(id)
            );';
        $pdo->exec($createFollowingTable);

                // イメージテーブル
                $createImageTable = 'CREATE TABLE Images (
                    id INTEGER PRIMARY KEY,
                    file_name varchar(255) NOT NULL,
                    uploaded_on datetime NOT NULL,
                    status enum(1,0) NOT NULL DEFAULT 1
                    );';
                $pdo->exec($createImageTable);

        // 投稿テーブル
        $createStatusTable = 'CREATE TABLE status (
            id INTEGER PRIMARY KEY,
            user_id INTEGER NOT NULL,
            body TEXT,
            created_at TEXT,
            FOREIGN KEY(user_id) REFERENCES user(id)
            );';
        $pdo->exec($createStatusTable);
    } catch (Exception $e) {
        echo 'データベースの作成・オープンに失敗しました。';
        echo $e->getMessage();
        print_r($pdo->errorInfo());
    }
}

// 実行
createDatabase();
