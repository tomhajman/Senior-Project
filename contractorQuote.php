<!DOCTYPE html>
<html>
<head>
    <title>Send Quote</title>
	 <link rel="stylesheet" href="css/ConQuote.css">
</head>

<body>
		
		<?php
			session_start();
			include 'DBCredentials.php';
			if(isset($_SESSION['contractorEmail'])){
				$userEmail = $_SESSION['contractorEmail'];
			  } else {
				header("Location: ContractorLogin.php?redirect=authFail");
				exit();
			  }
			$selectedJobTitle = $_SESSION['jobTitle'];
			$jobID = $_SESSION['quoteJobID'];
			
			function connectToDB() {
					global $HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME, $conn;
						$conn = new mysqli($HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME);
						
						if ($conn->connect_error) {
							die("Connection issue: ".$conn->connect_error);
						}			
					return $conn;
				}

			$conn = connectToDB();
			$getContractorInfo = $conn->prepare("SELECT contractorName, contractorID FROM contractor WHERE contractorEmail = ?");
			$getContractorInfo->bind_param("s", $userEmail);
			$getContractorInfo->execute();
			$result = $getContractorInfo->get_result();
			if ($result->num_rows > 0) {
				$row = $result->fetch_assoc();
				$contractorName = $row['contractorName'];
				$contractorID = $row['contractorID'];
			} else {
				$contractorName = "N/A";
				$contractorID = "N/A";
			}
			
			$quoteDate = date("Y-m-d");
			
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				$quoteCost = $_POST['quoteCost'];
				$quoteCompletionDate = $_POST['quoteCompletionDate'];
				$quoteDetails = $_POST['quoteDetails'];
				
				if (empty($quoteCompletionDate) || strtotime($quoteCompletionDate) < time())
					die("Invalid completion date");
 			
			
				$stmt = $conn->prepare("INSERT INTO jobQuote (jobID, contractorID, contractorName, quotePrice, quoteDate, estimatedCompletionDate, quoteDetails) VALUES (?, ?, ?, ?, ?, ?, ?)");
				$stmt-> bind_param("iisisss", $jobID, $contractorID, $contractorName, $quoteCost, $quoteDate, $quoteCompletionDate, $quoteDetails);
				if ($stmt->execute())
					echo "Quote sent successfully";
				else
					echo "Error: ".$stmt->error;
			
				$conn->close();
			}
		?>
		
	<div class="container">
		<h1>Send Quote for <?php echo $selectedJobTitle ?></h1>

		<form action="" method="post" enctype="multipart/form-data">
			<label for="quoteCost">Estimated Cost:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$</label>
			<input type="number" id="quoteCost" name="quoteCost" min="1">
			<br><br>
			
			
			<label for="quoteCompletionDate">Estimated Completion Date:</label>
			<input type="date" id="quoteCompletionDate" name="quoteCompletionDate"  value="<?php echo date('d-m-Y');?>" min="<?php echo date('d-m-Y');?>">
			
			<br><br>
			<label for="quoteDetails">Additional Details:</label>
			<textarea id="quoteDetails" name="quoteDetails" rows="5" cols="78">Include important information here.</textarea>
			
			<br><br>
			<input type="submit" value="Submit Quote">			
			
		</form>
			<button onclick ="window.location.href = 'AvailableJobs.php';">Return to Job Listings</button>
		
			
	</div>

</body>
</html>