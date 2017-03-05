<?php
include 'connect.php';

$username = $_POST['username'];
$password = $_POST['password'];

if (!isset($username) || !isset($password))
  return;

if (isset($username) && isset($password)) {
  try {
    $pdo = new PDO("mysql:host=".db.";dbname=".dbname, dbusername, dbpassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

    $query = "SELECT password, uid, username, first_name, last_name FROM users WHERE username = ?";

    $stmt = $pdo->prepare($query);
    $stmt->execute(array($username));
    if ($stmt->rowCount() > 0) {
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      $hash = $row['password'];
      if (password_verify($password, $hash)) {
        $json['success'] = true;
        $json['user'] = array(
          "uid" => (int)$row['uid'],
          "username" => $row['username'],
          "name" => $row['first_name']." ".$row['last_name']
        );
      }
      else {
        $json['success'] = false;
        $json['message'] = "Wrong username/password";
      }
    }
    else {
      password_verify($password, "$2y$11$00000000000000000000000000000000000000000000000000000");
      $json['success'] = false;
      $json['message'] = "Wrong username/password";
    }
  } catch(PDOException $e) {
    $json['success'] = false;
    $json['message'] = "Failed to connect to the server. Try again soon.";
  }
}
echo json_encode($json);
?>