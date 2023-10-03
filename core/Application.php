<?php

/**
 * フレームワークの中心となるクラス
 */
abstract class Application
{

    protected $debug = false;
    protected $request;
    protected $response;
    protected $session;
    protected $db_manager;

    /**
     * コンストラクタ
     * 
     * @param boolean $debug デバッグモード true : 全てのエラーを表示
     */
    public function __construct($debug = false)
    {
        $this->setDebugMode($debug);
        $this->initialize();
        $this->configure();
    }

    /**
     * デバッグモードを設定
     * 
     * @param boolean $debug true : 全てのエラーを表示
     */
    protected function setDebugMode($debug)
    {
        if ($debug) {
            $this->debug = true;
            ini_set('display_errors', 1);
            error_reporting(-1);
        } else {
            $this->debug = false;
            ini_set('display_errors', 0);
        }
    }

    /**
     * アプリケーションを初期化
     */
    protected function initialize()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->db_manager = new DbManager();
        $this->router = new Router($this->registerRoutes());
    }

    protected function configure()
    {

    }

    /**
     * プロジェクトのルートディレクトリを取得
     * 
     * @return string 絶対バスのルートディレクトリ
     */
    abstract public function getRootDir();

    /**
     * ルーティングを取得
     * 
     * @return array 
     */
    abstract protected function registerRoutes();

    /**
     * デバッグモードか判定
     * 
     * @return boolean
     */
    public function isDebugMode()
    {
        return $this->debug;
    }

    /**
     * リクエストを取得
     * 
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * レスポンスを取得
     * 
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * セッションを取得
     * 
     * @return Session
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * DbManagerを取得
     * 
     * @return DbManager
     */
    public function getDbManager()
    {
        return $this->db_manager;
    }

    /**
     * コントローラファイルの格納ディレクトリを取得
     * 
     * @return string
     */
    public function getControllerDir()
    {
        return $this->getRootDir() . '/controllers';
    }

    /**
     * ビューファイルの格納ディレクトリを取得
     * 
     * @return string
     */
    public function getViewDir()
    {
        return $this->getRootDir() . '/views';
    }

    /**
     * モデルファイルの格納ディレクトリを取得
     * 
     * @return string
     */
    public function getModelDir()
    {
        return $this->getRootDir() . '/models';
    }

    /**
     * ドキュメントルートへのパスを取得
     * 
     * @return string
     */
    public function getWebDir()
    {
        return $this->getRootDir() . '/web';
    }

    /**
     * アプリケーションを実行
     */
    public function run()
    {
        try {
            // ベースを除いたURLを元にルーティング定義を取得
            $params = $this->router->resolve($this->request->getPathInfo());
            if ($params === false) {
                // 一致するルーティング定義が見つからなかった
                throw new HttpNotFoundException('不正なリクエストがこれから⇒ ' . $this->request->getPathInfo());
            }

            // 取得したルーティングパラメーターからコントローラ名とアクション名を特定
            $controller = $params['controller'];
            $action = $params['action'];
            // runActionメソッドでアクションを実行
            $this->runAction($controller, $action, $params);
        } catch (HttpNotFoundException $e) {
            $this->render404Page($e);
        } catch (UnauthorizedActionException $e) {
            // ログイン画面のアクションを呼び出す
            list($controller, $action) = $this->login_action;
            $this->runAction($controller, $action);
        }

        // レスポンスを返す
        $this->response->send();
    }

    /**
     * 指定されたアクションを実行
     * 
     * @param string $controller_name コントローラ名
     * @param string $action アクション名
     * @param array $params コントローラに渡すパラメータ
     */
    public function runAction($controller_name, $action, $params = array())
    {
        // コントローラのクラス名はコントローラに'Controller'をつける
        $controller_class = ucfirst($controller_name) . 'Controller';

        $controller = $this->findController($controller_class);
        if ($controller === false) {
            // コントローラ定義が見つからなかった
            throw new HttpNotFoundException($controller_class . ' コントローラが見つからないよ。');
        }

        //コントローラのアクションを実行
        $content = $controller->run($action, $params);

        $this->response->setContent($content);
    }

    /**
     * コントローラ名から対応するControllerインスタンスを取得
     * 
     * @param string $controller_name コントローラ名
     * @return Contoroller|false
     */
    protected function findController($controller_class)
    {
        // コントローラクラスが定義済みか確認し、
        if (!class_exists($controller_class)) {
            // 未定義ならファイルを読み込む
            $controller_file = $this->getControllerDir() . '/' . $controller_class . '.php';

            if (!is_readable($controller_file)) {
                return false;
            } else {
                require_once $controller_file;

                if (!class_exists($controller_class)) {
                    return false;
                }
            }
        }

        return new $controller_class($this);
    }

    /**
     * 404エラー画面を設定
     * 
     * @param Exception $e
     */
    protected function render404Page($e)
    {
        $this->response->setStatusCode(404, 'Not Found');
        $message = $this->isDebugMode() ? $e->getMessage() : 'そのページは見つかりません。';
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        $this->response->setContent(<<<EOF
<!DOCTYPE html>
<html>
<head>
    <title>404 - Reast</title>
    <meta charset="UTF-8">
</head>
<body>
<h3>What happened?</h3>
    {$message}
<p><a href="/">ログイン画面・ホームに戻る</a></p>
</body>
</html>
EOF
        );
    }
}