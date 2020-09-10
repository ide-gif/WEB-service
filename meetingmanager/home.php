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

<div class="center">
<p class="left">
<?php
    session_start();
    $name = $_SESSION['name'];
    //データベースへの接続
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    echo "ようこそ！　";
    echo $name;
    echo "さん";    
    if(isset($_POST["show"])){
        $nengetu = $_POST["nengetu"];
        if($nengetu==NULL){
            echo "参照期間を選択してください";
        }
    }
    else{
        $nengetu = new DateTime();
        $nengetu = $nengetu ->format('Y-m');
    }
?></p>
</div>


<div class="center">
<form action="" method="post">
    <p class="left">照会期間：
    <input type="month" name="nengetu" value="<?php echo $nengetu;?>">
    <input type="submit" name="show" value="参照" class="detail"></p>
</form>
</div>


<hr>
<?php
    //結果を表示
    $schedule = array();        //ユーザーのスケジュールを保存する用

    $sql = 'SELECT * FROM schedule WHERE (owner = :owner) OR (participant like :participant)';
    $search = '%'.$name.'%';
    $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
    $stmt->bindParam(':owner', $name, PDO::PARAM_STR);
    $stmt->bindParam(':participant', $search, PDO::PARAM_STR);
    $stmt->execute();  
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //あいまい検索からより詳細に検索する
        $participant = explode(',', $row["participant"]);       //参加者を配列に入れる
        array_push($participant, $row["owner"]);                //主催者も配列に入れる

        //$nameが$participantに含まれるか調べる
        if (in_array($name, $participant)){
            array_push($schedule, $row);        //この$scheduleが出すべき配列
            
        }
    }

    $show_schedule = array();   //見せるスケジュール 
    $sort_date = array();       //ソートする用。テーブルのstartdateを格納する    
    foreach($schedule as $row){
        $startdate = new DateTime($row['startdate']);
        $startdate = $startdate -> format('Y-m');
        if($nengetu==$startdate){
            array_push($show_schedule, $row);
            array_push($sort_date, $row["startdate"]);        //ソートする用
        }
    }

    $sche_num = count($show_schedule);

?>
<div class="center">
<table border="1" class="border">
<tr>
<th class="gray">ミーティング名</th>
<th class="gray">開始時間</th>
<th class="gray">使用ソフト</th>
<th class="width5"></th>
</tr>


<?php
    array_multisort( $sort_date, SORT_ASC, SORT_STRING, $show_schedule);
    foreach ($show_schedule as $row){
        $startdate = new DateTime($row['startdate']);
        $startdate = $startdate -> format('Y-m-d H:i');
        //$rowの中にはテーブルのカラム名が入る
        echo "<tr>";
        echo "<td>".$row['meetingname']."</td>";
        echo "<td>".$startdate."</td>";
        echo "<td>".$row['soft']."</td>";
        echo "<td>";
        echo "<form action = detail.php method=post>";
        echo "<input type=hidden name=id value=".$row["id"].">";
        echo "<input type=submit name=detail value=詳細 class=detail>";
        echo "</form>";
        echo "</td>";
        echo "<tr>";
    }

    if($sche_num==0){
        echo "ミーティングは予定されていません。";
    }
    
?>
</table>
</div>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ホーム</title>
    <link rel="stylesheet" href="./homedesign.css">
</head>
<body>

</body>
</html>
