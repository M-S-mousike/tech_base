<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes">
		<meta charset="utf-8"/>
		<title>mission_6-2_data_eraser</title>
	</head>
	<body>
  	  <h1>IDを指定し、データベースからデータを削除</h1>

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
//初期設定
$delete_message = "";
$delete_message2 = "";
$delete_message3 = "";
$delete_message4 = "";
$delete_message5 = "";

/*

削除1

*/

	//deleteボタンが押された場合に、以下の処理が行われる
		if(isset($_POST["delete"])){
			
	 	   		//以下、前提処理（入力内容が不十分の場合、メッセージを表示する）
	 	   		//送信内容が空の場合の処理（メッセージ）
	 	   		if ($_POST["delete_number"] == ""){
	 	   				$delete_message = "<em>※削除番号が指定されていません</em>";
	 	   		}
	 	   		
	 	   		//それ以外の場合の処理（削除を行った上で、今までの投稿を表示）
	 	   		else {		 	   			
	 	   				//A. まず削除番号とパスワードを取得（削除番号は、入力値が数字でなかった場合、0となる）
		 	   				$delete_number = (int)$_POST["delete_number"];
	 	   				
	 	   				//B. 削除番号をもとに、データベースから削除する処理
							//削除番号があったかを判定する変数を定義
							$wrong_number = false;
							
							//削除の処理
								//(i)削除対象のパスワードを取得する
									//プリペアドステートメントを用意
									$sql = 'SELECT * FROM pre_user where id=:id';
									
									//プリペアドステートメントをセットする
									$stmt = $pdo->prepare($sql);
									
									//変数を紐付ける
									$stmt->bindParam(':id', $id, PDO::PARAM_INT);
									$id = $delete_number;
									
									//命令を実行し、結果を配列として格納
									$stmt->execute();
									$result = $stmt->fetch();
									
										/*
										注）fetchメソッドは、該当するデータがないときにはfalseを返す。
										故に、削除番号が間違っている場合、$result = false、となっているはず。
										*/
									
								//(iii) 状況に応じて場合わけ
									//該当データが存在しない場合
									if($result == false){
											$wrong_number = true;
									}
									
									//その他の場合（＝パスワードが正しい場合）、削除の処理を行う
									else{
											//プリペアドステートメントを用意
											$sql = 'delete from pre_user where id=:id';
											//プリペアドステートメントをセットする
											$stmt = $pdo->prepare($sql);
											//変数を紐付ける
											$stmt->bindParam(':id', $id, PDO::PARAM_INT);
											$id = $delete_number;
											//削除処理（プリペアドステートメント）を実行
											$stmt->execute();
									}
									
							
						//C. 処理に応じてメッセージを表示							
							//パスワードは間違っていないが、削除番号に該当するidがなかった場合
							if($wrong_number){
									$delete_message = "<em>※削除番号に該当する投稿がありません</em>";										
							}
							
							//削除に成功した場合
							else{												
									$delete_message = "<em>ID".$delete_number."の削除が完了しました</em>";		
							}				
					}
		 	 }
		 	 
/*

削除2

*/

	//delete2ボタンが押された場合に、以下の処理が行われる
		elseif(isset($_POST["delete2"])){
			
	 	   		//以下、前提処理（入力内容が不十分の場合、メッセージを表示する）
	 	   		//送信内容が空の場合の処理（メッセージ）
	 	   		if ($_POST["delete_number2"] == ""){
	 	   				$delete_message2 = "<em>※削除番号が指定されていません</em>";
	 	   		}
	 	   		
	 	   		//それ以外の場合の処理（削除を行った上で、今までの投稿を表示）
	 	   		else {		 	   			
	 	   				//A. まず削除番号とパスワードを取得（削除番号は、入力値が数字でなかった場合、0となる）
		 	   				$delete_number2 = (int)$_POST["delete_number2"];
	 	   				
	 	   				//B. 削除番号をもとに、データベースから削除する処理
							//削除番号があったかを判定する変数を定義
							$wrong_number2 = false;
							
							//削除の処理
								//(i)削除対象のパスワードを取得する
									//プリペアドステートメントを用意
									$sql = 'SELECT * FROM usertable where id=:id';
									
									//プリペアドステートメントをセットする
									$stmt = $pdo->prepare($sql);
									
									//変数を紐付ける
									$stmt->bindParam(':id', $id, PDO::PARAM_INT);
									$id = $delete_number2;
									
									//命令を実行し、結果を配列として格納
									$stmt->execute();
									$result = $stmt->fetch();
									
										/*
										注）fetchメソッドは、該当するデータがないときにはfalseを返す。
										故に、削除番号が間違っている場合、$result = false、となっているはず。
										*/
									
								//(iii) 状況に応じて場合わけ
									//該当データが存在しない場合
									if($result == false){
											$wrong_number2 = true;
									}
									
									//その他の場合（＝パスワードが正しい場合）、削除の処理を行う
									else{
											//プリペアドステートメントを用意
											$sql = 'delete from usertable where id=:id';
											//プリペアドステートメントをセットする
											$stmt = $pdo->prepare($sql);
											//変数を紐付ける
											$stmt->bindParam(':id', $id, PDO::PARAM_INT);
											$id = $delete_number2;
											//削除処理（プリペアドステートメント）を実行
											$stmt->execute();
									}
									
							
						//C. 処理に応じてメッセージを表示							
							//パスワードは間違っていないが、削除番号に該当するidがなかった場合
							if($wrong_number2){
									$delete_message2 = "<em>※削除番号に該当する投稿がありません</em>";										
							}
							
							//削除に成功した場合
							else{												
									$delete_message2 = "<em>ID".$delete_number2."の削除が完了しました</em>";		
							}				
					}
		 	 }
		 	 
/*

削除3

*/

	//delete3ボタンが押された場合に、以下の処理が行われる
		elseif(isset($_POST["delete3"])){
			
	 	   		//以下、前提処理（入力内容が不十分の場合、メッセージを表示する）
	 	   		//送信内容が空の場合の処理（メッセージ）
	 	   		if ($_POST["delete_number3"] == ""){
	 	   				$delete_message3 = "<em>※削除番号が指定されていません</em>";
	 	   		}
	 	   		
	 	   		//それ以外の場合の処理（削除を行った上で、今までの投稿を表示）
	 	   		else {		 	   			
	 	   				//A. まず削除番号とパスワードを取得（削除番号は、入力値が数字でなかった場合、0となる）
		 	   				$delete_number3 = (int)$_POST["delete_number3"];
	 	   				
	 	   				//B. 削除番号をもとに、データベースから削除する処理
							//削除番号があったかを判定する変数を定義
							$wrong_number3 = false;
							
							//削除の処理
								//(i)削除対象のパスワードを取得する
									//プリペアドステートメントを用意
									$sql = 'SELECT * FROM image_date where id=:id';
									
									//プリペアドステートメントをセットする
									$stmt = $pdo->prepare($sql);
									
									//変数を紐付ける
									$stmt->bindParam(':id', $id, PDO::PARAM_INT);
									$id = $delete_number3;
									
									//命令を実行し、結果を配列として格納
									$stmt->execute();
									$result = $stmt->fetch();
									
										/*
										注）fetchメソッドは、該当するデータがないときにはfalseを返す。
										故に、削除番号が間違っている場合、$result = false、となっているはず。
										*/
									
								//(iii) 状況に応じて場合わけ
									//該当データが存在しない場合
									if($result == false){
											$wrong_number3 = true;
									}
									
									//その他の場合（＝パスワードが正しい場合）、削除の処理を行う
									else{
											//プリペアドステートメントを用意
											$sql = 'delete from image_date where id=:id';
											//プリペアドステートメントをセットする
											$stmt = $pdo->prepare($sql);
											//変数を紐付ける
											$stmt->bindParam(':id', $id, PDO::PARAM_INT);
											$id = $delete_number3;
											//削除処理（プリペアドステートメント）を実行
											$stmt->execute();
									}
									
							
						//C. 処理に応じてメッセージを表示							
							//パスワードは間違っていないが、削除番号に該当するidがなかった場合
							if($wrong_number3){
									$delete_message3 = "<em>※削除番号に該当する投稿がありません</em>";										
							}
							
							//削除に成功した場合
							else{												
									$delete_message3 = "<em>ID".$delete_number3."の削除が完了しました</em>";		
							}				
					}
		 	 }
		 	 
/*

削除4

*/

	//delete4ボタンが押された場合に、以下の処理が行われる
		elseif(isset($_POST["delete4"])){
			
	 	   		//以下、前提処理（入力内容が不十分の場合、メッセージを表示する）
	 	   		//送信内容が空の場合の処理（メッセージ）
	 	   		if ($_POST["delete_number4"] == ""){
	 	   				$delete_message4 = "<em>※削除番号が指定されていません</em>";
	 	   		}
	 	   		
	 	   		//それ以外の場合の処理（削除を行った上で、今までの投稿を表示）
	 	   		else {		 	   			
	 	   				//A. まず削除番号とパスワードを取得（削除番号は、入力値が数字でなかった場合、0となる）
		 	   				$delete_number4 = (int)$_POST["delete_number4"];
	 	   				
	 	   				//B. 削除番号をもとに、データベースから削除する処理
							//削除番号があったかを判定する変数を定義
							$wrong_number4 = false;
							
							//削除の処理
								//(i)削除対象のパスワードを取得する
									//プリペアドステートメントを用意
									$sql = 'SELECT * FROM video_date where id=:id';
									
									//プリペアドステートメントをセットする
									$stmt = $pdo->prepare($sql);
									
									//変数を紐付ける
									$stmt->bindParam(':id', $id, PDO::PARAM_INT);
									$id = $delete_number4;
									
									//命令を実行し、結果を配列として格納
									$stmt->execute();
									$result = $stmt->fetch();
									
										/*
										注）fetchメソッドは、該当するデータがないときにはfalseを返す。
										故に、削除番号が間違っている場合、$result = false、となっているはず。
										*/
									
								//(iii) 状況に応じて場合わけ
									//該当データが存在しない場合
									if($result == false){
											$wrong_number4 = true;
									}
									
									//その他の場合（＝パスワードが正しい場合）、削除の処理を行う
									else{
											//プリペアドステートメントを用意
											$sql = 'delete from video_date where id=:id';
											//プリペアドステートメントをセットする
											$stmt = $pdo->prepare($sql);
											//変数を紐付ける
											$stmt->bindParam(':id', $id, PDO::PARAM_INT);
											$id = $delete_number4;
											//削除処理（プリペアドステートメント）を実行
											$stmt->execute();
									}
									
							
						//C. 処理に応じてメッセージを表示							
							//パスワードは間違っていないが、削除番号に該当するidがなかった場合
							if($wrong_number4){
									$delete_message4 = "<em>※削除番号に該当する投稿がありません</em>";										
							}
							
							//削除に成功した場合
							else{												
									$delete_message4 = "<em>ID".$delete_number4."の削除が完了しました</em>";		
							}				
					}
		 	 }


/*

削除5

*/

	//delete5ボタンが押された場合に、以下の処理が行われる
		elseif(isset($_POST["delete5"])){
			
	 	   		//以下、前提処理（入力内容が不十分の場合、メッセージを表示する）
	 	   		//送信内容が空の場合の処理（メッセージ）
	 	   		if ($_POST["delete_number5"] == ""){
	 	   				$delete_message5 = "<em>※削除番号が指定されていません</em>";
	 	   		}
	 	   		
	 	   		//それ以外の場合の処理（削除を行った上で、今までの投稿を表示）
	 	   		else {		 	   			
	 	   				//A. まず削除番号とパスワードを取得（削除番号は、入力値が数字でなかった場合、0となる）
		 	   				$delete_number5 = (int)$_POST["delete_number5"];
	 	   				
	 	   				//B. 削除番号をもとに、データベースから削除する処理
							//削除番号があったかを判定する変数を定義
							$wrong_number5 = false;
							
							//削除の処理
								//(i)削除対象のパスワードを取得する
									//プリペアドステートメントを用意
									$sql = 'SELECT * FROM comment_date where id=:id';
									
									//プリペアドステートメントをセットする
									$stmt = $pdo->prepare($sql);
									
									//変数を紐付ける
									$stmt->bindParam(':id', $id, PDO::PARAM_INT);
									$id = $delete_number5;
									
									//命令を実行し、結果を配列として格納
									$stmt->execute();
									$result = $stmt->fetch();
									
										/*
										注）fetchメソッドは、該当するデータがないときにはfalseを返す。
										故に、削除番号が間違っている場合、$result = false、となっているはず。
										*/
									
								//(iii) 状況に応じて場合わけ
									//該当データが存在しない場合
									if($result == false){
											$wrong_number5 = true;
									}
									
									//その他の場合（＝パスワードが正しい場合）、削除の処理を行う
									else{
											//プリペアドステートメントを用意
											$sql = 'delete from comment_date where id=:id';
											//プリペアドステートメントをセットする
											$stmt = $pdo->prepare($sql);
											//変数を紐付ける
											$stmt->bindParam(':id', $id, PDO::PARAM_INT);
											$id = $delete_number5;
											//削除処理（プリペアドステートメント）を実行
											$stmt->execute();
									}
									
							
						//C. 処理に応じてメッセージを表示							
							//パスワードは間違っていないが、削除番号に該当するidがなかった場合
							if($wrong_number5){
									$delete_message5 = "<em>※削除番号に該当する投稿がありません</em>";										
							}
							
							//削除に成功した場合
							else{												
									$delete_message5 = "<em>ID".$delete_number2."の削除が完了しました</em>";		
							}				
					}
		 	 }
		 	 
?>

<hr>
<div id="form"> 	   
	<div id="delete_form">
 	   <h3>（１）pre_user削除用フォーム</h3>	
 	   <form method="POST">
 	     	<p>
  				  削除対象のID：<input type="text" size="10" name="delete_number"><br>
 			 </p> 	   
  			 <p>
  				  <input type="submit" value="削除！" name="delete"><br>
 			 </p> 	   
 			 <?php echo $delete_message ?>
 	   </form>
 	</div>
 	
	<div id="delete_form2">
 	   <h3>（２）usertable削除用フォーム</h3>	
 	   <form method="POST">
 	     	<p>
  				  削除対象のID：<input type="text" size="10" name="delete_number2"><br>
 			 </p> 	   
  			 <p>
  				  <input type="submit" value="削除" name="delete2"><br>
 			 </p> 	   
 			 <?php echo $delete_message2 ?>
 	   </form>
 	</div>
 	
	<div id="delete_form3">
 	   <h3>（３）image_date削除用フォーム</h3>	
 	   <form method="POST">
 	     	<p>
  				  削除対象のID：<input type="text" size="10" name="delete_number3"><br>
 			 </p> 	   
  			 <p>
  				  <input type="submit" value="削除" name="delete3"><br>
 			 </p> 	   
 			 <?php echo $delete_message3 ?>
 	   </form>
 	</div>
 	
	<div id="delete_form4">
 	   <h3>（４）video_date削除用フォーム</h3>	
 	   <form method="POST">
 	     	<p>
  				  削除対象のID：<input type="text" size="10" name="delete_number4"><br>
 			 </p> 	   
  			 <p>
  				  <input type="submit" value="削除" name="delete4"><br>
 			 </p> 	   
 			 <?php echo $delete_message4 ?>
 	   </form>
 	</div>
 	
	<div id="delete_form5">
 	   <h3>（５）comment_date削除用フォーム</h3>	
 	   <form method="POST">
 	     	<p>
  				  削除対象のID：<input type="text" size="10" name="delete_number5"><br>
 			 </p> 	   
  			 <p>
  				  <input type="submit" value="削除" name="delete5"><br>
 			 </p> 	   
 			 <?php echo $delete_message5 ?>
 	   </form>
 	</div>
</div>
<p></p>
<hr>
<hr>
