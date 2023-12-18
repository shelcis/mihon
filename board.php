<?php
session_start();
mb_internal_encoding("utf8");

// 1.ログインしていなければ、ログインページにリダイレクト
if (!isset($_SESSION['id'])) {
    header("Location:login.php");
}


if($_SERVER["REQUEST_METHOD"] == "POST"){
    $_SESSION["title"]=htmlentities($_POST["title"]??"",ENT_QUOTES); 
    $_SESSION["comments"]=htmlentities($_POST["comments"]??"",ENT_QUOTES); 
    $errors = validate_form();
    #エラーが起きてない場合
    if(empty($errors)){
        #データベースの接続、sql文insertを実行
        try{
            #PDOでhost=OOO、dbname=OOOに接続
            $pdo = new PDO("mysql:dbname=php_jissen;host=localhost;","root","");
            $pdo-> setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        
            #postテーブルに()内の内容を個数分入れる
            $stmt = $pdo->prepare("INSERT INTO post (user_id,title,comments) VALUES(?,?,?)");
            $stmt->execute(array($_SESSION["id"],$_SESSION["title"],$_SESSION["comments"])); 
        } catch(PDOException $e){
            $e->getMessage();
        }
        
        $pdo =null;
        
        $SESSION = array();
        
        if(isset($_COOKIE["session_name()"])){
            setcookie("session_name()","",time() - 1800,"/");

            
        header("Location:board.php");
        }
    }
}

function validate_form(){
    $form_errors = array();

    $input["title"]=trim($_POST["title"]??"");
    if(strlen($input["title"])==0){
        $form_errors["title"]="タイトルを入力してください";
    }

    $input["comments"]=trim($_POST["comments"]??"");
    if(strlen($input["comments"])==0){
        $form_errors["comments"]="コメントを入力してください";
    }


    return $form_errors;
} 

try {
    $pdo = new PDO("mysql:dbname=php_jissen;host=localhost;", "root", ""); // DBに接続
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // エラーモードを「警告」に設定
    $posts = $pdo->query(" SELECT title,comments,name,posted_at FROM post INNER JOIN user ON post.user_id = user.id ORDER BY posted_at DESC");
    $pdo = NULL; // DB切断
} catch (PDOException $e) {
    $e->getMessage(); // 例外発生時にエラーメッセージを出力
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="style.css">

    <title>4eachblog</title>
</head>

<!-- Flexboxを利用してレイアウト設定 -->
<body>
    <!-- ロゴマーク -->
    <div class="logo">
        <img src="img/4eachblog_logo.jpg">
        <div class="logout">
            <p>こんにちは<?php echo $_SESSION["name"] ?>さん</p>
            <a href="logout.php">ログアウト</a>
        </div>
    </div>

    <header>
        <!-- メニューリスト -->
        <ul>
            <li>トップ</li>
            <li>プロフィール</li>
            <li>4eachについて</li>
            <li>登録フォーム</li>
            <li>問い合わせ</li>
            <li>その他</li>
        </ul>
    </header>

    <main>
    
    <div class="left">
            <!-- 見出し -->
            <h2>プログラミングに役立つ掲示板</h2>
        <form method="POST" action="">
            <div class="item">
                <h1 class="form_title">入力フォーム</h1>
                <label>タイトル</label>
                <input type="text" class="text" size="35" name="title"value="<?php echo $_SESSION["title"]??"";?>">
                <?php if (!empty($errors["title"])):?>
                    <p class="err_message"><?php echo $errors["title"];?></p>
                    <?php endif; ?>
            </div>
            <div class="item">
                <label>コメント</label>
                <textarea cols="40" rows="7" name="comments"><?php echo $_SESSION["comments"]??"";?></textarea>
                <?php if (!empty($errors["comments"])):?>
                    <p class="err_message"><?php echo $errors["comments"];?></p>
                    <?php endif; ?>
            </div>
            <div class="item">
                <input type="submit" class="submit" name="com" value="送信する">
            </div>
        </form>
        <?php foreach ($posts as $post) : ?>
            <div class='kiji'>
                <h3><?php echo $post["title"] ?></h3>
                <div class='contents'><?php echo $post["comments"] ?></div>
                <div class='handlename'>投稿者：<?php echo $post["name"]; ?></div>
                <div class='time'>投稿時間：
                    <?php
                    // 日付のフォーマットの変更
                    $posted_at = new DateTime($post["posted_at"]);
                    echo $posted_at->format('Y年m月d日 H時i分');
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="right">
            <!-- 小タイトル + 箇条書きリスト -->
            <h3>人気の記事</h3>
            <ul>
                <li>PHPオススメ本</li>
                <li>PHP MyAdminの使い方</li>
                <li>今人気のエディタ Top5</li>
                <li>HTMLの基礎</li>
            </ul>
            <!-- 小タイトル + 箇条書きリスト -->
            <h3>オススメリンク</h3>
            <ul>
                <li>インターノウス株式会社</li>
                <li>XAMPPのダウンロード</li>
                <li>Eclipseのダウンロード</li>
                <li>Bracketsのダウンロード</li>
            </ul>
            <!-- 小タイトル + 箇条書きリスト -->
            <h3>カテゴリ</h3>
            <ul>
                <li>HTML</li>
                <li>PHP</li>
                <li>MySQL</li>
                <li>JavaScript</li>
            </ul>
        </div>
    </main>

</body>
</html>