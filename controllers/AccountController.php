<?php

/**
 * ユーザーアカウント
 */
class AccountController extends Controller
{
    // 認証を必要とするアクション
    protected $auth_actions = array('index', 'signout', 'follow', 'unfollow', 'profileimageupload', 'email_setting', 'validation_email');

    /**
     * アカウント仮登録
     * 
     * @return string 遷移先画面
     */
    public function signupAction()
    {
        $errors = array();
        $error_status = $this->request->getGet('error');
        if($error_status){
            $errors[] = '残念ながら無効なtokenです。もう一度お試しください。';
        }

        return $this->render(array(
            'email' => "",
            'errors' =>  $errors,
            '_token'    => $this->generateCsrfToken('account/tmp_register'),
        ));
    }

    /**
     * パスワードリセット
     * 
     * @return string 遷移先画面
     */
    public function resetAction()
    {
        $errors = array();
        $error_status = $this->request->getGet('error');
        if($error_status){
            $errors[] = '残念ながら無効なtokenです。もう一度お試しください。';
        }

        return $this->render(array(
            'email' => "",
            'errors' =>  $errors,
            '_token'    => $this->generateCsrfToken('account/reset_check'),
        ));
    }
    /**
     * アカウント設定処理アクション５（リセット）
     */
    public function reset_checkAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/reset_check', $token)) {
            return $this->redirect('/account/reset');
        }

        $email = $this->request->getPost('email');
        
        $errors = array();
        $user_repository = $this->db_manager->get('User');
        $user_1 = $user_repository->fetchUserIDbyEmail($email);

                  // 投稿内容の入力チェック（バリデーション）
                  if (!strlen($email)) {
                     $errors[] = 'メールアドレスを入力してください';
                  } else if (!$this->CheckEmailAddress($email)) {
                      $errors[] = '不正なメールアドレスです。';
                  } else if (!$user_1){
                    $errors[] = '不正なメールアドレスです。';
                  }
            

        // データベースに登録し、ホーム画面へ移動
        if (count($errors) === 0) {
            $register_Token = bin2hex(random_bytes(32));

            // URLはご自身の環境に合わせてください
             $url = "https://reast.info/account/reset_password?token={$register_Token}";

             $subject =  '[Reast]パスワード変更のご案内';

             $body = <<<EOD
                        いつもReastをご利用いただきまして誠にありがとうございます。
                        
                        パスワード変更のご依頼を承りました。
                        
                        次のURLにアクセスをして、変更を完了してください。
                        （URLが2行になっている場合は1行にしてください）
                        
                        {$url}
                        
                        ご不明点がございましたら、プロフィールのご要望にてお問い合わせください。
                    EOD;

            // Fromはご自身の環境に合わせてください
             $headers = "From : reast_setting@narikasya.ie-t.net\n";
            // text/htmlを指定し、html形式で送ることも可能
             $headers .= "Content-Type : text/plain";

       
            $isSent = mb_send_mail($email, $subject, $body, $headers);
            if($isSent){
               $this->db_manager->get('User')->registertoken_update($user_1['id'], $register_Token, null);
               $errors[] = '変更用URLをそちらのメールに送信しました。';
            }else{
               $errors[] = '認証メールを送信できませんでした。';
            }
        
            
        } 
    
        return $this->render(array(
            'email' => "",
            '_token'    => $this->generateCsrfToken('account/reset_check'),
            'errors' => $errors,
        ), 'reset');
    }

    /**
     * パスワードリセットフォーム
     * 
     * @return string 遷移先画面
     */
    public function reset_passwordAction()
    {

        $token = $this->request->getGet('token');
        if (!$token) {
            return $this->redirect('/');
        }
        //tokenから検索したメールが見つからない場合の遷移
        if (!$this->db_manager->get('User')->fetchEmailbyToken($token)){
            return $this->redirect('/account/signup?error=1');
        }

        $date = $this->db_manager->get('User')->fetchTokenSentAtbyToken($token);
        $tokenValidPeriod = (new \DateTime())->modify("-1 hour")->format('Y-m-d H:i:s');

        // 仮登録が1時間以上前の場合、有効期限切れとする
        if ($date['register_token_sent_at'] < $tokenValidPeriod){
            return $this->redirect('/account/signup?error=2');
        }

        return $this->render(array(
            'password'  => "",
            '_token'    => $this->generateCsrfToken('account/reset_password_check'),
            'register_token' => $token,
        ));
    }
    
       /**
     * アカウント設定処理アクション1
     */
    public function reset_password_checkAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/reset_password_check', $token)) {
            return $this->redirect('/account/reset_password');
        }

        // 画面に入力された投稿内容を取得
        $email = $this->request->getPost('email');
        $password_2 = $this->request->getPost('password_2');
        
        $errors = array();
        $user_repository = $this->db_manager->get('User');
        $user_1 = $user_repository->fetchUserIDbyEmail($email);

                 // 投稿内容の入力チェック（バリデーション）
                          if (!strlen($email)) {
                            $errors[] = 'メールアドレスを入力してください';
                         } else if (!$this->CheckEmailAddress($email)) {
                             $errors[] = '不正なメールアドレスです。';
                         } else if (!$user_1){
                           $errors[] = '不正なメールアドレスです。';
                         }

                  // 投稿内容の入力チェック（バリデーション）
                  if (!strlen($password_2)) {
                     $errors[] = '新しいパスワードを入力してください';
                  } else if (!preg_match('/\A(?=.*?[a-z])(?=.*?[A-Z])(?=.*?\d)[a-zA-Z\d]{8,30}+\z/',$password_2)) {
                    $errors[] = '新しいパスワードは半角英小文字・大文字、数字をそれぞれ1種類以上含む8文字以上30文字以下にしてください。';
                }
            


        // データベースに登録し、ホーム画面へ移動
        if (count($errors) === 0) {
            $user_repository->update_password($user_1['id'],$password_2);
            return $this->redirect('/');
        } 
    
        return $this->render(array(
            'password' => $password_2,
            '_token'    => $this->generateCsrfToken('account/reset_password_check'),
            'errors' => $errors,
        ), 'reset_password');
    }

    /**
     * アカウント仮登録(メール送信完了)
     * 
     * @return string 遷移先画面
     */
    public function completeAction()
    {
        return $this->render();
    }

    /**
     * アカウント本登録
     * 
     * @return string 遷移先画面
     */
    public function registerAction()
    {

        $token = $this->request->getGet('token');
        if (!$token) {
            return $this->redirect('/');
        }
        //tokenから検索したメールが見つからない場合の遷移
        if (!$this->db_manager->get('User')->fetchEmailbyToken($token)){
            return $this->redirect('/account/signup?error=1');
        }

        $date = $this->db_manager->get('User')->fetchTokenSentAtbyToken($token);
        $tokenValidPeriod = (new \DateTime())->modify("-1 hour")->format('Y-m-d H:i:s');

        // 仮登録が1時間以上前の場合、有効期限切れとする
        if ($date['register_token_sent_at'] < $tokenValidPeriod){
            return $this->redirect('/account/signup?error=2');
        }

        return $this->render(array(
            'user_name' => "",
            'screen_name' => "",
            'password'  => "",
            '_token'    => $this->generateCsrfToken('account/check_register'),
            'register_token' => $token,
        ));
    }

    /**
     * 仮登録の入力チェックを行い、DBに新規登録
     * 
     * @return string 遷移先画面
     */
    public function tmp_registerAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        // CSRFトークンのチェック
        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/tmp_register', $token)) {
            return $this->redirect('/account/tmp_register');
        }

        $email = $this->request->getPost('email');

        $errors = array();

        if (!strlen($email)) {
            $errors[] = 'メールアドレスを入力してください';
            // 正規表現は/で囲む ^先頭　\w半角英数アンダーバー {3,20}3〜20文字 $末尾
        } else if (!$this->CheckEmailAddress($email)) {
            $errors[] = '不正なメールアドレスです。';
        }else if($this->db_manager->get('User')->isPublicEmail($email)){
            $errors[] = '問題が発生しました。もう一度お試しください。';
        }

        // エラーがなければDBへユーザ新規登録
        if (count($errors) === 0) {
            $register_Token = bin2hex(random_bytes(32));

                // URLはご自身の環境に合わせてください
                 $url = "https://reast.info/account/register?token={$register_Token}";

                 $subject =  '[Reast]仮登録が完了しました';

                 $body = <<<EOD
                         Reastへ会員登録していただき、ありがとうございます。

                         1時間以内に下記URLへアクセスし、本登録を完了してください。
                         なお、期限を過ぎると再度仮登録が必要でございますのでご留意ください。
                                                {$url}
                          ※このメールが見覚えがない場合、他者が誤って登録してしまった可能性があります。
                          　その場合にはこのメールは廃棄してください。
                        EOD;

                // Fromはご自身の環境に合わせてください
                 $headers = "From : register@narikasya.ie-t.net\n";
                // text/htmlを指定し、html形式で送ることも可能
                 $headers .= "Content-Type : text/plain";

            if ($this->db_manager->get('User')->isTentativeEmail($email)) {
                $isSent = mb_send_mail($email, $subject, $body, $headers);
                if($isSent){
                   $this->db_manager->get('User')->tmp_insert_for_forget($email, $register_Token);
                   return $this->redirect('/account/complete');
                }else{
                   $errors[] = '認証メールを送信できませんでした。';
                }
            }else{
                 $isSent = mb_send_mail($email, $subject, $body, $headers);
                 if($isSent){
                    $this->db_manager->get('User')->tmp_insert($email, $register_Token);
                    return $this->redirect('/account/complete');
                 }else{
                    $errors[] = '認証メールを送信できませんでした。';
                 }
                }
        }

        return $this->render(array(
            'email' => $email,
            'errors'    => $errors,
            '_token'    => $this->generateCsrfToken('account/tmp_register'),
        ), 'signup');
    }


    /**
     * 本登録フォームでのアカウントの入力チェックを行い、DBに新規登録
     * 
     * @return string 遷移先画面
     */
    public function check_registerAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        // CSRFトークンのチェック
        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/check_register', $token)) {
            return $this->redirect('/account/register');
        }

        $screen_name = $this->request->getPost('screen_name');
        $user_name = $this->request->getPost('user_name');
        $password = $this->request->getPost('password');
        $register_Token = $this->request->getPost('register_token');

        $errors = array();

        if (!strlen($screen_name)) {
            $errors[] = '表示名を入力してください';
            
        } else if (mb_strlen($screen_name) > 20) {
            $errors[] = '表示名は20文字以内で入力してください';
        } 

        if (!strlen($user_name)) {
            $errors[] = 'ユーザIDを入力してください';
            // 正規表現は/で囲む ^先頭　\w半角英数アンダーバー {3,20}3〜20文字 $末尾
        } else if (!preg_match('/^\w{3,20}$/', $user_name)) {
            $errors[] = 'ユーザIDは半角英数字およびアンダースコアを3〜20文字以内で入力してください';
        } else if (!$this->db_manager->get('User')->isUniqueUserName($user_name)) {
            $errors[] = 'そのユーザIDは既に使用されています';
        }

        if (!strlen($password)) {
            $errors[] = 'バスワードを入力してください';
        } else if (!preg_match('/\A(?=.*?[a-z])(?=.*?[A-Z])(?=.*?\d)[a-zA-Z\d]{8,30}+\z/',$password)) {
            $errors[] = 'パスワードは半角英小文字・大文字、数字をそれぞれ1種類以上含む8文字以上30文字以下にしてください。';
        }

        // エラーがなければDBへユーザ新規登録
        if (count($errors) === 0) {
            $this->db_manager->get('User')->insert($screen_name, $user_name, $password, $register_Token);
            $this->session->setAuthenticated(true);

            $user = $this->db_manager->get('User')->fetchByUserName($user_name);
            $this->session->set('user', $user);

            return $this->redirect('/');
        }

        return $this->render(array(
            'register_token' => $register_Token,
            'screen_name' => $screen_name,
            'user_name' => $user_name,
            'password'  => $password,
            'errors'    => $errors,
            '_token'    => $this->generateCsrfToken('account/check_register'),
        ), 'register');
    }

    /**
     * アカウント情報トップ
     * 
     * @return string 遷移先画面
     */
    public function indexAction()
    {
        $editer = false;
        $editer = $this->request->getGet('editer');
        $user = $this->session->get('user');

        $followings = $this->db_manager->get('User')->fetchAllFollowingsByUserId($user['id']);
        $followed = $this->db_manager->get('User')->fetchAllFollowedByUserId($user['id']);
        $profile_image = $this->db_manager->get('User')->fetchProfileImage($user['id']);
        $screen_name = $this->db_manager->get('User')->fetchByUserName($user['user_name']);
        $introduce = $this->db_manager->get('User')->fetchByUserName($user['user_name']);

        //累計投稿数所得
        $all_posts = $this->db_manager->get('User')->fetchAllPostsByUserId($user['id']);

        // 画像投稿のためにCSRFトークンも渡す
        return $this->render(array(
            'user'      => $user,
            'screen_name' => $screen_name['screen_name'],
            'profile_image' => $profile_image,
            'followings'=> $followings,
            'followed' => $followed,
            'all_posts' => $all_posts,
            'introduce' => $screen_name['introduce'],
            'editer' => $editer,
            '_token'    => $this->generateCsrfToken('account/profile_update'),
        ));
    }

    /**
     * ログインアクション
     * 
     * @return string 遷移先画面
     */
    public function signinAction()
    {
        // 既にログインしている場合、アカウント情報トップへリダイレクト
        if ($this->session->isAuthenticated()) {
            return $this->redirect('/profile');
        }

        // ログイン画面を返す
        return $this->render(array(
            'user_name' => '',
            'password'  => '',
            '_token'    => $this->generateCsrfToken('account/signin'),
        ));
    }

    /**
     * ログイン処理
     * 
     * @return string 遷移先画面
     */
    public function authenticateAction()
    {
        // 既にログインしている場合、アカウント情報トップへリダイレクト
        if ($this->session->isAuthenticated()) {
            return $this->redirect('/profile');
        }

        if (!$this->request->isPost()) {
            $this->foward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/signin', $token)) {
            return $this->redirect('/account/signin');
        }

        $user_name = $this->request->getPost('user_name');
        $password = $this->request->getPost('password');
        $remember_me = $this->request->getPost('remember_me');

        $errors = array();

        if (!strlen($user_name)) {
            $errors[] = 'ユーザIDを入力してください';
        }else if($this->CheckEmailAddress($user_name)){
            $id = $this->db_manager->get('User')->fetchUserIDByEmail($user_name);
            if($id){
             $user_name = $id['user_name'];
            }else{
             $errors[] = 'ユーザIDかパスワードが不正です';
            }
        }

        if (!strlen($password)) {
            $errors[] = 'パスワードを入力してください';
        }

        if (count($errors) === 0) {

            $user_repository = $this->db_manager->get('User');
            $user = $user_repository->fetchByUserName($user_name);

            // パスワードチェック
            if (!$user
                || (!$user_repository->verifyPassword($password, $user['password']))
            ) {
                $errors[] = 'ユーザIDかパスワードが不正です';
            } else {
                if($remember_me){
                    //自動ログイン機能
                    $rememberToken = sha1(bin2hex(random_bytes(32)) . '_auto_login');
                    $user_repository->remember_me($user_name, $rememberToken);
                    $this->response->cookie('remember_token',$rememberToken);
                }

                $session = $user_repository->fetchforSessionByUserName($user_name);

                $this->session->setAuthenticated(true);
                $this->session->set('user', $session);

                return $this->redirect('/');
            }
        }

        return $this->render(array(
            'user_name' => $user_name,
            'password'  => $password,
            'errors'    => $errors,
            '_token'    => $this->generateCsrfToken('account/signin'),
        ), 'signin');
    }

/** ユーザー一覧 */
    public function listAction(){
        // ユーザ情報取得
        $user = $this->session->get('user');
        $screen_name = $this->db_manager->get('User')->fetchByUserName($user['user_name']);
        //ユーザーのプロフ画像取得
        $profile_image = $this->db_manager->get('User')
            ->fetchProfileImage($user['id']);
        //フォロー取得
        $followings = $this->db_manager->get('User')
            ->fetchAllFollowingsByUserId($user['id']);
        //フォロワー取得
        $followed = $this->db_manager->get('User')
            ->fetchAllFollowedByUserId($user['id']);
        //累計投稿数所得
        $all_posts = $this->db_manager->get('User')
            ->fetchAllPostsByUserId($user['id']);

        return $this->render(array(
            'user' => $user,
            'profile_image' => $profile_image,
            'followings'=> $followings,
            'followed' => $followed,
            'screen_name' => $screen_name['screen_name'],
            'all_posts' => $all_posts,
        ));
    }
/** フォーム */
public function formsAction(){
        $post = false;
        $post = $this->request->getGet('post');
        $user = $this->session->get('user');
        $followings = $this->db_manager->get('User')->fetchAllFollowingsByUserId($user['id']);
        $followed = $this->db_manager->get('User')->fetchAllFollowedByUserId($user['id']);
        $profile_image = $this->db_manager->get('User')->fetchProfileImage($user['id']);
        $screen_name = $this->db_manager->get('User')->fetchByUserName($user['user_name']);
        $introduce = $this->db_manager->get('User')->fetchByUserName($user['user_name']);
        $all_posts = $this->db_manager->get('User')->fetchAllPostsByUserId($user['id']);
        $contents = '';
        $errors = array();

        if($post){
            if (!$this->request->isPost()) {
                $this->foward404();
            }
            $token = $this->request->getPost('_token');
            if (!$this->checkCsrfToken('forms', $token)) {
                return $this->redirect('/forms');
            }
            $contents = $this->request->getPost('contents');
            if (!strlen($contents)) {
                $errors[] = '内容を入力してください';
            }else if (mb_strlen($contents) > 500) {
                $errors[] = '500文字以内で入力してください';
            }
            if (count($errors) === 0) {
            $contents1 = $contents . '　(' . $user['user_name'] . ')';

            $message = array(
            'username' => 'Reast_問題報告通知',    // 投稿者名
            'content'  => $contents1
            );
            $webhook_url = 'https://discord.com/api/webhooks/1150362234803986492/PYUaCkHscvm9uPd_vZAtG0uptCMrO8lql7LCqIJI1u6VN0D6aTIbCeKfW24f09DQcErJ';
            $options = array(
            'http' => array(
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($message),
            )
            );
            $resp = file_get_contents($webhook_url, false, stream_context_create($options));
            $errors[] = '管理者に送信しました。ありがとうございました。';
            }
        }

        return $this->render(array(
            'user'      => $user,
            'screen_name' => $screen_name['screen_name'],
            'profile_image' => $profile_image,
            'followings'=> $followings,
            'followed' => $followed,
            'all_posts' => $all_posts,
            'introduce' => $screen_name['introduce'],
            'contents'     => $contents,
            '_token'    => $this->generateCsrfToken('forms'),
            'errors' => $errors,
        ));
}
/** ユーザーID設定画面 */
public function userid_settingAction(){
    $user = $this->session->get('user');
    $followings = $this->db_manager->get('User')->fetchAllFollowingsByUserId($user['id']);
    $followed = $this->db_manager->get('User')->fetchAllFollowedByUserId($user['id']);
    $profile_image = $this->db_manager->get('User')->fetchProfileImage($user['id']);
    $screen_name = $this->db_manager->get('User')->fetchByUserName($user['user_name']);
    $introduce = $this->db_manager->get('User')->fetchByUserName($user['user_name']);
    $all_posts = $this->db_manager->get('User')->fetchAllPostsByUserId($user['id']);

    return $this->render(array(
        'user'      => $user,
        'screen_name' => $screen_name['screen_name'],
        'profile_image' => $profile_image,
        'followings'=> $followings,
        'followed' => $followed,
        'all_posts' => $all_posts,
        'introduce' => $screen_name['introduce'],
        '_token'    => $this->generateCsrfToken('account/change_user_id'),
    ));
}
/** パスワード設定画面 */
public function password_settingAction(){
    $user = $this->session->get('user');
    $followings = $this->db_manager->get('User')->fetchAllFollowingsByUserId($user['id']);
    $followed = $this->db_manager->get('User')->fetchAllFollowedByUserId($user['id']);
    $profile_image = $this->db_manager->get('User')->fetchProfileImage($user['id']);
    $screen_name = $this->db_manager->get('User')->fetchByUserName($user['user_name']);
    $introduce = $this->db_manager->get('User')->fetchByUserName($user['user_name']);
    $all_posts = $this->db_manager->get('User')->fetchAllPostsByUserId($user['id']);


    return $this->render(array(
        'user'      => $user,
        'screen_name' => $screen_name['screen_name'],
        'profile_image' => $profile_image,
        'followings'=> $followings,
        'followed' => $followed,
        'all_posts' => $all_posts,
        'introduce' => $screen_name['introduce'],
        '_token'    => $this->generateCsrfToken('account/change_password'),
    ));
}
/** メール設定画面 */
public function email_settingAction(){
    $user = $this->session->get('user');
    $followings = $this->db_manager->get('User')->fetchAllFollowingsByUserId($user['id']);
    $followed = $this->db_manager->get('User')->fetchAllFollowedByUserId($user['id']);
    $profile_image = $this->db_manager->get('User')->fetchProfileImage($user['id']);
    $screen_name = $this->db_manager->get('User')->fetchByUserName($user['user_name']);
    $introduce = $this->db_manager->get('User')->fetchByUserName($user['user_name']);
    $all_posts = $this->db_manager->get('User')->fetchAllPostsByUserId($user['id']);


    return $this->render(array(
        'user'      => $user,
        'screen_name' => $screen_name['screen_name'],
        'profile_image' => $profile_image,
        'followings'=> $followings,
        'followed' => $followed,
        'all_posts' => $all_posts,
        'introduce' => $screen_name['introduce'],
        '_token'    => $this->generateCsrfToken('account/change_email'),
    ));
}
/** アカウント削除画面 */
public function deleteAction(){
    $user = $this->session->get('user');
    $followings = $this->db_manager->get('User')->fetchAllFollowingsByUserId($user['id']);
    $followed = $this->db_manager->get('User')->fetchAllFollowedByUserId($user['id']);
    $profile_image = $this->db_manager->get('User')->fetchProfileImage($user['id']);
    $screen_name = $this->db_manager->get('User')->fetchByUserName($user['user_name']);
    $introduce = $this->db_manager->get('User')->fetchByUserName($user['user_name']);
    $all_posts = $this->db_manager->get('User')->fetchAllPostsByUserId($user['id']);


    return $this->render(array(
        'user'      => $user,
        'screen_name' => $screen_name['screen_name'],
        'profile_image' => $profile_image,
        'followings'=> $followings,
        'followed' => $followed,
        'all_posts' => $all_posts,
        'introduce' => $screen_name['introduce'],
        '_token'    => $this->generateCsrfToken('account/delete_account'),
    ));
}
   /**
     * アカウント設定処理アクション1
     */
    public function change_passwordAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/change_password', $token)) {
            return $this->redirect('/account/password_setting');
        }

        // 画面に入力された投稿内容を取得
        $old_password = $this->request->getPost('old_password');
        $new_password = $this->request->getPost('new_password');
        
        $user = $this->session->get('user');
        $errors = array();
        $user_repository = $this->db_manager->get('User');
        $user_1 = $user_repository->fetchByUserName($user['user_name']);

            // パスワードチェック
            if (!$user_1
                || (!$user_repository->verifyPassword($old_password, $user_1['password']))
            ) {
                $errors[] = 'パスワードが不正です';
            } else {
                  // 投稿内容の入力チェック（バリデーション）
                  if (!strlen($new_password)) {
                     $errors[] = '新しいパスワードを入力してください';
                  } else if (!preg_match('/\A(?=.*?[a-z])(?=.*?[A-Z])(?=.*?\d)[a-zA-Z\d]{8,30}+\z/',$new_password)) {
                    $errors[] = '新しいパスワードは半角英小文字・大文字、数字をそれぞれ1種類以上含む8文字以上30文字以下にしてください。';
                }
            }


        // データベースに登録し、ホーム画面へ移動
        if (count($errors) === 0) {
            $user_repository->update_password($user['id'],$new_password);
            $errors[] = 'パスワードを変更しました。';
        } 
        $followings = $this->db_manager->get('User')->fetchAllFollowingsByUserId($user['id']);
        $followed = $this->db_manager->get('User')->fetchAllFollowedByUserId($user['id']);
        $profile_image = $this->db_manager->get('User')->fetchProfileImage($user['id']);
        $screen_name = $this->db_manager->get('User')->fetchByUserName($user['user_name']);
        $introduce = $this->db_manager->get('User')->fetchByUserName($user['user_name']);
        $all_posts = $this->db_manager->get('User')->fetchAllPostsByUserId($user['id']);
    
    
        return $this->render(array(
            'user'      => $user,
            'screen_name' => $screen_name['screen_name'],
            'profile_image' => $profile_image,
            'followings'=> $followings,
            'followed' => $followed,
            'all_posts' => $all_posts,
            'introduce' => $screen_name['introduce'],
            '_token'    => $this->generateCsrfToken('account/password_setting'),
            'errors' => $errors,
        ), 'password_setting');
    }
    /**
     * アカウント設定処理アクション2
     */
    public function change_user_idAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/change_user_id', $token)) {
            return $this->redirect('/account/userid_setting');
        }

        // 画面に入力された投稿内容を取得
        $password = $this->request->getPost('password');
        $new_user_id = $this->request->getPost('new_user_name');
        
        $user = $this->session->get('user');
        $errors = array();
        $user_repository = $this->db_manager->get('User');
        $user_1 = $user_repository->fetchByUserName($user['user_name']);

            // パスワードチェック
            if (!$user_1
                || (!$user_repository->verifyPassword($password, $user_1['password']))
            ) {
                $errors[] = 'パスワードが不正です';
            } else {
                  // 投稿内容の入力チェック（バリデーション）
                  if (!strlen($new_user_id)) {
                     $errors[] = '新しいユーザーIDを入力してください';
                  } else if (mb_strlen($new_user_id) > 32) {
                      $errors[] = 'ユーザーIDは32文字以内で入力してください。';
                  }
            }


        // データベースに登録し、ホーム画面へ移動
        if (count($errors) === 0) {
            $user_repository->update_user_name($user['id'],$new_user_id);
            $this->session->clear();
            $this->session->setAuthenticated(false);
            $this->redirect('/account/signin');
        } 
        $followings = $this->db_manager->get('User')->fetchAllFollowingsByUserId($user['id']);
        $followed = $this->db_manager->get('User')->fetchAllFollowedByUserId($user['id']);
        $profile_image = $this->db_manager->get('User')->fetchProfileImage($user['id']);
        $user_info = $this->db_manager->get('User')->fetchByUserID($user['id']);
        $all_posts = $this->db_manager->get('User')->fetchAllPostsByUserId($user['id']);
    
    
        return $this->render(array(
            'user'      => $user,
            'screen_name' => $user_info['screen_name'],
            'profile_image' => $profile_image,
            'followings'=> $followings,
            'followed' => $followed,
            'all_posts' => $all_posts,
            'introduce' => $user_info['introduce'],
            '_token'    => $this->generateCsrfToken('account/userid_setting'),
            'errors' => $errors,
        ), 'userid_setting');
    }

    /**
     * アカウント設定処理アクション3
     */
    public function change_emailAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/change_email', $token)) {
            return $this->redirect('/account/email_setting');
        }

        // 画面に入力された投稿内容を取得
        $password = $this->request->getPost('password');
        $email = $this->request->getPost('new_email');
        
        $user = $this->session->get('user');
        $errors = array();
        $user_repository = $this->db_manager->get('User');
        $user_1 = $user_repository->fetchByUserName($user['user_name']);

            // パスワードチェック
            if (!$user_1
                || (!$user_repository->verifyPassword($password, $user_1['password']))
            ) {
                $errors[] = 'パスワードが不正です';
            } else {
                  // 投稿内容の入力チェック（バリデーション）
                  if (!strlen($email)) {
                     $errors[] = '新しいメールアドレスを入力してください';
                  } else if (!$this->CheckEmailAddress($email)) {
                      $errors[] = '不正なメールアドレスです。';
                  }
            }


        // データベースに登録し、ホーム画面へ移動
        if (count($errors) === 0) {
            $register_Token = bin2hex(random_bytes(32));

            // URLはご自身の環境に合わせてください
             $url = "https://reast.info/account/validation_email?token={$register_Token}";

             $subject =  '[Reast]メールアドレス変更のご案内';

             $body = <<<EOD
                        いつもReastをご利用いただきまして誠にありがとうございます。
                        
                        メールアドレス変更のご依頼を承りました。
                        
                        ご登録されたメールアドレスが有効であることを確認するために、
                        次のURLにアクセスをして、変更を完了してください。
                        （URLが2行になっている場合は1行にしてください）
                        
                        {$url}
                        
                        ご不明点がございましたら、プロフィールのご要望にてお問い合わせください。
                    EOD;

            // Fromはご自身の環境に合わせてください
             $headers = "From : reast_setting@narikasya.ie-t.net\n";
            // text/htmlを指定し、html形式で送ることも可能
             $headers .= "Content-Type : text/plain";

       
            $isSent = mb_send_mail($email, $subject, $body, $headers);
            if($isSent){
               $this->db_manager->get('User')->registertoken_update($user['id'], $register_Token, $email);
               $errors[] = 'メールアドレスの確認のため、変更用URLをそちらのメールに送信しました。';
            }else{
               $errors[] = '認証メールを送信できませんでした。';
            }
        
            
        } 
        $followings = $this->db_manager->get('User')->fetchAllFollowingsByUserId($user['id']);
        $followed = $this->db_manager->get('User')->fetchAllFollowedByUserId($user['id']);
        $profile_image = $this->db_manager->get('User')->fetchProfileImage($user['id']);
        $user_info = $this->db_manager->get('User')->fetchByUserID($user['id']);
        $all_posts = $this->db_manager->get('User')->fetchAllPostsByUserId($user['id']);
    
    
        return $this->render(array(
            'user'      => $user,
            'screen_name' => $user_info['screen_name'],
            'profile_image' => $profile_image,
            'followings'=> $followings,
            'followed' => $followed,
            'all_posts' => $all_posts,
            'introduce' => $user_info['introduce'],
            '_token'    => $this->generateCsrfToken('account/change_email'),
            'errors' => $errors,
        ), 'email_setting');
    }
    
    /**
     * アカウント設定処理アクション4
     */
    public function delete_accountAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/delete_account', $token)) {
            return $this->redirect('/account/delete');
        }

        $user = $this->session->get('user');;
        $user_name = $this->request->getPost('user_name');
        $password = $this->request->getPost('password');

        $errors = array();

        if (!strlen($user_name)) {
            $errors[] = 'メールアドレスを入力してください';
        }
        if($this->CheckEmailAddress($user_name)){
           $id = $this->db_manager->get('User')->fetchUserIDByEmail($user_name);
           if($id){
            $user_name = $id['user_name'];
           }else{
            $errors[] = 'メールアドレスかパスワードが不正です';
           }
        }else{
            $errors[] = 'メールアドレスが不正です';
        }
        if (!strlen($password)) {
            $errors[] = 'パスワードを入力してください';
        }

        if (count($errors) === 0) {

            $user_repository = $this->db_manager->get('User');
            $user_2 = $user_repository->fetchByUserName($user_name);

            // パスワードチェック
            if (!$user
                || (!$user_repository->verifyPassword($password, $user_2['password']))
            ) {
                $errors[] = 'メールアドレスかパスワードが不正です';
            } else {
                $user_repository->delete_account($user['id']);
                $this->session->clear();
                $this->session->setAuthenticated(false);
                return $this->render(array(),'delete_complete');
            }
        }

        $followings = $this->db_manager->get('User')->fetchAllFollowingsByUserId($user['id']);
        $followed = $this->db_manager->get('User')->fetchAllFollowedByUserId($user['id']);
        $profile_image = $this->db_manager->get('User')->fetchProfileImage($user['id']);
        $user_info = $this->db_manager->get('User')->fetchByUserID($user['id']);
        $all_posts = $this->db_manager->get('User')->fetchAllPostsByUserId($user['id']);

        return $this->render(array(
            'user'      => $user,
            'screen_name' => $user_info['screen_name'],
            'profile_image' => $profile_image,
            'followings'=> $followings,
            'followed' => $followed,
            'all_posts' => $all_posts,
            'introduce' => $user_info['introduce'],
            'errors' => $errors,
            '_token'    => $this->generateCsrfToken('account/delete_account'),
        ), 'delete');
    }
    /**
     * トークンのチェックを行い、DBに更新
     * 
     * @return string 遷移先画面
     */
    public function validation_emailAction()
    {
        $token = $this->request->getGet('token');
        if (!$token) {
            return $this->redirect('/');
        }
        //tokenから検索したメールが見つからない場合の遷移
        if (!$this->db_manager->get('User')->fetchEmailbyToken($token)){
            return $this->redirect('/account/email_setting');
        }

        $date = $this->db_manager->get('User')->fetchTokenSentAtbyToken($token);
        $tokenValidPeriod = (new \DateTime())->modify("-1 hour")->format('Y-m-d H:i:s');

        // 仮登録が1時間以上前の場合、有効期限切れとする
        if ($date['register_token_sent_at'] < $tokenValidPeriod){
            return $this->redirect('/account/email_setting');
        }

        $email = $this->db_manager->get('User')->fetchTmp_EmailbyToken($token);

        $this->db_manager->get('User')->email_update($email['tmp_email'], $token);

        return $this->render();
    }

    /**
     * サインアウト（ログアウト）アクション
     * 
     * @return string 遷移先画面
     */
    public function signoutAction()
    {
        if (!empty($_COOKIE['remember_token'])) {
            $user = $this->session->get('user');
            $this->response->delete_cookie('remember_token', $_COOKIE['remember_token']);
            $this->db_manager->get('User')->token_deleteByUserID($user['id']);
        }
        $this->session->clear();
        $this->session->setAuthenticated(false);
        $this->redirect('/account/signin');
    }

    public function followAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $following_name = $this->request->getPost('following_name');
        if (!$following_name) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/follow', $token)) {
            return $this->redirect('/user/' . $following_name);
        }

        // フォローしたいユーザの存在確認
        $follow_user = $this->db_manager->get('User')
            ->fetchByUserName($following_name);
        if (!$follow_user) {
            $this->forward404();
        }

        $user = $this->session->get('user');

        $following_repository = $this->db_manager->get('Following');
        // 既にフォロー済みでないかチェック
        if ($user['id'] !== $follow_user['id'] && !$following_repository->isFollowing($user['id'], $follow_user['id'])) {
            $following_repository->insert($user['id'], $follow_user['id']);
        }

        return $this->redirect('/user/' . $following_name);
    }

    public function unfollowAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $unfollowing_name = $this->request->getPost('following_name');
        if (!$unfollowing_name) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/follow', $token)) {
            return $this->redirect('/profile');
        }

        $follow_user = $this->db_manager->get('User')
            ->fetchByUserName($unfollowing_name);
        if (!$follow_user) {
            $this->forward404();
        }

        $user = $this->session->get('user');

        $following_repository = $this->db_manager->get('Following');
        if ($user['id'] !== $follow_user['id'] 
            && $following_repository->isFollowing($user['id'], $follow_user['id'])
        ) {
            $following_repository->outsert($user['id'], $follow_user['id']);
        }

        return $this->redirect('/user/' . $unfollowing_name);
    }

    public function profile_updateAction()
    {
        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('account/profile_update', $token)) {
            $errors[] = '不正なリクエストです。';
        }

        $user = $this->session->get('user');
        //フォロー取得
        $followings = $this->db_manager->get('User')
            ->fetchAllFollowingsByUserId($user['id']);
        //フォロワー取得
        $followed = $this->db_manager->get('User')
        ->fetchAllFollowedByUserId($user['id']);
                //累計投稿数所得
                $all_posts = $this->db_manager->get('User')->fetchAllPostsByUserId($user['id']);
        
        $profile_image = $this->db_manager->get('User')->fetchProfileImage($user['id']);
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $errors = array();
        $introduce = $this->request->getPost('introduce'); //自己紹介文
        $screen_name = $this->request->getPost('screen_name'); //表示名
        $upload_profileimage = $this->request->getFile('upload_profileimage', 'name');
        $image_bg = $this->request->getFile("upload_image", "name");
        $delete_bg = $this->request->getPost('delete_bg'); //表示名

                // 投稿内容の入力チェック（バリデーション）
                if (!strlen($introduce)) {
                    $errors[] = '自己紹介文を書いてください';
                } else if (mb_strlen($introduce) > 50) {
                    $errors[] = '自己紹介文は50文字以内で入力してください';
                }
                                // 投稿内容の入力チェック（バリデーション）
                                if (!strlen($screen_name)) {
                                    $errors[] = '名前を書いてください';
                                } else if (mb_strlen($screen_name) > 50) {
                                    $errors[] = '名前は50文字以内で入力してください';
                                }
     if (count($errors) === 0) {
        $this->db_manager->get('User')->profile_update($user['id'], $introduce, $screen_name);
        if($delete_bg){
            $this->db_manager->get('User')->update_bg($user['id'],null);
        }
        
        if ($upload_profileimage) {
            $error = $this->request->getFile('upload_profileimage','error');
            $tmp_name = $this->request->getFile('upload_profileimage','tmp_name');
            $profile_image_old = $this->db_manager->get('User')->fetchProfileImage($user['id']);

            switch ($error) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errors[] = 'a';
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errors[] = '16MB以下のファイルを選択してください';
                default:
                $errors[] ='エラーが発生しました';
            }

            if (!$extension = array_search(
                mime_content_type($tmp_name),
                array(
                    'jpg' => 'image/jpeg',
                    'png' => 'image/png',
                ),
                true
            )) {
                $errors[] = 'プロフィール画像で投稿可能なのはjpgとpngのみです';
            }

            $image_name = md5(uniqid(rand(), true)) . '.' . $extension;
            $new_image_path = '../web/profileimages/' . $image_name;
        
        
            if (move_uploaded_file($tmp_name, $new_image_path)) {

                $this->createThumb($new_image_path, $new_image_path);
        
                chmod($new_image_path, 0644);
                $this->db_manager->get('User')->pimage_insert($user['id'], $image_name);
                if($profile_image_old["0"]["profile_image"] != 'default.png'){
                    if(!unlink('../web/profileimages/' . $profile_image_old["0"]["profile_image"])){
                        $errors[] = '以前のプロフィール画像を削除できません。管理者に連絡してください。';
                    }
                }
                return $this->redirect('/profile');
            }else{
                $errors[] = 'アップロードできませんでした。もう一度お試しください。';
            }
        }
        if($image_bg){
            $error = $this->request->getFile('upload_image','error');
            $tmp_name = $this->request->getFile('upload_image','tmp_name');
        if (!$extension = array_search(
            mime_content_type($tmp_name),
            array(
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
            ),
            true
        )) {
            $errors[] = '背景画像で投稿可能なのはjpgとpngのみです';
        }else {
            $image = md5(uniqid(rand(), true)) . '.' . $extension;
            $FilePath = '../web/images/' . $image;
                                    // サーバーにファイルをアップロード
                                    if(move_uploaded_file($tmp_name, $FilePath)){
                                        // データベースに画像ファイル名を挿入
                                        $this->db_manager->get('User')->update_bg($user['id'],$image);
                                    }else{
                                        $errors[] =  "申し訳ありませんが、ファイルのアップロードに失敗しました";
                                    }
        }
    }
        return $this->redirect('/profile');
     }



        return $this->render(array(
            'user'      => $user,
            'followings'=> $followings,
            'followed'=> $followed,
            'screen_name' => $screen_name,
            'profile_image'=> $profile_image,
            'introduce'=> $introduce,
            'all_posts' => $all_posts,
            'editer' => 'true',
            '_token'    => $this->generateCsrfToken('account/profile_update'),
            'errors'    => $errors,
        ), 'index');
    }

}
