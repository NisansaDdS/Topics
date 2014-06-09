<?php session_start(); ?>
<head>

<!-- Bootstrap core CSS -->
    <link href="./bootstrap-3.1.1-dist/css/bootstrap.css" rel="stylesheet">

<!-- Custom styles for this template -->
    <link href="./bootstrap-3.1.1-dist/css/dashboard.css" rel="stylesheet">
	
<!-- Bootstrap theme -->
    <link href="./bootstrap-3.1.1-dist/css/bootstrap-theme.css" rel="stylesheet">
 <!--   <link href="./bootstrap-3.1.1-dist/css/signin.css" rel="stylesheet">  -->	
	
	<style type="text/css">
		.title-container { padding:50px 0px; }
		
		.form-signin {
		  max-width: 400px;		 
		}
	</style>
	
	
</head>

<body>

<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Table Topics Manager</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#">University of Moratuwa Toastmasters Club</a></li>            
            <li>
			<?php 				
				if(isset($_SESSION['Name'])){
					echo '<a href=".?page=LogOut">'.$_SESSION['Name'].' (Log out)</a>';
				}
				else{
					echo '<a href=".?page=Login&Next=Random">Log in</a>';					
				}
			?>			
			</li>
          </ul>
          <form class="navbar-form navbar-right" action="." method="get">
            <input id="searchBox" type="text" class="form-control" placeholder="Search..." name="q" >
          </form>
        </div>
      </div>
    </div>

	<?php
		  
			if (isset($_GET["page"]))
			{
				$page=$_GET["page"];								
			}
			else if(isset($_GET["q"]))
			{
				$page='Search';
			}
			else if (isset($_POST["page"]))
			{
				$page=$_POST["page"];								
			}	
			else{				
				$page='Random';
			}
			
	?>
	
	<div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
		    <?php
				if($page=='Random'){
					echo "<li class='active'>";
				}
				else{
					echo "<li>";
				}				
			?>
            <a href="?page=Random">Random Topic!</a></li>
			<?php
				if($page=='Search'){
					echo "<li class='active'>";
				}
				else{
					echo "<li>";
				}				
			?>
			<a href="?page=Search">Search</a></li>
            <?php
				if($page=='Add'){
					echo "<li class='active'>";
				}
				else{
					echo "<li>";
				}				
			?>
			<a href="?page=Add">Add Topic
			<?php
				if(!isset($_SESSION['Name'])){
					echo '<span class="glyphicon glyphicon-lock"></span>';
				}
			?>
			</a></li>
            <?php
				if($page=='Edit'){
					echo "<li class='active'>";
				}
				else{
					echo "<li>";
				}				
			?>
			<a href="?page=Edit&id=1">Edit Topics
			<?php
				if(!isset($_SESSION['Name'])){
					echo '<span class="glyphicon glyphicon-lock"></span>';
				}
			?>
			</a></li>            
			<?php
				if($page=='AddUser'){
					echo "<li class='active'>";
				}
				else{
					echo "<li>";
				}				
			?>
			<a href="?page=AddUser">Add User
			<?php
				if(!isset($_SESSION['Name'])){
					echo '<span class="glyphicon glyphicon-lock"></span>';
				}
				else if(isset($_SESSION['Level'])){
					if($_SESSION['Level']<1){
						echo '<span class="glyphicon glyphicon-lock"></span>';
					}
				}
			?>
			</a></li>
          </ul>          
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <?php
		
			$key1='MA';
			$key2='1988';
			$un="toastadmin";
			$dbPass="T$$$$$$U0M"
		
		
			
			$con=mysqli_connect("localhost",$un,$dbPass,"toastmastersdb");

			if (mysqli_connect_errno()) {
			  echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}
									
			$topics =array();
			LoadAllTopics();
			//echo $topics[2];
			
			$TitleID=0;
			$Title="";
			$keys=array();
			
			
			if($page=='Login'){	//User interface for logging in	
				if(isset($_GET["Wrong"])){
					echo '<div class="alert alert-danger alert-dismissable">';
					echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
					echo '<strong>Warning!</strong> Wrong email address or password!';
					echo '</div>';
				}
				echo '<div class="container"><form class="form-signin" role="form" action="." method="post">';
				echo '<h2 class="form-signin-heading">Please sign in</h2>';
				echo '<input type="hidden" class="form-control" value="CheckLogin" name="page">';
				if(!isset($_GET["Wrong"])){
					echo '<input type="hidden" class="form-control" value="'.$_SERVER['QUERY_STRING'].'" name="Redirect">';
				}
				echo '<input type="email" class="form-control" placeholder="Email address" name="email" ';
				if(isset($_GET["Wrong"])){
					echo 'value='.$_GET["email"];
				}
				echo 'required autofocus>';
				echo '<input type="password" class="form-control" placeholder="Password" name="password" required>';
				echo '<label class="checkbox">';
				echo '<input type="checkbox" value="remember-me"> Remember me';
				echo '</label>';
				echo '<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>';
				echo '</form></div>';
			}
			else if($page=='CheckLogin') //Business logic to check login details
			{				
				if(isset($_POST["email"]) && isset($_POST["password"])){
					$pass=CryptPass($_POST["password"]);
					$email=$_POST["email"];					
					$sql="SELECT * FROM `tt_members` WHERE`Email` = '".$email."'";
					$result =mysqli_query($con,$sql);
					if (!$result){
						die('Error: ' . mysqli_error($con));
					}
					else{							   
						while($row = mysqli_fetch_array($result)) {
							if($pass==$row['Password']){
								$_SESSION['Name']=$row['Name'];
								$_SESSION['Level']=$row['Level'];
								if($_POST["Redirect"]!=""){
									$params = explode("page=Login&Next=",(string)$_POST["Redirect"]);
									header( "Location: ./?page=".$params[1]);
								}
								else{
									header( "Location: ./?page=Random"); //Does not work/////////////////////////////////////////////////////////////////
								}								
							}
							else{
								header( "Location: ./?page=Login&Wrong&email=".$_POST["email"]);
							}
						}						   
					}
				}
			}
			else if($page=='LogOut') //Business logic LogOut
			{
				if(isset($_SESSION['Name'])){
						unset($_SESSION['Name']);
						unset($_SESSION['Level']);
				}
				header( "Location: ./?page=Random");
			}
			else if($page=='Show'){
				if (isset($_GET["id"]))
				{
					$TitleID=$_GET["id"];
					FetchTitle($TitleID,$topics,$con);								
					ShowTitle();
				}
				else{					
					ErrorMesage();
				}						
			}
			else if($page=='Random'){
				$TitleID=array_rand($topics, 1);				
				FetchTitle($TitleID,$topics,$con);
								
				ShowTitle();
				echo '<hr>';
				echo ' <a href="?page=Random"><button type="button" class="btn btn-lg btn-success">More Random!</button></a>';
				
			}
			else if($page=='Search'){
				$sql="SELECT DISTINCT`tt_title`.`Topic`,`tt_title`.`Index` FROM tt_title,(SELECT `tt_title_to_key`.`Title` FROM tt_title_to_key,tt_key WHERE `tt_title_to_key`.`Key` =`tt_key`.`Index`";
					
				if (isset($_GET["q"]))
				{
					$q=$_GET["q"];
					echo '<h2 class="sub-header">Search Results for "'.$q.'"</h2>';
					$sql=$sql."AND `tt_key`.`Key` = '".$q."'";
				}
				else{					
					echo '<h2 class="sub-header">Listing All Topics</h2>';					
				}
				$sql=$sql.") AS Sel WHERE `Sel`.`Title` =`tt_title`.`Index`";
							
				
				$result =mysqli_query($con,$sql);
				if (!$result) {
					die('Error: ' . mysqli_error($con));
				}
				
				echo '<div class="title-container">';
				echo '<div class="list-group">';
				while($row = mysqli_fetch_array($result))
				{					
					echo '<a href="?page=Show&id='.$row['Index'].'" class="list-group-item" style:width="100%">'.$row['Topic'].'</a>';
				}						
				echo '</div>';
				echo '</div>';
			}
			else if($page=='Edit'){ //Ui for Edit
				if(isset($_SESSION['Level'])){					
						if (isset($_GET["id"]))
						{
							$TitleID=$_GET["id"];
							if($TitleID<=0){
								$TitleID=1;
							}
							else if($TitleID>=count($topics)){
								$TitleID=count($topics)-1;
							}	
							FetchTitle($TitleID,$topics,$con);	
							echo '<h2 class="sub-header"><a href="?page=Edit&id='.($TitleID-1).'"><button type="button" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-backward"></span></button></a> ';
							echo 'Editing Topic Number '.$TitleID.' ';
							echo '<a href="?page=Edit&id='.($TitleID+1).'"><button type="button" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-forward"></span></button></a></h2><br>';
							ShowInputForm();
							if (isset($_GET["Updated"]))
							{
								echo '<div class="alert alert-success alert-dismissable">';
								echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
								echo 'Topic Updated!';
								echo '</div>';
							}

						}
						else{					
							ErrorMesage();
						}					
				}
				else{
					$params = explode("page=",(string)$_SERVER['QUERY_STRING']);
					header( "Location: ./?page=Login&Next=".$params[1]);
				}
			}
			else if($page=='Add'){ //Ui for Add
				if(isset($_SESSION['Level'])){
					echo '<h2 class="sub-header">Adding New Topic</h2><br>';
					ShowInputForm();
				}
				else{
					$params = explode("page=",(string)$_SERVER['QUERY_STRING']);
					header( "Location: ./?page=Login&Next=".$params[1]);
				}
			}
			else if($page=='Update'){  //Handles the DB operation of both Edit and Add requests
				if(isset($_SESSION['Level'])){ 
					if ((isset($_GET["topic"]))&&(isset($_GET["keys"])))
					{
						ExtractDBwriterInput();
						$isAdd=true;
						
						if(isset($_GET["id"])){ //ID is already set, i.e. This is an update
							$isAdd=false;
							$TitleID=$_GET["id"];
							
							//Checking and updating keys one by one is an unwanted overhead. So keys are dropped and reinserted.
							//This is not a problem because duplicates are not inserted. First, the Topic to Key mappings are dropped.
							$sql="DELETE FROM tt_title_to_key WHERE `Title` = '".$TitleID."'";	
							if (!mysqli_query($con,$sql)) {
								die('Error: ' . mysqli_error($con));
							}
							
							//Now update the Topic
							$sql="UPDATE tt_title SET `Topic`='".$Title."' WHERE `Index`='".$TitleID."'";
							if (!mysqli_query($con,$sql)) {
								die('Error: ' . mysqli_error($con));
							}
							
						}
						else{  //There is no id i.e this is an add 
						
							//Add the topic
							$sql="INSERT INTO tt_title (`Topic`) VALUES ('".$Title."')";
							if (!mysqli_query($con,$sql)) {
								die('Error: ' . mysqli_error($con));
							}
							$TitleID=mysqli_insert_id($con);
						}
						
						
						//Now insert Keys and Topic to key mappings
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
								$sql="INSERT INTO `tt_title_to_key` (`Title`,`Key`) VALUES ('".$TitleID."','".$keyID."');";	
								if (!mysqli_query($con,$sql)) {
									die('Error: ' . mysqli_error($con));
								}
								$keyID=mysqli_insert_id($con);	
							
							}
						}
						
						//Now we need to clean up the orphaned keys
						
						//The following will return the indexes of the orphaned keys
						$sql="SELECT Res.Index FROM (SELECT tt_title_to_key.Title,tt_key.Index,tt_key.Key FROM tt_title_to_key RIGHT OUTER JOIN tt_key ON tt_title_to_key.Key = tt_key.Index) AS Res WHERE Res.Title IS NULL;";
						$result=mysqli_query($con,$sql);
						if (!$result) {
							die('Error: ' . mysqli_error($con));
						}
						
						//Now delete the orphaned keys
						while($row = mysqli_fetch_array($result)) {
							$sql="DELETE FROM tt_key WHERE `Index` = '".$row['Index']."'";	
							if (!mysqli_query($con,$sql)) {
								die('Error: ' . mysqli_error($con));
							}						
						}
						
						//Refresh loaded topic details
						LoadAllTopics();

						if($isAdd){
							//Re-fetch the Added Topic and show it
							FetchTitle($TitleID,$topics,$con);						
							ShowTitle();	
						}
						else{
							//Do forwarding!!!!
							header( "Location: ./?page=Edit&id=".$TitleID."&Updated") ;				
						}										
					}
					else{
						ErrorMesage();
					}
				}
				else{ //This should never happen due to this being an internal function. But I put the check to prevent hacking
					$params = explode("page=",(string)$_SERVER['QUERY_STRING']);
					header( "Location: ./?page=Login&Next=".$params[1]);
				}
			}			
			else if($page=='AddUser') //User interface for adding users
			{
				if(isset($_SESSION['Level'])){
					if($_SESSION['Level']==1){
						if(isset($_GET["PWMM"])){
							echo '<div class="alert alert-danger alert-dismissable">';
							echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
							echo '<strong>Warning!</strong> Passwords do not match!';
							echo '</div>';
						}
						echo '<form action="." class="form-signin" role="form" method="post">';
						echo '<input type="hidden" class="form-control" value="AddUserDB" name="page">';
						echo '<h4>Name</h4>';
						echo '<input type="text" class="form-control" placeholder="Full Name" name="Name" ';
						if(isset($_GET["Name"])){
							echo 'value ="'.$_GET["Name"].'"';
						}
						echo 'required autofocus>';
						echo '<h4>Email address (Used to login)</h4>';
						echo '<input type="text" class="form-control" placeholder="Email address" name="Email" ';
						if(isset($_GET["Email"])){
							echo 'value ="'.$_GET["Email"].'"';
						}
						echo 'required>';
						echo '<h4>Password</h4>';
						echo '<input type="password" class="form-control" placeholder="Password" name="Password" required>';
						echo '<h4>Confirm Password</h4>';
						echo '<input type="password" class="form-control" placeholder="Confirm Password" name="CPassword" required>';
						echo '<label class="checkbox">';
						echo '<input type="checkbox" name="admin"> Is administrator';
						echo '</label>';
						echo '<br><button type="submit" class="btn btn-success btn-lg"><span class="glyphicon glyphicon-ok-circle"></span> Add User</button></form>';
					}
					else{
						echo '<div class="alert alert-warning alert-dismissable">';
						echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
						echo 'You do not have admin powers!';
						echo '</div>';
					}
				}
				else{
					$params = explode("page=",(string)$_SERVER['QUERY_STRING']);
					header( "Location: ./?page=Login&Next=".$params[1]);
				}
			}
			else if($page=='AddUserDB') //Business logic to add user
			{
				if(isset($_SESSION['Level'])){
					if($_SESSION['Level']==1){
						if(isset($_POST["Name"]) && isset($_POST["Email"]) && isset($_POST["Password"])&& isset($_POST["CPassword"])){
							//Check if passwords match if not error
							if($_POST["Password"]==$_POST["CPassword"]){						
								$pass=CryptPass($_POST["Password"]);
								if(isset($_POST["admin"])){
									if($_POST["admin"]=='on'){
										$status=1;
									}
									else{
										$status=0;
									}
								}
								else{
										$status=0;
								}
								$sql="INSERT INTO `tt_members` (`Name`,`Email`,`Password`,`Level`) VALUES ('".$_POST["Name"]."','".$_POST["Email"]."','".$pass."','".$status."');";
								if (!mysqli_query($con,$sql)) {
									die('Error: ' . mysqli_error($con));
								}
								else{
									echo '<div class="alert alert-success alert-dismissable">';
									echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
									echo 'User '.$_POST["Name"].' added!';
									echo '</div>';
									RandomButton();
								}
							}
							else{
								header( "Location: ./?page=AddUser&Name=".$_POST["Name"]."&Email=".$_POST["Email"]."&PWMM") ;
							}
						}
					}
					else{
						echo '<div class="alert alert-warning alert-dismissable">';
						echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
						echo 'You do not have admin powers!';
						echo '</div>';
					}
				}
				else{
					$params = explode("page=",(string)$_SERVER['QUERY_STRING']);
					header( "Location: ./?page=Login&Next=".$params[1]);
				}				
			}
			
			function CryptPass($passW){
				global $key1,$key2;
				$hashed_password = crypt($key1,$key2);
				$pass=crypt($passW, $hashed_password);
				return $pass;
			}
			
			function ShowInputForm(){
				global $Title,$keys,$TitleID; //Refer to the global variables
			
				echo '<form action="." method="get">';
				echo '<h4 class="sub-header">Topic</h4>';
				echo '<input type="hidden" class="form-control" value="Update" name="page">';
				if($TitleID>0){
					echo '<input type="hidden" class="form-control" value="'.$TitleID.'" name="id">';
				}
				echo '<input type="text" class="form-control" value="'.$Title.'" name="topic" required autofocus>';
				echo '<h4 class="sub-header">Keys</h4>';
				$keyList="";
				if($TitleID>0){
					$keyList=$keys[0];
					for ($i = 1; $i < count($keys); $i++) {
						$keyList=$keyList.", ".$keys[$i];
					}	
				}				
				echo '<input type="text" class="form-control" value="'.$keyList.'" name="keys" required>';					
				echo '<br>';	
				echo '<button type="submit" class="btn btn-success btn-lg"><span class="glyphicon glyphicon-ok-circle"></span> ';
				if($TitleID>0){
					echo 'Update';
				}
				else{
					echo 'Add';
				}
				echo '</button> </form>';
			}
			
			
			function LoadAllTopics(){
				global $topics,$con;
				
				$result = mysqli_query($con,"SELECT * FROM tt_title");
				
				$topics =array($result->num_rows);
				while($row = mysqli_fetch_array($result))
				{
					$topics[$row['Index']] = $row['Topic'];
				}			
			}
			
			
			
			function ExtractDBwriterInput(){
			     global $Title,$keys,$TitleID; //Refer to the global variables
				
				$Title=$_GET["topic"];
				$keysString=$_GET["keys"];
				
					
				//KeyString cleaning
				$keysString=str_replace(array("(",")"," ","? "), "", $keysString);					
				$keysString=str_replace(array("?", ".", "'",",,"), "", $keysString);
				$keysString=str_replace("\n", "", $keysString);
					
				//Extract keys
				$keys = explode(",",(string)$keysString);
			}
			
			function ErrorMesage(){
				echo '<h2 class="sub-header">Invalid!</h2>';
				RandomButton();
			}
			
			function RandomButton(){
				echo '<a href="?page=Random"><button type="button" class="btn btn-lg btn-success">View a random topic!</button></a>';
			}
			
			function ShowTitle() {
				global $Title,$keys,$TitleID; //Refer to the global variables
				
				echo '<h2 class="sub-header">'.$Title;
				echo '<a href="?page=Edit&id='.$TitleID.'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-pencil"></span></button></a></h2>';
				print '<h4>Keys; </h4>';
				for ($i = 0; $i < count($keys); $i++) {
					echo '<a href="?page=Search&q='.$keys[$i].'"><button type="button" class="btn btn-sm btn-info">'.$keys[$i].'</button></a> ';
				}
			}
			
			
			function FetchTitle($Title_ID,$topics,$con) {
				global $Title,$keys; //Refer to the global variables
				
				$Title=$topics[$Title_ID];
				$keys=array();
				
				$sql="SELECT `tt_key`.`Key` FROM tt_title_to_key,tt_key WHERE `tt_title_to_key`.`Title` = '".$Title_ID."' AND`tt_title_to_key`.`Key` =`tt_key`.`Index`";
				$result =mysqli_query($con,$sql);
				if (!$result) {
					die('Error: ' . mysqli_error($con));
				}
				while($row = mysqli_fetch_array($result))
				{
					$keys[] = $row['Key'];
				}
			}		
			
			mysqli_close($con); //Close the MYSQL connector
		  ?>

        </div>
      </div>
    </div>
	
<!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="./bootstrap-3.1.1-dist/js/bootstrap.min.js"></script>
    <script src="./bootstrap-3.1.1-dist/js/docs.min.js"></script>
	
</body>