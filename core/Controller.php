<?php

/**
 * コントローラ
 */
abstract class Controller
{
    protected $controller_name;
    protected $action_name;
    protected $application;
    protected $request;
    protected $response;
    protected $session;
    protected $db_manager;
    /**
     * 認証が必要なアクションのリスト
     */
    protected $auth_actions = array();

    /**
     * コンストラクタ
     * 
     * @param Application $application アプリ
     */
    public function __construct($application)
    {
        // コントローラ名をクラス名から取得
        // get_class($this)で自身のクラス名を取得し、substrの-10によって後ろの'Controller'を取り除く
        $this->controller_name = strtolower(substr(get_class($this), 0, -10));

        $this->application = $application;
        $this->request     = $application->getRequest();
        $this->response    = $application->getResponse();
        $this->session     = $application->getSession();
        $this->db_manager  = $application->getDbManager();
    }

    /**
     * Applicationクラスから呼ばれ、アクションを実行する
     * 
     * @param string $action アクション名
     * @param array $params アクションメソッドに渡すパラメータ
     * @return string レスポンスとして返すコンテンツ
     */
    public function run($action, $params = array())
    {
        $this->action_name = $action;

        // アクションのメソッド名 : アクション名+'Action'
        $action_method = $action . 'Action';
        if (!method_exists($this, $action_method)) {
            // アクションメソッドが存在しない場合404エラー画面へ
            $this->forward404();
        }

        // ログインせずに認証が必要なアクションを呼び出した場合、例外を投げる
        if ($this->needsAuthentication($action) && !$this->session->isAuthenticated() && !isset($_COOKIE['remember_token'])) {
            throw new UnauthorizedActionException();
        }

        if (isset($_COOKIE['remember_token']) && $this->needsAuthentication($action) && !$this->session->isAuthenticated()) {
            $auto_login_key = $_COOKIE['remember_token'];
            
            $is_auto_login = $this->db_manager->get('User')->fetchforSessionByToken($auto_login_key);
            
            if ($is_auto_login) {
                if (!empty($auto_login_key)) {
                    $this->response->delete_cookie('remember_token',$auto_login_key);
                }

                $_SESSION['dummy'] = 1;
                $rememberToken = sha1(bin2hex(random_bytes(32)) . '_auto_login');
                $this->db_manager->get('User')->remember_me($is_auto_login['user_name'], $rememberToken);
                $this->response->cookie('remember_token',$rememberToken);
                
                $this->session->setAuthenticated(true);
                $this->session->set('user', $is_auto_login);
            }else{
                throw new UnauthorizedActionException(); 
            }
        }

        // アクションメソッドを呼び出しコンテンツを受け取る
        $content = $this->$action_method($params);

        return $content;
    }

    /**
     * ビューファイルをレンダリング
     * 
     * @param array $variables ビューファイルに渡す連想配列
     * @param string $template ビューファイル名(nullの場合はアクション名を使用)
     * @param string $layout レイアウトファイル名
     * @return string レンダリング結果
     */
    protected function render($variables = array(), $template = null, $layout = 'layout')
    {
        // Viewクラスのコンストラクタに渡すデフォルト値
        $defaults = array(
            'request'  => $this->request,
            'base_url' => $this->request->getBaseUrl(),
            'session'  => $this->session,
        );

        // Viewクラスのインスタンス作成
        $view = new View($this->application->getViewDir(), $defaults);

        // ビューファイルが指定されなかった場合は、アクション名を使用
        if (is_null($template)) {
            $template = $this->action_name;
        }

        // ビューファイルへのパスにコントロール名を追加
        $path = $this->controller_name . '/' . $template;

        return $view->render($path, $variables, $layout);
    }

    /**
     * 404画面へリダイレクト
     * 
     * @throws HttpNotFoundException
     */
    protected function forward404()
    {
        throw new HttpNotFoundException('Forwarded 404 page from '
            . $this->controller_name . '/' . $this->action_name);
    }

    /**
     * 任意のURLへリダイレクト
     * 
     * @param string $url
     */
    protected function redirect($url)
    {
        // リダイレクトには絶対URLが必要なため、'https://'または、'http://'で始まっていない場合
        // Requestオブジェクトを元に絶対URLを組み立てる
        if (!preg_match('#http?://#', $url)) {
            $protocol = $this->request->isSsl() ? 'https://' : 'http://';
            $host = $this->request->getHost();
            $base_url = $this->request->getBaseUrl();

            $url = $protocol . $host . $base_url . $url;
        }

        // リダイレクト
        $this->response->setStatusCode(302, 'Found');
        $this->response->setHttpHeader('Location', $url);
    }

    /**
     * CSRFトークンを生成
     * 
     * @param string $form_name
     * @return string
     */
    protected function generateCsrfToken($form_name)
    {
        $key = 'csrf_tokens/' . $form_name;
        $tokens = $this->session->get($key, array());
        if (count($tokens) >= 10) {
            array_shift($tokens);
        }

        // CSRFトークンとなるランダムな文字列を生成
        $bytes = random_bytes(32);
        $token = str_replace(['/', '+', '='], '', base64_encode($bytes));
        $tokens[] = $token;

        $this->session->set($key, $tokens);

        return $token;
    }

    /**
     * CSRFトークンが妥当か判定
     * 
     * @param string $form_name
     * @param string $token
     * @return boolean
     */
    protected function checkCsrfToken($form_name, $token)
    {
        $key = 'csrf_tokens/' . $form_name;
        $tokens = $this->session->get($key, array());
        // セッションに一致するトークンが格納されているか判定
        if (false !== ($pos = array_search($token, $tokens, true))) {
            // 1度利用したトークンは削除する
            unset($tokens[$pos]);
            $this->session->set($key, $tokens);

            return true;
        }

        return false;
    }

    /**
     * E-mailの正当性を判定する
     * 
     * @param string $email
     * @return boolean
     */
    protected function CheckEmailAddress($email)
    {
        if(preg_match('/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD', $email)){
            list($username,$domain)=explode('@',$email);
            if(!checkdnsrr($domain,'MX') || !checkdnsrr($domain,'A')){
                return false;
            }
        return true;
        }
    return false;
    }

    /**
     * 指定されたアクションに認証が必要か判定
     * 
     * @param string $action
     * @return boolean
     */
    protected function needsAuthentication($action)
    {
        if ($this->auth_actions === true
            || (is_array($this->auth_actions)
                && in_array($action, $this->auth_actions))) {
                return true;
        }
        
        return false;
    }

    public function createThumb($filename1, $filename2, $resize = 100)
    {
        //画像ロード
        list($w1, $h1, $type) = getimagesize($filename1);
        switch ($type) {
            case 1://GIF
                $image1 = imagecreatefromgif($filename1);
                break;
            case 2://JPEG
                $image1 = imagecreatefromjpeg($filename1);
                break;
            case 3://PNG
                $image1 = imagecreatefrompng($filename1);
                break;
            default:
                return false;
        }
        $x = 0;
        $y = 0;
        $this->fitCover50($resize, $w1, $h1, $w2, $h2, $x, $y);
        //fitContain($resize, $w1, $h1, $w2, $h2);
        $image2 = ImageCreateTrueColor($w2, $h2);
        
        //縮小しながらコピー
        imagecopyresampled($image2, $image1, 0, 0, $x, $y, $w2, $h2, $w1, $h1);
        
        //変換した画像をファイルに保存
        switch ($type) {
            case 1://GIF
                imagegif($image2, $filename2);
                break;
            case 2://JPEG
                imagejpeg($image2, $filename2, 85);
                break;
            case 3://PNG
                imagepng($image2, $filename2);
                break;
        }
        //メモリ解放
        ImageDestroy($image1);
        ImageDestroy($image2);
    }
    //矩形範囲でトリミング（真ん中を切り取る）
   public function fitCover50($resize, &$w1, &$h1, &$w2, &$h2, &$x, &$y)
    {
        $w2 = $resize; //出力先は問答無用で矩形範囲のサイズ
        $h2 = $resize; //
        if ($w1 > $h1) {
            $x = floor(($w1 - $h1) / 2);	//開始位置調整
            $w1 = $h1;	//横長画像は幅を高さに合わせる
        } else {
            $y = floor(($h1 - $w1) / 2);	//開始位置調整
            $h1 = $w1;	//縦長画像は高さを幅に合わせる
 }
}
}
