<?php

/**
 * データベースのユーザ操作クラス
 */
class UserRepository extends DbRepository
{
    /**
     * DBにユーザを仮登録
     * 
     * @param string $user_name ユーザ名
     * @param string $password パスワード
     */
    public function tmp_insert($email, $register_token)
    {
        $now = new DateTime();

        $sql = "INSERT INTO user(email, register_token, register_token_sent_at) VALUES(:email, :register_token, :register_token_sent_at)";

        $stmt = $this->execute($sql, array(
            ':email'  => $email,
            ':register_token'   => $register_token,
            ':register_token_sent_at' => $now->format('Y-m-d H:i:s'),
        ));
    }

    /**
     * DBにユーザを仮登録(有効期限切れの場合)
     * 
     * @param string $user_name ユーザ名
     * @param string $password パスワード
     */
    public function tmp_insert_for_forget($email, $register_token)
    {

        $now = new DateTime();

        $sql = "UPDATE user SET register_token = :register_token, register_token_sent_at = :register_token_sent_at WHERE email = :email";

        $stmt = $this->execute($sql, array(
            ':email'  => $email,
            ':register_token'   => $register_token,
            ':register_token_sent_at' => $now->format('Y-m-d H:i:s'),
        ));
    }
    /**
     * ユーザのregistertokenを変更
     * 
     * @param string $user_name ユーザ名
     * @param string $password パスワード
     */
    public function registertoken_update($id, $register_token, $email)
    {

        $now = new DateTime();

        $sql = "UPDATE user SET tmp_email = :tmp_email, register_token = :register_token, register_token_sent_at = :register_token_sent_at WHERE id = :id";

        $stmt = $this->execute($sql, array(
            ':id'  => $id,
            ':tmp_email'  => $email,
            ':register_token'   => $register_token,
            ':register_token_sent_at' => $now->format('Y-m-d H:i:s'),
        ));
    }

    /**
     * ユーザのメールアドレスを変更
     * 
     * @param string $user_name ユーザ名
     * @param string $password パスワード
     */
    public function email_update($email, $register_token)
    {

        $now = new DateTime();

        $sql = "UPDATE user SET email = :email, 
                                tmp_email = NULL, 
                                register_token = NULL, 
                                register_token_sent_at = NULL 
                                WHERE register_token = :register_token";

        $stmt = $this->execute($sql, array(
            ':email'  => $email,
            ':register_token'   => $register_token,
        ));
    }

    /**
     * remember_tokenをセットする
     * 
     * @param string $user_name ユーザ名
     * @param string $password パスワード
     */
    public function remember_me($user_name, $remember_token)
    {

        $now = new DateTime();

        $sql = "UPDATE user SET remember_token = :remember_token
                                WHERE user_name = :user_name";

        $stmt = $this->execute($sql, array(
            ':user_name'  => $user_name,
            ':remember_token'   => $remember_token,
        ));
    }

    /**
     * Tokenからメール変更用のメールアドレスを取得
     * 
     * @param string $user_name 検索ユーザ名
     * @return array
     */
    public function fetchTmp_EmailbyToken($token)
    {
        $sql = "SELECT tmp_email FROM user WHERE register_token = :register_token";

        return $this->fetch($sql, array(':register_token' => $token));
    }

    /**
     * DBにユーザを新規登録
     * 
     * @param string $screen_name 表示名
     * @param string $user_name ユーザ名
     * @param string $password パスワード
     */
    public function insert($screen_name,$user_name, $password, $register_token)
    {
        // パスワードはハッシュ化してDBに登録
        $password = $this->hashPassword($password);
        $now = new DateTime();

        $sql = "UPDATE user SET 
                screen_name = :screen_name, user_name = :user_name, 
                password = :password, created_at = :created_at, 
                status = 'public', register_token = NULL, 
                register_token_sent_at = NULL
                WHERE register_token = :register_token;";

        $stmt = $this->execute($sql, array(
            ':screen_name'  => $screen_name,
            ':user_name'  => $user_name,
            ':password'   => $password,
            ':register_token'   => $register_token,
            ':created_at' => $now->format('Y-m-d H:i:s'),
        ));
    }

    /**
     * アカウントをDBから削除
     * 
     * @param string $user_id ユーザID
     * @param string $body 投稿内容
     * @param string $image_path 画像までのpath
     */
    public function delete_account($user_id)
    {
        $now = new DateTime();

        $sql = "DELETE FROM user WHERE id = :user_id;";

        $stmt = $this->execute($sql, array(
            ':user_id'    => $user_id,
        ));
    }

    /**
     * パスワード更新
     * 
     * @param string $user_name ユーザ名
     * @param string $password パスワード
     */
    public function update_password($user_id, $password)
    {
        // パスワードはハッシュ化してDBに登録
        $password = $this->hashPassword($password);

        $sql = "UPDATE user 
        SET password = :password
        WHERE id = :user_id; ";

        $stmt = $this->execute($sql, array(
            ':user_id'  => $user_id,
            ':password'   => $password,
        ));
    }
    /**
     * ユーザーID更新
     * 
     * @param string $user_name ユーザ名
     * @param string $password パスワード
     */
    public function update_user_name($user_id, $user_name)
    {
        $sql = "UPDATE user 
        SET user_name = :user_name
        WHERE id = :user_id; ";

        $stmt = $this->execute($sql, array(
            ':user_id'  => $user_id,
            ':user_name'   => $user_name,
        ));
    }

    /**
     * パスワードのハッシュを作る
     * 
     * @param string $password
     * @return string
     */
    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * パスワードのハッシュを検証する
     * 
     * @param string $password
     * @param string $hash
     * @return boolean
     */
    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * ユーザ名からユーザ情報を取得
     * 
     * @param string $user_name 検索ユーザ名
     * @return array
     */
    public function fetchByUserName($user_name)
    {
        $sql = "SELECT id, user_name, screen_name, password, introduce, bg_image, created_at FROM user WHERE user_name = :user_name";

        return $this->fetch($sql, array(':user_name' => $user_name));
    }
    /**
     * ユーザ名からユーザ情報を取得
     * 
     * @param string $user_name 検索ユーザ名
     * @return array
     */
    public function fetchforSessionByUserName($user_name)
    {
        $sql = "SELECT id, user_name, bg_image FROM user WHERE user_name = :user_name";

        return $this->fetch($sql, array(':user_name' => $user_name));
    }
    /**
     * ユーザ名からユーザ情報を取得
     * 
     * @param string $user_name 検索ユーザ名
     * @return array
     */
    public function fetchforSessionByToken($token)
    {
        $sql = "SELECT id, user_name, bg_image FROM user WHERE remember_token = :token";

        return $this->fetch($sql, array(':token' => $token));
    }
    /**
     * ユーザ名からユーザ情報を取得
     * 
     * @param string $user_name 検索ユーザ名
     * @return array
     */
    public function token_deleteByUserID($id)
    {
        $sql = "UPDATE user SET remember_token = NULL WHERE id = :id";

        return $this->fetch($sql, array(':id' => $id));
    }
    /**
     * ユーザ名からユーザ情報を取得
     * 
     * @param string $user_name 検索ユーザ名
     * @return array
     */
    public function update_bg($id, $image)
    {
        $sql = "UPDATE user SET bg_image = :image WHERE id = :id";

        return $this->fetch($sql, array(':id' => $id, ':image' => $image));
    }
    /**
     * ユーザIDからユーザ情報を取得
     * 
     * @param string $user_name 検索ユーザ名
     * @return array
     */
    public function fetchByUserID($user_id)
    {
        $sql = "SELECT id, user_name, screen_name, password, introduce, created_at FROM user WHERE id = :user_id";

        return $this->fetch($sql, array(':user_id' => $user_id));
    }

    /**
     * 既に同じユーザ名が登録済みか判定
     * 
     * @param string $user_name 検索ユーザ名
     * @return bool
     */
    public function isUniqueUserName($user_name)
    {
        $sql = "SELECT COUNT(id) as count FROM user WHERE user_name = :user_name";

        $row = $this->fetch($sql, array(':user_name' => $user_name));
        if ($row['count'] == 0) {
            return true;
        }

        return false;
    }

    /**
     * 既に同じemailが登録済みか判定
     * 
     * @param string $user_name 検索ユーザ名
     * @return bool
     */
    public function isTentativeEmail($email)
    {
        $sql = "SELECT COUNT(status = 'tentative') as count FROM user WHERE email =:email";

        $row = $this->fetch($sql, array(':email' => $email));
        if ($row['count'] == 0) {
            return false;
        }

        return true;
    }

     /**
     * 既に登録済みか判定
     * 
     * @param string $user_name 検索ユーザ名
     * @return bool
     */
    public function isPublicEmail($email)
    {
        $sql = "SELECT count(status) as count FROM user WHERE email =:email AND status = 'public'";

        $row = $this->fetch($sql, array(':email' => $email));
        if ($row['count'] == 0) {
            return false;
        }

        return true;
    }

     /**
     * 既に登録tokenがあるか判定
     * 
     * @param string $user_name 検索ユーザ名
     * @return bool
     */
    public function fetchEmailbyToken($register_token)
    {
        $sql = "SELECT count(email) as count FROM user WHERE register_token =:register_token";

        $row = $this->fetch($sql, array(':register_token' => $register_token));
        if ($row['count'] == 0) {
            return false;
        }

        return true;
    }

    /**
     * 登録されたtokenから仮登録日時を判定
     * 
     * @param string $user_name 検索ユーザ名
     * @return bool
     */
    public function fetchTokenSentAtbyToken($register_token)
    {
        $sql = "SELECT register_token_sent_at FROM user WHERE register_token =:register_token";

        return $this->fetch($sql, array(':register_token' => $register_token));
    }

    /**
     * メアドからユーザーidを取得
     * 
     * @param string $user_name 検索ユーザ名
     * @return bool
     */
    public function fetchUserIDbyEmail($email)
    {
        $sql = "SELECT id, user_name FROM user WHERE email =:email";

        return $this->fetch($sql, array(':email' => $email));
    }

    /**
     * フォローしているユーザを取得
     * 
     * @param string $user_id
     * @return array
     */
    public function fetchAllFollowingsByUserId($user_id)
    {
        $sql = "SELECT u.id, u.user_name, u.password
                FROM user u
                    LEFT JOIN following f ON f.following_id = u.id
                WHERE f.user_id = :user_id;";

        return $this->fetchAll($sql, array(':user_id' => $user_id));
    }

    /**
     * フォローされているユーザを取得
     * 
     * @param string $user_id
     * @return array
     */
    public function fetchAllFollowedByUserId($following_id)
    {
        $sql = "SELECT u.id, u.user_name, u.password
                FROM user u
                    LEFT JOIN following f ON f.user_id = u.id
                WHERE f.following_id = :user_id;";

        return $this->fetchAll($sql, array(':user_id' => $following_id));
    }
     /**
     * ユーザーのすべての投稿数を取得
     * 
     * @param string $user_id
     * @return array
     */
    public function fetchAllPostsByUserId($following_id)
    {
        $sql = "SELECT COUNT(a.user_id) as count
        FROM status a
            LEFT JOIN user u ON a.user_id = u.id
        WHERE u.id = :user_id
        ORDER BY a.created_at DESC;";

        return $this->fetchAll($sql, array(':user_id' => $following_id));
    }
/**
     * フォローしているユーザー一覧を取得api
     * 
     * @param string $user_id
     * @return array
     */
    public function fetchAllFollowingUserByUserId($following_id, $page)
    {
        $page = ($page-1)*10;
        $sql = "select u.id, u.user_name, u.screen_name, u.introduce,u.profile_image, u.created_at, following, followers, all_posts from 
        (
        SELECT u.id, u.user_name ,count(following_id) as followers
                        FROM user u
                            left outer JOIN following f ON f.following_id = u.id 
                        GROUP BY
            `id`
        ) ff
        left join user u on u.id=ff.id
        
        left join 
        (
        SELECT u.user_name ,count(user_id) as following
                        FROM user u
                            left JOIN following f ON f.user_id = u.id 
                        GROUP BY
            `id`
        ) ff2 on u.user_name=ff2.user_name
        
        inner join(
select user.id, count(status.user_id) as all_posts 
from user left outer join status on user.id = status.user_id 
left join following f ON f.following_id = status.user_id AND f.user_id = :user_id WHERE f.user_id = :user_id
group by user.id
        ) ap on u.id=ap.id
		ORDER BY created_at DESC
        limit :page,10;";

        return $this->fetchAll($sql, array(':user_id' => $following_id, ':page' => $page));
    }

    /**
     * フォローされているユーザー一覧を取得api
     * 
     * @param string $user_id
     * @return array
     */
    public function fetchAllFollowersUserByUserId($following_id, $page)
    {
        $page = ($page-1)*10;
        $sql = "select u.id, u.user_name, u.screen_name,u.profile_image, u.introduce, u.created_at, following, followers, all_posts from 
        (
        SELECT u.id, u.user_name ,count(following_id) as followers
                        FROM user u
                            left outer JOIN following f ON f.following_id = u.id 
                        GROUP BY
            `id`
        ) ff
        left join user u on u.id=ff.id
        
        left join 
        (
        SELECT u.user_name ,count(user_id) as following
                        FROM user u
                            left JOIN following f ON f.user_id = u.id 
                        GROUP BY
            `id`
        ) ff2 on u.user_name=ff2.user_name
        
        inner join(
			select user.id, count(status.user_id) as all_posts 
from user left outer join status on user.id = status.user_id 
left join following f ON f.user_id = user.id 
WHERE f.following_id = :user_id
group by user.id
        ) ap on u.id=ap.id
		ORDER BY created_at DESC
        limit :page,10;";

        return $this->fetchAll($sql, array(':user_id' => $following_id, ':page' => $page));
    }


    /**
     * 全ユーザを取得
     * 
     * @param string
     * @return array
     */
    public function all($page){
        $page = ($page-1)*10;
        $sql = "select u.id, u.user_name, u.screen_name,u.profile_image, u.introduce, u.created_at, following, followers, all_posts from 
        (
        SELECT u.id, u.user_name ,count(following_id) as followers
                        FROM user u
                            left outer JOIN following f ON f.following_id = u.id 
						Where u.user_name IS NOT NULL
                        GROUP BY
            `id`
        ) ff
        left join user u on u.id=ff.id and status='public'
        
        left join 
        (
        SELECT u.user_name ,count(user_id) as following
                        FROM user u
                            left JOIN following f ON f.user_id = u.id 
                        GROUP BY
            `id`
        ) ff2 on u.user_name=ff2.user_name
        
        left join(
			select user.id , count(user_id) as all_posts from user left outer join status on user.id = status.user_id Group by user_name
            ) ap on u.id=ap.id
		ORDER BY created_at DESC
        LIMIT :page,10;";
        return $this->fetchAll($sql, array(':page' => $page));
    }

    /**
     * ユーザ情報を取得
     * 
     * @param string
     * @return array
     */
    public function user_info($user_name){
        $sql = "SELECT u.id, u.user_name, u.password
                FROM user u
                    LEFT JOIN following f ON f.user_id = u.id
                WHERE f.following_id = :user_id;";

        return $this->fetchAll($sql);
    }

    /**
     * プロフィール画像をDBに登録
     * 
     * @param string $user_id ユーザID
     * @param string $profile_image 画像のパス
     */
    public function pimage_insert($user_id, $profile_image)
    {
        $sql = "UPDATE user 
        SET profile_image = :profile_image
        WHERE id = :user_id; ";

        $stmt = $this->execute($sql, array(
            ':user_id' => $user_id,
            ':profile_image' => $profile_image
        ));
    }

    /**
     * プロフィールを更新
     * 
     * @param string $user_id ユーザID
     * @param string $profile_image 画像のパス
     */
    public function profile_update($user_id, $introduce, $screen_name)
    {
        $sql = "UPDATE user 
        SET introduce = :introduce , screen_name =:screen_name
        WHERE id = :user_id; ";

        $stmt = $this->execute($sql, array(
            ':user_id' => $user_id,
            ':screen_name' => $screen_name,
            ':introduce' => $introduce
        ));
    }
   
    /**
     * 所定日付のフォーマット
     * 
     * @param string $user_id ユーザID
     * @return string 
     */
    public function date_formater($mode, $date)
    {
        if($mode == '1'){
            return date('Y年m月d日',  strtotime($date));
        }
        if($mode == '2'){
            return date('Y年m月',  strtotime($date));
        }
        if($mode == '3'){
            return date('Y年m月d日 H時i分s秒',  strtotime($date));
        }
        
    }

    /**
     * 所定日付のフォーマット
     * 
     * @param string $user_id ユーザID
     * @return string 
     */
    public function fetchProfileImage($user_id)
    {
        $sql = "SELECT profile_image FROM user 
        WHERE id = :user_id; ";

        return $this->fetchAll($sql, array(':user_id' => $user_id));
    }

        
    
}