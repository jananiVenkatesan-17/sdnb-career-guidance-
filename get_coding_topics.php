<?php
header("Content-Type: application/json");
include("../config/db.php");

$sql = "SELECT topic, description, link FROM coding_topics ORDER BY id";
$result = mysqli_query($conn,$sql);

$data = [];

while($row = mysqli_fetch_assoc($result)){
    $data[] = $row;
}

echo json_encode([
    "status"=>"success",
    "data"=>$data
]);
?>
