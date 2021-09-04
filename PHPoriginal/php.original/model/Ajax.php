<?php
$host = 'localhost';
$name = 'original_php';
$user = 'root';
$pass = 'root';

$user_id = $_POST["user_id"];
$post_id = $_POST["post_id"];

//データベース接続
try{
$db = new PDO("mysql:host={$host};dbname={$name};charset=utf8mb4", $user,$pass);


}catch (PDOException $e) {
  echo "エラー!: " . $e->getMessage() . "<br/gt;";
  exit;
}

$sql = 'INSERT INTO favorites (user_id,trouble_id) VALUES(?,?)';
$stmt = ($db->prepare($sql));
$result = $stmt->execute(array($user_id, $post_id));
