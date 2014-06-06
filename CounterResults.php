<?php


$home_dir_path = ".";
echo "<table border='1'>";
explore($home_dir_path,"","");
echo "</table>";


function explore($dir_path,$indent,$parentPath)
{
	if (is_dir($dir_path)) {
		if ($dir_handler = opendir($dir_path)) {
			while (($fileName = readdir($dir_handler)) !== false) {
				$parts=explode(".",(string)$fileName);
				$path=$dir_path."/".$fileName;
				$hit_count = @file_get_contents(".".$subpath."/".$fileName);
				
				
					$PaPath=$path;
				
				
				if(is_dir($path)){
					if(!endsWith($path, ".")){							
						explore($path,$indent."_",$PaPath);						
					}					
				}
				
				
				if(count($parts) >1 ){				
					if($parts[1]=="txt"){
						if($parentPath<>""){
							echo "<tr><td colspan='2''>".$parentPath."</td></tr>";
						}
						$hit_count = @file_get_contents($path);						
						echo "<tr><td>".$indent."$fileName </td><td>".$hit_count."</td></tr>";
					}
				}
			}
			closedir($dir_handler);
		}
	}
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

?>