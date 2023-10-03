<?php

/**
 * フォロー
 */
class FollowingRepository extends DbRepository
{
    /**
     * フォローをDBに登録
     * 
     * @param string $user_id ユーザID
     * @param string $following_id フォローユーザID
     */
    public function insert($user_id, $following_id)
    {
        $now = new DateTime();

        $sql = "INSERT INTO following (user_id, following_id, created_at)
                VALUES (:user_id, :following_id, :created_at);";

        $stmt = $this->execute($sql, array(
            ':user_id'      => $user_id,
            ':following_id' => $following_id,
            ':created_at'   => $now->format('Y-m-d H:i:s'),
        ));
    }

    /**
     * フォローを解除
     * 
     * @param string $user_id ユーザID
     * @param string $following_id フォローユーザID
     */
    public function outsert($user_id, $following_id)
    {
        $now = new DateTime();

        $sql = "DELETE FROM following where user_id = :user_id and following_id = :following_id;";

        $stmt = $this->execute($sql, array(
            ':user_id'      => $user_id,
            ':following_id' => $following_id,
        ));
    }

    /**
     * フォローしているか確認
     * 
     * @param string $user_id ユーザID
     * @param string $following_id フォローユーザID
     * @return boolean
     */
    public function isFollowing($user_id, $following_id)
    {
        $sql = "SELECT COUNT(user_id) as count
                FROM following
                WHERE user_id = :user_id
                  AND following_id = :following_id;";

        $row = $this->fetch($sql, array(
            ':user_id'      => $user_id,
            ':following_id' => $following_id,
        ));

        if ($row['count'] != 0) {
            return true;
        }

        return false;
    }

    /**
     * フォローされているか確認
     * 
     * @param string $user_id ユーザID
     * @param string $following_id フォローユーザID
     * @return boolean
     */
    public function isFollower($following_id, $user_id)
    {
        $sql = "SELECT COUNT(user_id) as count
                FROM following
                WHERE user_id = :user_id
                  AND following_id = :following_id;";

        $row = $this->fetch($sql, array(
            ':user_id'      => $user_id,
            ':following_id' => $following_id,
        ));

        if ($row['count'] != 0) {
            return true;
        }

        return false;
    }
}
