<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes">
    	<meta charset="utf-8"/>
        <title>mission_6-2_database_checker</title>
    </head>
    <body>
  	  <h1>データベースの内容確認</h1>

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

<hr><hr>
<h2>pre_userテーブルを表示（仮登録テーブル）</h2>
<?php

//PHP2. pre_userテーブルを表示

	//（１）データベースからデータを取得
		// A. SQL命令文を定義（tbtest（データベース名）の全ての列のデータを取得）
				
				/*SELECT文で値を取得
				（※「*」(アスタリスク)は「すべてのカラム（列）」と言う意味）*/
				$sql = 'SELECT * FROM pre_user';
		
		// B. 上記のSQL文を実行（queryメソッドを使用）
				
				/*PDOインスタンスのqueryメソッドで、Aで定義したSQL文を実行する
				戻り値のPDOStatementオブジェクト（インスタンス）を、変数$stmtに代入*/
				$stmt = $pdo->query($sql);
			
		//C. 実行結果から、データを配列として取得（fetchAllメソッドを使用）
		
				/*PDOStatementクラスのfetchAllメソッドを使う
				fetchAllメソッドは該当する全てのデータを配列として返す
				戻り値である配列を、変数$resultに代入*/
				$results = $stmt->fetchAll();
				
				/*
				cf. SQLインジェクションについて
				「$result = $pdo->query($sql);を利用する方法もありますが、変数の値を直接SQL文に埋め込むのはとても危険！
				やめましょう。」
				→SQLインジェクション：不正な「SQL」の命令を攻撃対象のウェブサイトに「注入する（inject）」こと
				→変数の値を直接SQL文に埋め込むと、例えば外部から変数を設定可能にしたときに、外部からSQL文の中身に直接影響を与えることができてしまう
				　（e.g. webページからPOST送信で受け取った値を変数に代入する場合）
				*/
						
	//（２）取得したデータを表示	
			//$result変数に配列の形で格納されているので、foreachで中身を出力する
				foreach ($results as $row){						
						//$rowの中にはテーブルのカラム名が入る
						echo "ID：".$row['id']."　";
						echo "メールアドレス：".$row['email']."　";
						echo "認証コード：".$row['token']."<br>";	
						
					echo "<hr>";
					} 	   		
?>


<hr><hr>
<h2>usertableテーブルを表示（本登録テーブル）</h2>

<?php

//PHP3. usertableを表示

	//（１）データベースからデータを取得
		// A. SQL命令文を定義（tbtest（データベース名）の全ての列のデータを取得）
				
				/*SELECT文で値を取得
				（※「*」(アスタリスク)は「すべてのカラム（列）」と言う意味）*/
				$sql = 'SELECT * FROM usertable';
		
		// B. 上記のSQL文を実行（queryメソッドを使用）
				
				/*PDOインスタンスのqueryメソッドで、Aで定義したSQL文を実行する
				戻り値のPDOStatementオブジェクト（インスタンス）を、変数$stmtに代入*/
				$stmt = $pdo->query($sql);
			
		//C. 実行結果から、データを配列として取得（fetchAllメソッドを使用）
		
				/*PDOStatementクラスのfetchAllメソッドを使う
				fetchAllメソッドは該当する全てのデータを配列として返す
				戻り値である配列を、変数$resultに代入*/
				$results = $stmt->fetchAll();
				
				/*
				cf. SQLインジェクションについて
				「$result = $pdo->query($sql);を利用する方法もありますが、変数の値を直接SQL文に埋め込むのはとても危険！
				やめましょう。」
				→SQLインジェクション：不正な「SQL」の命令を攻撃対象のウェブサイトに「注入する（inject）」こと
				→変数の値を直接SQL文に埋め込むと、例えば外部から変数を設定可能にしたときに、外部からSQL文の中身に直接影響を与えることができてしまう
				　（e.g. webページからPOST送信で受け取った値を変数に代入する場合）
				*/
		
	//（２）取得したデータを表示	
			//$result変数に配列の形で格納されているので、foreachで中身を出力する
				foreach ($results as $row){						
						//$rowの中にはテーブルのカラム名が入る
						echo "ID：".$row['id']."　";
						echo "名前：".$row['name']."　";
						echo "メールアドレス：".$row['email']."　";
						echo "パスワード：".$row['password']."<br>";	
						
					echo "<hr>";
					} 	   		
?>

<hr><hr>
<h2>image_dateテーブルを表示（アップロードされた画像のテーブル）</h2>

<?php

//PHP4. image_dateを表示

	//（１）データベースからデータを取得
		// A. SQL命令文を定義（tbtest（データベース名）の全ての列のデータを取得）
				
				/*SELECT文で値を取得
				（※「*」(アスタリスク)は「すべてのカラム（列）」と言う意味）*/
				$sql = 'SELECT * FROM image_date';
		
		// B. 上記のSQL文を実行（queryメソッドを使用）
				
				/*PDOインスタンスのqueryメソッドで、Aで定義したSQL文を実行する
				戻り値のPDOStatementオブジェクト（インスタンス）を、変数$stmtに代入*/
				$stmt = $pdo->query($sql);
			
		//C. 実行結果から、データを配列として取得（fetchAllメソッドを使用）
		
				/*PDOStatementクラスのfetchAllメソッドを使う
				fetchAllメソッドは該当する全てのデータを配列として返す
				戻り値である配列を、変数$resultに代入*/
				$results = $stmt->fetchAll();
		
	//（２）取得したデータを表示	
			//$result変数に配列の形で格納されているので、foreachで中身を出力する
				foreach ($results as $row){						
						//$rowの中にはテーブルのカラム名が入る
						echo "ID：".$row['id']."　";
						echo "ユーザID：".$row['userID']."　";
						echo "ファイル名：".$row['filename']."　";
						echo "日付：".$row['date']."<br>";	
						
					echo "<hr>";
					} 	   		
?>

<hr><hr>
<h2>video_dateテーブルを表示（アップロードされた動画のテーブル）</h2>

<?php

//PHP4. video_dateを表示

	//（１）データベースからデータを取得
		// A. SQL命令文を定義（tbtest（データベース名）の全ての列のデータを取得）
				
				/*SELECT文で値を取得
				（※「*」(アスタリスク)は「すべてのカラム（列）」と言う意味）*/
				$sql = 'SELECT * FROM video_date';
		
		// B. 上記のSQL文を実行（queryメソッドを使用）
				
				/*PDOインスタンスのqueryメソッドで、Aで定義したSQL文を実行する
				戻り値のPDOStatementオブジェクト（インスタンス）を、変数$stmtに代入*/
				$stmt = $pdo->query($sql);
			
		//C. 実行結果から、データを配列として取得（fetchAllメソッドを使用）
		
				/*PDOStatementクラスのfetchAllメソッドを使う
				fetchAllメソッドは該当する全てのデータを配列として返す
				戻り値である配列を、変数$resultに代入*/
				$results = $stmt->fetchAll();
		
	//（２）取得したデータを表示	
			//$result変数に配列の形で格納されているので、foreachで中身を出力する
				foreach ($results as $row){						
						//$rowの中にはテーブルのカラム名が入る
						echo "ID：".$row['id']."　";
						echo "ユーザID：".$row['userID']."　";
						echo "ファイル名：".$row['filename']."　";
						echo "日付：".$row['date']."<br>";	
						
					echo "<hr>";
					} 	   		
?>


<hr><hr>
<h2>comment_dateテーブルを表示（アップロードされたコメントのテーブル）</h2>

<?php

//PHP5. comment_dateを表示

	//（１）データベースからデータを取得
		// A. SQL命令文を定義（tbtest（データベース名）の全ての列のデータを取得）
				
				/*SELECT文で値を取得
				（※「*」(アスタリスク)は「すべてのカラム（列）」と言う意味）*/
				$sql = 'SELECT * FROM comment_date';
		
		// B. 上記のSQL文を実行（queryメソッドを使用）
				
				/*PDOインスタンスのqueryメソッドで、Aで定義したSQL文を実行する
				戻り値のPDOStatementオブジェクト（インスタンス）を、変数$stmtに代入*/
				$stmt = $pdo->query($sql);
			
		//C. 実行結果から、データを配列として取得（fetchAllメソッドを使用）
		
				/*PDOStatementクラスのfetchAllメソッドを使う
				fetchAllメソッドは該当する全てのデータを配列として返す
				戻り値である配列を、変数$resultに代入*/
				$results = $stmt->fetchAll();
		
	//（２）取得したデータを表示	
			//$result変数に配列の形で格納されているので、foreachで中身を出力する
				foreach ($results as $row){						
						//$rowの中にはテーブルのカラム名が入る
						echo "ID：".$row['id']."　";
						echo "ユーザID：".$row['userID']."　";
						echo "コメント内容：".$row['comment']."　";
						echo "日付：".$row['date']."<br>";	
						
					echo "<hr>";
					} 	   		
?>
