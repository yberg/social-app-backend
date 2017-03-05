<?php
include 'connect.php';

$uid = $_POST['uid'];
$profile = $_POST['profile'];

if (!isset($uid) || !isset($profile))
  return;

try {
  $pdo = new PDO("mysql:host=".db.";dbname=".dbname, dbusername, dbpassword,
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

  $query = "SELECT uid, username, first_name, last_name, email, image, joined, "
      . "(SELECT COUNT(*) FROM posts WHERE uid = :profile) AS posts, "
      . "(SELECT COUNT(*) FROM comments WHERE uid = :profile) AS comments, "
      . "(SELECT COUNT(*) FROM relations WHERE follows = :profile) as followers, "
      . "(SELECT COUNT(*) FROM relations WHERE user = :profile) as following, "
      . "(SELECT COUNT(*) FROM relations WHERE user = :uid AND follows = :profile) as follows "
      . "FROM users "
      . "WHERE uid = :profile";
  $stmt = $pdo->prepare($query);
  $stmt->execute(array(
    ":profile" => $profile,
    ":uid" => $uid
  ));

  if ($stmt->rowCount() > 0) {

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Base64 encode the image
    $image = null;
    if ($row['image'] != null) {
      $filePath = $row['image'];
      $data = file_get_contents($filePath);
      $image = base64_encode($data);
    }

    $_profile = array(
      "uid" => (int)$row['uid'],
      "username" => $row['username'],
      "firstName" => $row['first_name'],
      "lastName" => $row['last_name'],
      "email" => $row['email'],
      "image" => $image,
      "joined" => $row['joined'],
      "posts" => (int)$row['posts'],
      "comments" => (int)$row['comments'],
      "followers" => (int)$row['followers'],
      "following" => (int)$row['following'],
      "follows" => (int)$row['follows']
    );

    $query = "SELECT posts.pid, posts.text, posts.image, posts.posted, posts.edited, "
        . "(SELECT COUNT(*) FROM votes WHERE pid = posts.pid AND type = 1) as upvotes, "
        . "(SELECT COUNT(*) FROM votes WHERE pid = posts.pid AND type = -1) as downvotes, "
        . "(SELECT COUNT(*) FROM comments WHERE pid = posts.pid) as comments, "
        . "IFNULL(votes.type, 0) AS voted "
        . "FROM posts "
        . "LEFT JOIN votes "
        . "ON votes.uid = ? and votes.pid = posts.pid "
        . "WHERE posts.uid = ? "
        . "ORDER BY pid DESC "
        . "LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array($profile, $profile));

    if ($stmt->rowCount() > 0) {

      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // Base64 encode the image
      $image = null;
      if ($row['image'] != null) {
        $filePath = $row['image'];
        $data = file_get_contents($filePath);
        $image = base64_encode($data);
      }

      $post = array(
        "pid" => (int)$row['pid'],
        "text" => $row['text'],
        "image" => $image,
        "posted" => $row['posted'],
        "edited" => $row['edited'],
        "comments" => (int)$row['comments'],
        "upvotes" => (int)$row['upvotes'],
        "downvotes" => (int)$row['downvotes'],
        "voted" => (int)$row['voted']
      );

    }

    $json['success'] = true;
    $json['profile'] = $_profile;
    $json['post'] = $post;
  }
  else {
    $json['success'] = false;
    $json['message'] = "Couldn't get user.";
  }
} catch(PDOException $e) {
  $json['success'] = false;
  $json['message'] = "Failed to connect to the server. Try again soon.";
}
echo json_encode($json);
?>