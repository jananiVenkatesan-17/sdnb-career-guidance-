<?php
header("Content-Type: application/json");
include("../config/db.php");

$resume = isset($_POST["resume"]) ? strtolower($_POST["resume"]) : "";

if($resume==""){
    echo json_encode([
        "status"=>"error",
        "message"=>"Resume text missing"
    ]);
    exit;
}

$sql="SELECT keyword FROM ats_keywords";
$result=mysqli_query($conn,$sql);

$keywords=[];
while($row=mysqli_fetch_assoc($result)){
    $keywords[]=$row["keyword"];
}

$matched=[];
foreach($keywords as $k){
    if(strpos($resume,$k)!==false){
        $matched[]=$k;
    }
}

$score = count($matched)*5;
if($score>100) $score=100;

$matchedText=implode(", ",$matched);

mysqli_query($conn,
"INSERT INTO ats_analysis(resume_text,score,matched_keywords)
VALUES('$resume',$score,'$matchedText')");

echo json_encode([
    "status"=>"success",
    "score"=>$score,
    "matched"=>$matched
]);
?>
