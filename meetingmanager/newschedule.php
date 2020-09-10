<div class="center">
<table border="0" class="border">
    <tr>
        <td class = "blue">
            <a href= 'https://tb-220287.tech-base.net/meetingmanager/home.php' >TOP</a>
        </td>
        <td class = "blue">
            <a href= 'https://tb-220287.tech-base.net/meetingmanager/newschedule.php' >予定を追加</a>
        </td>
        <td class = "blue">
            <a href= 'https://tb-220287.tech-base.net/meetingmanager/login.php' >ログイン画面へ戻る</a>
        </td>
    </tr>
</table>
</div>

<hr>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新規予定作成</title>
    <link rel="stylesheet" href="./newscheduledesign.css">
</head>
<body>
    <div class="center">

        <p> ■必要事項の入力を行い、「確認」ボタンを押してください。</p>
        <form action="" method="post" enctype="multipart/form-data">
        <p class="left">ミーティング名*：<input type="text" name="meetingname" size="50" required></p> 
        <p class="left">URL*：<input type="url" name="url" required></p> 
        <p class="left">ミーティングID：<input type="text" name="meetingid" size="50"></p> 
        <p class="left">ミーティングパスワード：<input type="text" name="meetingpass" size="50"></p> 
        <p class="left">開始日時*：<input type="datetime-local" name="startdate" required></p> 
        <p class="left">終了日時：<input type="datetime-local" name="enddate"></p> 
        <p class="left">招待する人のユーザーID：<input type="text" name="participant"></p> 
        <p>※招待する人が複数人いる場合、ユーザーIDはコンマ（,)で区切ってください。</p>
        <p class="left">使用するソフト*：<select name="soft" required>
            <option value="">選択してください</option>
            <option value="ZOOM">ZOOM</option>
            <option value="Microsoft Teams">Microsoft Teams</option>
            <option value="Skype">Skype</option>
            <option value="Webex">Webex</option>
            <option value="Hangouts Meet">Hangouts Meet</option>
            <option value="Whereby">Whereby</option>
            <option value="その他">その他</option>
            </select>
        <p class="left">共有ファイル：<input type="file" name="file"></p> 
        <input type="submit" name="submit" value="確認" class="button">
        </form>
        <p>*がついている個所は必須事項です。</p>

    </div>
</body>
</html>

<?php
    session_start();
    //データベースへの接続
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //テーブルを作成
    $sql = "CREATE TABLE IF NOT EXISTS schedule"    //ミーティングの予定を格納するテーブル
        . "("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"  //自動で登録されているナンバリング//
        . "meetingname VARCHAR(50) NOT NULL,"     //ミーティング名//
        . "url VARCHAR(500) NOT NULL,"      //URL//
        . "meetingid VARCHAR(50),"     //meetingid
        . "meetingpass VARCHAR(50)," //パスワード//    
        . "startdate DATETIME NOT NULL," 
        . "enddate DATETIME,"
        . "owner TEXT,"
        . "participant TEXT,"
        . "soft VARCHAR(20),"
        . "file VARCHAR(50)"
        .");";
    $stmt = $pdo->query($sql);
        

    if(isset($_POST["submit"])){
        $meetingname=$_POST["meetingname"];
        $url=$_POST["url"];
        $meetingid = $_POST["meetingid"];
        $meetingpass=$_POST["meetingpass"];
        $startdate=$_POST["startdate"];
        $enddate=$_POST["enddate"];
        $owner=$_SESSION["name"];
        $participant=$_POST["participant"];
        $soft=$_POST["soft"];
        $file=$_FILES["file"];
        
        //アップロードファイルを格納するための準備

    //提案：$filename = uniqid()を用いて唯一の値を生成

        $savedir = uniqid();
        echo $savedir;
        $directory = $savedir;

        //$sql = 'select count(*) from schedule';
        //$stmt = $pdo->query($sql);
        //$schenum = $stmt->fetchColumn();        //scheduleの件数
        //$schenum = $schenum+1;  //これはテーブルに格納されるスケジュールのidと同じ数
        $storedir = 'files/';
        //ディレクトリの作成
        if (!file_exists($storedir.$savedir."/")) {
            mkdir($storedir.$savedir."/", 0777);
        }

        //ファイルがアップロードされたとき
        if($file["error"]==0){
            //アップロードされた拡張子を調べる
            //$ext = pathinfo($file["name"], PATHINFO_EXTENSION);
            //$filename = uniqid().".".$ext;      //ファイルの名前
            $filename = $file["name"];
            $dir = $storedir.$savedir;      //保存するディレクトリ
            $savefile = $dir."/".$filename;       //保存するファイル
            $savedir = mb_convert_encoding($savefile,'SJIS',"AUTO");    //文字化け防止
            move_uploaded_file($file["tmp_name"], $savedir);       //保存するディレクトリに移動させる
        }
        else{
            $savefile = NULL;
        }

        //enddateはDATETIME型→空欄の時にNULLにする
        if(empty($enddate)==1){
            $enddate = NULL;
        }

            //scheduleへの追加
        $sql = $pdo -> prepare("INSERT INTO schedule (meetingname, url, meetingid, meetingpass, startdate, enddate, owner, participant, soft, file) 
        VALUES (:meetingname, :url, :meetingid, :meetingpass, :startdate, :enddate, :owner, :participant, :soft, :file)");
        $sql -> bindParam(':meetingname', $meetingname, PDO::PARAM_STR);
        $sql -> bindParam(':url', $url, PDO::PARAM_STR);
        $sql -> bindParam(':meetingid', $meetingid, PDO::PARAM_STR);
        $sql -> bindParam(':meetingpass', $meetingpass, PDO::PARAM_STR);
        $sql -> bindParam(':startdate', $startdate, PDO::PARAM_STR);
        $sql -> bindParam(':enddate', $enddate, PDO::PARAM_STR);
        $sql -> bindParam(':owner', $owner, PDO::PARAM_STR);
        $sql -> bindParam(':participant', $participant, PDO::PARAM_STR);
        $sql -> bindParam(':soft', $soft, PDO::PARAM_STR);
        $sql -> bindParam(':file', $directory, PDO::PARAM_STR);
        $sql -> execute();

        echo "スケジュールが追加されました。<br>";

        header( "Location: https://tb-220287.tech-base.net/meetingmanager/home.php" ) ;
        exit ;
    }

?>
