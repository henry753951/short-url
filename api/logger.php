<?php 
ini_set('display_errors', 'On');
date_default_timezone_set('Asia/Taipei');
require_once('conn.php');
$data = json_decode(file_get_contents("php://input"),true);
$id = $data['uid'];
$pos = $data['pos'];
$stmt = mysqli_prepare($conn,"SELECT * FROM `viewLogger` WHERE `id` = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = $result->fetch_object();
$jsonData = json_decode($row->data,true);
mysqli_stmt_close($stmt);


$newData = json_encode([
    'ip' => $jsonData['ip'],
    'os' => $jsonData['os'],
    'browser' => $jsonData['browser'],
    'pos' => $pos,
    'time' => $jsonData['time']
]);


$sql = "UPDATE `viewLogger` SET `data` = '$newData' WHERE id='$id';";
$result = mysqli_query($conn,$sql);

echo json_encode(['msg' => 'success']);



?>