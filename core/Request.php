<?php
/**
 * @author Katsuhiro Ogawa <fivestar@nequal.jp> 変更あり
 */
class Request
{
    /**
     * リクエストURIを取得
     * @return string
     */
    public function getRequestUri()
    {
// 現在のURI（ドメイン以下のパス）を返す。
// http://example.com/foo/bar/index.php(フロントコントローラー)/list?foo=bar
// foo/bar/index.php(フロントコントローラー)/list?foo=bar が リクエストURL
        return $_SERVER['REQUEST_URI'];
    }

    /** 
     * フロントコントローラーまでのパスがベースURL
     * URLにフロントコントローラーまでのフルパスが入力されている時
     * http://example.com/foo/bar/index.php(フロントコントローラー)/list
     * /foo/bar/index.php が ベースURL
     * 
     * URLにフロントコントローラーのindex.phpファイルがない時
     * http://example.com/foo/bar/list
     * /foo/bar/ が ベースURL
     * 
     * URLにフロントコントローラーまでのフルパスがない時 （普通はこれ）
     * http://example.com/list
     * ベースURLはない。
     *
     * @return string
     */
// 要するに このメソッドでは 👇 ながなが書きましたが結論はこれだけです。
// 🔴リクエストされた URL のなかから ベースURLを取得したい
    public function getBaseUrl()
    {
// http://example.com/foo/bar/index.php/list なら
// １⃣ `/foo/bar/index.php`
// http://example.com/foo/bar/list なら
// ２⃣  `/foo/bar/`
// http://example.com/list なら
// ３⃣ ``
        $script_name = $_SERVER['SCRIPT_NAME'];
        $request_uri = $this->getRequestUri();

//1⃣ URLに$script_name が 完全に含まれるとき
        if (0 === strpos($request_uri, $script_name)) {
//http://localhost/web/index.php/user/yamadataro?name=tarro
// dd($script_name); // reast/web/index.php

            return $script_name;

// ２⃣ URLにindex.php(ファイル名がない時) // reast/web/ の時
        } else if (0 === strpos($request_uri, dirname($script_name))) {
            // http://localhost/web/user/yamadataro?name=tarro
// dd(rtrim(dirname($script_name), '/')); // /web

            return rtrim(dirname($script_name), '/');

// 追加 url に web がない場合 // reast/index.php の時
// perfectPHPでは必要ない reast が含まれるため追加の処理
        } else if (0 === strpos($request_uri, str_replace('/web', '', $script_name))) {
// dd(str_replace('/web', '', $script_name)); // reast/index.php

            
            return str_replace('/web', '', $script_name);

// ３⃣ http://localhost/user/yamadataro?name=tarro
        } else {
// reastというアプリケーション名があるため工夫する必要がある
// ドメインからindex.php含むまでのパスは ベースURLとして処理するため。
            return ''; // これはよくないフレームワークのコアクラスにこのようなプロジェクトに依存するような書き方はダメ
                    // env や configファイルから取得するとかした方がいい
                    // もとのコードをあまり変更したくないので。
            //return ''; パーフェクトPHPでは空を返す。
        }
    }

    /**
     * PATH_INFOを取得
     * http://example.com/foo/bar/index.php/list?foo=bar
     * フロントコントローラー以下でクエリパラメーターを含めない /list が $PATH_INFO
     * ?foo=bar(クエリパラメーター)はpath_infoではないので削除
     *
     * @return string
     */
    public function getPathInfo()
    {
// request_urlからbase_urlとクエリパラメーターを削除して作成する。
        $base_url = $this->getBaseUrl();
// request_uri は '/base_url/path_info?query=value' でできている。
        $request_uri = $this->getRequestUri();

        if (false !== ($pos = strpos($request_uri, '?'))) {
// GETパラメーターを削除している
            $request_uri = substr($request_uri, 0, $pos);
        }

// リクエストURLからベースURLを削除して$path_infoを作る
        $path_info = (string) substr($request_uri, strlen($base_url));
        // dd($path_info); //`list`が取得できる
        // $str = 'abcdefg' substr($str,3); 返り値は 'defg'

        return $path_info;
    }

// +
    /**
     * リクエストメソッドがPOSTかどうか判定
     *
     * @return boolean
     */
    public function isPost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return true;
        }

        return false;
    }
// +
    /**
     * GETパラメータを取得
     *
     * @param string $name
     * @param mixed $default 指定したキーが存在しない場合のデフォルト値
     * @return mixed
     */
    public function getGet($name, $default = null)
    {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }

        return $default;
    }
// +
    /**
     * POSTパラメータを取得
     *
     * @param string $name
     * @param mixed $default 指定したキーが存在しない場合のデフォルト値
     * @return mixed
     */
    public function getPost($name, $default = null)
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }

        return $default;
    }
// +
    /**
     * FILEパラメータを取得
     *
     * @param string $name
     * @param mixed $default 指定したキーが存在しない場合のデフォルト値
     * @return mixed
     */
    public function getFile($name, $name2, $default = null)
    {
        if (isset($_FILES[$name])) {
            return $_FILES[$name][$name2];
        }

        return $default;
    }
// +
    /**
     * ホスト名を取得
     *
     * @return string
     */
    public function getHost()
    {
        if (!empty($_SERVER['HTTP_HOST'])) {
            return $_SERVER['HTTP_HOST'];
        }
        
        return $_SERVER['SERVER_NAME'];
    }
// +
    /**
     * SSLでアクセスされたかどうか判定
     *
     * @return boolean
     */
    public function isSsl()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            return true;
        }
        return false;
    }
}