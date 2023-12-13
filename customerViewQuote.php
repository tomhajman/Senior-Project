<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>View Quote</title>
 <link rel="stylesheet" href="css/CusViewQuote.css">

</head>
<body>
	<header>
		<div class="dropdown">
			<button class="dropbtn">...</button>
			<div class="dropdown-content">
			<a href="CustomerPage.php">Home</a>
			<a href="CustomerMessageCenter.php">Messages</a>
			<a href="CustomerManageJobs.php">Service History</a>
            <a href="Contractors.php">View Contractors</a>
            <a href="CustomerUpdatePage.php">Account Settings</a>
            <a href="Logout.php">Log Out</a>
			</div>
		</div>
		<div class="pageInfo">View Quote</div>
	</header>
<?php
	//DB connection and session handling.
	include 'DBCredentials.php';
	date_default_timezone_set('America/New_York');
	if(isset($_SESSION['customerEmail'])){
		$userEmail = $_SESSION['customerEmail'];
	  } else {
		header("Location: CustomerLogin.php?redirect=authFail");
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
		
	if (isset($_GET['id'])) {
		$conn = connectToDB();
		//Gathers data needed to display quote information.
		$currentDate = date("Y-m-d");
		$id = $_GET['id'];
		$getQuoteInfo = "SELECT jobID, contractorName, quotePrice, quoteDate, estimatedCompletionDate, quoteDetails from jobQuote WHERE quoteID = '$id'";
		$result = $conn->query($getQuoteInfo);
		$record = mysqli_fetch_assoc($result);
		$getTitle = "SELECT jobTitle FROM customerJob WHERE jobID = {$record['jobID']}";
		$result2 = $conn->query($getTitle);
		$record2 = mysqli_fetch_assoc($result2);
		$contractorName = $record['contractorName'];
		$getContractorInfo = "SELECT contractorID, contractorEmail FROM contractor WHERE contractorName = ?";
		$stmt = $conn->prepare($getContractorInfo);
		$stmt->bind_param("s", $contractorName);
		$stmt->execute();
		$result3 = $stmt->get_result();
		$record3 = mysqli_fetch_assoc($result3);
		echo '<div class="quoteDetails">'.'<h2>'."Quote for: ".$record2['jobTitle'].'</h2>';
		echo "Contractor Name: ".$record['contractorName']."<br>";
		echo "Quote Price: $".$record['quotePrice']."<br>";
		echo "Quote Date: ".$record['quoteDate']."<br>";
		echo "Estimated Completion Date: ".$record['estimatedCompletionDate']."<br><br>";
		echo "Additional Details: ".$record['quoteDetails']."<br><br><br>";
		//Information for conversation.
		if(isset($_POST['jobIDforConversation']) && is_numeric($_POST['jobIDforConversation'])){
		$jobIDforConversation = $_POST['jobIDforConversation'];
		
		$findConversation = $conn->prepare("SELECT * FROM conversations WHERE jobID=? AND customerEmail=? AND contractorEmail=?");
		$findConversation->bind_param("iss", $jobIDforConversation, $userEmail, $record3['contractorEmail']);
		if($findConversation->execute()){
			$conversation = $findConversation->get_result();
			$findConversation->close();
			if($conversation->num_rows > 0){
				$row = $conversation->fetch_assoc();
				header("Location: CustomerConversation.php?id={$row['conversationID']}");
				exit();
			} else {
				$createConversation = $conn->prepare("INSERT INTO conversations (customerEmail, contractorEmail, jobID) VALUES (?, ?, ?)");
				$createConversation->bind_param("ssi", $userEmail, $record3['contractorEmail'], $jobIDforConversation);
				if($createConversation->execute()){
					$lastInsertedId = mysqli_insert_id($conn);
					header("Location: CustomerConversation.php?id={$lastInsertedId}");
					exit();
				} else {
					echo "Something went wrong, try again later";
					echo $jobIDforConversation;
				}
			}
		}
	}
	}
	
	
?>
	<form action="#" method="post">
		<button id="acceptQuote" name="acceptQuote" type="submit">Accept Quote</button>
		<button id="rejectQuote" name="rejectQuote" type="submit">Reject Quote</button>
	</form>
	<form action="#" method="post">
		<input type="hidden" name="jobIDforConversation" value=<?php echo "'{$record['jobID']}'"; ?>>
		<button type="submit" id="info">Message Contractor</button>
	</form>

<?php		
		
	if (isset($_POST['acceptQuote'])) {
		//Check if estimatedCompletionDate has passed, tell user to message contractor to update quote prior to acceptance.
		if ($currentDate > $record['estimatedCompletionDate']) {
			echo "<script>alert('Estimated Completion Date has past. Please contact Contractor and request that they update their quote before accepting.')</script>";
			exit();
		}
		//set jobStatus to In Progress and assign contractorID.
		$updateStatus = "UPDATE customerJob SET jobStatus = 'In Progress', contractorID = {$record3['contractorID']}, jobPrice = {$record['quotePrice']} WHERE jobID = {$record['jobID']}";
		$conn->query($updateStatus);
		header("Location: CustomerManageJobs.php");
		exit();
	}
	
	//Deletes the quote if customer declines.
	if (isset($_POST['rejectQuote'])) {
		$deleteQuery = "DELETE FROM jobQuote WHERE quoteID = '$id'";
		$result = $conn->query($deleteQuery);
		if ($result) {
		header("Location: viewQuotes.php?id={$record['jobID']}");
		exit();
		}
		else 
			echo "Error deleting quote" . $conn->error;
	}
?>

</body>
</html>