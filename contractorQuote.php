<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Send Quote</title>
	 <link rel="stylesheet" href="css/ConQuote.css">
	 <!--Needed to put this here, wouldn't work in dedicated style page for some reason-->
	 <style>
	 header {
            background-color: #333;
            color: #fff;
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
		.dropdown {
            position: absolute;
            left: 0;
        }

        .dropbtn {
            background-color: #333;
            color: #fff;
            padding: 16px;
            font-size: 24px;
            border: none;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #333;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            left: 0; /* Start from the left */
            top: 100%;
            z-index: 1;
        }

        .dropdown-content a {
            color: gray;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left; /* Align the menu items to the left */
        }

        .dropdown-content a:hover {
            background-color: #ddd;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown:hover .dropbtn {
            background-color: #3e8e41;
        }        
		.pageInfo {
			margin-right: 10px;
			margin-left: auto;
		}
	 </style>
</head>

<body>		
	<header>
		<div class="dropdown">
				<button class="dropbtn">...</button>
				<div class="dropdown-content">
					<a href="ContractorPage.php">Home</a>
					<a href="ContractorMessageCenter.php">Messages</a>
					<a href="AvailableJobs.php">Available Jobs</a>
					<a href="ContractorManageJobs.php">Job History</a>
					<a href="ViewRatings.php">View Ratings</a>
					<a href="ContractorUpdatePage.php">Account Settings</a>
					<a href="Logout.php">Log Out</a>
				</div>
		</div>
		<div class="pageInfo">Send Quote</div> 
	</header>
		<?php
			//DB connection, session handling.
			include 'DBCredentials.php';
			date_default_timezone_set('America/New_York');
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
			//Retrieves contractor info to be used in sent quote.
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
				//Checks to make sure the field is filled, and a valid date.
				if (empty($quoteCompletionDate) || strtotime($quoteCompletionDate) < time())
					die("Invalid completion date");
 			
				//Inserts data into DB.
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
			<input type="number" id="quoteCost" name="quoteCost" min="1" required>
			<br><br>
			
			
			<label for="quoteCompletionDate">Estimated Completion Date:</label>
			<input type="date" id="quoteCompletionDate" name="quoteCompletionDate"  value="<?php echo date('d-m-Y');?>" min="<?php echo date('d-m-Y');?>" required>
			
			<br><br>
			<label for="quoteDetails">Additional Details:</label>
			<textarea id="quoteDetails" name="quoteDetails" rows="5" cols="78">Include important information here.</textarea>
			
			<br><br>
			<input type="submit" value="Submit Quote">			
			<button onclick ="window.location.href = 'AvailableJobs.php';" type="button">Return to Job Listings</button>
		</form>

		
			
	</div>

</body>
</html>