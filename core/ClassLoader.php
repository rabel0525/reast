<?php
/**
 * クラスファイルの読み込みの自動化を行う、オートロードに関する処理をまとめたクラス
 */
class ClassLoader
{
    /**
     * オートロード対象となるディレクトリ
     */
    protected $dirs;

    /**
     * オートローダクラスを登録
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * クラスファイルの読み込み対象ディレクトリを登録
     * 
     * @param string $dir フルパスの登録対象ディレクトリ
     */
    public function registerDir($dir)
    {
        $this->dirs[] = $dir;
    }

    /**
     * オートロード時に呼ばれ、クラスファイルの読み込みを行う
     * 
     * @param string $class クラス名
     */
    public function loadClass($class)
    {
        foreach ($this->dirs as $dir) {
            $file = $dir . '/' . $class . '.php';
            if (is_readable($file)) {
                require $file;

                return;
            }
        }
    }
}