<?php
header("Content-Type: application/json");
include("../config/db.php");

$courseId = isset($_GET["courseId"]) ? intval($_GET["courseId"]) : 0;
$module   = isset($_GET["module"]) ? $_GET["module"] : "";

$moduleSafe = mysqli_real_escape_string($conn, $module);

$sql = "SELECT module, title, url
        FROM study_materials
        WHERE (course_id = 0 OR course_id = $courseId)";

if($moduleSafe !== ""){
  $sql .= " AND module = '$moduleSafe'";
}

$sql .= " ORDER BY module, id";

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
