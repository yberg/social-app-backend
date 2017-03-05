<?php
include 'connect.php';

$pid = $_POST['pid'];
$uid = $_POST['uid'];
$text = $_POST['text'];
$image = $_POST['image'];

if (!isset($pid) || !isset($uid) || !isset($text))
  return;

try {
  $pdo = new PDO("mysql:host=".db.";dbname=".dbname, dbusername, dbpassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

  $query = "INSERT INTO comments (pid, uid, text, image) VALUES (?, ?, ?, ?)";
  $stmt = $pdo->prepare($query);
  $stmt->execute(array($pid, $uid, $text, $image));

  $json['success'] = true;
} catch (PDOException $e) {
  $json['success'] = false;
  $json['message'] = "Failed to connect to the server. Try again soon.";
}
echo json_encode($json);
?>