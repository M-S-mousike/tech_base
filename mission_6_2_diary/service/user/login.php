<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes">
        <meta charset="utf-8"/>
        <title>mission_6_login</title>
    </head>
    <body>
      <h1>ログイン画面</h1>

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

/*

I. ログイン

*/

    //sendボタン（＝ログインボタン）が押された場合に、以下の処理が行われる
        if(isset($_POST["send"])){

                //以下、前提処理（入力内容が不十分の場合、メッセージを表示する）
                //（イ）まだ送信されていない場合の処理（メッセージ）
                if (!isset($_POST["email"])){
                        $send_message = "<em>メールアドレスとパスワードを入力してください</em>";
                } 

                //（ロ）送信内容が空の場合の処理（メッセージ）
                elseif (empty($_POST["email"])){
                        $send_message =  "<em>※メールアドレスが空です！</em>";
                }

                //（ハ）パスワードが空の場合の処理（メッセージ）
                elseif (empty($_POST["password"])){
                        $send_message = "<em>※パスワードが空です！</em>";
                }

                //（ニ）それ以外の場合の処理（＝アドレスもパスワードも空でない場合）
                else {
                    //A. まずメールアドレスとパスワードとを取得
                        $email = $_POST["email"];
                        $password = $_POST["password"];	 	  

                    //B. メールアドレスをもとに、パスワードの称号を行う処理																	
                        //該当するメールアドレスがあったかを判定する変数を定義
                        $wrong_email = false;

                        //パスワードが間違っていたかを判定する変数を定義
                        $wrong_password = false;

                        //照合の処理
                            //(i)メールアドレスに該当するパスワードを取得する
                                //プリペアドステートメントを用意
                                $sql = 'SELECT * FROM usertable where email=:email';

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
                                elseif(!password_verify($password, $result["password"])){
                                        $wrong_password = true;
                                }

                                //その他の場合（＝パスワードが正しい場合）、ログイン処理を行う
                                else{
                                        //セッションIDを更新する
                                        session_regenerate_id();

                                        //ユーザIDとユーザ名をセッションに保存
                                        $_SESSION["id"] = $result["id"];
                                        $_SESSION["name"] = $result["name"];

                                        //ログイン後のウェルカムページへジャンプし、処理を終了する
                                        header("Location: ../contents/welcome.php");
                                        exit();
                                }

                    //C. 処理に応じて、メッセージ等を変更
                        //パスワードが間違っていた場合
                        if($wrong_password){
                                $send_message = "<em>※パスワードが正しくありません</em>";		
                        }

                        //パスワードは間違っていないが、該当するアドレスがなかった場合
                        elseif($wrong_email){
                                $send_message = "<em>※該当するメールアドレスがありません</em>";										
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
    <div id="log_in">
        <h3>ログイン</h3>	
        <form method="POST" action="">
            <p>
                  <label for="email">メールアドレス：</label><br>
                  <input type="text" size="16" name="email" placeholder="メールアドレスを入力" value="">
             </p>	
              <p>
                  <label for="password">パスワード：</label><br>
                  <input type="text" size="16" name="password" placeholder="パスワードを入力" value="">
             </p>
             <p>
                  <input type="submit" value="log in" name="send" id="send"><br>
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
