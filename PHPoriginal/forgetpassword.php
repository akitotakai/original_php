<?php
session_start();
$flag = 'input';
mb_language("Japanese");
mb_internal_encoding("UTF-8");

require_once("php.original/config/config.php");
require_once("php.original/model/User.php");

// $to = 'almiw.xkax831@outlook.jp';
// if(mb_send_mail($to, $sub, $msg, "FROM: {$from}")) {
//   echo "送信ok";
// }

//サニタイジング
function h($post){
  return htmlspecialchars($post,ENT_QUOTES,'UTF-8');
}
//データベース接続
 try{
   $user = new User($host, $dbname, $user, $pass);
   $user->connectDB();


   if($_POST) {
     $result = $user->reset($_POST);
  // メールアドレスが一致していたら
     if($_POST['mail'] == $result['mail']) {
       // print_r($result);
       $pass = bin2hex(random_bytes(4));
       $from = 'From: test@gmail.com';
       $sub = "test送信";
       $msg = "パスワードを変更しました。\r\n" .$pass . "\r\n";
  //パスワードをランダムに生成
       
  //メール送信
       mb_send_mail($_POST['mail'], $sub, $msg, $from);
  //パスワード更新処理
       $user->newpass($_POST,$pass);
       $flag = 'cmp';
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
    <link rel="stylesheet" type="text/css" href="php.original/css/login.css">
    <link rel="stylesheet" type="text/css" href="php.original/css/base.css">

  </head>
  <body>
    <div id="wrapper">
  
    <?php if($flag == "input") { ?>
    <p id="login-p">メールアドレスを入力してください</p>
    <div id="white">

    <form id="new-r" action="" method="post">
      <dl>
       <dt class="mail">メールアドレス:
       <input id="mail" type="text" name="mail" value=""></dt>
     </dl>
     <input id="log-btn" type ="submit" name ="cmp" value="再発行">
    </form>
  </div>

<?php } elseif($flag == "cmp") { ?>
  <p id="login-p">パスワードの再発行が完了しました。</p>
  <a href="login.php">ログインページへ</a>
<?php };  ?>

    <?php
    require ("footer.html");
    ?>
  </div>
  </body>
</html>
