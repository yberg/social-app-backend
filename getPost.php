<?php
include 'connect.php';

$uid = $_POST['uid'];
$pid = $_POST['pid'];

if (!isset($uid) || !isset($pid))
  return;

try {
  $pdo = new PDO("mysql:host=".db.";dbname=".dbname, dbusername, dbpassword,
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

  $post_query = "SELECT posts.pid, posts.text, posts.image, posts.posted, posts.edited, "
      . "users.uid, users.username, users.first_name, users.last_name, users.image AS profile_pic, "
      . "(SELECT COUNT(*) FROM votes WHERE pid = posts.pid AND type = 1) as upvotes, "
      . "(SELECT COUNT(*) FROM votes WHERE pid = posts.pid AND type = -1) as downvotes, "
      . "(SELECT COUNT(*) FROM comments WHERE pid = posts.pid) as comments, "
      . "IFNULL(votes.type, 0) AS voted "
      . "FROM posts "
      . "INNER JOIN users "
      . "ON posts.uid = users.uid AND posts.pid = ? "
      . "LEFT JOIN votes "
      . "ON votes.uid = ? and votes.pid = posts.pid";
  $post_stmt = $pdo->prepare($post_query);
  $post_stmt->execute(array($pid, $uid));

  if ($post_stmt->rowCount() > 0) {

    $post_row = $post_stmt->fetch(PDO::FETCH_ASSOC);

    $comments_query = "SELECT comments.cid, comments.text, comments.image, comments.commented, "
        . "users.uid, users.username, users.first_name, users.last_name, users.image AS profile_pic "
        . "FROM comments "
        . "INNER JOIN posts "
        . "ON posts.pid = comments.pid AND posts.pid = ? "
        . "INNER JOIN users "
        . "ON users.uid = comments.uid "
        . "ORDER BY comments.commented ASC";
    $comments_stmt = $pdo->prepare($comments_query);
    $comments_stmt->execute(array($pid));

    $comments = [];
    if ($comments_stmt->rowCount() > 0) {
      $comments[] = array();
      $i = 0;
      while ($comment_row = $comments_stmt->fetch(PDO::FETCH_ASSOC)) {
        // Print each matching row in the database as a separate item

        // Base64 encode the image
        $comment_image = null;
        if ($comment_row['image'] != null) {
          $filePath = $comment_row['image'];
          $data = file_get_contents($filePath);
          $comment_image = base64_encode($data);
        }

        $comment_profile_pic = null;
        if ($comment_row['profile_pic'] != null) {
          $filePath = $comment_row['profile_pic'];
          $data = file_get_contents($filePath);
          $comment_profile_pic = base64_encode($data);
        }

        $comments[$i++] = array(
          "cid" => (int)$comment_row['cid'],
          "text" => $comment_row['text'],
          "image" => $comment_image,
          "commented" => $comment_row['commented'],
          "user" => array(
            "uid" => (int)$comment_row['uid'],
            "username" => $comment_row['username'],
            "name" => $comment_row['first_name']." ".$comment_row['last_name'],
            "image" => $comment_profile_pic
          )
        );
      }
    }

    // Base64 encode the images
    $post_image = null;
    if ($post_row['image'] != null) {
      $filePath = $post_row['image'];
      $data = file_get_contents($filePath);
      $post_image = base64_encode($data);
    }

    $profile_pic = null;
    if ($post_row['profile_pic'] != null) {
      $filePath = $post_row['profile_pic'];
      $data = file_get_contents($filePath);
      $profile_pic = base64_encode($data);
    }

    $post = array(
      "pid" => (int)$post_row['pid'],
      "user" => array(
        "uid" => (int)$post_row['uid'],
        "username" => $post_row['username'],
        "name" => $post_row['first_name']." ".$post_row['last_name'],
        "image" => $profile_pic
      ),
      "text" => $post_row['text'],
      "numberOfComments" => (int)$post_row['comments'],
      "comments" => $comments,
      "image" => $post_image,
      "posted" => $post_row['posted'],
      "edited" => $post_row['edited'],
      "upvotes" => (int)$post_row['upvotes'],
      "downvotes" => (int)$post_row['downvotes'],
      "voted" => (int)$post_row['voted']
    );

    $json['success'] = true;
    $json['post'] = $post;
  }
  else {
    $json['success'] = false;
    $json['message'] = "Couldn't get post.";
  }
} catch(PDOException $e) {
  $json['success'] = false;
  $json['message'] = "Failed to connect to the server. Try again soon.";
}
echo json_encode($json);
?>