<?php
$hit_count = @file_get_contents("./topics_old.csv");
$lines=explode("\n",(string)$hit_count);
$newlines=$lines;
$doc="";

//ini_set('mysql.connect_timeout', 0); 
ini_set('max_execution_time', 0); 

$con=mysqli_connect("127.0.0.1","toastadmin","T0aST$#U0M","toastmastersdb");

if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}


$result = mysqli_query($con,"SELECT * FROM TT_Title");

echo "<table border='1'>
<tr>
<th>Index</th>
<th>Topic</th>
</tr>";

while($row = mysqli_fetch_array($result)) {
  echo "<tr>";
  echo "<td>" . $row['Index'] . "</td>";
  echo "<td>" . $row['Topic'] . "</td>";
  echo "</tr>";
}

echo "</table>";




for ($i = 0; $i < count($lines); $i++) {
  $keysString=str_replace(array("(",")",",","? "), " ", $lines[$i]);
  $keysString=str_replace(" ", ",", $keysString);
  $keysString=str_replace(array("?", ".", "'",",,"), "", $keysString);
  $newlines[$i]=str_replace("\n", "", $newlines[$i]);
  $keysString=str_replace("\n", "", $keysString);
  
  //Add the topic
  $sql="INSERT INTO tt_title (`Topic`) VALUES ('".$newlines[$i]."')";
  if (!mysqli_query($con,$sql)) {
		die('Error: ' . mysqli_error($con));
  }
  $topicID=mysqli_insert_id($con);
 
  //Extract keys
  $keys = explode(",",(string)$keysString);
 
  for ($j = 0; $j < count($keys); $j++) {  
	if (preg_match("/^.(?=.*[a-z])|(?=.*[A-Z]).*$/", $keys[$j])){ //Take only valid strings
		//See if the key already exists
		$sql="SELECT * FROM tt_key WHERE `Key` = '".$keys[$j]."'";
		$result =mysqli_query($con,$sql);
		if (!$result) {
			die('Error: ' . mysqli_error($con));
		}
		else{
		   if($result->num_rows>0){ //Already exists
				while($row = mysqli_fetch_array($result)) {
					$keyID=$row['Index'];
				}
		   }
		   else{
			   //Add the key
			   $sql="INSERT INTO tt_key (`Key`) VALUES ('".$keys[$j]."');";	
				if (!mysqli_query($con,$sql)) {
					die('Error: ' . mysqli_error($con));
				}
				$keyID=mysqli_insert_id($con);
		   }
		}
		
		//Add title to key mapping
		$sql="INSERT INTO `tt_title_to_key` (`Title`,`Key`) VALUES ('".$topicID."','".$keyID."');";	
		if (!mysqli_query($con,$sql)) {
			die('Error: ' . mysqli_error($con));
		}
		$keyID=mysqli_insert_id($con);	
	
	}
  }
  
  //$newlines[$i]=$newlines[$i].";".$keysString;
  //$doc=$doc.$newlines[$i]."\n";
}


mysqli_close($con);
//@file_put_contents("./topics1.csv", $doc);
?>