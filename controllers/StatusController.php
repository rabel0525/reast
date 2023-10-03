<?php

class StatusController extends Controller
{
    // 認証を必要とするアクション
    protected $auth_actions = array('index', 'post');

    /**
     * ユーザのホームページにアクセスしたときのアクション
     * 
     * @return string ユーザの投稿一覧レスポンス画面
     */
    public function indexAction()
    {
        // ユーザ情報取得
        $user = $this->session->get('user');
        $screen_name = $this->db_manager->get('User')->fetchByUserName($user['user_name']);
        // ユーザの投稿一覧取得
        $statuses = $this->db_manager->get('Status')
            ->fetchAllPersonalArchivesByUserId($user['id'], '1');
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
            'introduce' => $screen_name['introduce'],
            'all_posts' => $all_posts,
            'body'     => '',
            '_token'   => $this->generateCsrfToken('status/post'),
        ));
    }

    /**
     * 投稿処理アクション
     */
    public function postAction()
    {
        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('status/post', $token)) {
            return $this->redirect('/');
        }

        // 画面に入力された投稿内容を取得
        $body = $this->request->getPost('body');
        $image_req = $this->request->getFile("upload_image", "name");
        
        

        $errors = array();

        // 投稿内容の入力チェック（バリデーション）
        if (!strlen($body)) {
            $errors[] = 'つぶやきを入力してください';
        } else if (mb_strlen($body) > 300) {
            $errors[] = 'つぶやきは300文字以内で入力してください';
        }

        // データベースに登録し、ホーム画面へ移動
        if (count($errors) === 0) {
            $user = $this->session->get('user');
        if(!$image_req['0']){
            $null = null;
            $this->db_manager->get('Status')->insert($user['id'], $body, $null);
            return $this->redirect('/');
        }else { //画像があるならば
            $tmp_name = $this->request->getFile('upload_image','tmp_name');
            $size = $this->request->getFile('upload_image','size');
            $image = array();
            $extension = array();

            if(count($tmp_name) > '4'){
                $errors[] = '画像は4枚のみ投稿可能です';
            }
        
        foreach ($tmp_name as $no => $tmp_names) {
            $extension[] = array_search(
                mime_content_type($tmp_names),
                array(
                    'jpg' => 'image/jpeg',
                    'png' => 'image/png',
                ),
                true
            );
            if (!$extension[$no]) {
                $errors[] = '画像で投稿可能なのはjpgとpngのみです';
            }else{
                if (count($errors) === 0) {
                if($size[$no] >= '8777216'){
                    $errors[] = '画像サイズは8mb以下で投稿してください。';
                }else{
                    $image[] = md5(uniqid(rand(), true)) . '.' . $extension[$no];
                    $FilePath = '../web/images/' . $image[$no];
                                            // サーバーにファイルをアップロード
                                            if(!move_uploaded_file($tmp_names, $FilePath)){
                                                $errors[] =  "申し訳ありませんが、". $no ."枚のファイルのアップロードに失敗しました";
                                            }
                }
                }
            }
        }
        if (count($errors) === 0) {
        $image_csv = implode(PHP_EOL, $image);
        $this->db_manager->get('Status')->insert($user['id'], $body, $image_csv);
        return $this->redirect('/');
        }
        }  
    }

        $user = $this->session->get('user');
        // エラー表示のために再度入力画面を表示するが、同一画面に一覧も表示しているため
        $profile_image = $this->db_manager->get('User')
            ->fetchProfileImage($user['id']);
            $screen_name = $this->db_manager->get('User')->fetchByUserName($user['user_name']);
            // ユーザの投稿一覧取得
            $statuses = $this->db_manager->get('Status')
                ->fetchAllPersonalArchivesByUserId($user['id'], '1');
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
            'errors'   => $errors,
            'body'     => $body,
            'user' => $user,
            'profile_image' => $profile_image,
            'followings'=> $followings,
            'followed' => $followed,
            'screen_name' => $screen_name['screen_name'],
            'introduce' => $screen_name['introduce'],
            'all_posts' => $all_posts,
            '_token'   => $this->generateCsrfToken('status/post'),
        ), 'index');
    }

    /**
     * ユーザの投稿一覧アクション
     * 
     * @param array $params
     * @return string 遷移先画面
     */
    public function userAction($params)
    {
        // ユーザの存在確認
        $user_info = $this->db_manager->get('User')
            ->fetchByUserName($params['user_name']);
        if (!$user_info) {
            $this->forward404();
        }

        $user = $this->session->get('user');
        if ($user['user_name'] == $params['user_name']) {
            $this->redirect('/profile');
        }

        $followings = $this->db_manager->get('User')->fetchAllFollowingsByUserId($user_info['id']);
        $followed = $this->db_manager->get('User')->fetchAllFollowedByUserId($user_info['id']);
        $profile_image = $this->db_manager->get('User')->fetchProfileImage($user_info['id']);
        $all_posts = $this->db_manager->get('User')->fetchAllPostsByUserId($user_info['id']);

        $following = null;
        $isfollower = false;
        $isfollower = $this->db_manager->get('Following')->isFollower($user['id'], $user_info['id']);
        if ($this->session->isAuthenticated()) {
            // アクセスしているのが自分自身ならフォローボタンは表示しない
            if ($user['id'] !== $user_info['id']) {
                $following = $this->db_manager->get('Following')
                    ->isFollowing($user['id'], $user_info['id']);
            }
        }

        // フォロー登録のためCSRFトークンも渡す
        return $this->render(array(
            'user'      => $user,
            'user'       => $user,
            'user_info'      => $user_info,
            'screen_name' => $user_info['screen_name'],
            'profile_image' => $profile_image,
            'followings'=> $followings,
            'followed' => $followed,
            'all_posts' => $all_posts,
            'introduce' => $user_info['introduce'],
            'following' => $following,
            'isfollower' => $isfollower,
            '_token'    => $this->generateCsrfToken('account/follow'),
        ));
    }

    /**
     * 投稿詳細アクション
     * 
     * @param array $params
     * @return string 遷移先画面
     */
    public function showAction($params)
    {
        // ユーザ情報取得
        $user = $this->session->get('user');
        // 投稿情報取得
        $status = $this->db_manager->get('Status')
            ->fetchByIdAndUserName($params['id'], $params['user_name']);

        if (!$status) {
            $this->forward404();
        }

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
            'status' => $status,
            'user' => $user,
            'profile_image' => $profile_image,
            'followings'=> $followings,
            'followed' => $followed,
            'screen_name' => $screen_name['screen_name'],
            'all_posts' => $all_posts,
            '_token'   => $this->generateCsrfToken('status/edit_complete')
        ));
    }
/** 設定画面 */
public function list_followingAction($params){
    $user_info = $this->db_manager->get('User')->fetchByUserName($params['user_name']);

    $followings = $this->db_manager->get('User')->fetchAllFollowingsByUserId($user_info['id']);
    $followed = $this->db_manager->get('User')->fetchAllFollowedByUserId($user_info['id']);
    $profile_image = $this->db_manager->get('User')->fetchProfileImage($user_info['id']);
    $all_posts = $this->db_manager->get('User')->fetchAllPostsByUserId($user_info['id']);

    $user = $this->session->get('user');

    $following = null;
    $isfollower = false;
    $isfollower = $this->db_manager->get('Following')->isFollower($user['id'], $user_info['id']);
    if ($this->session->isAuthenticated()) {
        $my = $this->session->get('user');
        // アクセスしているのが自分自身ならフォローボタンは表示しない
        if ($my['id'] !== $user_info['id']) {
            $following = $this->db_manager->get('Following')
                ->isFollowing($my['id'], $user_info['id']);
        }
    }

    return $this->render(array(
        'user'       => $user,
        'user_info'      => $user_info,
        'screen_name' => $user_info['screen_name'],
        'profile_image' => $profile_image,
        'followings'=> $followings,
        'followed' => $followed,
        'all_posts' => $all_posts,
        'introduce' => $user_info['introduce'],
        'following' => $following,
        'isfollower' => $isfollower,
        '_token'    => $this->generateCsrfToken('account/follow'),
    ));
}
/** 設定画面 */
public function list_followerAction($params){
    $user_info = $this->db_manager->get('User')->fetchByUserName($params['user_name']);

    $followings = $this->db_manager->get('User')->fetchAllFollowingsByUserId($user_info['id']);
    $followed = $this->db_manager->get('User')->fetchAllFollowedByUserId($user_info['id']);
    $profile_image = $this->db_manager->get('User')->fetchProfileImage($user_info['id']);
    $all_posts = $this->db_manager->get('User')->fetchAllPostsByUserId($user_info['id']);

    $user = $this->session->get('user');

    $following = null;
    $isfollower = false;
    $isfollower = $this->db_manager->get('Following')->isFollower($user['id'], $user_info['id']);
    if ($this->session->isAuthenticated()) {
        $my = $this->session->get('user');
        // アクセスしているのが自分自身ならフォローボタンは表示しない
        if ($my['id'] !== $user_info['id']) {
            $following = $this->db_manager->get('Following')
                ->isFollowing($my['id'], $user_info['id']);
        }
    }

    return $this->render(array(
        'user'       => $user,
        'user_info'      => $user_info,
        'screen_name' => $user_info['screen_name'],
        'profile_image' => $profile_image,
        'followings'=> $followings,
        'followed' => $followed,
        'all_posts' => $all_posts,
        'introduce' => $user_info['introduce'],
        'following' => $following,
        'isfollower' => $isfollower,
        '_token'    => $this->generateCsrfToken('account/follow'),
    ));
}

    /**
     * 更新履歴アクション
     * 
     * @param array $params
     * @return string 遷移先画面
     */
    public function updatesAction()
    {
        return $this->render();
    }
    
    /**
     * 編集アクション（ページ表示）
     * 
     * @param array $params
     * @return string 遷移先画面
     */
    public function editAction($params)
    {
        // ユーザ情報取得
        $user = $this->session->get('user');
        // 投稿情報取得
        $status = $this->db_manager->get('Status')
            ->fetchByIdAndUserName($params['id'], $params['user_name']);

        if (!$status) {
            $this->forward404();
        }

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
                   'status' => $status,
                   'user' => $user,
                   'profile_image' => $profile_image,
                   'followings'=> $followings,
                   'followed' => $followed,
                   'screen_name' => $screen_name['screen_name'],
                   'all_posts' => $all_posts,
                   '_token'   => $this->generateCsrfToken('status/edit_complete')
               ));
    }
    /**
     * 編集アクション（書き換え）
     * 
     * @param array $params
     * @return string 遷移先画面
     */
    public function editedAction()
    {
        // ユーザ情報取得
        $user = $this->session->get('user');
        $status_id = $this->request->getPost('status_id');
        $user_name = $this->request->getPost('user_name');

        // 投稿情報取得
        $status = $this->db_manager->get('Status')
            ->fetchByIdAndUserName($status_id, $user_name);

        if (!$status) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        if (!$this->checkCsrfToken('status/edit_complete', $token)) {
            return $this->redirect('/');
        }

        // 画面に入力された投稿内容を取得
        $body = $this->request->getPost('body');
        $errors = array();

        // 投稿内容の入力チェック（バリデーション）
        if (!strlen($body)) {
            $this->db_manager->get('Status')->delete($user['id'], $status_id);
            return $this->redirect('/');
        } else if (mb_strlen($body) > 300) {
            $errors[] = '変更するつぶやきは300文字以内で入力してください';
            $status['body'] = $body;
        }else if($status['body'] == $body){
            return $this->redirect('/');
        }

        // データベースに登録し、ホーム画面へ移動
        if (count($errors) === 0) {
            $this->db_manager->get('Status')->edit($user['id'], $status_id, $body);
            return $this->redirect('/');
        }


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


        return $this->render(array('errors' => $errors, 
                                   'status' => $status, 
                                   'user' => $user, 
                                   'profile_image' => $profile_image,
                                   'followings'=> $followings,
                                   'followed' => $followed,
                                   'screen_name' => $screen_name['screen_name'],
                                   'all_posts' => $all_posts,
                                   '_token'   => $this->generateCsrfToken('status/edit_complete')
                                ), 'edit');
    }
}
