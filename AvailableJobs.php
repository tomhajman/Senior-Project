<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Jobs</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
body,h1,h2,h3,h4,h5,h6 {font-family: "Raleway", sans-serif}
table {
	border-collapse: collapse;
	width: auto;
}
td {
	width: 160px;
	height: 90px;
	text-align: center;
}
tr:nth-child(even) {background-color: #b4cbed;}
</style>
</head>
<body class="w3-light-grey w3-content" style="max-width:1600px">

<!-- Sidebar/menu -->
<nav class="w3-sidebar w3-collapse w3-white w3-animate-left" style="z-index:3;width:150px;" id="mySidebar"><br>
  <div class="w3-container">
    <a href="#" onclick="w3_close()" class="w3-hide-large w3-right w3-jumbo w3-padding w3-hover-grey" title="close menu">
      <i class="fa fa-remove"></i>
    </a>
  
    
  </div>
  <div class="w3-bar-block">
    <a href="AvailableJobs.php" onclick="w3_close()" class="w3-bar-item w3-button w3-padding w3-text-teal"><i class="fa fa-briefcase fa-fw w3-margin-right"></i>AVAILABLE JOBS</a> 
    <a href="ContractorPage.php" onclick="w3_close()" class="w3-bar-item w3-button w3-padding"><i class="fa fa-user fa-fw w3-margin-right"></i>Home</a> 
  </div>
  
</nav>

<!-- Overlay effect when opening sidebar on small screens -->
<div class="w3-overlay w3-hide-large w3-animate-opacity" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>

<!-- !PAGE CONTENT! -->
<div class="w3-main" style="margin-left:150px">

  <!-- Header -->
  <header id="jobs">
    <a href="#"><img src="/w3images/avatar_g2.jpg" style="width:65px;" class="w3-circle w3-right w3-margin w3-hide-large w3-hover-opacity"></a>
    <span class="w3-button w3-hide-large w3-xxlarge w3-hover-text-grey" onclick="w3_open()"><i class="fa fa-bars"></i></span>
    <div class="w3-container">
    <h1><b>Available Jobs</b></h1>
    <div class="w3-section w3-bottombar w3-padding-16">
	<form class="modal-content" action="" method="post"
	 <input type="text" placeholder="Search Jobs" id="searchJobs" class="w3-input">
      <span class="w3-margin-right">Filter:</span> 
	  <!--Buttons are assigned values which are used to filter the page to requested job type-->
      <button class="w3-button w3-black" name="filterBtn" value="allJobsBtn">ALL</button>
      <button class="w3-button w3-white" name="filterBtn" value="Electrician"><i class="fa fa-wrench w3-margin-right"></i>Electrical</button>
      <button class="w3-button w3-white" name="filterBtn" value="Plumbing"><i class="fa fa-wrench w3-margin-right"></i>Plumbing</button>
      <button class="w3-button w3-white" name="filterBtn" value="Gardening"><i class="fa fa-leaf w3-margin-right"></i>Gardening</button>
      <button class="w3-button w3-white" name="filterBtn" value="HVAC"><i class="fa fa-asterisk w3-margin-right"></i>HVAC</button>
      <button class="w3-button w3-white" name="filterBtn" value="Painting"><i class="fa fa-paint-brush w3-margin-right"></i>Painting</button>
      <button class="w3-button w3-white" name="filterBtn" value="Mounting"><i class="fa fa-wrench w3-margin-right"></i>Mounting</button>
	 </form>
    </div>
    </div>
  </header>

  <!-- Rest of the content... -->
  	<?php
		//Connecting DB, passing contractorEmail, can later change this to boot user to login page if they are not signed in. For now will just throw an error.
		include 'DBCredentials.php';
		if(isset($_SESSION['contractorEmail'])){
			$userEmail = $_SESSION['contractorEmail'];
		  } else {
			header("Location: ContractorLogin.php?redirect=authFail");
			exit();
		  }
		function connectToDB() {
			global $HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME, $conn;
				$conn = new mysqli($HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME);
				
				if ($conn->connect_error) {
					die("Connection issue: ".$conn->connect_error);
				}			
			return $conn;
		}
		//Leaving this here if you guys can find something more efficient.
		/*$db = connectToDB();
		$getJobInfo = "SELECT jobType, jobTitle, jobDescription, jobPrice, jobCounty, jobCity, jobAddress, customerLastName FROM customerJob";
		$result = $db->query($getJobInfo);*/
		
		//Functions that will be called which will be attatched to the filters in the form, will find and retrieve jobs based on category.
		//Condensed the several functions down into just allJobs and jobTypeFilter, where instead of manually searching for each jobType, it just reads the variable which has the jobType assigned to it.
		//Fixed output to be in proper table format.
		function allJobs() {
			$db = connectToDB();
			$getJobInfo = "SELECT jobID, jobType, jobTitle, jobCounty, jobCity, jobAddress, jobUrgency, customerLastName FROM customerJob WHERE jobStatus = 'Pending'";
			$result = $db->query($getJobInfo);
			
			if (mysqli_num_rows($result) > 0) {
			echo '<table>';
			echo '<tr>';
			echo '<th></th>';
			echo '<th>Job Type</th>';
			echo '<th>Job Title</th>';
			echo '<th>Job County</th>';
			echo '<th>Job City</th>';
			echo '<th>Job Address</th>';
			echo '<th>Job Urgency</th>';
			echo '<th>Customer Last Name</th>';
			echo '</tr>';
			while ($record = mysqli_fetch_assoc($result)) {
				//Retrieve cover photo
				$getCoverPicture = $db->query("SELECT id FROM jobImages WHERE jobID='{$record['jobID']}' AND isCover = 1 ");
				$getID = $getCoverPicture->fetch_assoc();
				
				echo '<tr>';
				echo "<td><img src='jobImage.php?id={$getID['id']}' width='160px' height='90px' alt='Database Image'></td>";
				echo '<td>'.$record['jobType'].'</td>';
				echo '<td>'.$record['jobTitle'].'</td>';
				echo '<td>'.$record['jobCounty'].'</td>';
				echo '<td>'.$record['jobCity'].'</td>';
				echo '<td>'.$record['jobAddress'].'</td>';
				echo '<td>'.$record['jobUrgency'].'</td>';
				echo '<td>'.$record['customerLastName'].'</td>';
				echo '<td><a href="jobDetails.php?id='.$record['jobID'].'">View Listing</a></td>';
				echo '<input type="hidden" name="jobID" value="'.$record['jobID'].'">';
				echo '</tr>';
			}
		} else
			echo "NO RECORDS";
		}
		
		function jobTypeFilter($filter) {
			$db = connectToDB();
			$getJobInfo = "SELECT jobID, jobType, jobTitle, jobCounty, jobCity, jobAddress, jobUrgency, customerLastName FROM customerJob WHERE jobType= '$filter' AND jobStatus = 'Pending'";
			$result = $db->query($getJobInfo);
			
			if (mysqli_num_rows($result) > 0) {
			echo '<table>';
			echo '<tr>';
			echo '<th></th>';
			echo '<th>Job Type</th>';
			echo '<th>Job Title</th>';
			echo '<th>Job County</th>';
			echo '<th>Job City</th>';
			echo '<th>Job Address</th>';
			echo '<th>Job Urgency</th>';
			echo '<th>Customer Last Name</th>';
			echo '</tr>';
			while ($record = mysqli_fetch_assoc($result)) {				
				//Retrieve cover photo
				$getCoverPicture = $db->query("SELECT id FROM jobImages WHERE jobID='{$record['jobID']}' AND isCover = 1 ");
				$getID = $getCoverPicture->fetch_assoc();
				
				echo '<tr>';
				echo "<td><img src='jobImage.php?id={$getID['id']}' width='160px' height='90px' alt='Database Image'></td>";
				echo '<td>'.$record['jobType'].'</td>';
				echo '<td>'.$record['jobTitle'].'</td>';
				echo '<td>'.$record['jobCounty'].'</td>';
				echo '<td>'.$record['jobCity'].'</td>';
				echo '<td>'.$record['jobAddress'].'</td>';
				echo '<td>'.$record['jobUrgency'].'</td>';
				echo '<td>'.$record['customerLastName'].'</td>';
				echo '<td><a href="jobDetails.php?id='.$record['jobID'].'">View Listing</a></td>';
				echo '<input type="hidden" name="jobID" value="'.$record['jobID'].'">';
				echo '</tr>';
			}
		} else
			echo "NO RECORDS";
		}
		
		//Checks if filterBtn is checked, displayed requested jobs based on type using above functions in switch statement
		if (isset($_POST['filterBtn'])) {
			$filter = $_POST['filterBtn'];
			switch($filter){
				case 'allJobsBtn':
					allJobs();
					break;
				case 'Electrician':
					jobTypeFilter($filter);
					break;
				case 'Plumbing':
					jobTypeFilter($filter);
					break;
				case 'Gardening':
					jobTypeFilter($filter);
					break;
				case 'HVAC':
					jobTypeFilter($filter);
					break;
				case 'Painting':
					jobTypeFilter($filter);
					break;
				case 'Mounting':
					jobTypeFilter($filter);
					break;
				default:
					allJobs();
			}
		} else {
			allJobs();
		}
		
	?>

</div>

<script>
// Script to open and close sidebar
function w3_open() {
    document.getElementById("mySidebar").style.display = "block";
    document.getElementById("myOverlay").style.display = "block";
}
function w3_close() {
    document.getElementById("mySidebar").style.display = "none";
    document.getElementById("myOverlay").style.display = "none";
}
</script>

</body>
</html>
