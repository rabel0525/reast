<?php

/**
 * ビューファイルの読み込みや、渡す変数の制御を行う
 */
class View
{
    protected $base_dir;
    protected $defaults;
    protected $layout_variables = array();

    /**
     * コンストラクタ
     * 
     * @param string $base_dir ビューファイル格納ディレクトリの絶対パス
     * @param array $defaults ビューファイルへ渡すデフォルト変数
     */
    public function __construct($base_dir, $defaults = array())
    {
        $this->base_dir = $base_dir;
        $this->defaults = $defaults;
    }

    /**
     * レイアウトファイルに渡す変数を指定
     * 
     * @param string $name
     * @param mixed $value
     */
    public function setLayoutVar($name, $value)
    {
        $this->layout_variables[$name] = $value;
    }

    /**
     * ビューファイルをレンダリング
     * 
     * @param string $_path ビューファイルへのパス
     * @param array $_variables ビューファイルへ渡す変数
     * @param mixed $_layout レイアウトファイル名
     * @return string レンダリング結果
     */
    public function render($_path, $_variables = array(), $_layout = false)
    {
        $_file = $this->base_dir . '/' . $_path . '.php';

        // 連想配列のキーを変数名に、連想配列の値を変数の値に展開
        extract(array_merge($this->defaults, $_variables));

        // アウトプットバッファリングを開始する
        ob_start();
        ob_implicit_flush(false);

        require $_file;

        // バッファの内容を文字列として取り出す
        $content = ob_get_clean();

        // レイアウトファイルが指定されていれば、含めてレンダリングする
        if ($_layout) {
            $content = $this->render($_layout,
                array_merge($this->layout_variables, array(
                    '_content' => $content,
                    )
                )
            );
        }

        return $content;
    }

    /**
     * 指定された文字列をHTMLエスケープ
     * 
     * @param string $string エスケープ対象
     * @return string 結果
     */
    public function escape($string)
    {
        return nl2br(htmlspecialchars($string, ENT_QUOTES, 'UTF-8'));
    }

    /**
     * 指定された文字列をHTMLエスケープ
     * 
     * @param string $string エスケープ対象
     * @return string 結果
     */
    public function escape_edit($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * 指定された文字列をHTMLエスケープ2
     * 
     * @param string $string エスケープ対象
     * @return string 結果
     */
    public function escape_for_body($string)
    {
        $pattern = '/((?:https?|ftp):\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+)/';
        $replace = '<a href="$1">$1</a>';
        $escaped = nl2br(htmlspecialchars($string, ENT_QUOTES, 'UTF-8'));
        $string_2  = preg_replace( $pattern, $replace, $escaped );
        return $string_2;
    }
}