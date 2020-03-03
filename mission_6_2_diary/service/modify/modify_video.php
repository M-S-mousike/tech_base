<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes">
    	<meta charset="utf-8"/>
        <title>mission_6_modify_video</title>
    </head>
    <body>
  	  <h1>動画の編集・削除</h1>

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
$video_year = "";
$video_month = "";
$video_day = "";

if($login){
		//セッションに対象年月日が保存されている場合
		if(!empty($_SESSION["video_year"]) && !empty($_SESSION["video_month"]) && !empty($_SESSION["video_day"])){
				$video_year = $_SESSION["video_year"];
				$video_month = $_SESSION["video_month"];
				$video_day = $_SESSION["video_day"];
				
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
		//以下、編集処理に入る
		else{
				//A. テーブルへの書き込み（編集）
			  		//(i) プリペアドステートメントを用意
						$sql = 'update video_date
									set date=:date
									where id=:id';
									
					//(ii) プリペアドステートメントをセットする
						$stmt = $pdo->prepare($sql);
																	
					//(iii) 入力する変数を列に紐付ける（bindParam）。データ型に注意！
						$stmt -> bindParam(':id', $video_id, PDO::PARAM_INT);
						$stmt -> bindParam(':date', $new_date, PDO::PARAM_STR);
						
					//(iv) 変数に代入
						//投稿のIDを代入
						$video_id = (int)$_POST["edit_id"];
						
						//セレクトボックスで選択された日付を取得
						$new_date = $_POST["year"].$_POST["month"].$_POST["day"];
						
					//(v) 命令文を実行（プリペアドステートメントを実行）
						$stmt -> execute();
						
				//B. アップロードの完了を知らせる
					$edit_message2 = "日付の編集に成功しました！";
			
		}
}
?>

<?php
//PHP5. 削除機能

$delete_message2 = "";

//削除ボタンが押された場合
if(isset($_POST["delete_execute"])){
		if(empty($_POST["delete_id"]) || empty($_POST["delete_name"])){
				$delete_message2 = "削除対象が設定されていません！";
		}
		else{
				//投稿IDとファイル名を取得
				$delete_id = (int)$_POST["delete_id"];
				$delete_name = $_POST["delete_name"];
				
				//（１）データベースからの削除
					//プリペアドステートメントを用意
					$sql = 'delete from video_date where id=:id';
					//プリペアドステートメントをセットする
					$stmt = $pdo->prepare($sql);
					//変数を紐付ける
					$stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
					//削除処理（プリペアドステートメント）を実行
					$stmt->execute();
					
				//（２）ディレクトリからのファイルの削除（unlink関数）
					//パスを定義
					$directory = "../../uploaded/video/".$delete_name;
					
					//削除
					unlink($directory);
					
				$delete_message2 = "削除に成功しました。";
		}
}
?>

<?php

//PHP6. 対象の動画を取得
if($date_send){
		//日付検索用の変数を定義
		$video_date = $video_year.$video_month.$video_day;

		//データベースから、投稿日時が一致するデータを取得。
			//SQL命令文を定義
				$sql = 'SELECT * FROM video_date
							where userID=:userID and date=:date';
		
			//プリペアドステートメントをセットする
				$stmt = $pdo->prepare($sql);
			
			//変数を紐付ける
				$stmt->bindParam(':userID', $id, PDO::PARAM_INT);
				$stmt->bindParam(':date', $video_date, PDO::PARAM_STR);
												
			//上記のSQL文を実行
				$stmt->execute();
			
			//fetchAllメソッドでデータを配列として取得（該当データが0の場合、空の配列を返す）
				$video_result = $stmt->fetchAll();
}
?>

<?php
//PHP7. 編集対象の動画を取得
//メッセージ等を初期化
$edit_message = "";
$edit_id = 0;

if($date_send && isset($_POST["edit_send"])){
		if(empty($_POST["edit_number"])){
				$edit_message = "編集対象の投稿番号が入力されていません！";
		}
		else{
				//投稿番号を取得
				$edit_number = (int)$_POST["edit_number"];
				
				//A. 該当する投稿番号がない場合
				if($edit_number <1 || $edit_number > count($video_result)){
						$edit_message = "該当する投稿番号がありません。";
				}
				//B. そうでない場合、対象となっている投稿のデータを取得
				else{
						$edit_id = $video_result[$edit_number-1]["id"];
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
				if($delete_number <1 || $delete_number > count($video_result)){
						$delete_message = "該当する投稿番号がありません。";
				}
				//B. そうでない場合、対象となっている投稿のデータを取得
				else{
						$delete_id = $video_result[$delete_number-1]["id"];
						$delete_name = $video_result[$delete_number-1]["filename"];
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
		echo $video_year."年".$video_month."月".$video_day."日";
}

//取得できていない場合
else{
		echo "<em>まず<a href=\"../contents/view.php\">こちら</a>で編集・削除対象の日付を選択してください。</em>";
}
?>
	</p>
	<hr>
	
	<p>
		<h4>対象の動画</h4>
<?php
/*
取得した動画の表示
$video_result変数に配列の形で格納されているので、foreachで中身を出力する
データ取得済みの場合のみ、以下の処理を行う
*/
if($date_send){
		//（１）結果が空の場合
		if(count($video_result) == 0){
				echo "該当する動画がありません。";
		}
		
		//（２）空ではない場合
		else{
				//カウンタを定義
				$counter = 0;
		
				foreach ($video_result as $row){
						$counter ++;
						$video_source = "../../uploaded/video/".$row['filename'];
						echo "投稿番号".$counter."：<br>";
						echo "<video src=".$video_source." height=300px controls></video><br>";
					}
				
				echo "<em>動画を再生するにはvideoタグをサポートしたブラウザが必要です。</em>";
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
			西暦<input type="text" size="4" name="year" value="<?php echo $video_year;?>">年
			<select name="month">
			<?php
				//option属性を表示する
				for($i=1; $i<=12; $i++){
						//月を0埋めする
						$month_pad = sprintf('%02d', $i);
					
						echo "<option value=\"".$month_pad."\"";
						
						//初期値に該当する場合
						if($month_pad == $video_month){
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
						if($day_pad == $video_day){
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
		<input type="submit" value="<?php if(!empty($edit_number)){echo "投稿番号".$edit_number."の";}?>日付を編集" name="edit_execute">
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
		<input type="hidden" size="100" name="delete_name" value="<?php if($delete_selected){echo $delete_name;}?>">
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