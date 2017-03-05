<?php
include 'connect.php';

$uid = $_POST['uid'];
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$password = $_POST['password'];
$newPassword = $_POST['newPassword'];
$image = $_POST['image'];

if (!isset($uid))
  return;

try {
  $pdo = new PDO("mysql:host=".db.";dbname=".dbname, dbusername, dbpassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

  $query = "SELECT password FROM users WHERE uid = ?";
  $stmt = $pdo->prepare($query);
  $stmt->execute(array($uid));

  if ($stmt->rowCount() > 0) {

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $hash = $row['password'];

    if ((!isset($image) || $image != "") && password_verify($password, $hash)) {

      $query = "UPDATE users SET uid = uid";
      if (isset($firstName) && $firstName != "")
        $query .= ", first_name = :firstName";
      if (isset($lastName) && $lastName != "")
        $query .= ", last_name = :lastName";
      if (isset($email) && $email != "")
        $query .= ", email = :email";
      if (isset($newPassword) && $newPassword != "")
        $query .= ", password = :password";
      $query .= " WHERE uid = :uid";

      $stmt = $pdo->prepare($query);

      if (isset($firstName) && $firstName != "")
        $stmt->bindParam(":firstName", $firstName);
      if (isset($lastName) && $lastName != "")
        $stmt->bindParam(":lastName", $lastName);
      if (isset($email) && $email != "")
        $stmt->bindParam(":email", $email);
      if (isset($newPassword) && $newPassword != "") {
        // Generate new password
        $options = [
          'cost' => 11,
        ];
        $hash = password_hash($newPassword, PASSWORD_DEFAULT, $options);
        $stmt->bindParam(":password", $hash);
      }
      $stmt->bindParam(":uid", $uid);

      $stmt->execute();

      $json['success'] = true;
      $json['message'] = "Successfully updated profile";

    }
    else if (isset($image) && $image != "") {
      // Delete old image from server
      $query = "SELECT image FROM users WHERE uid = ?";
      $stmt = $pdo->prepare($query);
      $stmt->execute(array($uid));
      if ($stmt->rowCount() > 0) {
        $_image = $stmt->fetch(PDO::FETCH_ASSOC)['image'];
        if ($_image != null) {
          unlink($_image);
        }
      }

      $query = "UPDATE users SET image = ? WHERE uid = ?";
      $stmt = $pdo->prepare($query);
      $stmt->execute(array($image, $uid));

      $json['success'] = true;
      $json['message'] = "Successfully updated profile picture";
    }
    else {
      $json['success'] = false;
      $json['message'] = "Wrong password";
    }
  }
  else {
    password_verify($password, "$2y$11$00000000000000000000000000000000000000000000000000000");
    $json['success'] = false;
    $json['message'] = "Wrong password";
  }

} catch(PDOException $e) {
  $json['success'] = false;
  $json['message'] = "Failed to connect to the server. Try again soon.";
}
echo json_encode($json);
?>