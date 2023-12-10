<!DOCTYPE html>
<html>
<head>
<title>Job Details</title>
<link rel="stylesheet" href="css/JobDetails.css">
</head>
<body>
  	<?php
		//Connecting DB, passing contractorEmail, can later change this to boot user to login page if they are not signed in. For now will just throw an error.
		session_start();

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
        die("Connection issue: " . $conn->connect_error);
    }
    return $conn;
}

$db = connectToDB();

$getNameQuery = "SELECT contractorName FROM contractor WHERE contractorEmail = '$userEmail'";
$result = $db->query($getNameQuery);

if ($result) {
    $row = $result->fetch_assoc();
    $userName = $row['contractorName'];
} else {
    $userName = "Contractor";
}
	?>
	
	<header>
	<div class="dropdown">
            <button class="dropbtn">...</button>
            <div class="dropdown-content">
				<a href="CustomerPage.php">Home</a>
                <a href="ContractorMessageCenter.php">Messages</a>
                <a href="AvailableJobs.php">Available Jobs</a>
                <a href="RecentJobs.php">Job History</a>
                <a href="ViewRatings.php">View Ratings</a>
                <a href="ContractorUpdatePage.php">Account Settings</a>
                <a href="Logout.php">Log Out</a>
            </div>
    </div>
	<div class="pageInfo">View Listing Information</div> 
</header>
	
	<?php
	if (isset($_GET['id'])) {
		$conn = connectToDB();
		$id = $_GET['id'];
		$getJobInfo = "SELECT jobType, jobTitle, jobDescription, jobCounty, jobCity, jobAddress, jobUrgency, customerLastName, customerID FROM customerJob WHERE jobID = '$id'";
		$result = $conn->query($getJobInfo);
		$record = mysqli_fetch_assoc($result);
		//Pull customerID from customerJob table to later use to gather info from customer table
		$custID = $record['customerID'];
		
		$getCustInfo = "SELECT customerFirstName, customerEmail, customerPhoneNumber FROM customer WHERE customerID= {$record['customerID']}";
		$result2 = $conn->query($getCustInfo);
		$record2 = mysqli_fetch_assoc($result2);
		
		//Retrieve cover photo
		$getCoverPicture = $conn->query("SELECT id FROM jobImages WHERE jobID=$id AND isCover = 1 ");
		$getID = $getCoverPicture->fetch_assoc();
		
		//Retrieve other photos
		$getPhotos = $conn->query("SELECT id FROM jobImages WHERE jobID=$id AND isCover != 1");
		
		echo '<div class="jobDetails">'.'<h2>'.$record['jobTitle'].'</h1>';
		echo "<img src='jobImage.php?id={$getID['id']}' width='300px' height='120px' alt='Database Image'><br>";
		echo "Customer Name: ".$record2['customerFirstName']." ".$record['customerLastName']."<br>";
		echo "Customer Email: ".$record2['customerEmail']."<br>";
        echo "Customer Phone Number: ".$record2['customerPhoneNumber']."<br>";
		echo "Job Address: ".$record['jobAddress'].', '.$record['jobCity'].', '.$record['jobCounty']."<br>";
		echo "Job Urgency: ".$record['jobUrgency']."<br><br>";
		echo "Job Description: ".$record['jobDescription']."<br>";
		echo '</div>';
		echo '<div class="jobImages">';
		while ($imgs = $getPhotos->fetch_assoc()) {
			echo "<img src='jobImage.php?id={$imgs['id']}' width='400px' height='200px' alt='Database Image'>";
		} 
		echo '</div>';
		
	} else {
		echo "Error retrieving job info.";
	}
	
	$_SESSION['jobTitle'] = $record['jobTitle']; 
	$_SESSION['quoteJobID'] = $id;

	if(isset($_POST['jobIDforConversation']) && is_numeric($_POST['jobIDforConversation'])){
		$jobIDforConversation = $_POST['jobIDforConversation'];
		
		$findConversation = $conn->prepare("SELECT * FROM conversations WHERE jobID=? AND contractorEmail=?");
		$findConversation->bind_param("is", $jobIDforConversation, $userEmail);
		if($findConversation->execute()){
			$conversation = $findConversation->get_result();
			$findConversation->close();
			if($conversation->num_rows > 0){
				$row = $conversation->fetch_assoc();
				header("Location: ContractorConversation.php?id={$row['conversationID']}");
				exit();
			} else {
				$createConversation = $conn->prepare("INSERT INTO conversations (customerEmail, contractorEmail, jobID) VALUES (?, ?, ?)");
				$createConversation->bind_param("ssi", $record2['customerEmail'], $userEmail, $jobIDforConversation);
				if($createConversation->execute()){
					$lastInsertedId = mysqli_insert_id($conn);
					header("Location: ContractorConversation.php?id={$lastInsertedId}");
					exit();
				} else {
					echo "Something went wrong, try again later";
				}
			}
		}
	}
	
	?>
	
	<form action="#" method="post">
		<button id="quote" type="button" onclick="window.location.href = 'contractorQuote.php';">Send Quote</button>
	</form>
	<form action="#" method="post">
		<input type="hidden" name="jobIDforConversation" value=<?php echo "'{$id}'"; ?>>
		<button type="submit" id="info">Request More Information</button>
	</form>
	
</body>
</html>