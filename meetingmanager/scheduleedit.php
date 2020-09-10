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
<?php
    session_start();
    $name = $_SESSION['name'];
    //データベースへの接続
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //編集関係
    if(isset($_POST["edit"])){
        $id = $_POST["id"];

        
        $sql = 'SELECT * FROM schedule WHERE id=:id ';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetch(); 

        //主催者かどうか判別
        if($name == $results['owner']){
            echo "内容の変更を受け付けます。必要事項の入力を行い、「確認」ボタンを押してください。";
            echo "<form action = scheduleedit.php  method=post>";
            //ここvalueで内容を表示させるようにする！
            echo "ミーティング名*：";
            echo "<input type=hidden name=id value=".$id.">";
            echo "<input type=text name=meetingname value=".$results['meetingname']." required>";
            echo '<br>';
            echo "リンク*：";
            echo "<input type=url name=url value=".$results['url']." required>";
            echo '<br>';
            echo "ミーティングID：";
            echo "<input type=text name=meetingid value=".$results['meetingid'].">";
            echo '<br>';
            echo "ミーティングパスワード：";
            echo "<input type=text name=meetingpass value=".$results['meetingpass'].">";
            echo '<br>';
            echo "開始日時*：";
            echo "<input type=datetime-local name=startdate value=".$results['startdate']." required>";
            echo '<br>';
            echo "終了日時：";
            echo "<input type=datetime-local name=enddate value=".$results['enddate'].">";
            echo '<br>';
            echo "主催者：";
            echo $results['owner'];
            echo '<br>';
            echo "参加者：";
            echo "<input type=text name=participant value=".$results['participant'].">";
            echo '<br>';
            echo "使用するソフト*：";
            echo "<select name=soft required>";
            echo '<option value="">選択してください</option>';
            echo '<option value="ZOOM">ZOOM</option>';
            echo '<option value="Microsoft Teams">Microsoft Teams</option>';
            echo '<option value="Skype">Skype</option>';
            echo '<option value="Webex">Webex</option>';
            echo '<option value="Hangouts Meet">Hangouts Meet</option>';
            echo '<option value="Whereby">Whereby</option>';
            echo '<option value="その他">その他</option>';
            echo '</select>';
            echo '<br>';
            echo "<input type=submit name =change value=変更 class=change>";
            echo "</form>";
            echo "*がついている個所は必須事項です。";
        }
        else{
            echo "主催者以外は編集はできません。";
            echo "<form action = detail.php  method=post>";
            echo "<input type=hidden name=id value=".$id.">";
            echo "<input type=submit name = detail value=詳細画面へ戻る>";    
            echo "</form>";
        }
    }
    elseif(isset($_POST["change"])){
        $id = $_POST["id"];
        $meetingname=$_POST["meetingname"];
        $url=$_POST["url"];
        $meetingid = $_POST["meetingid"];
        $meetingpass=$_POST["meetingpass"];
        $startdate=$_POST["startdate"];
        $enddate=$_POST["enddate"];
        $owner=$_SESSION["name"];
        $participant=$_POST["participant"];
        $soft=$_POST["soft"];
        //enddateはDATETIME型→空欄の時にNULLにする
        if(empty($enddate)==1){
            $enddate = NULL;
        }

        $sql = 'UPDATE schedule SET meetingname=:meetingname, url=:url,
        meetingid =:meetingid,meetingpass = :meetingpass,startdate = :startdate,
         enddate= :enddate, participant = :participant, soft = :soft WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt -> bindParam(':meetingname', $meetingname, PDO::PARAM_STR);
        $stmt -> bindParam(':url', $url, PDO::PARAM_STR);
        $stmt -> bindParam(':meetingid', $meetingid, PDO::PARAM_STR);
        $stmt -> bindParam(':meetingpass', $meetingpass, PDO::PARAM_STR);
        $stmt -> bindParam(':startdate', $startdate, PDO::PARAM_STR);
        $stmt -> bindParam(':enddate', $enddate, PDO::PARAM_STR);
        $stmt -> bindParam(':participant', $participant, PDO::PARAM_STR);
        $stmt -> bindParam(':soft', $soft, PDO::PARAM_STR);
        $stmt -> execute();

        header( "Location: https://tb-220287.tech-base.net/meetingmanager/home.php" ) ;
        exit ;

    }
    elseif(isset($_POST["delete"])){      
        
        $id = $_POST["id"];

        $sql = 'SELECT * FROM schedule WHERE id=:id ';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetch(); 

        //主催者かどうか判別
        if($name == $results['owner']){
            echo "本当に削除しますか？";
            echo "<form action = scheduleedit.php  method=post>";
            echo "<input type=hidden name=id value=".$id.">";
            echo "<input type=submit name = yes value=はい class=change>";
            echo "</form>";
            echo "<form action = detail.php  method=post>";
            echo "<input type=hidden name=id value=".$id.">";
            echo "<input type=submit name = detail value=いいえ class=change>";
            echo "</form>";
        }
        else{
            echo "主催者以外は削除はできません。";
            echo "<form action = detail.php  method=post>";
            echo "<input type=hidden name=id value=".$id.">";
            echo "<input type=submit name = detail value=詳細画面へ戻る>";
            echo "</form>";
        }


    }

    elseif(isset($_POST["yes"])){
        $id = $_POST["id"];
        $sql = 'delete from schedule where id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo "削除しました。";
    }
    
 
?>
</div>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>スケジュールの編集</title>
    <link rel="stylesheet" href="./design.css">
</head>
<body>
</body>
</html>
