<!-- 仮登録から本登録へ-->
<div class = "center">
<?php
    //データベースへの接続
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    $urltoken = $_GET["urltoken"];
    $sql = 'SELECT * FROM pre_user WHERE urltoken=:urltoken';
	$stmt = $pdo->prepare($sql);
    $stmt->bindValue(':urltoken', $urltoken, PDO::PARAM_STR); // ←その差し替えるパラメータの値を指定
    $stmt ->execute();
    $results = $stmt->fetchAll();
    $date = new DateTime($results[0]['date']);
    $date = $date -> format('Y-m-d H:i:s');
    //すでに本登録が終わっていないか調べる
    if($results[0]['flag']==1){
        echo "このアカウントは既に本登録が完了しています。";
    }
    //1日未満かどうか検討する
    elseif(strtotime($date)<=(time() - 24 * 60 * 60)){
        echo "URLの有効期限が切れています。もう一度仮登録を行ってください。";
    }
    else{
        echo "会員登録が完了しました。";
        $mail = $results[0]['mail'];
        $name = $results[0]['name'];
        $pass = $results[0]['pass'];
        //userへの追加
        $sql = $pdo -> prepare("INSERT INTO user (mail, name, pass) VALUES (:mail, :name, :pass)");
        $sql -> bindParam(':mail', $mail, PDO::PARAM_STR);
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
        $sql -> execute();

        //pre_userのflagを変更
        $flag =1;
        $sql = 'UPDATE pre_user SET flag = :flag WHERE urltoken=:urltoken';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':flag', $flag, PDO::PARAM_INT);
        $stmt->bindValue(':urltoken', $urltoken, PDO::PARAM_STR); // ←その差し替えるパラメータの値を指定
        $stmt->execute();
    }
?>
</div>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>会員本登録</title>
    <link rel="stylesheet" href="./logindesign.css">
</head>
<body>
    <div class = "center">
        <form action="login.php" method="post">
       <input type="submit" value="ログイン画面へ戻る" class="button">
       </form>
    </div>
</body>
</html>