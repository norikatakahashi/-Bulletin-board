<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Bulletin-board</title>
</head>
<body>
    
    <?php
    
    //変数定義（value用）
    $editnum="";
    $editname="";
    $editcomment="";
    $edit="";
    //パスワードの注意用
    $del="";
    $hen="";
    $ps="";
    
    //投稿読み込み
    $name=$_POST["name"];
    $com=$_POST["com"];

    //DB接続設定
	$dsn='mysql:dbname=******;host=localhost';
	$user='******';
	$password='PASS***WORD';
	$pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));

    //テーブル作成
	$sql = "CREATE TABLE IF NOT EXISTS toko"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "date DATETIME"
	.");";
	$stmt = $pdo->query($sql);

    //削除番号取得
    $snum=$_POST["snum"];
    
    //削除機能
    if(isset($_POST["ssubmit"]) && $snum!=null
    &&isset($_POST["spass"])){
    $spass=$_POST["spass"];
    if($spass==null){
        $del=1;
        $tyui= "パスワードを入れて下さい";
    }elseif($spass!="pass"){
        $del=2;
        $tyui= "パスワードが異なります";
    }else
    {
    $id=$snum;
    $sql = 'DELETE FROM toko WHERE id=:id';
    $stmt=$pdo->prepare($sql);
    $stmt->bindParam(':id',$id, PDO::PARAM_INT);
    
    $stmt->execute();
    }
        
    }

    //編集
    $hnum=$_POST["hnum"];
    $hname=$_POST["hname"];
    $hcom=$_POST["hcom"];
    
    //編集選択
    if(isset($_POST["hnum"])){
    $edit=$_POST["hnum"];
    $hpass=$_POST["hpass"];
    if($hpass==null){
        $ps=1;
        $tyui= "パスワードを入れて下さい";
    }elseif($hpass!="pass"){
        $ps=2;
        $tyui= "パスワードが異なります";
    }else{
    $editnum="";
    $editname="";
    $editcomment="";
    $sql="SELECT * FROM toko";
    $stmt=$pdo->query($sql);
    $results=$stmt->fetchAll();
    foreach($results as $row){
        $editID=$row["id"];
        $editNAME=$row["name"];
        $editCOMMENT=$row["comment"];
    if($editID==$edit){
        //編集番号一致したら
            $editnum=$editID;
            $editname=$editNAME;
            $editcomment=$editCOMMENT;
        }
    }}
    }
    
    //編集機能
    if(!empty($_POST["blank"]) && !empty($_POST["name"]) &&
!empty($_POST["com"])){
    $hiddenNo=$_POST["blank"];
    $name=$_POST["name"];
    $comment=$_POST["com"];
    $date=date("Y/m/d H:i:s");
    $pass=$_POST["pass"];
    if($pass==null){
        $ps=1;
        $tyui= "パスワードを入れて下さい";
    }elseif($pass!="pass"){
        $ps=2;
        $tyui= "パスワードが異なります";
    }else{
    $sql= 'UPDATE toko SET name=:name,comment=:comment,date=:date WHERE id=:id';
    $stmt=$pdo->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->bindParam(':id',$hiddenNo,PDO::PARAM_INT);

	$stmt->execute();
    }
}

    //データ入力
    if(isset($_POST["submit"]) && $name!=null && $com!=null
    && empty($_POST["blank"])){
    
    $sql = $pdo -> prepare("INSERT INTO toko 
    (name, comment,date) VALUES (:name, :comment,:date)");
    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindParam(':date', $date, PDO::PARAM_STR);
	$name = $name;
	$comment =$com;
	$date=date('Y/m/d H:i:s');
	$sql -> execute();
    }
    
    ?>
           
    <form  method="post">
    【投稿フォーム】<br>
        名前:<input type="text" name="name" placeholder="名前"
        value="<?php if($editname!=""){echo "$editname";} ?>"><br>
        コメント:<input type="text" name="com" 
        placeholder="コメント" value="<?php if($editcomment!=""){echo "$editcomment";} ?>"><br>
        パスワード:<input type="password" 
        name="pass" placeholder="パスワード"
        readonly onfocus="this.removeAttribute('readonly');"><br>
        <input type="submit" name="submit">
        <p><font color="#800000"><?php if($ps!="")
        {echo "$tyui";} ?></font></p>
         <input type="hidden" name="blank" 
        value="<?php if($edit!=""){echo "$edit";} ?>">
    </form>
    <form  method="post">
    【削除フォーム】<br>
        投稿番号:<input type="number" name="snum" 
        placeholder="削除対象番号" ><br>
        パスワード:<input type="password" 
        readonly onfocus="this.removeAttribute('readonly');" 
        name="spass" placeholder="パスワード"><br>
        <input type="submit" name="ssubmit">
        <p><font color="#800000"><?php if($del!="")
        {echo "$tyui";} ?></font></p>
    </form>
    <form  method="post">
    【編集フォーム】<br>
        投稿番号:<input type="number" name="hnum"
        placeholder="編集対象番号"><br>
        パスワード:<input type="password" 
        name="hpass" placeholder="パスワード"
        readonly onfocus="this.removeAttribute('readonly');"><br>
        <input type="submit" name="hsubmit">
        <p><font color="#800000"><?php if($hen!="")
        {echo "$tyui";} ?></font></p>
    </form>
    ---------------------------------------<br>
    【投稿一覧】<br>
    
    <?php	//入力・編集・削除した結果のデータ表示
    $sql = 'SELECT * FROM toko';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].'.';
		echo $row['name'].'　';
		echo  "「".$row['comment']."」".'　';
		echo $row['date'].'<br>';
	echo "<hr>";
	}
    
    ?>
</body>
</html>
