<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes">
    	<meta charset="utf-8"/>
        <title>mission_5-2</title>
        <link rel="stylesheet" href="mission_5.css">
    </head>
    <body>
  	  <h1>ふぇいばりっと・ふーど（mission5-1）</h1>
    	    <p>
	    		<strong><em>好きな食べ物について、お好きなコメントを残してください！</em></strong>
	    	</p>
	    	<p>
	    	<em>（※既存の投稿は、<a href=#link>ページ下部</a>）</em>
	    	</p>

<?php

//PHP1. まず、データベースの接続・構築を行う

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

*/

		/*
		（１）実行する命令文を定義。
		　これは後で、インスタンス（$pdo）のメソッドに対する引数として利用する。
		*/
		
					/*A. まず、「（既に同じテーブルが存在しない場合にのみ）テーブルを作る」という命令を入力。
					「foodtable」はテーブル名。*/
					$sql = "CREATE TABLE IF NOT EXISTS foodtable"
					." ("
					/*B. フィールド名「id」に対して、整数型（INT）を指定。（＝投稿番号）
					AUTO_INCREMENT属性は、その名の通り自動的に数値が増加するものである。
					PRIMARY KEYは、値の重複がある場合にエラーを発生させる（idを一意にする為に設定）。
					*/
					. "id INT AUTO_INCREMENT PRIMARY KEY,"
					/*C. フィールド名「name」に対して、固定長文字型（char）を指定。（＝投稿者名）
					文字列の長さ（文字数）は32に設定*/
					. "name char(64),"
					//D. フィールド名「comment」に対して、テキスト型（TEXT）を指定。（＝投稿内容）
					. "comment TEXT,"
					//E. フィールド名「time」に対して、固定長文字型（char）を指定。（＝投稿日時）
					. "time char(32),"
					//F. フィールド名「lastTime」に対して、固定長文字型（char）を指定。（＝最後の編集日時）
					. "lastTime char(32),"
					//G. フィールド名「password」に対して、可変長文字型（varchar）を指定。（＝パスワード）
					. "password varchar(64)"					
					.");";
			
		/*
		（２）命令文を実行
		　（１）で定義した命令文（$sql）を実行する。
			このとき、インスタンス（$pdo）のqueryメソッドを使用する。
		*/
					
					//命令文の実行結果を$stmt変数に代入
					$stmt = $pdo->query($sql);
	
?>


<?php

//PHP2. 新規投稿・編集・削除それぞれの機能を実装

/*

処理の前提として、変数の初期値を定義
何のボタンも押されていない間は、これらの初期値が適用される。

*/
		//編集対象の名前と投稿内容とを格納する変数を定義しておく（入力フォームに表示するもの）
		$edit_name = "";
		$edit_comment = "";
		$edit_password = "";
		
		//処理に応じて表示されるメッセージを格納する変数を定義しておく
		$send_message = "";
		$delete_message = "";
		$edit_message = "";
		
		//編集番号を確認する為の変数を定義しておく（「編集」か「新規投稿」かの分岐に使用）
		$edit_checker = "";
		
		//投稿ボタンに表示される文言
		$submit_message = "新規投稿";


/*

I. 投稿（新規投稿・編集投稿）

*/

	//sendボタンが押された場合に、以下の処理が行われる
		if(isset($_POST["send"])){
			
	 	   		//以下、前提処理（入力内容が不十分の場合、メッセージを表示する）
	 	   		//（イ）まだ送信されていない場合の処理（メッセージ）
	 	   		if (!isset($_POST["comment"])){
	 	   				$send_message = "<em>投稿お願いいたします！（内容はご自由に）</em>";
	 	   		} 
	 	   		
	 	   		//（ロ）送信内容が空の場合の処理（メッセージ）
	 	   		elseif ($_POST["comment"] == ""){
	 	   				$send_message =  "<em>※投稿内容が空です！</em>";
	 	   		}
	 	   		
	 	   		//（ハ）名前が空の場合の処理（メッセージ）
	 	   		elseif ($_POST["name"] == ""){
	 	   				$send_message = "<em>※名前を入力してください！</em>";
	 	   		}		
	 	   		 	
	 	   		//（ニ）パスワードが空の場合の処理（メッセージ）
	 	   		elseif ($_POST["password"] == ""){
	 	   				$send_message = "<em>※パスワードを入力してください！</em>";
	 	   		}
	 	   		
	 	   		//（ホ）それ以外の場合の処理
	 	   		else {
	 	   			//モード判定の為のテキストボックスの値を取得
	 	   			$edit_checker = $_POST["mode_checker"];
	 	   			
	 	   			//（１）新規投稿の場合の処理
		 	   			if ($edit_checker == ""){
				 	   				//A まずPOST送信されたデータを変数$_POSTから取得
					 	   				$name = $_POST["name"];
					 	   			 	$comment = $_POST["comment"];
					 	   			 	$password = $_POST["password"];
				 	   			 	 
				 	   			 	 //B. 投稿日時を取得して、変数に格納
				 	   			 	 //cf. タイムゾーンも余裕があったら後で設定方法等を調べよう
					 	   			 	 $time = date("Y/m/d H:i:s");
					 	   			 	 
					 	   			 //C. これらのデータをデータベースに書き込む
					 	 		  			//(i) prepareメソッドの戻り値（PDOStatementオブジェクト）を、変数$sqlに代入
												$sql = $pdo -> prepare("INSERT INTO foodtable (name, comment, time, lastTime, password) 
																						VALUES (:name, :comment, :time, :lastTime, :password)");
																							
											//(ii) 入力する変数を列に紐付ける（bindParam）。データ型に注意！
												$sql -> bindParam(':name', $name, PDO::PARAM_STR);
												$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
												$sql -> bindParam(':time', $time, PDO::PARAM_STR);
												$sql -> bindParam(':lastTime', $time, PDO::PARAM_STR);
												$sql -> bindParam(':password', $password, PDO::PARAM_STR);
												
											//(iii) 命令文を実行（プリペアドステートメントを実行）
												$sql -> execute();
																					
									//D. メッセージを表示する
										$send_message = "<em>無事送信されました！</em>";
		 	   				}
	 	   			
	 	   			
	 	   			//（２）編集の場合の処理
		 	   			else{
		 	   						//A. まず、編集番号・変更後の名前、投稿番号を取得
				 	   					$edit_number = (int)$edit_checker;
				 	   					$new_name = $_POST["name"];
						 	   			$new_comment = $_POST["comment"];
						 	   			$new_password = $_POST["password"];
				 	   			
			 	   					//B. 編集番号をもとに、ファイルを書き換える処理
					 	 		  			//(i) プリペアドステートメントを用意
												$sql = 'update foodtable 
															set name=:name,comment=:comment,lastTime=:lastTime,password=:password 
															where id=:id';
											
											//(ii) プリペアドステートメントをセットする
												$stmt = $pdo->prepare($sql);
											
											//(iii) 変数を紐付ける
												$stmt->bindParam(':name', $new_name, PDO::PARAM_STR);
												$stmt->bindParam(':comment', $new_comment, PDO::PARAM_STR);
												$stmt -> bindParam(':lastTime', $new_time, PDO::PARAM_STR);
												$stmt->bindParam(':password', $new_password, PDO::PARAM_STR);
												$stmt->bindParam(':id', $id, PDO::PARAM_INT);
												
												$id = $edit_number;
												$new_time = date("Y/m/d H:i:s");
											
											//(iv) 命令を実行
												$stmt->execute();
									
									//C. モードを判定する変数の値を初期値に戻しておく
										$edit_checker = "";
										
									//D. メッセージを表示する
										$send_message = "<em>無事編集されました！</em>";
		 	   				}
					}
			}


/*

II. 削除

*/

	//deleteボタンが押された場合に、以下の処理が行われる
		elseif(isset($_POST["delete"])){
			
	 	   		//以下、前提処理（入力内容が不十分の場合、メッセージを表示する）
	 	   		//（イ）送信内容が空の場合の処理（メッセージ）
	 	   		if ($_POST["delete_number"] == ""){
	 	   				$delete_message = "<em>※削除番号が指定されていません</em>";
	 	   		}
	 	   		
	 	   		//（ロ）パスワードが空の場合の処理（メッセージ）
	 	   		elseif ($_POST["delete_password"] == ""){
	 	   				$delete_message = "<em>※パスワードを入力してください</em>";
	 	   		}
	 	   		
	 	   		//（ハ）それ以外の場合の処理（削除を行った上で、今までの投稿を表示）
	 	   		else {		 	   			
	 	   				//A. まず削除番号とパスワードを取得（削除番号は、入力値が数字でなかった場合、0となる）
		 	   				$delete_number = (int)$_POST["delete_number"];
		 	   				$delete_password = $_POST["delete_password"];
	 	   				
	 	   				//B. 削除番号をもとに、データベースから削除する処理
							//削除番号があったかを判定する変数を定義
							$wrong_number = false;
							
							//パスワードが間違っていたかを判定する変数を定義
							$wrong_password = false;
							
							//削除の処理
								//(i)削除対象のパスワードを取得する
									//プリペアドステートメントを用意
									$sql = 'SELECT * FROM foodtable where id=:id';
									
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
									
									//パスワードが間違っている場合
									elseif($delete_password != $result["password"]){
											$wrong_password = true;
									}
									
									//その他の場合（＝パスワードが正しい場合）、削除の処理を行う
									else{
											//プリペアドステートメントを用意
											$sql = 'delete from foodtable where id=:id';
											//プリペアドステートメントをセットする
											$stmt = $pdo->prepare($sql);
											//変数を紐付ける
											$stmt->bindParam(':id', $id, PDO::PARAM_INT);
											$id = $delete_number;
											//削除処理（プリペアドステートメント）を実行
											$stmt->execute();
									}
									
							
						//C. 処理に応じてメッセージを表示
							//パスワードが間違っていた場合
							if($wrong_password){
									$delete_message = "<em>※パスワードが正しくありません</em>";		
							}
							
							//パスワードは間違っていないが、削除番号に該当するidがなかった場合
							elseif($wrong_number){
									$delete_message = "<em>※削除番号に該当する投稿がありません</em>";										
							}
							
							//削除に成功した場合
							else{												
									$delete_message = "<em>投稿番号".$delete_number."の削除が完了しました</em>";		
							}				
					}
		 	 }


/*

III. 編集の準備

*/

	//editボタンが押された場合に、以下の処理が行われる
		elseif(isset($_POST["edit"])){
	 	   		//（イ）送信内容が空の場合の処理（メッセージ）
	 	   		if ($_POST["edit_number"] == ""){
	 	   				$edit_message = "<em>※編集番号が指定されていません</em>";
	 	   		}
	 	   		
	 	   		//（ロ）パスワードが空の場合の処理（メッセージ）
	 	   		elseif ($_POST["edit_password"] == ""){
	 	   				$edit_message = "<em>※パスワードを入力してください</em>";
	 	   		}
	 	   		
	 	   		//（ハ）それ以外の場合の処理
	 	   		else {
	 	   				//A. まず編集番号とパスワードを取得（編集番号は、入力値が数字でなかった場合、0となる）
		 	   				$edit_number = (int)$_POST["edit_number"];
			 	   			$edit_password = $_POST["edit_password"];	 	  
		 	   			 				
	 	   				//B. 編集番号をもとに、編集対象データを取得する処理																	
							//編集番号があったかを判定する変数を定義
							$wrong_number = false;
							
							//パスワードが間違っていたかを判定する変数を定義
							$wrong_password = false;
							
							//編集の処理
								//(i)編集対象のパスワードを取得する
									//プリペアドステートメントを用意
									$sql = 'SELECT * FROM foodtable where id=:id';
									
									//プリペアドステートメントをセットする
									$stmt = $pdo->prepare($sql);
									
									//変数を紐付ける
									$stmt->bindParam(':id', $id, PDO::PARAM_INT);
									$id = $edit_number;
									
									//命令を実行し、結果を配列として格納
									$stmt->execute();
									$result = $stmt->fetch();
									
										/*
										注）fetchメソッドは、該当するデータがないときにはfalseを返す。
										故に、編集番号が間違っている場合、$result = false、となっているはず。
										*/
									
								//(iii) 状況に応じて場合わけ
									//該当データが存在しない場合
									if($result == false){
											$wrong_number = true;
									}
									
									//パスワードが間違っている場合
									elseif($edit_password != $result["password"]){
											$wrong_password = true;
									}
									
									//その他の場合（＝パスワードが正しい場合）、編集の準備の処理を行う
									else{
											//投稿内容を取得
											$edit_name = $result["name"];
											$edit_comment =  $result["comment"];	
											$edit_password =  $result["password"];	
									}
							
						//C. 処理に応じて、メッセージ等を変更
							//パスワードが間違っていた場合
							if($wrong_password){
									$edit_message = "<em>※パスワードが正しくありません</em>";		
							}
							
							//パスワードは間違っていないが、編集番号に該当するidがなかった場合
							elseif($wrong_number){
									$edit_message = "<em>※編集番号に該当する投稿がありません</em>";										
							}
							
							//編集番号を経た場合の処理
							else{
									//編集番号を、「編集モード」チェックの為の変数に代入する
									$edit_checker = $edit_number;
									
									//送信ボタンに表示されるメッセージも変更
									$submit_message = "編集（投稿番号：".$edit_number."）";
							}
						}
		 	   	}
?>

<hr>
	    <!-- POST送信を使用するので、method="POST"と指定 -->
<div id="form">
	<div id="send_form">
	    <h3>（１）入力用フォーム</h3>	
	    <!-- 編集モードチェック用のテキストボックスを作成。後で隠します。 -->
    	<form method="POST">
    		 <input type="hidden" size="10" name="mode_checker" value="<?php echo $edit_checker ?>">
  		  	<p>
  		  		  名前：<input type="text" size="16" name="name" value="<?php echo $edit_name ?>">
  			 </p>
  			 <p>
  		  		  語られよ！（あなたの好きな食べ物についてなど）：<br>
  		  		  <textarea name="comment" rows="4" cols="60" ><?php echo $edit_comment ?></textarea>
  			 </p>  			
  			  <p>
  		  		  パスワード（<em>※編集・削除時に必要となります</em>）：<br>
  		  		  <input type="text" size="16" name="password" value="<?php echo $edit_password ?>">
  			 </p>
  			 <p>
  				  <input type="submit" value="<?php echo $submit_message ?>" name="send" id="send"><br>
 			 </p>
 			 <?php echo $send_message ?>
 	   </form>
 	</div>
 	   
	<div id="delete_form">
 	   <h3>（２）削除用フォーム</h3>	
 	   <form method="POST">
 	     	<p>
  				  削除対象の投稿番号：<input type="text" size="10" name="delete_number"><br>
 			 </p> 	   
 			  <p>
  		  		  当該投稿のパスワード：<input type="text" size="16" name="delete_password">
  			 </p>
  			 <p>
  				  <input type="submit" value="削除！" name="delete" id="delete"><br>
 			 </p> 	   
 			 <?php echo $delete_message ?>
 	   </form>
 	</div>
 	
	<div id="edit_form">
 	   <h3>（３）編集用フォーム</h3>	
 	   <form method="POST">
 	     	<p>
  				  編集対象の投稿番号：<input type="text" size="10" name="edit_number"><br>
 			 </p> 	   
 			<p>
  		  		  当該投稿のパスワード：<input type="text" size="16" name="edit_password">
  			 </p>
  			 <p>
  				  <input type="submit" value="編集番号を指定" name="edit" id="edit"><br>
 			 </p> 	   
 			 <?php echo $edit_message ?>
 	   </form>
 	</div>
</div>

<p></p>

<hr>
<hr>

	<h2><a name="link"></a>今までの書き込み</h2>
	<hr>
	
<?php

//PHP3. 既存の投稿を表示

	//（１）データベースからデータを取得
		// A. SQL命令文を定義（テーブルの全ての列のデータを取得）
				$sql = 'SELECT * FROM foodtable';
		
		// B. 上記のSQL文を実行（queryメソッドを使用）
				$stmt = $pdo->query($sql);
			
		//C. 実行結果から、データを配列として取得（fetchAllメソッドを使用）
		
				/*PDOStatementクラスのfetchAllメソッドを使う
				fetchAllメソッドは該当する全てのデータを配列として返す
				戻り値である配列を、変数$resultに代入*/
				$results = $stmt->fetchAll();
		
	//（２）取得したデータを表示
			//削除された投稿番号を判定する為のカウンタを用意
				$counter = 0;
	
			//$result変数に配列の形で格納されているので、foreachで中身を出力する
				foreach ($results as $row){
						//カウンタを+1する
						$counter ++;
						
						//カウンタがid番号と等しくなるまで、（その間の番号の投稿が）削除されている旨の表示を続ける
						while($counter < $row['id']){
								echo "<em>投稿番号".$counter."の投稿は削除されました。</em>";
								echo "<br><hr>";
								$counter ++;
						}
						
						//$rowの中にはテーブルのカラム名が入る
						echo "投稿番号：".$row['id']."　";
						echo "名前：".$row['name']."　";
						echo "投稿日時：".$row['time']."　";
						echo "最終編集日時：".$row['lastTime']."<br>";	
						
						//投稿内容の中の改行コードは、<br>に変換してから出力する
						$br_comment = nl2br($row['comment']);
						echo "投稿内容：<br>",$br_comment,"<br>";		
						
					echo "<hr>";
					}
		 	   		
?>