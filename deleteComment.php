<?php
include 'connect.php';

$uid = $_POST['uid'];
$cid = $_POST['cid'];

if (!isset($uid) || !isset($cid))
  return;

try {
  $pdo = new PDO("mysql:host=".db.";dbname=".dbname, dbusername, dbpassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

  $query = "SELECT image FROM comments WHERE cid = ?";
  $stmt = $pdo->prepare($query);
  $stmt->execute(array($cid));
  if ($stmt->rowCount() > 0) {
    $image = $stmt->fetch(PDO::FETCH_ASSOC)['image'];
    if ($image != null) {
      unlink($image);
    }
  }

  $query = "DELETE FROM comments WHERE cid = ?";
  $stmt = $pdo->prepare($query);
  $stmt->execute(array($cid));

  $json['success'] = true;
} catch (PDOException $e) {
  $json['success'] = false;
  $json['message'] = "Failed to connect to the server. Try again soon.";
}
echo json_encode($json);
?>