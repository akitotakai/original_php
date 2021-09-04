
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

//サニタイジング
function h($post){
  return htmlspecialchars($post,ENT_QUOTES,'UTF-8');
}
  //データベース接続
try{
  $user = new User($host, $dbname, $user, $pass);
  $user->connectDB();
//ログインしていた場合
  if(isset($_SESSION['User'])) {
   
    $favo = $user->favorite($_SESSION['User']['id']);
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
    <link rel="stylesheet" type="text/css" href="php.original/css/favo.css">
    <link rel="stylesheet" type="text/css" href="php.original/css/base.css">
    <script type="text/javascript" src="jquery.js"></script>
    <script>

      $(function(){

      $(".favorite_btn").click(function() {
        var user_id = <?php echo $_SESSION["User"]["id"]; ?>;
        var post_id = $(this).attr('id');
        // alert(post_id);

        if($(this).hasClass("on")){
          $(this).removeClass("on");
          $(this).text("いいねを外す");
          $(this).addClass("off");
          $.ajax({
            url:'php.original/model/Ajax.php',
            type:'POST',
            // dataType:'json',
            data:{
              "user_id" : user_id,
              "post_id" : post_id
            }
          }).done(function(data){
            alert("登録しました");
             location.reload();
          }).fail(function(data){
            alert("失敗");
          });

        }else{
          $(this).removeClass("off");
          $(this).addClass("on");
          $(this).text("いいね！");
          $.ajax({
            url:'php.original/model/Ajax2.php',
            type:'POST',
            data:{
              "user_id" : user_id,
              "post_id" : post_id
            },
            // dataType:'json'
          }).done(function(data){
            alert("解除しました");
             location.reload();
          }).fail(function(data){
            alert("失敗");
          });
        }
      });
          });
    </script>


  </head>
  <body>

    <script src="jquery.js"></script>
    <script src="h-navi.js"></script>

    <div id="wrapper">
    <div id="box">
    <?php
    require ("g-navi.html");
    ?>

    <!-- お気に入りページ -->
   <div id="favo_area">
     <img id="tro_logo" src="php.original/image/okini.png" alt="お気に入りロゴ">
     <?php if($favo): ?>
     <div class="favotrouble">
     <?php foreach ($favo as $row): ?>
       <table id="favo">
        <tr><td><?php echo h($row['create_at']) ?></td></tr>
        <tr><th>お名前 : <?php echo $row['name'] ?></th></tr>
        
        <tr><th>カテゴリー : 
          <?php
            if($row['category_id'] ==1) {
              echo "禁煙開始宣言" ;
            } elseif ($row['category_id'] ==2) {
              echo "禁煙初日〜１週間" ;
            } elseif ($row['category_id']==3) {
              echo "禁煙１週間〜1ヶ月" ;
            } elseif ($row['category_id']==4){
              echo "禁煙1ヶ月〜3ヶ月" ;
            } else{
              echo "禁煙3ヶ月〜1年";
            }
          ?>
        </th></tr>
        

        <tr><th>内容</th></tr>
        <tr><td class="left-c"><?php echo $row['trouble'] ?></td></tr>
        <tr><th>
        <button type="button" id ="<?php echo $row['id'] ?>" name="favorite" class="favorite_btn">
          いいねを外す
        </button>
        </tr></th>
     </table>
      <?php endforeach; ?>
    </div>
   </div>

   <?php else: ?>
    <div class="notfav">
      <p>お気に入りの投稿はありません</p>
    </div>
   <?php endif; ?>
   </div>
   <?php
    require ("footer.html");
    ?>
  </div>

    

  </body>
</html>
 
    