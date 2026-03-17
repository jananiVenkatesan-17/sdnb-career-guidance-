<?php
header("Content-Type: application/json");
include("../config/db.php");

$sql = "SELECT * FROM courses";
$result = mysqli_query($conn, $sql);

if(!$result){
    echo json_encode([
        "status" => "error",
        "message" => "Query failed",
        "mysql_error" => mysqli_error($conn),
        "sql" => $sql
    ]);
    exit;
}

$courses = [];
while($row = mysqli_fetch_assoc($result)){
    $courses[] = $row;
}

echo json_encode([
    "status" => "success",
    "count" => count($courses),
    "data" => $courses
]);
?>
