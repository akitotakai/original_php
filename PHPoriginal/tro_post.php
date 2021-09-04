
<?php
session_start();
// print_r($_POST);

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

//サニタイジング
function h($post){
  return htmlspecialchars($post,ENT_QUOTES,'UTF-8');
}

  //データベース接続
try{
  $user = new User($host, $dbname, $user, $pass);
  $user->connectDB();

  //バリデーションに引っかからなければ登録
 if($_POST) {
   $message = $user->validate3($_POST);
   if(empty($message['name']) && empty($message['trouble'])){
   $user->trouble($_POST);
   header('Location:tro_complete.php');

   exit();
     }
   }

  } catch(PDOException $e) {
    echo "エラー:".$e->getMessage()."<br>";
  }
?>


<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>Never Smoke Again</title>
    <link rel="stylesheet" type="text/css" href="php.original/css/tro_post.css">
    <link rel="stylesheet" type="text/css" href="php.original/css/base.css">
  </head>
  <body>
<div id="wrapper">

  <script src="jquery.js"></script>
  <script src="h-navi.js"></script>

    <?php
    require ("g-navi.html");
    ?>

    <h3>禁煙記録を投稿する</h3>
    <?php if(isset($message["name"])) echo "<p class='red'>".$message["name"]."</p>" ?>
    <?php if(isset($message["trouble"])) echo "<p class='red'>".$message["trouble"]."</p>" ?>

    <div id="toukou">
    <form action=""  method="post">
      <p>お名前を入力してください(ニックネーム可)</p>
      <input id="name-t" type="text" name="name"  value="">
      
    <p>項目を選択してください</p>
      <select name="category_id" value="">
        <option value="1">禁煙開始宣言</option>
        <option value="2">禁煙初日〜１週間</option>
        <option value="3">禁煙１週間〜1ヶ月</option>
        <option value="4">禁煙1ヶ月〜3ヶ月</option>
        <option value="5">禁煙3ヶ月〜1年</option>
      </select> 

      <p>内容</p>
      <textarea id="tro_comment" name="trouble"></textarea>
      <input id="t-btn" type="submit" onclick = "if(!confirm('この内容を投稿します。宜しいでしょうか？')) return false;" value="投稿する">
      <input type="hidden" name="user_id" value="<?php  echo $_SESSION['User']['id'] ?>">
    </form>
  </div>

    <?php
    require ("footer.html");
    ?>

</div>
  </body>
</html>
