<?php

include("../config/db.php");

$target="../uploads/";

$file=$_FILES["resume"]["name"];
$tmp=$_FILES["resume"]["tmp_name"];

move_uploaded_file($tmp,$target.$file);

/* For now demo text extraction */
$text=file_get_contents($target.$file);

$text=strtolower($text);

$sql="SELECT keyword_name FROM ats_keywords";
$result=mysqli_query($conn,$sql);

$matched=[];

while($row=mysqli_fetch_assoc($result)){

if(strpos($text,strtolower($row["keyword_name"]))!==false){
$matched[]=$row["keyword_name"];
}

}

$score=count($matched)*5;

echo json_encode([
"score"=>$score,
"matched"=>$matched
]);

?>
