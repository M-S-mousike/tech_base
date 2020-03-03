<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes">
    	<meta charset="utf-8"/>
        <title>mission_6_modify_comment</title>
    </head>
    <body>
  	  <h1>コメントの編集・削除</h1>

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

$login = false;

if(!empty($_SESSION["id"]) && !empty($_SESSION["name"])){
	$id = $_SESSION["id"];
	$name = $_SESSION["name"];
	$message = "ようこそ！".$name."（ID：".$id."）さん！";
	$login = true;
}else{
	$message = "ログインしてください！";
}

?>

<?php

//PHP3. セッションから対象の日付を取得
//初期値を設定
$date_send = false;
$comment_year = "";
$comment_month = "";
$comment_day = "";

if($login){
		//セッションに対象年月日が保存されている場合
		if(!empty($_SESSION["comment_year"]) && !empty($_SESSION["comment_month"]) && !empty($_SESSION["comment_day"])){
				$comment_year = $_SESSION["comment_year"];
				$comment_month = $_SESSION["comment_month"];
				$comment_day = $_SESSION["comment_day"];
				
				$date_send = true;
		}
}

?>


<?php
//PHP4. 編集機能

$edit_message2 = "";

//日付が選択されているかを判定（送信後）
$date_selected = false;

if($login && isset($_POST["edit_execute"])){
		if(!empty($_POST["year"]) && !empty($_POST["month"]) && !empty($_POST["day"])){
				if(preg_match("/^[0-9]{1,4}$/", $_POST["year"])){
						$date_selected = true;
				}
				else{
						$edit_message2 = "<em>西暦の形式が正しくありません</em>";
				}
		}
		else{
				$edit_message2 = "<em>日付を選択してください！</em>";
		}
}

//日付が選択されている場合のみ、処理に移る
if($date_selected){
		if(empty($_POST["edit_id"])){
				$edit_message2 = "編集対象番号が指定されていません。";
		}
		elseif(empty($_POST["comment"])){
				$edit_message2 = "コメントが空です。";
		}
		//以下、編集処理に入る
		else{
				//A. テーブルへの書き込み（編集）
			  		//(i) プリペアドステートメントを用意
						$sql = 'update comment_date
									set comment=:comment, date=:date
									where id=:id';
									
					//(ii) プリペアドステートメントをセットする
						$stmt = $pdo->prepare($sql);
																	
					//(iii) 入力する変数を列に紐付ける（bindParam）。データ型に注意！
						$stmt -> bindParam(':id', $comment_id, PDO::PARAM_INT);
						$stmt -> bindParam(':comment', $new_comment, PDO::PARAM_STR);
						$stmt -> bindParam(':date', $new_date, PDO::PARAM_STR);
						
					//(iv) 変数に代入
						//投稿のIDを代入
						$comment_id = (int)$_POST["edit_id"];
					
						//コメントを変数に代入
						$new_comment = $_POST["comment"];
						
						//セレクトボックスで選択された日付を取得
						$new_date = $_POST["year"].$_POST["month"].$_POST["day"];
						
					//(v) 命令文を実行（プリペアドステートメントを実行）
						$stmt -> execute();
						
				//B. アップロードの完了を知らせる
					$edit_message2 = "コメントの投稿に成功しました！";
			
		}
}
?>

<?php
//PHP5. 削除機能

$delete_message2 = "";

//削除ボタンが押された場合
if(isset($_POST["delete_execute"])){
		if(empty($_POST["delete_id"])){
				$delete_message2 = "削除対象の投稿番号が入力されていません！";
		}
		else{
				//投稿IDを取得
				$delete_id = (int)$_POST["delete_id"];
				
				//削除を実施
					//プリペアドステートメントを用意
					$sql = 'delete from comment_date where id=:id';
					//プリペアドステートメントをセットする
					$stmt = $pdo->prepare($sql);
					//変数を紐付ける
					$stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
					//削除処理（プリペアドステートメント）を実行
					$stmt->execute();
					
				$delete_message2 = "削除に成功しました。";
		}
}
?>

<?php

//PHP6. 対象のコメントを取得
if($date_send){
		//日付検索用の変数を定義
		$comment_date = $comment_year.$comment_month.$comment_day;

		//3つのデータベースそれぞれから、投稿日時が一致するデータを取得。
			//A. まず、該当するコメントの内容を取得
				//SQL命令文を定義
					$sql = 'SELECT * FROM comment_date
								where userID=:userID and date=:date';
			
				//プリペアドステートメントをセットする
					$stmt = $pdo->prepare($sql);
				
				//変数を紐付ける
					$stmt->bindParam(':userID', $id, PDO::PARAM_INT);
					$stmt->bindParam(':date', $comment_date, PDO::PARAM_STR);
													
				//上記のSQL文を実行
					$stmt->execute();
				
				//fetchAllメソッドでデータを配列として取得（該当データが0の場合、空の配列を返す）
					$comment_result = $stmt->fetchAll();
}
?>

<?php
//PHP7. 編集対象のコメントを取得
//メッセージ等を初期化
$edit_message = "";
$edit_id = 0;
$edit_comment = "";

if($date_send && isset($_POST["edit_send"])){
		if(empty($_POST["edit_number"])){
				$edit_message = "編集対象の投稿番号が入力されていません！";
		}
		else{
				//投稿番号を取得
				$edit_number = (int)$_POST["edit_number"];
				
				//A. 該当する投稿番号がない場合
				if($edit_number <1 || $edit_number > count($comment_result)){
						$edit_message = "該当する投稿番号がありません。";
				}
				//B. そうでない場合、対象となっている投稿のデータを取得
				else{
						$edit_id = $comment_result[$edit_number-1]["id"];
						$edit_comment = $comment_result[$edit_number-1]["comment"];
				}
		}
}
?>

<?php
//PHP8. 削除対象の番号を取得

$delete_message = "";
$delete_selected = false;

//削除ボタンが押された場合
if($login && isset($_POST["delete_send"])){
		if(empty($_POST["delete_number"])){
				$delete_message = "削除対象の投稿番号が入力されていません！";
		}
		else{
				//投稿番号を取得
				$delete_number = (int)$_POST["delete_number"];
				
				//A. 該当する投稿番号がない場合
				if($delete_number <1 || $delete_number > count($comment_result)){
						$delete_message = "該当する投稿番号がありません。";
				}
				//B. そうでない場合、対象となっている投稿のデータを取得
				else{
						$delete_id = $comment_result[$delete_number-1]["id"];
				}
				
				$delete_selected = true;
		}
}
?>

<?php

//PHP9. ログインページへジャンプ

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

//PHP10. ログアウトページへジャンプ

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
<div id="top">
	<?php echo $message; ?>
</div>

<div id="main">
	<p>
		<h4>対象の日付：</h4>
<?php
//選択された日付の表示

//セッションから取得できている場合
if($date_send){
		echo $comment_year."年".$comment_month."月".$comment_day."日";
}

//取得できていない場合
else{
		echo "<em>まず<a href=\"../contents/view.php\">こちら</a>で編集・削除対象の日付を選択してください。</em>";
}
?>
	</p>
	<hr>
	
	<p>
		<h4>対象のコメント</h4>
<?php
/*
取得したコメントの表示
$comment_result変数に配列の形で格納されているので、foreachで中身を出力する
データ取得済みの場合のみ、以下の処理を行う
*/
if($date_send){
		//（１）結果が空の場合
		if(count($comment_result) == 0){
				echo "該当するコメントがありません。";
		}
		
		//（２）空ではない場合
		else{
				//カウンタを定義
				$counter = 0;
		
				foreach ($comment_result as $row){
						$counter ++;
						
						//投稿内容の中の改行コードは、<br>に変換してから出力する
						$comment = nl2br($row['comment']);
						echo "投稿番号".$counter."：<br>",$comment,"<br><br>";		
					}
		}
}
?>
	</p>
	<hr>
	
	<p>
		<h4>（１）編集</h4>
		<p>
		<form method="POST">
		編集対象の投稿番号を入力：<input type="text" size="4" name="edit_number" value=""><br>
		<input type="submit" value="投稿番号を決定" name="edit_send">
		</forn>
		<?php 
		if($login && $date_send && isset($_POST["edit_send"])){
				echo "<br>",$edit_message;
		}
		?>
		</p>
		<form method="POST">
		<p>
		<input type="hidden" size="100" name="edit_id" value="<?php if(!empty($edit_id)){echo $edit_id;}?>">
		・日記の日付を選択（変更）<br>
			西暦<input type="text" size="4" name="year" value="<?php echo $comment_year;?>">年
			<select name="month">
			<?php
				//option属性を表示する
				for($i=1; $i<=12; $i++){
						//月を0埋めする
						$month_pad = sprintf('%02d', $i);
					
						echo "<option value=\"".$month_pad."\"";
						
						//初期値に該当する場合
						if($month_pad == $comment_month){
								echo "selected>";
						}
						//それ以外の場合
						else{
								echo ">";
						}
						echo $i;
						echo"</option>";
				}
			?>
			</select>月
			<select name="day">
			<?php
				//option属性を表示する
				for($i=1; $i<=31; $i++){
						//月を0埋めする
						$day_pad = sprintf('%02d', $i);
					
						echo "<option value=\"".$day_pad."\"";
						
						//初期値に該当する場合
						if($day_pad == $comment_day){
								echo "selected>";
						}
						//それ以外の場合
						else{
								echo ">";
						}
						echo $i;
						echo"</option>";
				}
			?>
			</select>日<br>
		</p>
		<p>
		・コメント（日記の内容）：<br>
		<textarea name="comment" rows="4" cols="60" ><?php if(!empty($edit_comment)){echo $edit_comment;}?></textarea><br>
		<input type="submit" value="編集内容を投稿" name="edit_execute">
		<?php if(!empty($edit_message2)){echo "<br>",$edit_message2;}?>
		</p>
		</form>
	<hr>
	
		<h4>（２）削除</h4>
		<p>
		<form method="POST">
		削除対象の投稿番号を入力：<input type="text" size="4" name="delete_number" value=""><br>
		<input type="submit" value="投稿番号を決定" name="delete_send">
		</forn>
		<?php 
		if($login && $date_send && isset($_POST["delete_send"])){
				echo "<br>",$delete_message;
		}
		?>
		</p>
		<p>
		<form method="POST">
		<input type="hidden" size="100" name="delete_id" value="<?php if($delete_selected){echo $delete_id;}?>">
		<?php
		if($delete_selected){
				echo "<input type=\"submit\" name=\"delete_execute\" value=\"投稿番号".$delete_number."の投稿を削除する\">";
		}
		echo $delete_message2;
		?>
		</p>
</div>

<div id="welcome">
	<hr>
	<p>
		<a href="../contents/welcome.php">ログイン後のメインページへ戻る</a>
	</p>
	<hr>
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