<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes">
    	<meta charset="utf-8"/>
        <title>mission_6_welcome</title>
    </head>
    <body>
  	  <h1>Welcome!</h1>

<?php

//PHP1. まず、データベースへの接続を行う
		//（１）インスタンス作成の際の引数を定義
				/*
				まずDSN（Data Source Name）を定義
				*/
				$dsn = 'データベース名';
				
				//次に、MySQLのユーザー名を定義
				$user = 'ユーザー名';
				
				//ユーザー名に対応するパスワードを定義
				$password = 'パスワード';
				
				//例外処理の為のオプションも設定しておく（オプションは連想配列（＝ディクショナリ）で指定）。
				$option = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);
		
		//（２）PDOクラスのインスタンスを作成（名前は$pdoとする）。（（１）で定義した引数を使用。）
				$pdo = new PDO($dsn, $user, $password, $option);

?>


<?php

//PHP2. ユーザ情報の取得

// セッションの有効期限を30分に設定
//（※必ずセッション開始前に設定すること）
session_set_cookie_params(60*30);

// セッション開始
session_start();

if(!empty($_SESSION["id"]) && !empty($_SESSION["name"])){
	$id = $_SESSION["id"];
	$name = $_SESSION["name"];
	$message = "ようこそ！".$name."（ID：".$id."）さん！";
}else{
	$message = "ログインしてください！";
}

?>

<?php

//PHP3. ログインページへジャンプ

//ボタンが押された場合に、以下の処理が行われる
	if(isset($_POST["login"])){
			//セッションを初期化
			$_SESSION = array();
			//セッションを破棄する
			session_destroy();
		
			//ログインのページへジャンプし、処理を終了する
			header("Location: ../user/login.php");
			exit();
	}	

?>

<?php

//PHP4. ログアウトページへジャンプ

//ボタンが押された場合に、以下の処理が行われる
	if(isset($_POST["logout"])){
			//セッションを初期化
			$_SESSION = array();
			//セッションを破棄する
			session_destroy();
				
			//ログアウト完了を知らせるページへジャンプし、処理を終了する
			header("Location: ../user/logout.php");
			exit();
	}	

?>

<hr>
<div id="welcome">
	<?php echo $message; ?>
</div>

<div id="main">
　<h3>この素敵な世界のメインコンテンツ</h3>
	<p>
		<a href="upload.php">（１）日記の投稿ページへ</a>
	</p>
	<p>
		<a href="view.php">（２）日記の閲覧ページへ</a>
	</p>
	<p>
		<a href="select_date.php">（３）既存の投稿の日付一覧</a>
	</p>
</div>

<div id="login">
 	   <form method="POST" action="">
  			 <p>
  				  <input type="submit" value="（再）ログインされる方はこちら" name="login"><br>
 			 </p> 	   
 	   </form>
</div>

<div id="logout">
 	   <form method="POST" action="">
  			 <p>
  				  <input type="submit" value="ログアウトされる方はこちら" name="logout"><br>
 			 </p> 	   
 	   </form>
</div>