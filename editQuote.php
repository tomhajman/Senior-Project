<?php
	session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/EditQuote.css">
    <title>Edit job</title>
	
</head>
<body>
	<?php
		include 'DBCredentials.php';
		date_default_timezone_set('America/New_York');
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
		
		$conn = connectToDB();
		
		if(isset($_GET['id']) && is_numeric($_GET['id'])){
            $stmt = $conn->prepare("SELECT quotePrice, estimatedCompletionDate, quoteDetails, contractorID, jobID FROM jobQuote WHERE quoteID=?");
            $stmt->bind_param("i", $_GET['id']);
            if(!($stmt->execute())){
                die("quote with ID = {$_GET['id']} doesn't exist");
            }
            $result = $stmt->get_result();
            $oldInfo = $result->fetch_assoc();
            $stmt->close();
			$jobTitleQuery = $conn->prepare("SELECT jobTitle FROM customerJob WHERE jobID = ?");
			$jobTitleQuery->bind_param("i", $oldInfo['jobID']);
			$jobTitleQuery->execute();
			$result = $jobTitleQuery->get_result();
			$row = $result->fetch_assoc();
			$jobTitle = $row['jobTitle'];
			
			$checkAccess = $conn->prepare("SELECT contractorEmail FROM contractor WHERE contractorID=?");
            $checkAccess->bind_param("i", $oldInfo['contractorID']);
            if($checkAccess->execute()){
                $checkAccess->bind_result($dbEmail);
                $checkAccess->fetch();
                if($dbEmail != $userEmail){
                    header("Location: ContractorManageJobs.php?redirect=accessDenied");
                    exit();
                }
            } else {
                die("Error connecting to database: ". $conn->error);
            }
        } else {
            header("Location: ContractorManageJobs.php?redirect=notFound");
            exit();
        }
		
		//Sanitize only old text input
		$details = htmlspecialchars($oldInfo['quoteDetails']);
		
		//Form submission and error handling
		$errors = [];
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$quotePrice = $_POST['quotePrice'];
			$estimatedCompletionDate = $_POST['estimatedCompletionDate'];
			$quoteDetails = $_POST['quoteDetails'];
			$quoteDate = date("Y-m-d");
			
			if (empty($estimatedCompletionDate) || strtotime($estimatedCompletionDate) < time()) {
				echo "<script>alert('Invalid completion date.');</script>";
				$errors['estimatedCompletionDate'] = "Invalid date";
			}
			
			if (empty($errors)) {
				$conn = connectToDB();
				
				//Update quote data in DB
				$stmt = $conn->prepare("UPDATE jobQuote SET quotePrice=?, quoteDate=?, estimatedCompletionDate=?, quoteDetails=? WHERE quoteID=?");
				$stmt->bind_param("dsssi", $quotePrice, $quoteDate, $estimatedCompletionDate, $quoteDetails, $_GET['id']);
				
				if ($stmt->execute()) {
					echo "<script>alert('Quote updated successfully.');</script>";
					echo "<script>window.location='editQuote.php?id={$_GET['id']}'</script>";
					//echo "Quote updated successfully";
				} else {
					echo "<script>alert('Error'" . $stmt->error . ");</script>";
					//echo "Error: " . $stmt->error;
				}
				$stmt->close();
				
				$conn->close();
			}
		}
		
	?>
	
	<header>
		<div class="jobTitle"><h1>Edit Quote for <?php echo $jobTitle ?></h1></div>
	</header>
	<div class="container">
		<form action="" method="post">
			<label for="quotePrice">Estimated Cost:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$</label>
			<input type="number" id="quotePrice" name="quotePrice" min="1" value=<?php echo $oldInfo['quotePrice']?>>
			<br><br>			
			
			<label for="estimatedCompletionDate">Estimated Completion Date:</label>
			<input type="date" id="estimatedCompletionDate" name="estimatedCompletionDate"  value="<?php echo $oldInfo['estimatedCompletionDate'];?>" min="<?php echo date('d-m-Y');?>">
			
			<br><br>
			<label for="quoteDetails">Additional Details:</label>
			<textarea id="quoteDetails" name="quoteDetails" rows="5" cols="78"><?php echo $details; ?></textarea>
			
			<br><br>
			<button type="submit">Update Quote</button>
			<a href="ContractorManageJobs.php">Return to Manage Jobs page</a>
		</form>
	</div>
</body>
</html> 