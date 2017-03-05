<?php
include 'connect.php';

$uid = $_POST['uid'];

if (!isset($uid))
  return;

try {
  $pdo = new PDO("mysql:host=".db.";dbname=".dbname, dbusername, dbpassword,
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));

  $feed_query = "SELECT users.uid, users.username, users.first_name, users.last_name, users.image AS profile_pic, "
      . "posts.pid, posts.text, posts.image, posts.posted, posts.edited, "
      . "(SELECT COUNT(*) FROM votes WHERE pid = posts.pid AND type = 1) as upvotes, "
      . "(SELECT COUNT(*) FROM votes WHERE pid = posts.pid AND type = -1) as downvotes, "
      . "(SELECT COUNT(*) FROM comments WHERE pid = posts.pid) as comments, "
      . "IFNULL(votes.type, 0) AS voted "
      . "FROM posts "
      . "INNER JOIN relations "
      . "ON relations.user = ? AND relations.follows = posts.uid "
      . "INNER JOIN users "
      . "ON users.uid = posts.uid "
      . "LEFT JOIN votes "
      . "ON votes.uid = ? AND votes.pid = posts.pid "
      . "ORDER BY posts.pid DESC";
  $feed_stmt = $pdo->prepare($feed_query);
  $feed_stmt->execute(array($uid, $uid));

  if ($feed_stmt->rowCount() > 0) {
    $feed[] = array();
    $i = 0;
    while ($feed_row = $feed_stmt->fetch(PDO::FETCH_ASSOC)) {

      $comments_query = "SELECT comments.cid, comments.text, comments.image, comments.commented, "
          . "users.uid, users.username, users.first_name, users.last_name, users.image AS profile_pic "
          . "FROM comments "
          . "INNER JOIN posts "
          . "ON posts.pid = comments.pid AND posts.pid = ? "
          . "INNER JOIN users "
          . "ON users.uid = comments.uid "
          . "ORDER BY comments.commented DESC "
          . "LIMIT 1";
      $comments_stmt = $pdo->prepare($comments_query);
      $comments_stmt->execute(array($feed_row['pid']));
      $comments = [];
      if ($comments_stmt->rowCount() > 0) {
        $comments[] = array();
        $j = 0;
        while ($comment_row = $comments_stmt->fetch(PDO::FETCH_ASSOC)) {

          // Base64 encode the images
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

          $comments[$j++] = array(
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
      if ($feed_row['image'] != null) {
        $filePath = $feed_row['image'];
        $data = file_get_contents($filePath);
        $post_image = base64_encode($data);
      }

      $post_profile_pic = null;
      if ($feed_row['profile_pic'] != null) {
        $filePath = $feed_row['profile_pic'];
        $data = file_get_contents($filePath);
        $post_profile_pic = base64_encode($data);
      }

      // Print each matching row in the database as a separate item
      $feed[$i++] = array(
        "pid" => (int)$feed_row[ 'pid'],
        "user" => array(
          "uid" => (int)$feed_row['uid'],
          "username" => $feed_row['username'],
          "name" => $feed_row['first_name']." ".$feed_row['last_name'],
          "image" => $post_profile_pic
        ),
        "text" => $feed_row['text'],
        "numberOfComments" => (int)$feed_row['comments'],
        "comments" => $comments,
        "image" => $post_image,
        "posted" => $feed_row['posted'],
        "edited" => $feed_row['edited'],
        "upvotes" => (int)$feed_row['upvotes'],
        "downvotes" => (int)$feed_row['downvotes'],
        "voted" => (int)$feed_row['voted']
      );
    }
    $json['success'] = true;
    $json['feed'] = $feed;
  }
  else {
    $json['success'] = true;
    $json['feed'] = null;
  }
} catch(PDOException $e) {
  $json['success'] = false;
  $json['message'] = "Failed to connect to the server. Try again soon.";
}
echo json_encode($json);
?>