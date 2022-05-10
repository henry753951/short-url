<?php
require_once('conn.php');
$data = json_decode(file_get_contents("php://input"),true);
$id = $data['id'];
$result = $conn->query("SELECT * FROM `viewLogger` WHERE `relateID`='$id'");

if (!$result) {
    die($conn->error);
}
$_data = [ 'list' => array()];
while ($row = $result->fetch_assoc()) {
    array_push($_data['list'],[ 'id' => $row['id'],'data' => json_decode($row['data'])]);
}
echo json_encode($_data);
