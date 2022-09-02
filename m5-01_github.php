<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-01</title>
</head>
<body>
    <h1>好きな食べ物</h1>
    <p>※パスワードは編集できません。</p>
    <?php
    // DB接続設定
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    //データベース内にテーブルを作成
    $sql = "CREATE TABLE IF NOT EXISTS board2"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "time DATETIME ,"
    . "pass TEXT"
    .");";
    $stmt = $pdo->query($sql);
    
    //編集
    if (isset($_POST["edit"])&& isset($_POST["pass"])) {
        $edit = $_POST["edit"];//POST送信で「編集対象番号」を送信
        $pass = $_POST["pass"];    
        $sql = 'SELECT * FROM board2';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            if ($row['id']==$edit && $row['pass']==$pass){
                $editname=$row['name'];
                $editcomment=$row['comment'];
            }elseif($row['id']==$edit && $row['pass']!=$pass){
                $editname="";
                $editcomment="";
            }
        }
    }

    ?>

   <form action="" method="post">
   <input type="hidden" name="NO" placeholder="投稿番号" value=<?php if(isset($_POST["edit"])) {echo $edit;} ?>>
       <input type="text" name="name" placeholder="名前" value=<?php if(isset($_POST["edit"])) {echo $editname;} ?>>
       <input type="text" name="comment" placeholder="コメント" value=<?php if(isset($_POST["edit"])) {echo $editcomment;} ?>>
       <input type="text" name="pass" placeholder="パスワード">       
       <input type="submit" name="submit">
    </form>
   <form action="" method="post">
       <input type="text" name="delete" placeholder="削除対象番号">
       <input type="text" name="pass" placeholder="パスワード">
       <input type="submit" name="削除">
    </form>
   <form action="" method="post">
       <input type="text" name="edit" placeholder="編集対象番号">
       <input type="text" name="pass" placeholder="パスワード">
       <input type="submit" name="編集">
    </form>
    <?php
    if(isset($_POST["name"]) && isset($_POST["comment"]) && isset($_POST["pass"])) {
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $pass = $_POST["pass"];
        $time = date('Y-m-d H:i:s');
        //新規投稿
        if (empty($_POST["NO"])) {
            // プリペアドステートメントで SQLをあらかじめ用意しておく
            //INSERT INTO テーブル名 (列名1, 列名2,...) VALUES (値1, 値2,...);
            $sql = $pdo -> prepare("INSERT INTO board2 (name, comment, time, pass) VALUES (:name, :comment, :time, :pass)");
            //bindParam ($パラメータID, $バインドする変数 , $PDOデータ型定数 )
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':time', $time, PDO::PARAM_STR);
            $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
            // executeでクエリを実行
            $sql -> execute();
        //編集
        }else{
            $edit = $_POST["NO"];
            //UPDATE [テーブル名] SET [更新処理] WHERE [条件式];
            $sql = 'UPDATE board2 SET name=:name, comment=:comment WHERE id=:edit AND pass=:pass';
            $stmt = $pdo->prepare($sql);
            $stmt-> bindParam(':edit', $edit, PDO::PARAM_INT);
            $stmt-> bindParam(':pass', $pass, PDO::PARAM_STR);
            $stmt-> bindParam(':name', $name, PDO::PARAM_STR);
            $stmt-> bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt-> execute();
        }
    }  

    //削除
    if (isset($_POST["delete"]) && isset($_POST["pass"])) {
        $delete = $_POST["delete"];
        $pass = $_POST["pass"];    
        $sql = 'DELETE FROM board2 WHERE id=:delete AND pass=:pass';
        $stmt = $pdo->prepare($sql);
        $stmt-> bindParam(':delete', $delete, PDO::PARAM_INT);
        $stmt-> bindParam(':pass', $pass, PDO::PARAM_STR);
        $stmt-> execute();
    }
    
    //表示
    $sql = 'SELECT * FROM board2';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['time'].'<br>';
    }
    ?>
</body>
</html>