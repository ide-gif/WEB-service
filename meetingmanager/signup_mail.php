<!-- 仮登録のページ-->
<!DOCTYPE html>
<html lang="ja">
<head>
    <title>会員仮登録</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./logindesign.css">
</head>
<body>
<div class="center">
        
<!-- 登録画面 -->

    <p class="header"> ■必要事項の入力を行い、「確認」ボタンを押してください。</p>
   <form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="post">
   <b>メールアドレス：<input type="text" name="mail" size="50" required></b> <br>
   <b>ユーザーID：<input type="text" name="ID" size="50" maxlength='20' minlength='4' required></b> <br>
   <b>パスワード：<input type="password" name="pass" size="50" maxlength='20' minlength='4' required></b> <br><br>
       <input type="submit" name="submit" value="確認" class="button">
   </form>

   <br>
   <p class="left">・入力していただいたＥメールアドレスに、案内メールを送信させていただきます。</p>
   <p class="left">・入力していただいたユーザーＩＤおよびパスワードは、サイトへのログイン時に必要となりますのでお手元にお控えください。</p>
   <p class="left">・ユーザーＩＤおよびパスワードは、任意の４～２０文字でご登録ください。（アルファベットの大文字・小文字にご注意ください）</p>
</div>
</body>
</html>


<?php
    //データベースへの接続
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //テーブルを作成
    $sql = "CREATE TABLE IF NOT EXISTS pre_user"    //仮登録会員
        . "("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"  //自動で登録されているナンバリング//
        . "urltoken VARCHAR(128) NOT NULL,"     //トークンの作成//
        . "mail VARCHAR(50) NOT NULL,"      //メールアドレス//
        . "name VARCHAR(128) NOT NULL,"     //名前
        . "pass VARCHAR(128) NOT NULL," //パスワード//    
        . "date DATETIME NOT NULL,"   //仮登録をしたタイムスタンプ//
        . "flag TINYINT(1) NOT NULL DEFAULT 0"   //仮登録をしたタイムスタンプ//
        .");";
    $stmt = $pdo->query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS user"    //登録会員
    . "("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"  //自動で登録されているナンバリング//
    . "mail VARCHAR(50) NOT NULL,"      //メールアドレス//
    . "name VARCHAR(128) NOT NULL,"     //名前
    . "pass VARCHAR(128) NOT NULL" //パスワード//    
    .");";
    $stmt = $pdo->query($sql);


    //送信ボタンを押した後の処理
    if(isset($_POST["submit"])){
        $a =0;
        //メールアドレスが空欄の時
        if(empty($_POST["mail"])){
            echo  "メールアドレスが未入力です。";
        }
        elseif(empty($_POST["ID"])){
            echo "ユーザーIDが未入力です。";
        }
        elseif(empty($_POST["pass"])){
            echo "パスワードが未入力です。";
        }
        else{
            //POSTされたデータを変数に入れる
            $mail = $_POST["mail"];
            $name = $_POST["ID"];
            $pass = $_POST["pass"];
            //passのハッシュ化
            $pass = password_hash ($pass, PASSWORD_DEFAULT);
 
            if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)){
                echo  "メールアドレスの形式が正しくありません。";
           }

           else{
               //DB確認
               $sql = 'SELECT id FROM user WHERE mail=:mail';
               $stmt = $pdo->prepare($sql);
               $stmt ->bindValue(':mail', $mail, pdo::PARAM_STR);
               $stmt ->execute();
               $result = $stmt -> fetch(PDO::FETCH_ASSOC);
               $a =1;
               //userテーブルに同じメールアドレスがある場合、$resultは数値をもつ
               //数値を持つとき、エラー表示
               if(isset($result["id"])){
                   echo "メールアドレスはすでに使用されています。";
                   $a =0;
               }
               $sql = 'SELECT id FROM user WHERE name=:name';
               $stmt = $pdo->prepare($sql);
               $stmt ->bindValue(':name', $name, pdo::PARAM_STR);
               $stmt ->execute();
               $result = $stmt -> fetch(PDO::FETCH_ASSOC);
               //userテーブルに同じユーザーIDがある場合、$resultは数値をもつ
               //数値を持つとき、エラー表示
               if(isset($result["id"])){
                   echo "ユーザーIDはすでに使用されています。";
                   $a =0;
               }
              }

        //エラーがない場合、pre_userテーブルにインサート
        if($a ===1){
            $urltoken = hash('sha256', uniqid(rand(),1));
            $url = "https://tb-220287.tech-base.net/meetingmanager/signup.php?urltoken=".$urltoken;
            $date = new DateTime();
            $date = $date ->format('Y-m-d H:i:s');
            $flag = 0;
            $sql = $pdo -> prepare("INSERT INTO pre_user (urltoken, mail, name, pass, date) VALUES (:urltoken, :mail, :name, :pass, :date)");
            $sql -> bindParam(':urltoken', $urltoken, PDO::PARAM_STR);
            $sql -> bindParam(':mail', $mail, PDO::PARAM_STR);
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql -> execute();
            $mailaddress = $mail;
            //require "phpmailer/phpmailer/send_test.php";
            echo  "テスト用（本来ならメールで送信される）：メールをお送りしました。24時間以内にメールに記載されたURLからご登録下さい。";
            echo "<br>".$url;
        }
        }

    }
?>

