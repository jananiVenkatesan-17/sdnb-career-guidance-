<?php
header("Content-Type: application/json");
include("../config/db.php");

$courseId = isset($_GET["courseId"]) ? intval($_GET["courseId"]) : 0;

$sql = "SELECT skill_type, skill_name
        FROM skills
        WHERE course_id = $courseId
        ORDER BY skill_type, skill_name";

$res = mysqli_query($conn, $sql);

$data = [];
while($row = mysqli_fetch_assoc($res)){
  $data[] = $row;
}

echo json_encode([
  "status" => "success",
  "count"  => count($data),
  "data"   => $data
]);
?>
