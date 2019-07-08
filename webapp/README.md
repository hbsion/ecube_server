# No Framework PHP MVC.

### ルート使用方法.
`routes.php`ファイルにルート定義し、呼出して使用:`[my project]/routes.php`
```php:routes.php
// ext) load::l('router')->add('/[uri path]','[template file name]');
load::l('router')->add('/','example');
// 正規表現を利用した Route 設定 (optional)
load::l('router')->add('/example/(.+)/([0-9]+)','home');
```

### コントローラ使用方法.
`controller`ディレクトリで`controller/file`を保存して使用:`[my project]/controller/example_c.php`

クラス名`Class Example_c {}`してコントローラー抽象クラスを拡張
```php:example_c.php
Class Example_c extends Controller
{
    public function __construct()
    {
        // URLセグメントから正規表現引数を取得 (optional を定義した場合)
        $reg = load::l('router')->reg_segments;
        // テンプレート変数 設定
        $this->setOutput(array('hello'=>"Hello World!"));
    }
}
```
### ビューとテンプレート使用方法.
`view`ディレクトリで`view/file`を保存して使用:`[my project]/view/example.php`

##### ビューを使用する場合
`[my project]/view/file`内で`extract(load::c('[controller name]')->getOutput());`を呼出使用
```html:example.php
<?php extract(load::c('example_c')->getOutput());?>
<h1><?php echo $hello;?></h1>
```
##### テンプレートも使用する場合
`[my project]/view/file`内で`load::v('[template file name]');`を呼出使用
```html:example
<?php extract(load::c('example_c')->getOutput());?>
// ヘッダーテンプレート呼出
<?php load::v('header');?>
<h1><?php echo $hello;?></h1>
// フッターテンプレート呼出
<?php load::v('footer');?>
```

### モデルとライブラリ使用する場合

##### モデルを使用する場合
`model`ディレクトリで`model/file`を保存して使用:`[my project]/model/example_m.php`

`[my project]/controller/file`内で`load::m('[model name]')->[method name];`を呼出使用

クラス名`Class Example_m {}`してモデル抽象クラスを拡張
```php:example_m.php
Class Example_m extends Model
{
    public function __construct()
    {
        // 処理
    }
}
```
##### ライブラリを使用する場合
`model`ディレクトリで`[my project]/library`を保存して使用:`[my project]/library/example_1.php`

`[my project]/controller/file`内で`load::l('[library name]')->[method name]`を呼出使用

クラス名`Class Example_l {}`してモデル抽象クラスを拡張
```php:example_l.php
Class Example_l extends Model
{
    public function __construct()
    {
        // 処理
    }
}
```
### 呼出方法(まとめ)
 ```php:load
 load::c('[controller name]',[arguments array optional]);
 load::m('[model name]',[arguments array optional]);
 load::v('[view name]',[arguments array optional]);
 load::l('[library name]',[arguments array optional]);
 ```

### 環境切替
`config-dev.php`を`削除`すると`本番環境`の`config.php`へ切り替わる.
