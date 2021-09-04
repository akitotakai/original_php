<?php
session_start();

require_once("php.original/config/config.php");
require_once("php.original/model/User.php");


//ログアウト処理
if(isset($_GET['logout'])) {
  //セッション情報を破棄する
  $_SESSION['User'] = array();
}
//新規登録、ログインページを経由していなかったら
if(empty($_SESSION["User"])){
  header("Location:login.php");
  exit;
}
?>


<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>Never Smoke Again</title>
    <link rel="stylesheet" type="text/css" href="php.original/css/base.css">
    <link rel="stylesheet" type="text/css" href="php.original/css/i_post_conplete.css">


  </head>
  <body>

    <script src="jquery.js"></script>
    <script src="h-navi.js"></script>

    <div id="wrapper">
    <?php
    require ("g-navi.html");
    ?>

   <h3>編集が完了しました！</h3>
   <a href="mypage.php"><button id="mypage" name="mypage">マイページへ</button></a>
    <?php
    require ("footer.html");
    ?>
</div>
  </body>
</html>
