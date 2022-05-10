<?php
require_once('conn.php');

$result = $conn->query("SELECT * FROM `url`");

if (!$result) {
    die($conn->error);
}
$data = [ 'list' => array()];
while ($row = $result->fetch_assoc()) {
    array_push($data['list'],[ 'id' => $row['id'],'name' => $row['name'],'ShortCode' => $row['ShortCode'] , 'toUrl' => $row['toUrl']]);
}
echo json_encode($data);
