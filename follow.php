<?php
include 'connect.php';

$uid = $_POST['uid'];
$follow = $_POST['follow'];

if (!isset($uid) || !isset($follow))
  return;

try {
  $pdo = new PDO("mysql:host=".db.";dbname=".dbname, dbusername, dbpassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

  $query = "SELECT rid FROM relations WHERE user = ? AND follows = ?";
  $stmt = $pdo->prepare($query);
  $stmt->execute(array($uid, $follow));
  if ($stmt->rowCount() > 0) {
    $query = "DELETE FROM relations WHERE user = ? AND follows = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array($uid, $follow));
    $json['success'] = true;
    $json['follows'] = false;
  }
  else {
    $query = "INSERT INTO relations (user, follows) VALUES (?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array($uid, $follow));
    $json['success'] = true;
    $json['follows'] = true;
  }

  $query = "SELECT "
      . "(SELECT COUNT(*) FROM relations WHERE follows = ?) as followers, "
      . "(SELECT COUNT(*) FROM relations WHERE user = ?) as following ";
  $stmt = $pdo->prepare($query);
  $stmt->execute(array($follow, $follow));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $json['followers'] = (int)$row['followers'];
  $json['following'] = (int)$row['following'];

} catch (PDOException $e) {
  $json['success'] = false;
  $json['message'] = "Failed to connect to the server. Try again soon.";
}
echo json_encode($json);
?>