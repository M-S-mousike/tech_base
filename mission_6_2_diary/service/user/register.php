<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes">
        <meta charset="utf-8"/>
        <title>mission_6_register</title>
    </head>
    <body>
      <h1>本登録・メール認証機能</h1>

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

//PHP2. ログイン機能の実装

// セッションの有効期限を30分に設定
//（※必ずセッション開始前に設定すること）
session_set_cookie_params(60*30);

// セッション開始
session_start();

/*

処理の前提として、変数の初期値を定義
何のボタンも押されていない間は、これらの初期値が適用される。

*/
        //処理に応じて表示されるメッセージを格納する変数を定義しておく
        $send_message = "";

        //セッションからメールアドレスを取得（空でない場合のみ）
        if(empty($_SESSION["email"])){
                echo "仮登録がお済みでなければ、<a href=\"sign_up.php\">こちら</a>からまず仮登録を行なってください。";
        }
        else{
                $email = $_SESSION["email"];
        }
/*

I. 認証コードの確認

*/

    //sendボタン（＝ログインボタン）が押された場合に、以下の処理が行われる
        if(isset($_POST["send"])){

                //以下、前提処理（入力内容が不十分の場合、メッセージを表示する）
                //（イ）まだ送信されていない場合の処理（メッセージ）
                if (!isset($_POST["email"])){
                        $send_message = "<em>メールアドレスとパスワードを入力してください</em>";
                } 	   		
                //（ロ）メールアドレスが空の場合の処理（メッセージ）
                elseif (empty($_POST["email"])){
                        $send_message =  "<em>※メールアドレスが空です！</em>";
                }
                //（ハ）メールアドレスの形式が正しくない場合の処理
                elseif (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $_POST["email"])){
                        $send_message = "<em>メールアドレスの形式が正しくありません！</em>";
                }
                //（ニ）認証コードが空の場合の処理（メッセージ）
                elseif (empty($_POST["code"])){
                        $send_message =  "<em>※認証コードが空です！</em>";
                }
                //（ホ）ユーザ名が空の場合の処理（メッセージ）
                elseif (empty($_POST["name"])){
                        $send_message =  "<em>※ユーザ名が空です！</em>";
                }
                //（ヘ）パスワードが空の場合の処理（メッセージ）
                elseif (empty($_POST["password"])){
                        $send_message = "<em>※パスワードが空です！</em>";
                }

                //（ト）それ以外の場合の処理（＝入力内容に形式上の問題がない場合）
                else {
                    //A. まず入力内容を取得
                        $email = $_POST["email"];
                        $code =$_POST["code"];
                        $name =$_POST["name"];
                        $password = $_POST["password"];	 	  

                    //B. メールアドレスをもとに、認証コードの称号を行う処理			
                        //既に本登録されているメールアドレスかどうかを判定する変数を定義
                        $already_registerd = false;

                        //該当するメールアドレスがあったかを判定する変数を定義
                        $wrong_email = false;

                        //コードが間違っていたかを判定する変数を定義
                        $wrong_code = false;

                        //本登録照合の処理
                            //(i)メールアドレスに該当するパスワードを取得する（※本登録ユーザのテーブルから）
                                //プリペアドステートメントを用意
                                $sql = 'SELECT * FROM usertable where email=:email';

                                //プリペアドステートメントをセットする
                                $stmt = $pdo->prepare($sql);

                                //変数を紐付ける
                                $stmt->bindParam(':email', $email, PDO::PARAM_STR);

                                //命令を実行し、結果を配列として格納
                                $stmt->execute();
                                $result = $stmt->fetch();

                            //(ii)分岐
                                //既に本登録されている場合
                                if($result != false){
                                        $already_registerd = true;
                                }
                                //そうでない場合のみ、次の照合へすすむ
                                else{

                                        //仮登録照合の処理
                                            //(i)メールアドレスに該当するパスワードを取得する（※仮登録ユーザのテーブルから）
                                                //プリペアドステートメントを用意
                                                $sql = 'SELECT * FROM pre_user where email=:email';

                                                //プリペアドステートメントをセットする
                                                $stmt = $pdo->prepare($sql);

                                                //変数を紐付ける
                                                $stmt->bindParam(':email', $email, PDO::PARAM_STR);

                                                //命令を実行し、結果を配列として格納
                                                $stmt->execute();
                                                $result = $stmt->fetch();

                                                    /*
                                                    注）fetchメソッドは、該当するデータがないときにはfalseを返す。
                                                    */

                                            //(iii) 状況に応じて場合わけ
                                                //該当データが存在しない場合
                                                if($result == false){
                                                        $wrong_email = true;
                                                }

                                                //パスワードが間違っている場合（ハッシュにマッチするかを検証）
                                                elseif(!password_verify($code, $result["token"])){
                                                        $wrong_code = true;
                                                }
                                }

                    //C. 処理に応じて、メッセージ等を変更
                        //認証コードが間違っていた場合
                        if($wrong_code){
                                $send_message = "<em>※認証コードが正しくありません</em>";		
                        }

                        //パスワードは間違っていないが、該当するアドレスがなかった場合
                        elseif($wrong_email){
                                $send_message = "<em>※該当するメールアドレスがありません</em>";										
                        }
                        //既に本登録されていた場合
                        elseif($already_registerd){
                                $send_message = "<em>※そのアドレスは既に本登録されています</em>";										
                        }
/*

II. ユーザの新規登録

*/			
                //メールアドレスにも認証コードにも問題がなかった場合のみ、新規登録へと進む
                if(!$already_registerd && !$wrong_email && !$wrong_code){
                        //メールアドレス、ユーザ名とパスワードとをデータベースに登録する処理（todo）
                             //A. これらのデータをデータベースに書き込む
                                    //(i) prepareメソッドの戻り値（PDOStatementオブジェクト）を、変数$sqlに代入
                                        $sql = $pdo -> prepare("INSERT INTO usertable (email, name, password) 
                                                                                VALUES (:email, :name, :password)");

                                    //(ii) 入力する変数を列に紐付ける（bindParam）。データ型に注意！
                                        $sql -> bindParam(':email', $email, PDO::PARAM_STR);
                                        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                                        $sql -> bindParam(':password', $hash, PDO::PARAM_STR);

                                    //(iii) パスワードをハッシュ（第二引数は絶対必要！！！）
                                        $hash = password_hash($password, PASSWORD_DEFAULT);

                                    //(iv) 命令文を実行（プリペアドステートメントを実行）
                                        $sql -> execute();

                            //B. メッセージを表示する
                                $send_message = "<em>無事登録されました！</em>";
                }
        }
}

?>

<?php

//ボタンが押された場合に、以下の処理が行われる
    if(isset($_POST["top"])){		
            //トップページへジャンプし、処理を終了する
                header("Location: top.php");
                exit();
    }	

?>

<hr>
<div id="form">
    <div id="register">
        <h3>本登録</h3>	
        <form method="POST" action="">
            <p>
                  <label for="email">仮登録したメールアドレス：</label><br>
                  <input type="text" size="16" name="email" value="<?php if(isset($email)){echo $email;}?>">
             </p>	
              <p>
                  <label for="code">認証コード（メールアドレスに送信されたもの）：</label><br>
                  <input type="text" size="16" name="code" placeholder="認証コードを入力" value="">
             </p>
              <p>
                  <label for="name">ユーザ名の設定：</label><br>
                  <input type="text" size="16" name="name" placeholder="ユーザ名を設定" value="">
             </p>
              <p>
                  <label for="password">パスワードの設定：</label><br>
                  <input type="text" size="16" name="password" placeholder="パスワードを設定" value="">
             </p>
             <p>
                  <input type="submit" value="本登録" name="send" id="send"><br>
             </p>
             <?php echo $send_message ?>
       </form>
    </div>
        <div id="jump">
        <form method="POST" action="">
             <p>
                  <input type="submit" value="トップページへ戻る" name="top"><br>
             </p>
       </form>
    </div>
</div>
