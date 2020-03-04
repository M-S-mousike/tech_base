<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=320, height=480, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=yes">
        <meta charset="utf-8"/>
        <title>mission_6_view</title>
    </head>
    <body>
      <h1>メインコンテンツ（２）：日記の閲覧</h1>

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

//（１）日付が選択されているかを判定（送信後）
$chose_message = "";
$date_selected = false;
if($login && isset($_POST["send"])){
        if(!empty($_POST["year"]) && !empty($_POST["month"]) && !empty($_POST["day"])){
                if(preg_match("/^[0-9]{1,4}$/", $_POST["year"])){
                        $date_selected = true;
                }
                else{
                        $chose_message = "<em>西暦の形式が正しくありません</em>";
                }
        }
        else{
                $chose_message = "<em>日付を選択してください</em>";
        }
}

//（２）日付が選択された場合、データベースから取得
//データベースからの取得済みを示す変数を定義
$data_got = false;

if($login && $date_selected && isset($_POST["send"])){
        //選択された日付を取得し、変数に代入
        $year = $_POST["year"];
        $month = $_POST["month"];
        $day = $_POST["day"];

        //日付検索用の変数を定義
        $date = $year.$month.$day;

        //3つのデータベースそれぞれから、投稿日時が一致するデータを取得。
            //A. まず、該当するコメントの内容を取得
                //SQL命令文を定義
                    $sql = 'SELECT * FROM comment_date
                                where userID=:userID and date=:date';

                //プリペアドステートメントをセットする
                    $stmt = $pdo->prepare($sql);

                //変数を紐付ける
                    $stmt->bindParam(':userID', $id, PDO::PARAM_INT);
                    $stmt->bindParam(':date', $date, PDO::PARAM_STR);

                //上記のSQL文を実行
                    $stmt->execute();

                //fetchAllメソッドでデータを配列として取得（該当データが0の場合、空の配列を返す）
                    $comment_result = $stmt->fetchAll();

            //B. 該当する画像のファイル名を取得
                //SQL命令文を定義
                    $sql = 'SELECT * FROM image_date
                                where userID=:userID and date=:date';

                //プリペアドステートメントをセットする
                    $stmt = $pdo->prepare($sql);

                //変数を紐付ける
                    $stmt->bindParam(':userID', $id, PDO::PARAM_INT);
                    $stmt->bindParam(':date', $date, PDO::PARAM_STR);

                //上記のSQL文を実行
                    $stmt->execute();

                //fetchAllメソッドでデータを配列として取得（該当データが0の場合、空の配列を返す）
                    $image_result = $stmt->fetchAll();

            //C. 該当する動画のファイル名を取得
                //SQL命令文を定義
                    $sql = 'SELECT * FROM video_date
                                where userID=:userID and date=:date';

                //プリペアドステートメントをセットする
                    $stmt = $pdo->prepare($sql);

                //変数を紐付ける
                    $stmt->bindParam(':userID', $id, PDO::PARAM_INT);
                    $stmt->bindParam(':date', $date, PDO::PARAM_STR);

                //上記のSQL文を実行
                    $stmt->execute();

                //fetchAllメソッドでデータを配列として取得（該当データが0の場合、空の配列を返す）
                    $video_result = $stmt->fetchAll();

        //データの取得を変数に反映
        $data_got = true;

}

?>

<?php

//PHP4. 日付の初期値を設定

//現在時刻を取得し、変数に代入
$default_year = date("Y");
$default_month = date("m");
$default_day = date("d");

//下準備として、日付（ymd）から年月日をバラして配列に格納する関数を定義しておく
function ymd_splitter($ymd){
    $ymd_array = array();
    $length = mb_strlen($ymd);

    $year = substr($ymd, 0, $length-4);
    $month = substr($ymd, $length-4, 2);
    $day = substr($ymd, $length-2, 2);
    array_push($ymd_array, $year, $month, $day);

    return $ymd_array;
}

//select_dateページからPOST送信を受けている場合には、初期値を上書き
if(!empty($_POST["ymd"])){
        $ymd_array = ymd_splitter($_POST["ymd"]);
        $default_year = $ymd_array[0];
        $default_month = $ymd_array[1];
        $default_day = $ymd_array[2];
}

?>

<?php

//編集・削除のページへジャンプ

//（１）コメントの編集・削除
if(isset($_POST["comment_edit"])){
        //コメント編集ページへジャンプし、処理を終了する
        header("Location: ../modify/modify_comment.php");
        exit();
}	

//（２）画像の編集・削除
if(isset($_POST["image_edit"])){
        //コメント編集ページへジャンプし、処理を終了する
        header("Location: ../modify/modify_image.php");
        exit();
}	

//（３）動画の編集・削除
if(isset($_POST["video_edit"])){
        //コメント編集ページへジャンプし、処理を終了する
        header("Location: ../modify/modify_video.php");
        exit();
}	
?>

<?php

//ログインページへジャンプ

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

//ログアウトページへジャンプ

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

<div id="chose">
    <h4>日付の選択</h4>
    <em><a href="select_date.php">既存の投稿の日付一覧はこちら</a></em>
    <p>
        <form method="POST">
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
        <input type="submit" value="この日付の投稿を表示" name="send">
        </form>
    </p>
    <p><?php echo $chose_message;?></p>
</div>

<hr><hr>

<div id="main">
    <p>
        <h4>対象の日付：</h4>
<?php
//選択された日付の表示
if($data_got){
    echo $year."年".$month."月".$day."日";
}
?>
    </p>
    <hr>

    <p>
        <h4>（１）コメント</h4>
<?php
/*
取得したコメントの表示
$comment_result変数に配列の形で格納されているので、foreachで中身を出力する
データ取得済みの場合のみ、以下の処理を行う
*/

//該当コメントがあるかを判定する変数を定義
$comment_exist = false;

if($data_got){
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
                        echo "投稿内容".$counter."：<br>",$comment,"<br><br>";		
                    }

                $comment_exist = true;
        }
}
?>
    </p>
    <form method="POST">
    <?php
    //コメントが存在する場合のみ以下の処理を実施		
    //A. 日付をセッションに保存しておく
        if($comment_exist){
                $_SESSION["comment_year"] = $year;
                $_SESSION["comment_month"] = $month;
                $_SESSION["comment_day"] = $day;

    //B. 編集・削除へのリンクを表示する
                echo "<input type=\"submit\" name=\"comment_edit\" value=\"コメントを編集・削除する\">";
        }
    ?>
    </form>
    <hr>

    <p>
        <h4>（２）画像</h4>
<?php
/*
取得した画像名をもとに、画像を表示。
$image_result変数に配列の形で格納されているので、foreachで処理。
データ取得済みの場合のみ、以下の処理を行う。
*/

//該当画像があるかを判定する変数を定義
$image_exist = false;

if($data_got){
        //（１）結果が空の場合
        if(count($image_result) == 0){
                echo "該当する画像がありません。";
        }

        //（２）空ではない場合
        else{
                //カウンタを定義
                $counter = 0;

                foreach ($image_result as $row){
                        $counter ++;
                        $image_source = "../../uploaded/image/".$row['filename'];
                        echo "画像".$counter."：<br>";
                        echo "<img src=".$image_source." height=300px><br>";				
                    }

                $image_exist = true;
        }
}
?>
    </p>
    <form method="POST">
    <?php
    //画像が存在する場合のみ以下の処理を実施		
    //A. 日付をセッションに保存しておく
        if($image_exist){
                $_SESSION["image_year"] = $year;
                $_SESSION["image_month"] = $month;
                $_SESSION["image_day"] = $day;

    //B. 編集・削除へのリンクを表示する
                echo "<input type=\"submit\" name=\"image_edit\" value=\"画像を編集・削除する\">";
        }
    ?>
    </form>
    <hr>

    <p>
        <h4>（３）動画</h4>
<?php
/*
取得した動画名をもとに、動画を表示。
$image_result変数に配列の形で格納されているので、foreachで処理。
データ取得済みの場合のみ、以下の処理を行う。
*/

//該当動画があるかを判定する変数を定義
$video_exist = false;

if($data_got){
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
                        echo "動画".$counter."：<br>";
                        echo "<video src=".$video_source." height=300px controls></video><br>";
                    }

                echo "<em>動画を再生するにはvideoタグをサポートしたブラウザが必要です。</em>";

                $video_exist = true;
        }
}
?>
    </p>
    <form method="POST">
    <?php
    //画像が存在する場合のみ以下の処理を実施		
    //A. 日付をセッションに保存しておく
        if($video_exist){
                $_SESSION["video_year"] = $year;
                $_SESSION["video_month"] = $month;
                $_SESSION["video_day"] = $day;

    //B. 編集・削除へのリンクを表示する
                echo "<input type=\"submit\" name=\"video_edit\" value=\"動画を編集・削除する\">";
        }
    ?>
    </form>
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
