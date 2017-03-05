<?php

include 'connect.php';

header('Content-type: bitmap; charset=utf-8');

$uid = $_POST['uid'];
$image = $_POST['image'];

if (!isset($image))
  return;

$encodedString = $image;
$decodedString = base64_decode($encodedString);

$filePath = 'img/' . uniqid() . ".jpg";

$file = fopen($filePath, 'wb');
$isWritten = fwrite($file, $decodedString);
fclose($file);

if ($isWritten > 0) {
  $json['success'] = true;
  $json['fileName'] = $filePath;
}
else {
  $json['success'] = false;
  $json['message'] = "Couldn't upload image. Try again later.";
}
echo json_encode($json);

/*print_r($_FILES);

$target_path  = "./";
$target_path = $target_path . basename( $_FILES['image']['name']);
if(move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
    $json['success'] = true;
    $json['message'] = "The file ". basename( $_FILES['image']['name']) ." has been uploaded";
}
else {
    $json['success'] = false;
    $json['message'] = "There was an error uploading the file, please try again!";
}
echo json_encode($json);*/
?>