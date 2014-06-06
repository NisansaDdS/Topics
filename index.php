<head>

<!-- Bootstrap core CSS -->
    <link href="./bootstrap-3.1.1-dist/css/bootstrap.css" rel="stylesheet">

<!-- Custom styles for this template -->
    <link href="./bootstrap-3.1.1-dist/css/dashboard.css" rel="stylesheet">
	
<!-- Bootstrap theme -->
    <link href="./bootstrap-3.1.1-dist/css/bootstrap-theme.css" rel="stylesheet">
	
	
	<style type="text/css">
		.title-container { padding:50px 0px; }
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
            <li><a href="#">Nisansa de Silva</a></li>
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
				if($page=='Add'){
					echo "<li class='active'>";
				}
				else{
					echo "<li>";
				}				
			?>
			<a href="?page=Add">Add Topic</a></li>
            <?php
				if($page=='Edit'){
					echo "<li class='active'>";
				}
				else{
					echo "<li>";
				}				
			?>
			<a href="?page=Edit">Edit Topics</a></li>
            <?php
				if($page=='Search'){
					echo "<li class='active'>";
				}
				else{
					echo "<li>";
				}				
			?>
			<a href="?page=Search">Search</a></li>
          </ul>          
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
       <!--    <h1 class="page-header">Dashboard</h1>	 -->	  
      <!--    <div class="row placeholders">
            <div class="col-xs-6 col-sm-3 placeholder">
              <img data-src="holder.js/200x200/auto/sky" class="img-responsive" alt="Generic placeholder thumbnail">
              <h4>Label</h4>
              <span class="text-muted">Something else</span>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
              <img data-src="holder.js/200x200/auto/vine" class="img-responsive" alt="Generic placeholder thumbnail">
              <h4>Label</h4>
              <span class="text-muted">Something else</span>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
              <img data-src="holder.js/200x200/auto/sky" class="img-responsive" alt="Generic placeholder thumbnail">
              <h4>Label</h4>
              <span class="text-muted">Something else</span>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
              <img data-src="holder.js/200x200/auto/vine" class="img-responsive" alt="Generic placeholder thumbnail">
              <h4>Label</h4>
              <span class="text-muted">Something else</span>
            </div>
          </div>  -->
		  <?php
		  
			

			
			$con=mysqli_connect("127.0.0.1","toastadmin","T0aST$#U0M","toastmastersdb");

			if (mysqli_connect_errno()) {
			  echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}
			$result = mysqli_query($con,"SELECT * FROM TT_Title");
			
			$topics =array($result->num_rows);
			while($row = mysqli_fetch_array($result))
			{
				$topics[$row['Index']] = $row['Topic'];
			}
			//echo $topics[2];
			
			$TitleID=0;
			$Title="";
			$keys=array();
			
			if($page=='Show'){
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
			else if($page=='Edit'){
				if (isset($_GET["id"]))
				{
					$TitleID=$_GET["id"];
					FetchTitle($TitleID,$topics,$con);	
					echo '<h2 class="sub-header">Edit Topic</h2><br>';
					echo '<h4 class="sub-header">Topic</h4>';
					echo '<input type="text" class="form-control" value="'.$Title.'" required autofocus>';
					echo '<h4 class="sub-header">Keys</h4>';
					echo '<input type="text" class="form-control" value="'.$Title.'">';
				}
				else{					
					ErrorMesage();
				}
			}
			
			
			function ErrorMesage(){
				echo '<h2 class="sub-header">Invalid!</h2>';
				echo '<a href="?page=Random"><button type="button" class="btn btn-lg btn-success">View a random topic!</button></a>';
			}
			
			function ShowTitle() {
				global $Title,$keys,$TitleID; //Refer to the global variables
				
				echo '<h2 class="sub-header">'.$Title;
				echo '<a href="?page=Edit&id='.$TitleID.'"><button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-pencil"></span></button></a></h2>';
				print '<h4>Keys; </h4>';
				for ($i = 0; $i < count($keys); $i++) {
					echo '<a href="?page=Search&q='.$keys[$i].'"><button type="button" class="btn btn-lg btn-info">'.$keys[$i].'</button></a>';
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
			
			
		  ?>

         
		  
		  <!--
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Header</th>
                  <th>Header</th>
                  <th>Header</th>
                  <th>Header</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1,001</td>
                  <td>Lorem</td>
                  <td>ipsum</td>
                  <td>dolor</td>
                  <td>sit</td>
                </tr>
                <tr>
                  <td>1,002</td>
                  <td>amet</td>
                  <td>consectetur</td>
                  <td>adipiscing</td>
                  <td>elit</td>
                </tr>
                <tr>
                  <td>1,003</td>
                  <td>Integer</td>
                  <td>nec</td>
                  <td>odio</td>
                  <td>Praesent</td>
                </tr>
                <tr>
                  <td>1,003</td>
                  <td>libero</td>
                  <td>Sed</td>
                  <td>cursus</td>
                  <td>ante</td>
                </tr>
                <tr>
                  <td>1,004</td>
                  <td>dapibus</td>
                  <td>diam</td>
                  <td>Sed</td>
                  <td>nisi</td>
                </tr>
                <tr>
                  <td>1,005</td>
                  <td>Nulla</td>
                  <td>quis</td>
                  <td>sem</td>
                  <td>at</td>
                </tr>
                <tr>
                  <td>1,006</td>
                  <td>nibh</td>
                  <td>elementum</td>
                  <td>imperdiet</td>
                  <td>Duis</td>
                </tr>
                <tr>
                  <td>1,007</td>
                  <td>sagittis</td>
                  <td>ipsum</td>
                  <td>Praesent</td>
                  <td>mauris</td>
                </tr>
                <tr>
                  <td>1,008</td>
                  <td>Fusce</td>
                  <td>nec</td>
                  <td>tellus</td>
                  <td>sed</td>
                </tr>
                <tr>
                  <td>1,009</td>
                  <td>augue</td>
                  <td>semper</td>
                  <td>porta</td>
                  <td>Mauris</td>
                </tr>
                <tr>
                  <td>1,010</td>
                  <td>massa</td>
                  <td>Vestibulum</td>
                  <td>lacinia</td>
                  <td>arcu</td>
                </tr>
                <tr>
                  <td>1,011</td>
                  <td>eget</td>
                  <td>nulla</td>
                  <td>Class</td>
                  <td>aptent</td>
                </tr>
                <tr>
                  <td>1,012</td>
                  <td>taciti</td>
                  <td>sociosqu</td>
                  <td>ad</td>
                  <td>litora</td>
                </tr>
                <tr>
                  <td>1,013</td>
                  <td>torquent</td>
                  <td>per</td>
                  <td>conubia</td>
                  <td>nostra</td>
                </tr>
                <tr>
                  <td>1,014</td>
                  <td>per</td>
                  <td>inceptos</td>
                  <td>himenaeos</td>
                  <td>Curabitur</td>
                </tr>
                <tr>
                  <td>1,015</td>
                  <td>sodales</td>
                  <td>ligula</td>
                  <td>in</td>
                  <td>libero</td>
                </tr>
              </tbody>
            </table>
          </div>
		  -->
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