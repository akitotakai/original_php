
<?php
session_start();
// print_r($_SESSION['User']);
require_once("php.original/config/config.php");
require_once("php.original/model/User.php");


function h($post){
  return htmlspecialchars($post,ENT_QUOTES,'UTF-8');
}


//ログアウト処理
if(isset($_GET['logout'])) {
  //セッション情報を破棄する
  $_SESSION['User'] = array();
}

//ログイン画面を経由しているか確認
if(empty($_SESSION['User'])) {
  header('Location:login.php');
  exit();
}
if($_SESSION['User']['role'] === '0') {
  header('Location:role-mypage.php');
  exit();

}


//データベース接続
try{
  $user = new User($host, $dbname, $user, $pass);
  $user->connectDB();

  $date = $user->datetime($_SESSION['User']['id']);
  

  //削除処理
  if(isset($_GET['del'])) {
    $user->delete($_GET['del']);
  //参照処理
  $result = $user->findAll();
}  else {
  //参照処理
  $result = $user->findAll();
   }


  if(isset($_GET['del'])){
    $user->troubledel($_GET['del']);
    $post = $user->troublemy($_SESSION['User']['id']);

  }elseif(isset($_GET['update'])){
    //参照処理(選択された1件のみ抜き出して表示)
    $post['edit'] = $user->findById($_GET['update']);
    
  }elseif(isset($_GET['com'])) {
    //参照処理(選択された1件のみ抜き出して表示)
    $post['trouble'] = $user->findById($_GET['com']);
    //コメントキーがあったらコメントを表示
    $result2 = $user->commentAll($_GET['com']);

  } else {
    //全て参照
    $post = $user->troublemy($_SESSION['User']['id']);
    //コメント数をカウント
    // $result3 = $user->countcom();
    // print_r($result3);
  }

  if($_POST) {
    $message = $user->validate3($_POST);
    if(empty($message['name']) && empty($message['trouble'])){
    $user->update($_POST);
    header('Location:edit_my.php');
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
    <link rel="stylesheet" type="text/css" href="php.original/css/mypage.css">
    <link rel="stylesheet" type="text/css" href="php.original/css/base.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.css">

    
   
   

  </head>
  <body>

  <div id="wrapper">
    <?php
    require ("g-navi.html");
    ?>
   <div id="all">
   <div class="mypage">
      <div id="days">
        <h2><?=$_SESSION['User']['name']?>さんのマイページ</h2>
        <?php if(isset($date['create_at'])): ?>
        <?php date_default_timezone_set('Asia/Tokyo');
              $day = new DateTime($date['create_at']);
              $today = new DateTime('now');
              $diff = $day->diff($today);
              $result_day = $diff->format('%a');?>

        <?php $number = $_SESSION['User']['number']; ?>

        <?php function MultTime($time,$kake,$result_day){
              $tArry=explode(":",$time);
              $hour=$tArry[0]*60;//時間→分
              $secnd=round($tArry[2]/60,2);//秒→分
              $mins=$hour+$tArry[1]+$secnd;
              $ans= $mins*$kake*$result_day*60;//割合いを掛け算して秒に変換

              return date("H時間i分s秒",mktime(0,0,$ans));
              }

              $time="00:05:30";?>
        <table id="mypage">
          <tr><td>禁煙日数</br><span1><?php echo $result_day."日"; ?></span1><span2> </br>※禁煙開始宣言を投稿した日より算出しています。</span2></td></tr>
          <tr><td>節約金額</br><span1><?php echo $number*27.5*$result_day."円"; ?></span1><span2> </br>※1箱20本入り550円で算出しています。</span2></td></tr>
          <tr><td>延びた寿命</br><span1><?php echo MultTime($time,$number,$result_day); ?></span1><span2> </br>※1本あたり5分30秒寿命が縮むという定説を元に算出しています。</span2></td></tr>
        </table>
        

        
      <?php else: ?>
        <p>まずは禁煙掲示板で禁煙開始宣言をしましょう！</br>禁煙開始宣言を投稿すると禁煙日数の計測が開始します。</p>
      <?php endif; ?>
      </div>

    <div class="big-box">
    <div class="tro-box">

      <!-- updateキーがあった場合 -->
      <?php if(isset($_GET['update'])): ?>
        <?php if(isset($message["name"])) echo "<p class='red'>".$message["name"]."</p>" ?>
        <?php if(isset($message["trouble"])) echo "<p class='red'>".$message["trouble"]."</p>" ?>
        <div id="toukou">
        <h3>禁煙記録を編集する</h3>
        <form action=""  method="post">
          <p>お名前を入力してください(ニックネーム可)</p>
          <input id="name-t" type="text" name="name"  value="<?php if(isset($post['edit'])) echo $post['edit']['name']; ?>">
          
        <p>項目を選択してください</p>
          <select name="category_id" value="<?php if(isset($post['edit'])) echo $post['edit']['category_id']; ?>">
            <option value="1">禁煙開始宣言</option>
            <option value="2">禁煙初日〜１週間</option>
            <option value="3">禁煙１週間〜1ヶ月</option>
            <option value="4">禁煙1ヶ月〜3ヶ月</option>
            <option value="5">禁煙3ヶ月〜1年</option>
          </select> 

          <p>内容</p>
          <textarea id="tro_comment" name="trouble"><?php if(isset($post['edit'])) echo $post['edit']['trouble']; ?></textarea>
          <input id="t-btn" type="submit" onclick = "if(!confirm('この内容を投稿します。宜しいでしょうか？')) return false;" value="投稿する">
          <input type="hidden" name="id" value="<?php  echo $post['edit']['id'] ?>">
          <a id="can" href="mypage.php" >キャンセル</a>
        </form>
        </div>

        <!-- 通常表示 -->
        <?php else: ?>
        
        <?php if($post): ?>
          <div class="mytroubles">
          <h3>投稿一覧</h3>
        </div >
        <?php foreach ($post as $row): ?>
        <table id="trouble">
          <tr><td><?php echo h($row['create_at']) ?></td></tr>
          <tr><th>お名前 : <?php echo h($row['name']) ?></th></tr>
          <tr><th>カテゴリー : <?php echo h($row['category']) ?></th></tr> 
          <tr><th>内容 </th></tr>
          <tr><td><?php echo h($row['trouble']) ?></tr></td>
        
          <?php if(isset($_GET['com'])): ?>
            <tr><th>
              <a id="right" href="mypage.php">&ensp;&emsp;コメント閉じる</a>
            </tr></th>
          <?php else: ?>
            <tr><th>
              <a id="right" href="?com=<?=$row['id']?> ">&ensp;&emsp;コメント表示</a>
              <a id="right" href = "?update=<?=$row['id']?>" >&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;編集&nbsp;|</a>
              <a href = "?del=<?=$row['id']?>" onclick = "if(!confirm('この投稿をを削除します。宜しいでしょうか？')) return false;">削除</a>
            </tr></th>
          <?php endif; ?>
          <?php endforeach; ?>
        </table>
        <?php else: ?>
          <div class="mytroubles">
          <h3>投稿一覧</h3>
        </div >
          <div class="nottrouble">
            <p>投稿がありません</p>
          </div> 
        <?php endif; ?>
        <?php endif; ?>
          <!-- comキーがあった場合 -->
        <?php  if(isset($_GET['com'])): ?>
          <?php foreach ($result2 as $row2): ?>
            <table id="comment-t">
              <tr><td><?php echo $row2['create_at'] ?>&emsp;に返信されたコメント</td></tr>
              <tr><th>お名前 : <?php echo h($row['name']) ?></th></tr>
              <tr><th>コメント</th></tr>
              <tr><td><?php echo $row2['comment'] ?></td></tr>
            </table>
            <?php  endforeach; ?>
            <?php if($result2 == null): ?>
              <div id="non">
              <p>この投稿へのコメントはありません</p>
              <a href="mypage.php"><p>戻る</p></a>
            </div>
            <?php endif; ?>
          <?php endif; ?>
      </div>
      </div>
   </div>
  </div>
      

  <?php
      require ("footer.html");
      ?>
  



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="h-navi.js"></script>
<script src="https://cdn.jsdelivr.net/bxslider/4.2.12/jquery.bxslider.min.js"></script>


<script>

$(function(){

var windowWidth = window.innerWidth;
var toppage = $("#wrapper").offset().top;

  $('.top').on('click',function(){
    $("html,body").animate({
      scrollTop:toppage
    });
    return false;
  });


});
</script> 


  </body>
</html>