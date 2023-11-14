<!DOCTYPE html>
<html>
<head>
<title>View Quote</title>
<style>
	body,h1,h2,h3,h4,h5,h6 {font-family: "Lato", sans-serif;}
        body, html {
          height: 100%;
          color: #333;
          line-height: 1.8;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

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
		
		.quoteDetails {
			padding-left: 50px;
			padding-right: 50px;
			background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
		}
		
		.jobImages {
			text-align: center;
		}
		button {
            background-color: #007bff;
            color: white;
            padding: 14px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
			text-align: center;
			margin-top: 10px;
			margin-bottom: 10px;
        }
		button:hover {
			background-color: #0056b3;
		}
			
</style>
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