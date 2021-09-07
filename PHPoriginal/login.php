
<?php
session_start();

require_once("php.original/config/config.php");
require_once("php.original/model/User.php");


//サニタイジング
function h($post){
  return htmlspecialchars($post,ENT_QUOTES,'UTF-8');
}
//データベース接続
 try{
   $user = new User($host, $dbname, $user, $pass);
   $user->connectDB();

//ログイン認証
  if($_POST) {
      $res = $user->login($_POST);

    if($_POST['mail'] === $res['mail']){

      if( password_verify($_POST['password'], $res['password'])){

        if(!empty($res)){
          $_SESSION['User'] = $res;
          header('Location:mypage.php');
          exit();
        }
      }else{$message = "メールアドレスまたはパスワードが間違っています";}
    }else{$message = "メールアドレスまたはパスワードが間違っています";}
    
  }
} catch(PDOException $e) {
    echo "エラー:".$e->getMessage()."<br>";
  }


?>

<!DOCTYPE html>
<html lang="jp">
  <head>
    <meta charset="utf-8">
    <title>Never Smoke Again</title>
    <link rel="stylesheet" type="text/css" href="php.original/css/login.css">
    <link rel="stylesheet" type="text/css" href="php.original/css/base.css">
    

  </head>
  <body>
    <div id="wrapper">
    <img id="logo" src="php.original/image/loginlogo.png" alt="禁煙サイトロゴ">
    <h3>ログイン</h3>
    <div id="white">
    <?php if(isset($message)) echo "<p class='red'>".$message."</p>"  ?>
    <form id="new-r" action="" method="post">
      <dl>
       <dt class="name">メールアドレス:
       <input id="mail" type="text" name="mail" value=""></dt>
       <dt class="pass">パスワード:
       <input id="password" type="password" name="password" value=""></dt>
     </dl>
     <input id="log-btn" type ="submit" name ="log-btn" value="ログイン">
    </form>
    <a href="newregister.php">新規登録はこちら</a>
    <a href="forgetpassword.php">パスワードを忘れてしまった方はこちら</a>
  </div>
    <?php
    require ("footer.html");
    ?>
  </div>


  </body>
</html>
