<?php
include 'connect.php';

$uid = $_POST['uid'];

if (!isset($uid))
  return;

try {
  $pdo = new PDO("mysql:host=".db.";dbname=".dbname, dbusername, dbpassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

  $query = "SELECT users.uid, users.username, users.first_name, users.last_name, users.image "
      . "FROM users "
      . "INNER JOIN relations "
      . "ON relations.user = ? AND relations.follows = users.uid "
      . "ORDER BY first_name ASC";
  $stmt = $pdo->prepare($query);
  $stmt->execute(array($uid));

  $following = null;
  if ($stmt->rowCount() > 0) {
    $following[] = array();
    $i = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

      // Base64 encode image
      $image = null;
      if ($row['image'] != null) {
        $filePath = $row['image'];
        $data = file_get_contents($filePath);
        $image = base64_encode($data);
      }

      $following[$i++] = array(
        "uid" => (int)$row['uid'],
        "username" => $row['username'],
        "name" => $row['first_name']." ".$row['last_name'],
        "image" => $image
      );
    }
  }

  $json['success'] = true;
  $json['following'] = $following;

} catch (PDOException $e) {
  $json['success'] = false;
  $json['message'] = "Failed to connect to the server. Try again soon.";
}
echo json_encode($json);
?>