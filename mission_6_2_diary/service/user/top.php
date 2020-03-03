<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes">
    	<meta charset="utf-8"/>
        <title>mission_6_top</title>
    </head>
    <body>
  	  <h1>トップページ</h1>

<?php

//まず、データベースへの接続、テーブルの構築を行う

/*

I. データベースへの接続

*/
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


/*

II. テーブルの作成
※データベースは作成済みのため、作成するのはテーブルだけでよい。
*/
		
		/*
		（１）実行する命令文を定義。
		　これは後で、インスタンス（$pdo）のメソッドに対する引数として利用する。
		*/
		
					/*A. まず、「（既に同じテーブルが存在しない場合にのみ）テーブルを作る」という命令を入力。
							「usertable」はテーブル名。*/
					$sql = "CREATE TABLE IF NOT EXISTS usertable"
					." ("
					/*B. フィールド名「id」に対して、整数型（INT）を指定。（＝投稿番号）
							AUTO_INCREMENT属性は、その名の通り自動的に数値が増加するものである。
							PRIMARY KEYは、値の重複がある場合にエラーを発生させる（idを一意にする為に設定）。
					*/
					. "id INT AUTO_INCREMENT PRIMARY KEY,"
					/*C. フィールド名「email」に対して、可変長文字型（char）を指定。（＝メールアドレス）
							カブっては困るので、UNIQUEを指定*/
					. "email varchar(255) UNIQUE,"
					//D. フィールド名「name」に対して、可変長文字型（char）を指定。（＝ユーザ名）
					. "name varchar(32),"
					/*E. フィールド名「password」に対して、可変長文字型（varchar）を指定。（＝パスワード）
							ハッシュすることを見越して長めに設定*/
					. "password varchar(255)"					
					.");";
			
		/*
		（２）命令文を実行
		　（１）で定義した命令文（$sql）を実行する。
			このとき、インスタンス（$pdo）のqueryメソッドを使用する。
			（※アロー演算子（->）の使用法
			　　：「$インスタンスを代入した変数名->呼び出したいインスタンスのプロパティまたはメソッド名」）
		*/
				
					//命令文の実行結果を$stmt変数に代入
					$stmt = $pdo->query($sql);
					
					/*
					queryメソッドとは、プリペアドステートメントは使わずに、そのままSQL文を実行できるものである。
					引数に指定したSQL文（ここでは$sql）がデータベースに対して発行される。
					結果（戻り値）として、SQL文を発行した結果が含まれているPDOStatementクラスのオブジェクトを返す。
					（ここでは、変数$stmtに結果（＝Statmentクラスのオブジェクト）を代入している）
					*/
	
?>

<?php

//ユーザ情報の取得

// セッションの有効期限を30分に設定
//（※必ずセッション開始前に設定すること）
session_set_cookie_params(60*30);

// セッション開始
session_start();

//ログインを判定する変数
$login = false;

if(!empty($_SESSION["id"]) && !empty($_SESSION["name"])){
	$id = $_SESSION["id"];
	$name = $_SESSION["name"];
	$message = "ユーザ名".$name."（ID：".$id."）としてログインしています";
	$login = true;
}else{
	$message = "現在ログインしていません";
}

?>

<?php

//ページジャンプ
	//sendボタンが押された場合に、以下の処理が行われる
		if(isset($_POST["login"])){
				//ログインのページへジャンプし、処理を終了する
				header("Location: login.php");
				exit();
		}	
		elseif(isset($_POST["signup"])){
				//新規登録のページへジャンプし、処理を終了する
				header("Location: sign_up.php");
				exit();
		}
		elseif(isset($_POST["welcome"])){
				//ログイン後のメインページへジャンプし、処理を終了する
				header("Location: ../contents/welcome.php");
				exit();
		}
			 	   		
?>

<hr>
<div id="message">
<em><?php echo $message;?></em>
</div>

<?php

//welcomeページへのリンク

//ログイン済みの場合のみ、welcomeページへのリンクを表示する
if($login){
		echo "<form method=\"POST\" action=\"\">";
		echo "<p>
					<input type=\"submit\" value=\"ログイン後のメインページへ\" name=\"welcome\"><br>
					</p>";
		echo "</form>";
}
?>

<div id="form">
	<div id="log_in">
	    <h3>既に登録されている場合はこちら</h3>	
    	<form method="POST" action="">
  			 <p>
  				  <input type="submit" value="ログインページへ" name="login"><br>
 			 </p>
 	   </form>
 	</div>
 	   
	<div id="register">
 	   <h3>まだ登録されていない場合はこちら</h3>	
 	   <form method="POST" action="">
  			 <p>
  				  <input type="submit" value="初回登録ページへ" name="signup"><br>
 			 </p> 	   
 	   </form>
 	</div>
</div>