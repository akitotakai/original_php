
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

  //削除処理
  if(isset($_GET['del'])) {
    $user->delete($_GET['del']);
  //参照処理
  $result = $user->findAll();
}  else {
  //参照処理
  $result = $user->findAll();
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
    <link rel="stylesheet" type="text/css" href="php.original/css/role-mypage.css">
    <link rel="stylesheet" type="text/css" href="php.original/css/base.css">

  </head>
  <body>

    <script src="jquery.js"></script>
    <script src="h-navi.js"></script>

    <div id="wrapper">
    <?php
    require ("g-navi.html");
    ?>
    <h3>ユーザー情報一覧</h3>
    <table id="users">
    <tr>
      <th>ID</th>
      <th>氏名</th>
      <th>フリガナ</th>
      <th>メールアドレス</th>
      <th>権限</th>
      <th> </th>
    </tr>
    <?php foreach ($result as $row): ?>
      <tr>
        <td><?php echo h($row['id']) ?></td>
         <td><?php echo h($row['name']) ?></td>
         <td><?php echo h($row['kana']) ?></td>
         <td><?php echo h($row['mail']) ?></td>
         <td>
           <?php  if(h($row['role']) === '0'): ?>
             管理者
           <?php else: ?>
             一般ユーザー
           <?php endif; ?>
         </td>
         <td>
           <a href = "?del=<?=$row['id']?>" onclick = "if(!confirm('ID:<?=$row['id']?>を削除します。宜しいでしょうか？')) return false;">削除</a>
         </td>
       </tr>
     <?php  endforeach; ?>
     </table>
    <?php
    require ("footer.html");
    ?>
</div>
  </body>
</html>
