<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes">
    	<meta charset="utf-8"/>
        <title>mission_6_sign_up</title>
    </head>
    <body>
  	  <h1>仮登録ページ</h1>

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
					$sql = "CREATE TABLE IF NOT EXISTS pre_user"
					." ("
					/*B. フィールド名「id」に対して、整数型（INT）を指定。（＝投稿番号）
							AUTO_INCREMENT属性は、その名の通り自動的に数値が増加するものである。
							PRIMARY KEYは、値の重複がある場合にエラーを発生させる（idを一意にする為に設定）。
					*/
					. "id INT AUTO_INCREMENT PRIMARY KEY,"
					/*C. フィールド名「email」に対して、可変長文字型（char）を指定。（＝メールアドレス）
							カブっては困るので、UNIQUEを指定*/
					. "email varchar(255) UNIQUE,"
					/*D. フィールド名「token」に対して、可変長文字型（varchar）を指定。（＝認証コード）
							ハッシュすることを見越して長めに設定*/
					. "token varchar(255)"					
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

//ボタンが押された場合に、以下の処理が行われる
	if(isset($_POST["top"])){		
			//トップページへジャンプし、処理を終了する
				header("Location: top.php");
				exit();
	}	

?>

<?php

//メール送信の事前設定

require '../../mail/src/Exception.php';
require '../../mail/src/PHPMailer.php';
require '../../mail/src/SMTP.php';
require '../../mail/setting.php';

// PHPMailerのインスタンス生成
    $mail = new PHPMailer\PHPMailer\PHPMailer();

    $mail->isSMTP(); // SMTPを使うようにメーラーを設定する
    $mail->SMTPAuth = true;
    $mail->Host = MAIL_HOST; // メインのSMTPサーバー（メールホスト名）を指定
    $mail->Username = MAIL_USERNAME; // SMTPユーザー名（メールユーザー名）
    $mail->Password = MAIL_PASSWORD; // SMTPパスワード（メールパスワード）
    $mail->SMTPSecure = MAIL_ENCRPT; // TLS暗号化を有効にし、「SSL」も受け入れます
    $mail->Port = SMTP_PORT; // 接続するTCPポート

    // メール内容設定
    $mail->CharSet = "UTF-8";
    $mail->Encoding = "base64";
    $mail->setFrom(MAIL_FROM,MAIL_FROM_NAME);
    
?>


<?php

//仮登録機能を実装

/*

処理の前提

*/

		//処理に応じて表示されるメッセージを格納する変数を定義しておく
		$send_message = "";
		
		// セッションの有効期限を30分に設定
		//（※必ずセッション開始前に設定すること）
		session_set_cookie_params(60*30);
		
		//セッションを開始
		session_start();
		
		//クリックジャッキング対策
		header('X-FRAME-OPTIONS: SAMEORIGIN');

/*

I. 仮登録

*/

	//sendボタンが押された場合に、以下の処理が行われる
		if(isset($_POST["send"])){
			
	 	   		//以下、前提処理（入力内容が不十分の場合、メッセージを表示する）
	 	   		//（イ）まだ送信されていない場合の処理
	 	   		if (!isset($_POST["email"])){
	 	   				$send_message = "<em>メールアドレスを入力してください</em>";
	 	   		} 
	 	   		
	 	   		//（ロ）送信内容が空の場合の処理
	 	   		elseif (empty($_POST["email"] )){
	 	   				$send_message =  "<em>メールアドレスが空です！</em>";
	 	   		}
	 	   		 	
	 	   		//（ハ）メールアドレスの形式が正しくない場合の処理
	 	   		elseif (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $_POST["email"])){
	 	   				$send_message = "<em>メールアドレスの形式が正しくありません！</em>";
	 	   		}
	 	   		
	 	   		//（ニ）その他の場合の処理
	 	   		else{
	 	   					//メールアドレスが本登録済みかを検証する処理
	 	   						$email = $_POST["email"];
	 	   						
	 	   						//メールアドレスをデータベース（本登録ユーザのテーブル）で探す
								//プリペアドステートメントを用意
								$sql = 'SELECT * FROM usertable where email=:email';
								
								//プリペアドステートメントをセットする
								$stmt = $pdo->prepare($sql);
								
								//変数を紐付ける
								$stmt->bindParam(':email', $email, PDO::PARAM_STR);
								
								//命令を実行し、結果を配列として格納
								$stmt->execute();
								$result = $stmt->fetch();
	 	   			
	 	   					//（１） 既に本登録されたメールアドレスの場合
			 	   			if (!$result == false){
			 	   				$send_message = "<em>※既に本登録されたアドレスです！</em>";
			 	   			}
			 	   		
			 	   			//（２）それ以外の場合の処理（＝仮登録の処理）
				 	   		else {
				 	   			//一度仮登録したことがあるアドレスかどうかを判定する処理
					 	   			//メールアドレスをデータベース（仮登録ユーザのテーブル）で探す
									//プリペアドステートメントを用意
									$sql = 'SELECT * FROM pre_user where email=:email';
									
									//プリペアドステートメントをセットする
									$stmt = $pdo->prepare($sql);
									
									//変数を紐付ける
									$stmt->bindParam(':email', $email, PDO::PARAM_STR);
									
									//命令を実行し、結果を配列として格納
									$stmt->execute();
									$result = $stmt->fetch();
									
								//認証コードに関する処理
									//A. 認証コードを作成（token）し、セッションに保存した上で、変数に代入
										$_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(8));
										$token = $_SESSION['token'];
				 	   			 	 
				 	   			 	 //B. 認証コードをハッシュする（第二引数は絶対必要！！！！）
					 	   			 	 $hash = password_hash($token, PASSWORD_DEFAULT);
								
				 	   			//分岐
				 		   				//1. 初めての仮登録の場合の処理
							 	   			if ($result == false){												 	   						 	   			 	 
										 	   			 //データベースに書き込む（メールアドレスと、認証コードのハッシュ）
										 	 		  			//(i) prepareメソッドの戻り値（PDOStatementオブジェクト）を、変数$sqlに代入
																	$sql = $pdo -> prepare("INSERT INTO pre_user (email, token) 
																											VALUES (:email, :token)");
																												
																//(ii) 入力する変数を列に紐付ける（bindParam）。データ型に注意！
																	$sql -> bindParam(':email', $email, PDO::PARAM_STR);
																	$sql -> bindParam(':token', $hash, PDO::PARAM_STR);
																	
																//(iii) 命令文を実行（プリペアドステートメントを実行）
																	$sql -> execute();
							 	   				}
						 	   			
						 	   			
						 	   			//2. 一度仮登録をしている場合の処理（トークンを書き換える）
							 	   			else{							 	   				
								 	   					//B. メールアドレスの登録されたIDをもとに、トークンを書き換える処理
										 	 		  			//(i) プリペアドステートメントを用意
																	$sql = 'update pre_user 
																				set token=:token 
																				where id=:id';
																
																//(ii) プリペアドステートメントをセットする
																	$stmt = $pdo->prepare($sql);
																
																//(iii) 変数を紐付ける
																	$stmt->bindParam(':token', $hash, PDO::PARAM_STR);
																	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
																	
																	$id = $result["id"];
																
																//(iv) 命令を実行
																	$stmt->execute();
							 	   				}
							 	   				
							 	//仮登録されたメールアドレスに認証コードを送信する処理
							 		//(i) 宛先や本文の内容を設定
								 	    $mail->addAddress($email, $email); 
									    $mail->Subject = MAIL_SUBJECT; // メールタイトル（settingの方で設定）
									    $mail->isHTML(true);// HTMLフォーマットの場合はコチラを設定します
									    $body = "あなたの認証コードは<br>".$token."<br>です。<br>本登録のページで入力してください。";
								    
								    //(ii) メール本文をセット
									    $mail->Body  = $body;
									    
									//(iii) メールを送信
										$mail->send();
							 	
							 	//本登録ページ（＝メール認証ページ）へとジャンプする処理
							 		//セッションにメールアドレスを保存しておく
							 		$_SESSION["email"] = $email;
							 		
							 		//本登録ページへ飛ぶ
									header("Location: register.php");
									exit();
						}
					}
			}

?>

<hr>
<div id="form"> 	   
	<div id="register">
 	   <h3>初回登録</h3>	
 	   <form method="POST" action="">
 	     	<p>
  				  <label for="email">メールアドレス：</label><br>
  				  <input type="text" size="16" name="email"><br>
 			 </p> 	   
  			 <p>
  				  <input type="submit" value="仮登録" name="send" id="send"><br>
 			 </p> 	   
 			 <?php echo $send_message ?>
 	   </form>
 	   <p>
 	   <em>※仮登録したメールアドレスに、認証用のコードが届きます。</em>
 	   </p>
 	</div>
 	 <div id="jump">
    	<form method="POST" action="">
  			 <p>
  				  <input type="submit" value="トップページへ戻る" name="top"><br>
 			 </p>
 	   </form>
 	</div>
 </div>