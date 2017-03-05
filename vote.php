<?php
include 'connect.php';

$uid = $_POST['uid'];
$pid = $_POST['pid'];
$type = $_POST['type'];

if (!isset($uid) || !isset($pid))
  return;

if ($type == -1 || $type == 1) {
  try {
    $pdo = new PDO("mysql:host=".db.";dbname=".dbname, dbusername, dbpassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

    $query = "SELECT type FROM votes WHERE uid = ? AND pid = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array($uid, $pid));
    if ($stmt->rowCount() > 0) {
      $vote = $stmt->fetch(PDO::FETCH_ASSOC)['type'];
      if ($vote == $type) {
        $query = "DELETE FROM votes WHERE uid = ? AND pid = ?";
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute(array($uid, $pid));
        $voted = false;
      }
      else { // if ($vote != $type)
        $query = "UPDATE votes SET type = ? WHERE uid = ? AND pid = ?";
        $stmt = $pdo->prepare($query);
        $result = $stmt->execute(array($type, $uid, $pid));
        $up = $type;
        $down = -$type;
        $voted = true;
      }
    }
    else {
      $query = "INSERT INTO votes (uid, pid, type) VALUES (?, ?, ?)";
      $stmt = $pdo->prepare($query);
      $result = $stmt->execute(array($uid, $pid, $type));
      $voted = true;
    }

    $query = "SELECT "
        . "(SELECT COUNT(*) FROM votes WHERE pid = ? AND type = 1) AS upvotes, "
        . "(SELECT COUNT(*) FROM votes WHERE pid = ? AND type = -1) AS downvotes";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array($pid, $pid));
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $json['success'] = true;
    $json['vote'] = (int)$type;
    $json['voted'] = $voted;
    $json['upvotes'] = (int)$result['upvotes'];
    $json['downvotes'] = (int)$result['downvotes'];
  } catch(PDOException $e) {
    $json['success'] = false;
    $json['message'] = "Failed to connect to the server. Try again soon.";
  }
}
else {
  $json['success'] = false;
  $json['message'] = "Invalid vote.";
}
echo json_encode($json);
?>