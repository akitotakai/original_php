<?php
 require_once("DB.php");

class User extends DB {

  //------------------------
  //新規登録画面
  //-------------------------

  //登録　INSERT(新規ユーザー登録)
  public function add($arr) {
    $sql = "INSERT INTO users (name,kana,number,mail,password)
    VALUES (:name,:kana,:number,:mail,:password)";
    $stmt = $this->connect->prepare($sql);
    //パスワードをハッシュ化
    $hash_pass = password_hash($arr['password'], PASSWORD_DEFAULT);
    $params = array (
      ':name'=>$arr['name'],
      ':kana'=>$arr['kana'],
      ':number'=>$arr['number'],
      ':mail'=>$arr['mail'],
      ':password'=>$hash_pass
    );
    $stmt->execute($params);
  }
  //入力チェック　validate(新規登録)
  public function validate($arr) {
    $message = array();
    //ユーザー名
    if(empty($arr['name'])) {
      $message['name'] = '氏名を入力してください。';
    } else if (mb_strlen($arr['name']) > 10) {
      $message['name'] = "氏名は10文字以内で入力してください<br>";
    }
    //フリガナ
    if(empty($arr['kana'])) {
      $message['kana'] = 'フリガナを入力してください。';
    } else if (mb_strlen($arr['kana']) > 10) {
      $message['kana'] = "フリガナは10文字以内で入力してください<br>";
    }
    //メールアドレス
    if(empty($arr['mail'])) {
      $message['mail'] = 'メールアドレスを入力してください。';
    } else if ( !filter_var($arr['mail'],FILTER_VALIDATE_EMAIL) ){
      $message['mail'] = "メールアドレスが不正です。<br>";
    }
    //パスワード
    if(empty($arr['password'])) {
      $message['password'] = 'パスワードを入力してください。';
    } else if (!preg_match('/^(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,100}$/i',$arr['password'])) {
      $message['password'] = "パスワードが不正です。<br>";
    }
    return $message;
  }

  //-------------------------
  //ログイン認証
  //-------------------------

  public function login($arr) {
    $sql = 'SELECT * FROM users WHERE mail = :mail ';
    $stmt = $this->connect->prepare($sql);
    $params = array (
      ':mail'=>$arr['mail']
    );
    $stmt->execute($params);
    $res = $stmt->fetch();
    // $result = $stmt->rowCount();
    return $res;
  }
  //-------------------------
    //パスワードリセット
  //-------------------------

  public function reset($arr) {
    $sql = 'SELECT * FROM users WHERE mail = :mail ';
    $stmt = $this->connect->prepare($sql);
    $params = array (
      ':mail'=>$arr['mail']
    );
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result;
  }
  public function newpass($arr,$pass) {
    $sql = "UPDATE users SET password = :password WHERE mail = :mail";
    $stmt = $this->connect->prepare($sql);
    //パスワードをハッシュ化
    $hash_pass = password_hash($pass, PASSWORD_DEFAULT);
    $params = array (
      ':mail'=>$arr['mail'],
      ':password'=>$hash_pass
      // ':password'=>$pass
    );
    $stmt->execute($params);
  }

  //-------------------------
  //ユーザー管理
  //-------------------------

  //参照　SELECT(ユーザー情報)
  public function findAll() {
    $sql = "SELECT * FROM users WHERE delflag=1";
    $stmt = $this->connect->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  }
  // 削除　DELETE(ユーザー情報)
  public function delete($id = null) {
    if(isset($id)) {
     $sql = "UPDATE users SET delflag=0 WHERE id = :id";
     $stmt = $this->connect->prepare($sql);
     $params = array(':id'=>$id);
     $stmt->execute($params);
     }
  }

  //-------------------------
  //お気に入り機能
  //-------------------------

  //参照
  public function favorite($arr) {
      $sql = 'SELECT i.id, i.name, i.category_id, i.trouble,i.create_at FROM troubles i JOIN favorites f ON i.id = f.trouble_id
              JOIN users u ON u.id = f.user_id WHERE f.user_id=:id';
      $stmt = $this->connect->prepare($sql);
      $params = array(':id'=>$arr);
      $stmt->execute($params);
      $favo = $stmt->fetchAll();
      return $favo;
  }
  //お気に入りの重複チェック

  public function favoriteAdd($user,$trouble){
    $sql = 'SELECT * FROM favorites WHERE user_id=? AND trouble_id=?';
    $stmt = $this->connect->prepare($sql);
    $param = array($user,$trouble);
    $stmt->execute($param);
    $res = $stmt->fetchAll();
    return $res;
  }

  //-------------------------
  //禁煙掲示板
  //-------------------------

  //登録　INSERT
  public function trouble($arr) {
    $sql = "INSERT INTO troubles (user_id,name,category_id,trouble,create_at)
    VALUES (:user_id,:name,:category_id,:trouble,:create_at)";
    $stmt = $this->connect->prepare($sql);
    date_default_timezone_set('Asia/Tokyo');
    $now = new DateTime();
    $date_now = $now->format('Y-m-d H:i:s');
    $params = array (
      ':user_id'=>$arr['user_id'],
      ':name'=>$arr['name'],
      ':category_id'=>$arr['category_id'],
      ':trouble'=>$arr['trouble'],
      ':create_at'=>$date_now
    );
    $stmt->execute($params);
  }

  //編集(マイページ)
public function update($arr){
  $sql = "UPDATE troubles SET category_id = :category_id,name = :name,trouble = :trouble
          WHERE id = :id";
  $stmt = $this->connect->prepare($sql);
  $params = array(
    ':id'=>$arr['id'],
    ':category_id'=>$arr['category_id'],
    ':name'=>$arr['name'],
    ':trouble'=>$arr['trouble']
  );
  $stmt->execute($params);
}

  // 削除　DELETE
  public function troubledel($id = null) {
    if(isset($id)) {
      $sql = 'DELETE comments,troubles FROM troubles LEFT JOIN comments ON comments.trouble_id = troubles.id WHERE troubles.id = :id';
      $stmt = $this->connect->prepare($sql);
      $params = array(':id'=>$id);
      $stmt->execute($params);
    }
  }
  //参照
   public function troubleAll() {
      $sql = 'SELECT t.id,t.user_id,t.category_id,t.name,t.trouble,t.create_at,c.category FROM troubles t join  categories c on t.category_id=c.id order by create_at desc';
      $stmt = $this->connect->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll();
      return $result;
   }

   public function trouble1() {
    $sql = 'SELECT t.id,t.user_id,t.category_id,t.name,t.trouble,t.create_at,c.category FROM troubles t join  categories c on t.category_id=c.id where t.category_id = 1 order by create_at desc';
    $stmt = $this->connect->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
 }

  public function trouble2() {
    $sql = 'SELECT t.id,t.user_id,t.category_id,t.name,t.trouble,t.create_at,c.category FROM troubles t join  categories c on t.category_id=c.id where t.category_id = 2 order by create_at desc';
    $stmt = $this->connect->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  }

  public function trouble3() {
    $sql = 'SELECT t.id,t.user_id,t.category_id,t.name,t.trouble,t.create_at,c.category FROM troubles t join  categories c on t.category_id=c.id where t.category_id = 3 order by create_at desc';
    $stmt = $this->connect->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  }

  public function trouble4() {
    $sql = 'SELECT t.id,t.user_id,t.category_id,t.name,t.trouble,t.create_at,c.category FROM troubles t join  categories c on t.category_id=c.id where t.category_id = 4 order by create_at desc';
    $stmt = $this->connect->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  }

  public function trouble5() {
    $sql = 'SELECT t.id,t.user_id,t.category_id,t.name,t.trouble,t.create_at,c.category FROM troubles t join  categories c on t.category_id=c.id  where t.category_id = 5 order by create_at desc';
    $stmt = $this->connect->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
  }

  public function troublemy($id) {
    $sql = 'SELECT t.id,t.user_id,t.category_id,t.name,t.trouble,t.create_at,c.category FROM troubles t join  categories c on t.category_id=c.id join users u on u.id=t.user_id WHERE u.id=:id order by create_at desc';
    $stmt = $this->connect->prepare($sql);
    $params = array(':id' => $id);
    $stmt->execute($params);
    $result = $stmt->fetchAll();
    return $result;
 }
  //入力チェック
  public function validate3($arr) {
    $message = array();

      if(empty($arr['name'])) {
       $message['name'] = 'お名前を入力してください。';
      }

      if(empty($arr['trouble'])) {
        $message['trouble'] = '内容を入力してください。';
      }
        return $message;
  }

  //-------------------------
  //コメント機能
  //-------------------------

  //参照(条件付き)　SELECT
  public function findById($id) {
    $sql = 'SELECT t.id,t.user_id,t.category_id,t.name,t.trouble,t.create_at,c.category FROM troubles t join  categories c on t.category_id=c.id  WHERE t.id = :id';
    $stmt = $this->connect->prepare($sql);
    $params = array(':id' => $id);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result;
  }
  //登録　INSERT
  public function comment($arr) {
    $sql = "INSERT INTO comments (user_id,trouble_id,name,comment,create_at)
    VALUES (:user_id,:trouble_id,:name,:comment,:create_at)";
    $stmt = $this->connect->prepare($sql);
    date_default_timezone_set('Asia/Tokyo');
    $now = new DateTime();
    $date_now = $now->format('Y-m-d H:i:s');
    $params = array (
      ':user_id'=>$arr['user_id'],
      ':trouble_id'=>$arr['trouble_id'],
      ':name'=>$arr['name'],
      ':comment'=>$arr['comment'],
      ':create_at'=>$date_now
    );
    $stmt->execute($params);
  }
//参照
public function commentAll($arr) {
   $sql = 'SELECT c.name, c.comment, c.create_at FROM comments c JOIN troubles t ON c.trouble_id = t.id WHERE t.id=:id';
   $stmt = $this->connect->prepare($sql);
   $params = array(':id'=>$arr);
   $stmt->execute($params);
   $result2 = $stmt->fetchAll();
   return $result2;
}

public function comment2() {
  $sql = 'SELECT trouble_id FROM comments ';
  $stmt = $this->connect->prepare($sql);
  $stmt->execute();
  $result3 = $stmt->fetchAll();
  return $result3;
}


//コメント数をカウント
public function countcom() {
  $sql = 'SELECT COUNT(comment) AS Comment FROM comments c JOIN troubles t ON c.trouble_id = t.id GROUP BY t.id';
  $stmt = $this->connect->prepare($sql);
  $stmt->execute();
  $count = $stmt->fetchAll();
  return $count;
}
//入力チェック
  public function validate4($arr) {
    $message = array();
    //名前
      if(empty($arr['name'])) {
        $message['name'] = 'お名前を入力してください。';
      }
    //コメント
      if(empty($arr['comment'])) {
        $message['comment'] = 'コメントを入力してください。';
      }
      return $message;
  }


//経過日数（マイページ）
public function datetime($id){
  $sql = 'SELECT create_at FROM troubles  where category_id = 1 and user_id = :id';
  $stmt = $this->connect->prepare($sql);
  $params = array(':id' => $id);
  $stmt->execute($params);
  $result = $stmt->fetch();
  return $result;
}

}

?>