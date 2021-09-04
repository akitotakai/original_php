
<?php
session_start();
// print_r($_SESSION['User']);
require_once("php.original/config/config.php");
require_once("php.original/model/User.php");


//サニタイジング
function h($post){
  return htmlspecialchars($post,ENT_QUOTES,'UTF-8');
}
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

//データベース接続
try{
  $user = new User($host, $dbname, $user, $pass);
  $user->connectDB();


//コメント登録処理
  if($_POST) {
  //入力チェック
    $message = $user->validate4($_POST);
  //入力チェックに引っかからなければ完了画面に遷移
    if(empty($message['name']) && empty($message['comment'])){
    $user->comment($_POST);
    header('Location:cm_complete.php');
    exit();
      }
    }
  //コメントフォーム表示
  if(isset($_GET['edit'])) {
    //参照処理(選択された1件のみ抜き出して表示)
    $result['trouble'] = $user->findById($_GET['edit']);

  } elseif(isset($_GET['com'])) {
    //参照処理(選択された1件のみ抜き出して表示)
    $result['trouble'] = $user->findById($_GET['com']);
    //コメントキーがあったらコメントを表示
    $result2 = $user->commentAll($_GET['com']);
    


  } else {
    if(isset($_POST['submit'])){
 
      if($_POST['selecate']==='0'){
        $result = $user->troubleAll();
      }
      elseif($_POST['selecate']==='1'){
        $result = $user->trouble1();
      }
      elseif($_POST['selecate']==='2'){
        $result = $user->trouble2();
      }
      elseif($_POST['selecate']==='3'){
        $result = $user->trouble3();
      }
      elseif($_POST['selecate']==='4'){
        $result = $user->trouble4();
      }
      elseif($_POST['selecate']==='5'){
        $result = $user->trouble5();
      }
    }
    //全て参照
    else{
    $result = $user->troubleAll();
    //コメント数をカウント
    //$result3 = $user->comment2();
    //$count = $user->countcom();
    }
  }

  //削除キーがあったら削除処理
  if(isset($_GET['del'])) {
    $user->troubledel($_GET['del']);
  //全て参照
    $result = $user->troubleAll();
   }
   
  /* if(isset($_SESSION['User']['id'])){
    foreach ($result as $row) {
      $post_id = $row['id'];

      //お気に入りの重複チェック
      if(isset($post_id)){
        $check = $user->favoriteAdd($_SESSION['User']['id'],$post_id);
        $favorite = $check;
        }
    }
  } */

  } catch(PDOException $e) {
    echo "エラー:".$e->getMessage()."<br>";
  }

?>


<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>Never Smoke Again</title>
    <link rel="stylesheet" type="text/css" href="php.original/css/trouble.css">
    <link rel="stylesheet" type="text/css" href="php.original/css/base.css">
  </head>
  <body>
   <div id="wrapper">

     <script src="jquery.js"></script>
     <script src="h-navi.js"></script>

    <?php
    require ("g-navi.html");
    ?>

  <div id="image">
    <img id="tro_logo" src="php.original/image/kinkomyu.png" alt="禁煙コミュニティ">
    <a href="tro_post.php"><button id="post_logo" >投稿する</button></a>
    <form id="category_se" action="trouble.php" method = "POST">
    <select name="selecate">
        <option value='0'>カテゴリー選択</option>
        <option value='0'>すべて表示</option>
        <option value='1'>禁煙開始宣言</option>
        <option value='2'>禁煙初日〜１週間</option>
        <option value='3'>禁煙１週間〜1ヶ月</option>
        <option value='4'>禁煙1ヶ月〜3ヶ月</option>
        <option value='5'>禁煙3ヶ月〜1年</option>
      </select>  
      <input type="submit"name="submit"value="検索"/>
    </form>
  </div>
  <?php if(!$result): ?>
    <div class="notpost">
      <p>投稿がありません</p>
    </div>
  <?php else: ?>
  <div class="big-box">
    <div class="tro-box">
  <?php foreach ($result as $row): ?>
 
    <table id="trouble">
     <tr><td><?php echo h($row['create_at']) ?></td></tr>
     <tr><th>お名前 : <?php echo h($row['name']) ?></th></tr>
    　<tr><th>カテゴリー : <?php echo h($row['category']) ?></th></tr>
     <tr><th >内容</th></tr>
     <tr><td class="last"><?php echo h($row['trouble']) ?></td></tr>
     <?php if(isset($_GET['com'])): ?>
            <tr><td>
              <a id="right" href="trouble.php">&ensp;&emsp;コメント閉じる</a>
            </tr></td>
     <?php else: ?>
     <tr>
       <td>
         <a href="?edit=<?=$row['id']?> ">コメントする</a>
         
         <a id="right" href="?com=<?=$row['id']?> ">&ensp;|&emsp;コメント表示</a>
       </td>
     </tr>
     <?php endif; ?>
     <?php if($row['user_id']===$_SESSION['User']['id']): ?>
          <p></p>
     <?php else: ?>
      <!-- お気に入り重複チェック -->   
         <?php $post_id = $row['id'];?>
         <?php $check = $user->favoriteAdd($_SESSION['User']['id'],$post_id);?>
      <tr><td>
         <?php if(!$check): ?>
          <input type="hidden" name="post_id" class="post_id">
          <button type="button" id ="<?php echo $row['id'] ?>" name="favorite" class="favorite_btn off">
           いいね！
         <?php else: ?>
          <input type="hidden" name="post_id" class="post_id">
          <button type="button" id ="<?php echo $row['id'] ?>" name="favorite" class="favorite_btn on">
           いいねを外す
         <?php endif; ?>
       </button>
      </tr></td>
  
      <?php endif; ?>
      <?php endforeach; ?>

     <!-- 管理者だった場合のみ表示 -->
      <?php if($_SESSION['User']['role'] == 0): ?>
     <tr>
       <td class="del">
         <a href = "?del=<?=$row['id']?>" onclick = "if(!confirm('ID:<?=$row['id']?>を削除します。宜しいでしょうか？')) return false;">削除</a>
       </td>
     </tr>
     <?php endif; ?>
     </tr>
     </form>
 </table>

 
 <!-- editキーがある場合のみ表示 -->
 <?php  if(isset($_GET['edit'])): ?>
   <div id="com-color">
   <h3><?php echo $row['name'] ?>さんへのコメント入力</h3>
   <form id="advice" action="" method="post">
     <?php if(isset($message["name"])) echo "<p class='red'>".$message["name"]."</p>" ?>
     <?php if(isset($message["comment"])) echo "<p class='red'>".$message["comment"]."</p>" ?>

     <dl>
       <p>お名前(ニックネーム可):</p>
       <dt><input id="name" type="text" name="name" value=""></dt>
       <p>コメント:</p>
       <dt><textarea id="comment" type="text" name="comment"></textarea></dt>
     </dl>
     <input id="c-btn" type ="submit" name ="c-btn" onclick = "if(!confirm('コメントを送信します。宜しいでしょうか？')) return false;" value="送信">
     <input type="hidden" name="user_id" value="<?php  echo $_SESSION['User']['id'] ?>">
     <input type="hidden" name="trouble_id" value="<?php  echo $result['trouble']['id'] ?>">
     <a id="can" href="trouble.php" >キャンセル</a>
   </form>
 </div>
 <?php endif; ?>
<?php endif; ?>
  <!-- comキーがある場合のみ表示 -->
  <?php  if(isset($_GET['com'])): ?>
    <?php foreach ($result2 as $row2): ?>
      <table id="comment-t">
        <tr><td><?php echo $row2['create_at'] ?>&emsp;に返信されたコメント</td></tr>
        <tr><th>お名前 : <?php echo h($row['name']) ?></th></tr>
        <tr><th>コメント</th></tr>
        <tr><td><?php echo $row2['comment'] ?></td></tr>
  
      <?php  endforeach; ?>
      <?php if($result2 == null): ?>
        <div id="non">
        <p>この投稿へのコメントはありません</p>
        <a href="trouble.php"><p>戻る</p></a>
      </div>
      <?php else: ?>
        <tr><th>
          <a id="right" href="trouble.php">&ensp;&emsp;コメント閉じる</a>
        </tr></th>
      <?php endif; ?>
    <?php endif; ?>
    </table>
    <button class=top>ページトップへ</button>
 </div>
</div>

<script>

  $(function(){
    var toppage = $("#wrapper").offset().top;
    $('.top').on('click',function(){
        $("html,body").animate({
          scrollTop:toppage
        });
        return false;
      });
  $(".favorite_btn").click(function() {
    var user_id = <?php echo $_SESSION["User"]["id"]; ?>;
    var post_id = $(this).attr('id');
    // alert(post_id);

    if($(this).hasClass("on")){
      $(this).removeClass("on");
      $(this).text("いいね！");
      $(this).addClass("off");
      $.ajax({
        url:'php.original/model/Ajax2.php',
        type:'POST',
        data:{
          "user_id" : user_id,
          "post_id" : post_id
        },
      }).done(function(data){
        alert("解除しました");
      }).fail(function(data){
        alert("失敗");
      });

    }else{
      $(this).removeClass("off");
      $(this).addClass("on");
      $(this).text("いいねを外す");
      $.ajax({
        url:'php.original/model/Ajax.php',
        type:'POST',
        data:{
          "user_id" : user_id,
          "post_id" : post_id
        }
      }).done(function(data){
        alert("登録しました");
      }).fail(function(data){
        alert("失敗");
      });

    }

    
  });
  });
  </script>

    <?php
    require ("footer.html");
    ?>

  </div>
  </body>
</html>