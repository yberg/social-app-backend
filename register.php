<?php
include 'connect.php';

$username = $_POST['username'];
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$password = $_POST['password'];

if (!isset($username) || !isset($firstName) || !isset($lastName) || !isset($email) || !isset($password))
  return;

try {
  $pdo = new PDO("mysql:host=".db.";dbname=".dbname, dbusername, dbpassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

  $query = "SELECT username FROM users WHERE username = ?";
  $stmt = $pdo->prepare($query);
  $stmt->execute(array($username));
  if ($stmt->rowCount() > 0) {
    $json['success'] = false;
    $json['error'] = 1;
    $json['message'] = "Username is taken";
  }
  else {

    $options = [
      'cost' => 11,
    ];
    $hash = password_hash($password, PASSWORD_DEFAULT, $options);
    $query = "INSERT INTO users (username, first_name, last_name, email, password) VALUES (?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($query);
    $stmt->execute(array($username, $firstName, $lastName, $email, $hash));

    $json['success'] = true;
  }
} catch(PDOException $e) {
  $json['success'] = false;
  $json['message'] = "Failed to connect to the server. Try again soon.";
}
echo json_encode($json);
?>