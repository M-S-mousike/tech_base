<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes">
    	<meta charset="utf-8"/>
        <title>mission_6_logout</title>
    </head>
    <body>
  	  <h1>ログアウトが完了しました</h1>

<hr>
<div id="top">
 	   <form method="POST" action="">
  			 <p>
  				  <input type="submit" value="トップページに戻る" name="top"><br>
 			 </p> 	   
 	   </form>
</div>

<?php

//ログアウト（直接このページに来た場合にも備えて）
	// セッションの有効期限を30分に設定
	//（※必ずセッション開始前に設定すること）
	session_set_cookie_params(60*30);

	//セッションを開始
	session_start();
	//セッションを初期化
	$_SESSION = array();
	//セッションを破棄する
	session_destroy();

?>

<?php

//ボタンが押された場合に、以下の処理が行われる
	if(isset($_POST["top"])){		
			//トップページへジャンプし、処理を終了する
				header("Location: top.php");
				exit();
	}	

?>