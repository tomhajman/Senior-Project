<!DOCTYPE html>
<html>
<head>
<title>View Quote</title>
<link rel="stylesheet" href="css/CustomerViewQuote.css">
</head>
<body>
	<header>
		<div class="dropdown">
			<button class="dropbtn">...</button>
			<div class="dropdown-content">
				<a href="#">Messages</a>
				<a href="#">Service History</a>
				<a href="Contractors.php">View Contractors</a>
				<a href="CustomerUpdatePage.php">Account Settings</a>
				<a href="CustomerLogin.php">Log Out</a>
			</div>
		</div>
		<div class="pageInfo">View Quote</div>
	</header>
<?php
	session_start();
	include 'DBCredentials.php';
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
		$id = $_GET['id'];
		$getQuoteInfo = "SELECT jobID, contractorName, quotePrice, quoteDate, estimatedCompletionDate, quoteDetails from jobQuote WHERE quoteID = '$id'";
		$result = $conn->query($getQuoteInfo);
		$record = mysqli_fetch_assoc($result);
		$getTitle = "SELECT jobTitle FROM customerJob WHERE jobID = {$record['jobID']}";
		$result2 = $conn->query($getTitle);
		$record2 = mysqli_fetch_assoc($result2);
		
		echo '<div class="quoteDetails">'.'<h2>'."Quote for: ".$record2['jobTitle'].'</h2>';
		echo "Contractor Name: ".$record['contractorName']."<br>";
		echo "Quote Price: $".$record['quotePrice']."<br>";
		echo "Quote Date: ".$record['quoteDate']."<br>";
		echo "Estimated Completion Date: ".$record['estimatedCompletionDate']."<br><br>";
		echo "Additional Details: ".$record['quoteDetails']."<br><br><br>";
	}
?>
	<form action="#" method="post">
		<button id="acceptQuote" name="acceptQuote" type="submit">Accept Quote</button>
		<button id="rejectQuote" name="rejectQuote" type="button">Reject Quote</button>
	</form>
	<form action="#" method="post">
		<input type="hidden" name="jobIDforConversation" value=<?php echo "'{$id}'"; ?>>
		<button type="submit" id="info">Message Contractor</button>
	</form>

<?php		
		
	if (isset($_POST['acceptQuote'])) {
		//set jobStatus to In Progress
		$updateStatus = "UPDATE customerJob SET jobStatus = 'In Progress' WHERE jobID = {$record['jobID']}";
		$conn->query($updateStatus);
		header("Location: CustomerManageJobs.php");
		exit();
	}
	
	if (isset($_POST['rejectQuote'])) {
		//Should I drop the quote from the jobQuotes table if rejected?
		header("Location: viewQuotes.php");
		exit();
	}
?>

</body>
</html>