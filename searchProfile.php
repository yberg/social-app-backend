<?php
include 'connect.php';

$name = $_POST['name'];

if (!isset($name))
  return;

try {
  $pdo = new PDO("mysql:host=".db.";dbname=".dbname, dbusername, dbpassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

  $query = "SELECT uid, username, first_name, last_name, image FROM users WHERE "
      . "username LIKE CONCAT('%', :name, '%') OR "
      . "first_name LIKE CONCAT('%', :name, '%') OR "
      . "last_name LIKE CONCAT('%', :name, '%') OR "
      . "CONCAT(first_name, ' ', last_name) LIKE CONCAT('%', :name, '%') "
      . "ORDER BY first_name "
      . "LIMIT 5";
  $stmt = $pdo->prepare($query);
  $stmt->execute(array(
    ":name" => $name
  ));

  $profiles = null;
  if ($stmt->rowCount() > 0) {
    $profiles[] = array();
    $i = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

      // Base64 encode image
      $image = null;
      if ($row['image'] != null) {
        $filePath = $row['image'];
        $data = file_get_contents($filePath);
        $image = base64_encode($data);
      }

      $profiles[$i++] = array(
        "uid" => (int)$row['uid'],
        "username" => $row['username'],
        "name" => $row['first_name']." ".$row['last_name'],
        "image" => $image
      );
    }
  }

  $json['success'] = true;
  $json['profiles'] = $profiles;
} catch (PDOException $e) {
  $json['success'] = false;
  $json['message'] = "Failed to connect to the server. Try again soon.";
}
echo json_encode($json);
?>