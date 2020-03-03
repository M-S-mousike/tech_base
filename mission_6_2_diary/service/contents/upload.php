<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes">
    	<meta charset="utf-8"/>
        <title>mission_6_upload</title>
    </head>
    <body>
  	  <h1>メインコンテンツ（１）：日記のアップロード</h1>

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

//ログインを確認する変数を初期化
$login = false;

if(!empty($_SESSION["id"]) && !empty($_SESSION["name"])){
	$id = $_SESSION["id"];
	$name = $_SESSION["name"];
	$login = true;
	$message = $name."（ID：".$id."）さんがログインしています。";
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

<?php

//PHP5. ファイルのアップロード

//（１）アップロード（ファイルの取得＆ディレクトリの移動）
//表示するメッセージの初期化
if($login){
		$upload_message = "<em>アップロードするファイルを選んでください。</em>";
		$comment_message = "<em>コメントを投稿してください。</em>";
}else{
		$upload_message = "<em>まずはログインしてください！</em>";
		$comment_message = "<em>まずはログインしてください！</em>";
}

//ファイルのアップロードに成功したかを判定する変数
$image_uploaded = false;
$video_uploaded = false;

//ファイルの形式が動画か画像かを判定する変数
$is_image = false;
$is_video = false;

//日付が選択されているかを判定（送信後）
$date_selected = false;
if($login && isset($_POST["file"])){
		if(!empty($_POST["year"]) && !empty($_POST["month"]) && !empty($_POST["day"])){
				if(preg_match("/^[0-9]{1,4}$/", $_POST["year"])){
						$date_selected = true;
				}
				else{
						$upload_message = "<em>西暦の形式が正しくありません</em>";
				}
		}
		else{
				$upload_message = "<em>日付を選択してください！</em>";
		}
}

//ログイン完了＆日付選択完了＆ファイルアップロードボタンが押された場合のみ、以下の処理を行う
if($login && $date_selected && isset($_POST["file"])){
		// name属性upからPOST送信されたファイルが存在した場合のみ、処理に入る
		if(isset($_FILES['up']) && is_uploaded_file($_FILES['up']['tmp_name'])){
				//もとのファイル名を変数に代入
			    $filename = $_FILES ['up'] ['tmp_name'];
			    
			    //新たなファイル名を定義（日付（秒数まで）＋擬似乱数＋拡張子）
				$new_name = date("YmdHis");
				$new_name .= mt_rand();
				
				//イメージの型によって場合わけ（関数の戻り値はMIME形式）
			    switch (mime_content_type( $filename)) {
			    	//画像の場合
			        case "image/gif":
			            $new_name .= '.gif';
			            $is_image = true;
			            break;
			        case "image/jpeg":
			            $new_name .= '.jpg';
			            $is_image = true;
			            break;
			        case "image/png":
			            $new_name .= '.png';
			            $is_image = true;
			            break;
			    	
			    	//動画の場合
			        case "video/mp4":
			            $new_name .= '.mp4';
			            $is_video = true;
			            break;
			        case "video/x-msvideo":
			            $new_name .= '.avi';
			            $is_video = true;
			            break;
			        case "video/quicktime":
			            $new_name .= '.mov';
			            $is_video = true;
			            break;
			            
			         //対応するファイル形式に該当しなかった場合
			        default :
			            break;
			    }
			    
			    //（１）画像がアップロードされた場合
			    if($is_image){
					    //アップロードされたファイルを保存するディレクトリを指定
					    $directory = "../../uploaded/image/".$new_name;
					    
					    // ファイルの移動に成功したかに応じて処理を変更
					    if(move_uploaded_file($filename, $directory)){
					        $image_uploaded = true;
					    }else {
					        $upload_message = "アップロードに失敗しました。";
					    }
			    }
			    //（２）動画がアップロードされた場合
			     elseif($is_video){
					    //アップロードされたファイルを保存するディレクトリを指定
					    $directory = "../../uploaded/video/".$new_name;
					    
					    // ファイルの移動に成功したかに応じて処理を変更
					    if(move_uploaded_file($filename, $directory)){
					        $video_uploaded = true;
					    }else {
					        $upload_message = "アップロードに失敗しました。";
					    }
			    }
			    //（３）対応するファイル形式に該当しなかった場合
			    else{
			    		$upload_message = "<em>ファイルの形式が正しくありません。</em>";
			    }
		}
		
		//ファイルが選択されていない場合
		else{
				$upload_message = "<em>アップロードするファイルを選択してください！</em>";
		}
}


//（２）A. 画像のファイル名と投稿者・日付とを紐づける処理
//データベースに新たなテーブルを作成し、紐付ける

//画像がアップロードされた場合のみ、以下の処理を行う
if($image_uploaded){
		//A. テーブルの作成
		//このテーブルでのID、投稿者のID、ファイル名、日付が必要
			$sql = "CREATE TABLE IF NOT EXISTS image_date"
			." ("
			//このテーブルでのID
			. "id INT AUTO_INCREMENT PRIMARY KEY,"
			//ユーザID（同一ユーザが複数ファイルをアップロードし得るので、重複を許す）
			. "userID int,"
			//アップロードされたファイル名
			. "filename varchar(125),"
			//対象の日付（ymd）
			. "date varchar(16)"		
			.");";
	
			$stmt = $pdo->query($sql);
						
	
		//B. テーブルへの書き込み
	  		//(i) prepareメソッドの戻り値（PDOStatementオブジェクト）を、変数$sqlに代入
				$sql = $pdo -> prepare("INSERT INTO image_date (userID, filename, date) 
														VALUES (:userID, :filename, :date)");
															
			//(ii) 入力する変数を列に紐付ける（bindParam）。データ型に注意！
				$sql -> bindParam(':userID', $id, PDO::PARAM_STR);
				$sql -> bindParam(':filename', $new_name, PDO::PARAM_STR);
				$sql -> bindParam(':date', $date, PDO::PARAM_STR);
				
			//(iii) 変数に代入（※$id, $new_nameは既に定義済み）
				//セレクトボックスで選択された日付を取得
				$date = $_POST["year"].$_POST["month"].$_POST["day"];
				
			//(iv) 命令文を実行（プリペアドステートメントを実行）
				$sql -> execute();
				
		//C. アップロードの完了を知らせる
			$upload_message = "画像のアップロードに成功しました！";
}


//（２）B. 動画のファイル名と投稿者・日付とを紐づける処理
//データベースに新たなテーブルを作成し、紐付ける

//動画がアップロードされた場合のみ、以下の処理を行う
if($video_uploaded){
		//A. テーブルの作成
		//このテーブルでのID、投稿者のID、ファイル名、日付が必要
			$sql = "CREATE TABLE IF NOT EXISTS video_date"
			." ("
			//このテーブルでのID
			. "id INT AUTO_INCREMENT PRIMARY KEY,"
			//ユーザID（同一ユーザが複数ファイルをアップロードし得るので、重複を許す）
			. "userID int,"
			//アップロードされたファイル名
			. "filename varchar(125),"
			//対象の日付（ymd）
			. "date varchar(16)"		
			.");";
	
			$stmt = $pdo->query($sql);
						
	
		//B. テーブルへの書き込み
	  		//(i) prepareメソッドの戻り値（PDOStatementオブジェクト）を、変数$sqlに代入
				$sql = $pdo -> prepare("INSERT INTO video_date (userID, filename, date) 
														VALUES (:userID, :filename, :date)");
															
			//(ii) 入力する変数を列に紐付ける（bindParam）。データ型に注意！
				$sql -> bindParam(':userID', $id, PDO::PARAM_STR);
				$sql -> bindParam(':filename', $new_name, PDO::PARAM_STR);
				$sql -> bindParam(':date', $date, PDO::PARAM_STR);
				
			//(iii) 変数に代入（※$id, $new_nameは既に定義済み）
				//セレクトボックスで選択された日付を取得
				$date = $_POST["year"].$_POST["month"].$_POST["day"];
				
			//(iv) 命令文を実行（プリペアドステートメントを実行）
				$sql -> execute();
				
		//C. アップロードの完了を知らせる
			$upload_message = "動画のアップロードに成功しました！";
}

?>

<?php

//PHP6. 日付とコメントを取得し、データベースに書き込む
//日付が選択されているかを判定（送信後）
$date_selected2 = false;

if($login && isset($_POST["send"])){
		if(!empty($_POST["year2"]) && !empty($_POST["month2"]) && !empty($_POST["day2"])){
				if(preg_match("/^[0-9]{1,4}$/", $_POST["year2"])){
						$date_selected2 = true;
				}
				else{
						$comment_message = "<em>西暦の形式が正しくありません</em>";
				}
		}
		else{
				$comment_message = "<em>日付を選択してください！</em>";
		}
}

//ログイン完了＆日付選択完了＆コメントアップロードボタンが押された場合のみ、以下の処理を行う
if($login && $date_selected2 && isset($_POST["send"])){
		if(empty($_POST["comment"])){
				$comment_message = "<em>コメントが空です</em>";
		}
		else{
				//A. テーブルの作成
				//このテーブルでのID、投稿者のID、コメント、日付が必要
					$sql = "CREATE TABLE IF NOT EXISTS comment_date"
					." ("
					//このテーブルでのID
					. "id INT AUTO_INCREMENT PRIMARY KEY,"
					//ユーザID（同一ユーザが複数ファイルをアップロードし得るので、重複を許す）
					. "userID int,"
					//コメント内容
					. "comment varchar(1024),"
					//投稿された日付（ymd）
					. "date varchar(16)"		
					.");";
			
					$stmt = $pdo->query($sql);
								
			
				//B. テーブルへの書き込み
			  		//(i) prepareメソッドの戻り値（PDOStatementオブジェクト）を、変数$sqlに代入
						$sql = $pdo -> prepare("INSERT INTO comment_date (userID, comment, date) 
																VALUES (:userID, :comment, :date)");
																	
					//(ii) 入力する変数を列に紐付ける（bindParam）。データ型に注意！
						$sql -> bindParam(':userID', $id, PDO::PARAM_STR);
						$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
						$sql -> bindParam(':date', $date, PDO::PARAM_STR);
						
					//(iii) 変数に代入（※$idは既に定義済み）
						//コメントを変数に代入
						$comment = $_POST["comment"];
						
						//セレクトボックスで選択された日付を取得
						$date = $_POST["year2"].$_POST["month2"].$_POST["day2"];
						
					//(iv) 命令文を実行（プリペアドステートメントを実行）
						$sql -> execute();
						
				//C. アップロードの完了を知らせる
					$comment_message = "コメントの投稿に成功しました！";
		}
}
?>

<?php
//PHP7. 日付の初期値を設定

//現在時刻を取得し、変数に代入
$default_year = date("Y");
$default_month = date("m");
$default_day = date("d");

?>

<hr>
<div id="status">
	<?php echo $message; ?>
</div>

<div id="fileup">
	<h4>画像・動画のアップロード</h4>
	<p>
		<em>※対応しているファイル形式、容量には制限があります。<br>
		詳しくは<a href="upload_info.php" target="_blank">こちらのページ</a>をご覧ください。</em>
		
	</p>
	<p>
		<form method="POST" enctype="multipart/form-data">
		・日記の日付を選択<br>
			西暦<input type="text" size="4" name="year" value="<?php echo $default_year;?>">年
			<select name="month">
			<?php
				//option属性を表示する
				for($i=1; $i<=12; $i++){
						//月を0埋めする
						$month_pad = sprintf('%02d', $i);
					
						echo "<option value=\"".$month_pad."\"";
						
						//初期値に該当する場合
						if($month_pad == $default_month){
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
						if($day_pad == $default_day){
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
			<input type="file" name="up" value="画像・動画を選択"><br>
			<input type="submit" value="ファイルをアップロード" name="file">
		</form>
	</p>
	<p>
	<?php
		echo $upload_message;
	?>
	</p>
</div>

<div id="comment_up">
	<h4>コメントのアップロード</h4>
	<p>
		<form method="POST">
		・日記の日付を選択<br>
			西暦<input type="text" size="4" name="year2" value="<?php echo $default_year;?>">年
			<select name="month2">
			<?php
				//option属性を表示する
				for($i=1; $i<=12; $i++){
						//月を0埋めする
						$month_pad = sprintf('%02d', $i);
					
						echo "<option value=\"".$month_pad."\"";
						
						//初期値に該当する場合
						if($month_pad == $default_month){
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
			<select name="day2">
			<?php
				//option属性を表示する
				for($i=1; $i<=31; $i++){
						//月を0埋めする
						$day_pad = sprintf('%02d', $i);
						
						echo "<option value=\"".$day_pad."\"";
						
						//初期値に該当する場合
						if($day_pad == $default_day){
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
		コメント（日記の内容）：<br>
		<textarea name="comment" rows="4" cols="60" ></textarea><br>
		<input type="submit" value="コメントを投稿" name="send">
		</form>
	</p>
	<p>
	<?php
		echo $comment_message;
	?>
	</p>
</div>

<div id="welcome">
	<hr>
	<p>
		<a href="welcome.php">ログイン後のメインページへ戻る</a>
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