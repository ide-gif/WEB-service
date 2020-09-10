<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
    <link rel="stylesheet" href="./logindesign.css">
</head>
<body>
<div class="center">
    <p class="header">ログイン</p>
        <form action="" method="post">
       <p class="center">ユーザーID：<input type="text" name="ID" size="50" value="<?php if( !empty($_POST['ID']) ){ echo $_POST['ID']; } ?>"></p> 
       <p>パスワード：<input type="password" name="pass" size="50" value="<?php if( !empty($_POST['pass']) ){ echo $_POST['pass']; } ?>"></p> 
       <input type="hidden" name="token" value="<?php echo $urltoken;?>">

        <input type="submit" name="submit" value="ログイン" class="button">
        <br>
       </form>

       <br>
       <a href= 'https://tb-220287.tech-base.net/meetingmanager/signup_mail.php' >会員登録はこちら</a>

       <h2>このサイトでできること</h2>
       1：オンライン会議を一括管理！<br>
       開始時間、URL、参加者などオンライン会議で必要な情報を管理できます。<br><br>
       2：ファイル共有機能<br>
       会議ごとにファイルを整理できます。<br><br>
       3：参加者とのチャット<br>
       会議の参加者限定でチャットが行えます。
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

    if(isset($_POST["submit"])){
        //POSTされたデータを変数に入れる
        $name = $_POST["ID"];
        $pass = $_POST["pass"];

        $sql = 'select count(*) from user WHERE name=:name';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt ->bindValue(':name', $name, pdo::PARAM_STR);
        $stmt->execute();                             // ←SQLを実行する。
        $num = $stmt->fetchColumn();        //scheduleの件数

        if($num==0){
            echo "入力された会員は存在しません。";
        }
        else{
            $sql = 'SELECT * FROM user WHERE name=:name';
            $stmt = $pdo->prepare($sql);
            $stmt ->bindValue(':name', $name, pdo::PARAM_STR);
            $stmt ->execute();
            $result = $stmt -> fetch(PDO::FETCH_ASSOC);
            if(password_verify ($pass, $result["pass"]) === TRUE){
                $_SESSION['name'] = $name;
                header( "Location: https://tb-220287.tech-base.net/meetingmanager/home.php" ) ;
                exit ;
            }
            else{
                echo "パスワードが異なります。";
            }
        }
    }
?>
