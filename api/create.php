<?php
error_reporting(E_ALL ^ E_WARNING); 
require_once('conn.php');
$stmt = mysqli_prepare($conn,"INSERT INTO  `url` (`name`,`ShortCode`, `toUrl`) VALUE (?,?,?) ");

$data = json_decode(file_get_contents("php://input"),true);
$name = $data["name"];
$ShortCode = $data["ShortCode"];
$toUrl = $data["toUrl"];
mysqli_stmt_bind_param($stmt, "sss", $name, $ShortCode, $toUrl);

$result = mysqli_stmt_execute($stmt);

if (mysqli_affected_rows($conn) > 0) {
    $new_id = mysqli_insert_id($conn);
    echo json_encode(['msg' => 'success']);
} elseif (mysqli_affected_rows($conn) == 0) {
    echo json_encode(['msg' => 'no data affected']);
} else {
    echo json_encode(['msg' => mysqli_error($conn)]);
}
?>