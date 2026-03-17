<?php
header("Content-Type: application/json");
include("../config/db.php");

$sql = "SELECT id, tip FROM resume_tips ORDER BY id";
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
