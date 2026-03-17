<?php
header("Content-Type: application/json");
include("../config/db.php");

$sql = "SELECT id, question, answer FROM aptitude_questions ORDER BY id";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        "status" => "error",
        "message" => mysqli_error($conn)
    ]);
    exit;
}

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

echo json_encode([
    "status" => "success",
    "count" => count($data),
    "data" => $data
]);
?>
