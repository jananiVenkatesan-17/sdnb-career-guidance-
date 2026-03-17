<?php
header("Content-Type: application/json");
include("../config/db.php");

$courseId = isset($_GET["courseId"]) ? intval($_GET["courseId"]) : 0;

if ($courseId <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid course id"
    ]);
    exit;
}

$sql = "SELECT question FROM mock_interview_questions WHERE course_id = $courseId LIMIT 15";
$res = mysqli_query($conn, $sql);

if (!$res) {
    echo json_encode([
        "status" => "error",
        "message" => "Query failed"
    ]);
    exit;
}

$data = [];
while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
}

echo json_encode([
    "status" => "success",
    "data" => $data
]);
?>
