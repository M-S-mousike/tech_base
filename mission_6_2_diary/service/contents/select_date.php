<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes">
        <meta charset="utf-8"/>
        <title>mission_6_select_date</title>
    </head>
    <body>
      <h1>メインコンテンツ（３）：既存の投稿の日付一覧</h1>

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

//PHP3. データベースから対象となる日記を取得

//（１）日付が選択された場合、データベースから取得
if($login){
        //3つのデータベースそれぞれから、IDが一致するデータを取得。
            //A. まず、該当するコメントの内容を取得
                //SQL命令文を定義
                    $sql = 'SELECT * FROM comment_date
                                where userID=:userID';

                //プリペアドステートメントをセットする
                    $stmt = $pdo->prepare($sql);

                //変数を紐付ける
                    $stmt->bindParam(':userID', $id, PDO::PARAM_INT);

                //上記のSQL文を実行
                    $stmt->execute();

                //fetchAllメソッドでデータを配列として取得（該当データが0の場合、空の配列を返す）
                    $comment_result = $stmt->fetchAll();

            //B. 該当する画像のファイル名を取得
                //SQL命令文を定義
                    $sql = 'SELECT * FROM image_date
                                where userID=:userID';

                //プリペアドステートメントをセットする
                    $stmt = $pdo->prepare($sql);

                //変数を紐付ける
                    $stmt->bindParam(':userID', $id, PDO::PARAM_INT);

                //上記のSQL文を実行
                    $stmt->execute();

                //fetchAllメソッドでデータを配列として取得（該当データが0の場合、空の配列を返す）
                    $image_result = $stmt->fetchAll();

            //C. 該当する動画のファイル名を取得
                //SQL命令文を定義
                    $sql = 'SELECT * FROM video_date
                                where userID=:userID';

                //プリペアドステートメントをセットする
                    $stmt = $pdo->prepare($sql);

                //変数を紐付ける
                    $stmt->bindParam(':userID', $id, PDO::PARAM_INT);

                //上記のSQL文を実行
                    $stmt->execute();

                //fetchAllメソッドでデータを配列として取得（該当データが0の場合、空の配列を返す）
                    $video_result = $stmt->fetchAll();		
}

//（２）コメント、画像、動画それぞれの日付を格納する配列を定義しておく
$comment_dates = array();
$image_dates = array();
$video_dates = array();

/*（３）日付（ymd）から年月日をバラして配列に格納する関数を定義しておく
（後で日付を綺麗に表示するための下準備）*/
function ymd_splitter($ymd){
    $ymd_array = array();
    $length = mb_strlen($ymd);

    $year = substr($ymd, 0, $length-4);
    $month = substr($ymd, $length-4, 2);
    $day = substr($ymd, $length-2, 2);
    array_push($ymd_array, $year, $month, $day);

    return $ymd_array;
}

?>

<?php

//PHP4. ログインページへジャンプ

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

//PHP5. ログアウトページへジャンプ

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

<hr>

<div id="main">	
    <p>
        <h4>（１）既存のコメント（投稿のある日付）</h4>
<?php
/*
取得したコメントの表示
$comment_result変数に配列の形で格納されているので、foreachで中身を出力する
データ取得済みの場合のみ、以下の処理を行う
*/
if($login){
        //（１）結果が空の場合
        if(count($comment_result) == 0){
                echo "コメントの投稿はありません。";
        }

        //（２）空ではない場合
        else{
                //A. 日付を取得し、配列に格納
                    foreach ($comment_result as $row){
                            //日付を取得し、整数型に変換
                            $date = (int)$row['date'];
                            //配列に既に含まれていない場合のみ、追加
                            if(!in_array($date, $comment_dates)){
                                    array_push($comment_dates, $date);
                            }
                    }

                //B. 日付をソートし、順次表示
                    sort($comment_dates);
                    //カウンタを定義
                    $counter = 0;

                    foreach ($comment_dates as $date){
                            $counter ++;

                            //日付を文字列に変換
                            $str = (string)$date;

                            //日付の形式を整える（「y年m月d日」という形に）
                            $ymd_array = ymd_splitter($str);
                            $ymd = $ymd_array[0]."年".$ymd_array[1]."月".$ymd_array[2]."日";

                            echo "<form method = \"POST\" action = \"view.php\">日付".$counter."：";
                            echo "<input type=\"hidden\" size=\"10\" name=\"ymd\" value=\"".$str."\">";
                            echo "<input type=\"submit\" value=\"".$ymd."\"></form><br>";
                    }
        }
}
?>
    </p>
    <hr>

    <p>
        <h4>（２）既存の画像（投稿のある日付）</h4>
<?php
/*
取得した画像名をもとに、画像を表示。
$image_result変数に配列の形で格納されているので、foreachで処理。
データ取得済みの場合のみ、以下の処理を行う。
*/

if($login){
        //（１）結果が空の場合
        if(count($image_result) == 0){
                echo "画像の投稿はありません。";
        }

        //（２）空ではない場合
        else{
                //A. 日付を取得し、配列に格納
                    foreach ($image_result as $row){
                            //日付を取得し、整数型に変換
                            $date = (int)$row['date'];
                            //配列に既に含まれていない場合のみ、追加
                            if(!in_array($date, $image_dates)){
                                    array_push($image_dates, $date);
                            }
                    }

                //B. 日付をソートし、順次表示
                    sort($image_dates);
                    //カウンタを定義
                    $counter = 0;

                    foreach ($image_dates as $date){
                            $counter ++;

                            //日付を文字列に変換
                            $str = (string)$date;

                            //日付の形式を整える（「y年m月d日」という形に）
                            $ymd_array = ymd_splitter($str);
                            $ymd = $ymd_array[0]."年".$ymd_array[1]."月".$ymd_array[2]."日";

                            echo "<form method = \"POST\" action = \"view.php\">日付".$counter."：";
                            echo "<input type=\"hidden\" size=\"10\" name=\"ymd\" value=\"".$str."\">";
                            echo "<input type=\"submit\" value=\"".$ymd."\"></form><br>";
                    }
        }
}
?>
    </p>
    <hr>

    <p>
        <h4>（３）既存の動画（投稿のある日付）</h4>
<?php
/*
取得した動画名をもとに、動画を表示。
$video_result変数に配列の形で格納されているので、foreachで処理。
データ取得済みの場合のみ、以下の処理を行う。
*/
if($login){
        //（１）結果が空の場合
        if(count($video_result) == 0){
                echo "動画の投稿はありません。";
        }

        //（２）空ではない場合
        else{
                //A. 日付を取得し、配列に格納
                    foreach ($video_result as $row){
                            //日付を取得し、整数型に変換
                            $date = (int)$row['date'];
                            //配列に既に含まれていない場合のみ、追加
                            if(!in_array($date, $video_dates)){
                                    array_push($video_dates, $date);
                            }
                    }

                //B. 日付をソートし、順次表示
                    sort($video_dates);
                    //カウンタを定義
                    $counter = 0;

                    foreach ($video_dates as $date){
                            $counter ++;

                            //日付を文字列に変換
                            $str = (string)$date;

                            //日付の形式を整える（「y年m月d日」という形に）
                            $ymd_array = ymd_splitter($str);
                            $ymd = $ymd_array[0]."年".$ymd_array[1]."月".$ymd_array[2]."日";

                            echo "<form method = \"POST\" action = \"view.php\">日付".$counter."：";
                            echo "<input type=\"hidden\" size=\"10\" name=\"ymd\" value=\"".$str."\">";
                            echo "<input type=\"submit\" value=\"".$ymd."\"></form><br>";
                    }
        }
}
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
