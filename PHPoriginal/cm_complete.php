<?php
session_start();

require_once("php.original/config/config.php");
require_once("php.original/model/User.php");


//新規登録、ログインページを経由していなかったら
if(!isset($_SESSION["User"])){
  header("Location:login.php");
  exit;
}
//ログアウト処理
if(isset($_GET['logout'])) {
  //セッション情報を破棄する
  $_SESSION['User'] = array();
}
?>


<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>Never Smoke Again</title>
    <link rel="stylesheet" type="text/css" href="php.original/css/complete.css">
    <link rel="stylesheet" type="text/css" href="php.original/css/base.css">


  </head>
  <body>

    <script src="jquery.js"></script>
    <script src="h-navi.js"></script>

    <div id="wrapper">
    <div id="box">
    <?php
    require ("g-navi.html");
    ?>

   <h3>コメントの送信が完了しました！</h3>
   <a href="trouble.php"><button id="mypage" name="mypage">禁煙掲示板に戻る</button></a>
   </div>
    <?php
    require ("footer.html");
    ?>
</div>
  </body>
</html>