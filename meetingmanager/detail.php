<div class="center">
<table border="0" class="border">
    <tr>
        <td class = "blue">
            <a href= 'https://tb-220287.tech-base.net/meetingmanager/home.php' class="head">TOP</a>
        </td>
        <td class = "blue">
            <a href= 'https://tb-220287.tech-base.net/meetingmanager/newschedule.php' class="head" >予定を追加</a>
        </td>
        <td class = "blue">
            <a href= 'https://tb-220287.tech-base.net/meetingmanager/login.php' class="head" >ログイン画面へ戻る</a>
        </td>
    </tr>
</table>
</div>

<hr>

<div class="center">
<span class="left">
<?php
    session_start();
    $name = $_SESSION['name'];
    //データベースへの接続
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));


    if(isset($_POST["detail"])||isset($_POST["submit"])||isset($_POST["upload"])){
        $id = $_POST["id"];     //表示するスケジュールのキー

        //結果を表示
        $sql = 'SELECT * FROM schedule WHERE id=:id ';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetch(); 
        $startdate = new DateTime($results['startdate']);
        $startdate = $startdate -> format('Y-m-d H:i');
        if($results['enddate']==NULL){
            $enddate =NULL;
        }
        else{
            $enddate = new DateTime($results['enddate']);
            $enddate = $enddate -> format('Y-m-d H:i');
        }

     

        echo "ミーティング名：".$results['meetingname'].'<br>';
        echo "リンク：";
        print "<a href= '".$results['url']."' >".$results['url']."</a><br>";

        echo "ミーティングID：".$results['meetingid'].'<br>';
        echo "ミーティングパスワード：".$results['meetingpass'].'<br>';
        echo "開始日時：".$startdate.'<br>';
        echo "終了日時：".$enddate.'<br>';
        echo "主催者：".$results['owner'].'<br>';
        echo "参加者：".$results['participant'].'<br>';
        echo "使用するソフト：".$results['soft'].'<br>';
        echo "ファイル：";


        //ファイルがアップロードされたとき
        if(isset($_POST["upload"])){
            $file=$_FILES["file"];
            //アップロードファイルを格納するための準備

            //ファイルがアップロードされたとき
            if($file["error"]==0){
                $filename = $file["name"];
                $dir = "files/".$results["file"];      //保存するディレクトリ
                $savefile = $dir."/".$filename;       //保存するファイル
                $savedir = mb_convert_encoding($savefile,'SJIS',"AUTO");    //文字化け防止
                move_uploaded_file($file["tmp_name"], $savedir);       //保存するディレクトリに移動させる
            }
        }
           
        //ファイルの中身を表示させる
        $dir = "files/".$results["file"]."/*";       //ファイルがあるディレクトリ
        $dir = sprintf("%s", $dir);             //文字列として認識
        $files = glob($dir);
        $file_array = array();
        foreach($files as $row){
            $file_array[] = mb_convert_encoding($row,'UTF-8',"AUTO");    //文字化け防止
        }

        $file_name = array();
        foreach($file_array as $row){
            $dir_detail = explode('/', $row);       //files/ミーティング番号/ファイル名をexplode
            $file_name = mb_convert_encoding($dir_detail[2],'UTF-8',"AUTO");          //ファイルの名前だけを格納する
            $dir = "files/".$results["file"]."/"; 
            $file_encode = $file_name;
            $file_encode = mb_convert_encoding($file_encode,'SJIS',"AUTO");
            $file_encode =urlencode($file_encode);
            $filelink = "https://tb-220287.tech-base.net/meetingmanager/".$dir.$file_encode;
            $filelink = sprintf("%s", $filelink);             //文字列として認識
            print "<a href= '".$filelink."' >$file_name</a>";
            echo "<br>";
        }
    }
?>
</span>
        <form action ="" method="post" enctype="multipart/form-data">
        <input type="file" name="file">
        <input type="hidden" name="id" value="<?php echo $id;?>">
        <input type="submit" name="upload" value="アップロード" class="button">
        </form>

        <form action = scheduleedit.php method=post>
        <input type=hidden name=id value="<?php echo $id;?>">
        <input type=submit name = edit value=ミーティングの編集 class="button">

        <input type=hidden name=id value="<?php echo $id;?>">
        <input type=submit name = delete value=ミーティングの削除 class="button">
        </form>
<?php
if(isset($_POST["detail"])||isset($_POST["submit"])||isset($_POST["upload"])){




        $bbsname = sprintf("%s", $results['file']);


        //掲示板用テーブルを作成
        $sql = "CREATE TABLE IF NOT EXISTS ".$bbsname.""    //ミーティングの予定を格納するテーブル
            . "("
            . "id INT AUTO_INCREMENT PRIMARY KEY,"  //自動で登録されているナンバリング//
            . "name char(32),"  //名前を入れる。文字列、半角英数で32文字
            . "comment TEXT,"    //コメントを入れる。文字列、長めの文章も入る
            . "date char(20)"  //日付を入れる。文字列、半角英数で20文字
            .");";
            $stmt = $pdo->query($sql);
    }
    else{
        echo "不正な遷移です。";
    }




    //以下掲示板機能
    //通常の投稿フォーム
    //4-5INSERT文：データを入力（データレコードの挿入）
    if(isset($_POST["submit"])){
        if(strlen($_POST["text"])){
        $sql = $pdo -> prepare("INSERT INTO ".$bbsname." (name, comment, date) VALUES (:name, :comment, :date)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            
            $comment = $_POST["text"];
            $date = date("Y/m/d H:i:s");
            $sql -> execute();
            //bindParamの引数名（:name など）はテーブルのカラム名に併せるとミスが少なくなります。最適なものを適宜決めよう。

        }
    }
            

    $sql = 'SELECT * FROM '.$bbsname.'';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
?>
</div>
<hr>
<div class="center">
<table border=2 class="border">
<tr>
    <th class="width5">番号</th>
    <th class="width10">名前</th>
    <th class="width75">コメント</th>
    <th class="width10">投稿時間</th>
</tr>
<?php
    foreach ($results as $row){
        echo "<tr>";
        echo "<td>".$row['id']."</td>";
        echo "<td>".$row['name']."</td>";
        echo "<td>".$row['comment']."</td>";
        echo "<td>".$row['date']."</td>";
        echo "<tr>";
    }
    echo "</table>";
?>

</table>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>詳細</title>
    <link rel="stylesheet" href="./design.css">
</head>
<body>
    <br>
        <form action="" method="post">
        <input type="text" name="text">
        <input type="hidden" name="id" value = "<?php echo $id;?>">
        <input type="submit" name="submit" value="コメントを送信" class="button">
        </form>
</body>
</html>

</div>