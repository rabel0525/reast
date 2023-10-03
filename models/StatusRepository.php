<?php

/**
 * 投稿内容データ
 */
class StatusRepository extends DbRepository
{
    /**
     * 投稿をDBに登録
     * 
     * @param string $user_id ユーザID
     * @param string $body 投稿内容
     * @param string $image_path 画像までのpath
     */
    public function insert($user_id, $body, $image_path)
    {
        $now = new DateTime();

        $sql = "INSERT INTO status(user_id, body, image_path, created_at)
                VALUES (:user_id, :body, :image_path, :created_at);";

        $stmt = $this->execute($sql, array(
            ':user_id'    => $user_id,
            ':body'       => $body,
            ':image_path' => $image_path,
            ':created_at' => $now->format('Y-m-d H:i:s'),
        ));
    }

    /**
     * 投稿をDBから削除
     * 
     * @param string $user_id ユーザID
     * @param string $body 投稿内容
     * @param string $image_path 画像までのpath
     */
    public function delete($user_id, $status_id)
    {
        $now = new DateTime();

        $sql = "DELETE FROM status WHERE id = :status_id AND user_id = :user_id;";

        $stmt = $this->execute($sql, array(
            ':status_id'  => $status_id,
            ':user_id'    => $user_id,
        ));
    }

    /**
     * 投稿の編集
     * 
     * @param string $user_id ユーザID
     * @param string $body 編集内容
     * @param string $status_id 投稿ID
     */
    public function edit($user_id, $status_id, $body)
    {
        $now = new DateTime();

        $sql = "UPDATE status 
        SET body = :body, edited = 'TRUE' WHERE user_id = :user_id AND id = :status_id;";

        $stmt = $this->execute($sql, array(
            ':status_id'  => $status_id,
            ':body'       => $body,
            ':user_id'    => $user_id,
        ));
    }

    /**
     * 投稿情報の一覧を取得
     * 
     * @param string $user_id
     * @return array
     */
    public function fetchAllPersonalArchivesByUserId($user_id, $page)
    {
        $page = ($page-1)*10;
        $sql = "SELECT a.id, a.user_id, a.body, a.image_path, u.screen_name, likes, is_liked, a.edited, a.created_at, u.user_name, u.profile_image
        FROM status a
            LEFT JOIN user u ON a.user_id = u.id
            left join(
                select status_id , count(user_id) as likes from like_status Group by status_id
                ) ap on a.id=ap.status_id
                left join(
						select status_id , count(user_id) as is_liked from like_status where user_id = :user_id Group by status_id
						) ap1 on a.id=ap1.status_id
                LEFT JOIN following f ON f.following_id = a.user_id
                        AND f.user_id = :user_id
                WHERE f.user_id = :user_id OR u.id = :user_id
                ORDER BY a.created_at DESC
                LIMIT :page,10;";
        
        return $this->fetchAll($sql, array(':user_id' => $user_id, ':page' => $page));
    }

    /**
     * ユーザIDから投稿一覧を取得
     * 
     * @param string $user_id
     * @return array
     */
    public function fetchAllByUserId($user_id, $user_id2, $page)
    {
        $page = ($page-1)*10;
        $sql = "SELECT a.id, a.user_id, a.body, a.image_path, u.screen_name, likes, is_liked, a.edited, a.created_at, u.user_name, u.profile_image
                FROM status a
                    LEFT JOIN user u ON a.user_id = u.id
                    left join(
						select status_id , count(user_id) as likes from like_status Group by status_id
						) ap on a.id=ap.status_id
                    left join(
						select status_id , count(user_id) as is_liked from like_status where user_id = :user_id2 Group by status_id
						) ap1 on a.id=ap1.status_id
                WHERE u.id = :user_id
                ORDER BY a.created_at DESC
                LIMIT :page,10;";

        return $this->fetchAll($sql, array(':user_id' => $user_id, ':user_id2' => $user_id2, ':page' => $page));
    }    

    /**
     * 投稿IDとユーザ名から投稿を取得
     * 
     * @param string $id
     * @param string $user_name
     * @return array
     */
    public function fetchByIdAndUserName($id, $user_name)
    {
        $sql = "SELECT a.id, a.user_id, a.body, a.image_path, a.edited, likes, is_liked, a.created_at, u.user_name, u.profile_image, screen_name
                FROM status a
                    LEFT JOIN user u ON a.user_id = u.id
                    left join(
						select status_id , count(user_id) as likes from like_status Group by status_id
						) ap on a.id=ap.status_id
                    left join(
						select status_id , count(user_id) as is_liked from like_status where user_id = :user_id Group by status_id
						) ap1 on a.id=ap1.status_id
                WHERE a.id = :id
                  AND u.user_name = :user_name;";

        return $this->fetch($sql, array(
            ':id' => $id,
            ':user_name' => $user_name,
        ));
    }    

    /**
     * いいねをDBに登録
     * 
     * @param string $user_id ユーザID
     * @param string $following_id フォローユーザID
     */
    public function like_insert($user_id, $status_id)
    {
        $now = new DateTime();

        $sql = "INSERT INTO like_status (user_id, status_id, created_at)
                VALUES (:user_id, :status_id, :created_at);";

        $stmt = $this->execute($sql, array(
            ':user_id'      => $user_id,
            ':status_id' => $status_id,
            ':created_at'   => $now->format('Y-m-d H:i:s'),
        ));
    }

    /**
     * いいねを解除
     * 
     * @param string $user_id ユーザID
     * @param string $following_id フォローユーザID
     */
    public function like_outsert($user_id, $status_id)
    {
        $now = new DateTime();

        $sql = "DELETE FROM like_status where user_id = :user_id and status_id = :status_id;";

        $stmt = $this->execute($sql, array(
            ':user_id'      => $user_id,
            ':status_id' => $status_id,
        ));
    }

}