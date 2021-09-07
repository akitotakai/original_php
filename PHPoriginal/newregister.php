
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

  //登録処理
 if($_POST) {
   $message = $user->validate($_POST);
   if(empty($message['name']) && empty($message['mail']) && empty($message['password'])){
   $user->add($_POST);
   header('Location:regist_comp.php');
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
    <link rel="stylesheet" type="text/css" href="php.original/css/newregister.css">
    <link rel="stylesheet" type="text/css" href="php.original/css/base.css">
    <script type="text/javascript" src="jquery.js"></script>
    <script src="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.min.js"></script>

    <script type="text/javascript">
        jQuery(function($){
            $(".skuquantity").after('<input type="button" value="+" id="add1" class="plus" />').before('<input type="button" value="-" id="minus1" class="minus" />');
            $(".plus").click(function(){
                var currentVal = parseInt(jQuery(this).prev(".skuquantity").val());
                if (!currentVal || currentVal=="" || currentVal == "NaN") currentVal = 0;
                $(this).prev(".skuquantity").val(currentVal + 1 );
        });
            $(".minus").click(function(){
                var currentVal = parseInt(jQuery(this).next(".skuquantity").val());
                if (currentVal == "NaN") currentVal = 0;
                if (currentVal > 0) {
                    $(this).next(".skuquantity").val(currentVal - 1 );
                }
            });
    });
    </script>
    <script>
    $(function(){
      $("#t-btn").on("click",function(){
      //氏名のバリデーション
      if($("#name").val() === "" || $("#name").val().length > 10){
        alert("氏名が未入力、\nまたは10文字以内で入力してください。");
        return false;
      }
      //フリガナのバリデーション
      /* if ($("#kana").val() === "" || $("#kana").val().length > 10) {
        alert("フリガナが未入力、\nまたは10文字以内で入力してください。");
        return false;
      } */
      //メールアドレスのバリデーション
      if($("#mail").val() === "" || !$("#mail").val().match(/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/)){
        alert("メールアドレスが未入力、\nまたはメールアドレスが不正です。");
        return false;
      }
      //お問い合わせ内容のバリデーション
      if($("#password").val() === "" || !$("#password").val().match(/^(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,100}$/i)) {
        alert("パスワードを入力してください。");
        return false;
      }
    
});
    </script>
  </head>
  <body>
  <div id="wrapper">

    <img id="logo" src="php.original/image/loginlogo.png" alt="禁煙サイトロゴ">
    <div id="white">
    <h3>新規登録</h3>
    <?php if(isset($message["name"])) echo "<p class='red'>".$message["name"]."</p>" ?>
 
    <?php if(isset($message["mail"])) echo "<p class='red'>".$message["mail"]."</p>" ?>
    <?php if(isset($message["password"])) echo "<p class='red'>".$message["password"]."</p>" ?>

    <form id="new-r" action="" method="post">
      <dl>
       <dt class="name">ユーザー名:
       <input id="name" type="text" name="name" value=""></dt>
      


       <dt class="number">1日に吸っていたタバコの本数:　　　
       <input name="number" type="text" id="number" class="skuquantity" value="1" ></dt>

       <dt class="mail">メールアドレス:
       <input id="mail" type="text" name="mail" value=""></dt>

       <dt class="pass">パスワード(8文字以上):
       <input id="password" type="text" name="password" value=""></dt>

     </dl>
     <input id="t-btn" type ="submit" name ="t-btn" value="登録">
    </form>
   </div>
  <?php
  require ("footer.html");
  ?>

</div>

  </body>
</html>
