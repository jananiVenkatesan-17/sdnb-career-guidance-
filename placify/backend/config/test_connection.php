<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

include("db.php"); // ← change to this

$tables = ["ats_keywords", "ats_analysis", "aptitude_questions", "courses", "hr_questions", "resume_tips", "companies", "coding_topics", "skills"];

$result = [];

foreach ($tables as $table) {
    $q = $conn->query("SELECT * FROM $table LIMIT 1");
    if ($q === false) {
        $result[$table] = "❌ Error: " . $conn->error;
    } else {
        $row = $q->fetch_assoc();
        $result[$table] = [
            "rows"    => $conn->query("SELECT COUNT(*) as c FROM $table")->fetch_assoc()['c'],
            "columns" => $row ? array_keys($row) : "empty"
        ];
    }
}

echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>