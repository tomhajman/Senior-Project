<?php
	session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="css/ConJobs.css">
  <script src="ImageLoading.js"></script>
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
		$getContractorInfo = $conn->prepare("SELECT contractorName, contractorID FROM contractor WHERE contractorEmail = ?");
        $getContractorInfo->bind_param("s", $userEmail);

		if ($getContractorInfo->execute()) {
			$getContractorInfo->bind_result($userName, $contractorID);
            $getContractorInfo->fetch();
		} else {
			$userName = "User";
		}
    $getContractorInfo->close();
	
	if(isset($_POST['jobIDforConversation']) && is_numeric($_POST['jobIDforConversation'])){
      $jobIDforConversation = $_POST['jobIDforConversation'];
      
      $findConversation = $conn->prepare("SELECT
        conv.conversationID
      FROM
        conversations conv
      JOIN
        customerJob cj ON conv.jobID = cj.jobID
      JOIN
        customer cust ON cj.customerID = cust.customerID
      WHERE
        cj.contractorID=? AND cj.jobID=?;");
      $findConversation->bind_param("ii", $contractorID, $jobIDforConversation);
      if($findConversation->execute()){
        $conversation = $findConversation->get_result();
        $findConversation->close();
        if($conversation->num_rows > 0){
          $result = $conversation->fetch_assoc();
          header("Location: ContractorConversation.php?id={$result['conversationID']}");
          exit();
        } else {
          $getCustomerEmail = $conn->query("SELECT customerEmail FROM customer cust JOIN customerJob cj ON cj.customerID = cust.customerID WHERE cj.jobID=$jobIDforConversation");
          $result = $getCustomerEmail->fetch_assoc();
          $createConversation = $conn->prepare("INSERT INTO conversations (customerEmail, contractorEmail, jobID) VALUES (?, ?, ?)");
          $createConversation->bind_param("ssi", $result['customerEmail'], $userEmail, $jobIDforConversation);
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

	<header>
		<div class="dropdown">
            <button class="dropbtn">...</button>
            <div class="dropdown-content">
                <a href="ContractorMessageCenter.php">Messages</a>
                <a href="AvailableJobs.php">Available Jobs</a>
                <a href="ContractorManageJobs.php">Job History</a>
                <a href="ViewRatings.php">View Ratings</a>
                <a href="ContractorUpdatePage.php">Account Settings</a>
                <a href="Logout.php">Log Out</a>
            </div>
        </div>
        <div class="welcome-contractor">Welcome, <?php echo isset($userName) ? $userName : 'Contractor'; ?></div>
    </header>
	
	<div class="w3-content w3-container w3-padding-64" id="book-service">
		<a href="ContractorPage.php" class="w3-button">Back</a>
		<h2>Manage Your Jobs</h2>
	</div>
	<div class="accepted-jobs">
	<?php
		$getJobs = $conn->prepare("SELECT * FROM customerJob WHERE contractorID = ?");
        $getJobs->bind_param("i", $contractorID);
		
		if ($getJobs->execute()) {
        $result = $getJobs->get_result();
        $getJobs->close();

        $rows = $result->fetch_all(MYSQLI_ASSOC);

        if (!empty($rows)) {
            echo '<div class="w3-row">';
            echo "<table border='1'>";
            echo "<tr>
                        <th></th>
                        <th>Title</th>
						<th>Customer Name</th>
                        <th>Job Type</th>
                        <th>Job Status</th>
                        <th>City</th>
                        <th>Address</th>
                        <th>Urgency</th>
                        <th>Job Price</th>
						<th></th>
                      </tr>";

            foreach ($rows as $row) {
                $getCoverPicture = $conn->query("SELECT id FROM jobImages WHERE jobID={$row['jobID']} AND isCover = 1 ");
                $getID = $getCoverPicture->fetch_assoc();
                $jobID = $row['jobID'];
                $urgencyPic = '';
                switch ($row['jobUrgency']) {
                  case 0:
                    $urgencyPic = 'lowUrgency.png';
                    break;
                  case 1:
                    $urgencyPic = 'mediumUrgency.png';
                    break;
                  case 2:
                    $urgencyPic = 'highUrgency.png';
                    break;
                  case 3:
                    $urgencyPic = 'criticalUrgency.png';
                    break;
                  default: 
                    $urgencyPic = 'lowUrgency.png';
                }
                $getJobPrice = $conn->query("SELECT quotePrice FROM jobQuote WHERE jobID = {$row['jobID']}");
                $priceRow = $getJobPrice->fetch_assoc();
                $jobPrice = $priceRow['quotePrice'];

                echo "<tr>
                            <td><img src='assets/loading.png' alt='Loading' class='loading-image' width='160px' height='90px'/>
                            <img src='jobImage.php?id={$getID['id']}' width='160px' height='90px' alt='Database Image' style='display: none;' onload='imageLoaded(this)'></td>
                            <td>{$row['jobTitle']}</td>
							              <td>{$row['customerLastName']}</td>
                            <td>{$row['jobType']}</td>
                            <td>{$row['jobStatus']}</td>
                            <td>{$row['jobCity']}</td>
                            <td>{$row['jobAddress']}</td>
                            <td><img src='assets/{$urgencyPic}' alt='Urgency Pic' width='24px' height='24px'></td>
                            <td>$$jobPrice</td>";

                $status = $row['jobStatus'];
                switch ($status) {
                    case 'In Progress':
                        echo "<td><form action='#' method='post'>
                                    <input type='hidden' name='jobIDforConversation' value='{$jobID}'>
                                    <button type='submit'>Message</button>
                                  </form>
                                  <form action='#' method='post'>
                                    <input type='hidden' name='jobIDforStatus' value='{$jobID}'>
                                  </form></td>";
                        break;
                    case 'Completed':
                        echo "<td>Job Finished!</td>";
                        break;
                    default:
                        break;
                }

                echo "</tr>";
            }

            echo "</table>";
            echo "</div>";
        } else {
            echo "Nothing here but crickets!";
        }

        $result->free();
    }
	?>
	</div>
	<div class="pending-quotes">
	<div class="manage-quote"><h2>Manage Pending Quotes</h2></div>
	<?php		
		$getQuotes = $conn->prepare("SELECT * FROM jobQuote JOIN customerJob ON jobQuote.jobID = customerJob.jobID WHERE contractorName = ? AND jobStatus = ?");
		$pendingStatus = "Pending";
		$getQuotes->bind_param("ss", $userName, $pendingStatus);

		if ($getQuotes->execute()) {
			$result2 = $getQuotes->get_result();
			$getQuotes->close();

			$rows2 = $result2->fetch_all(MYSQLI_ASSOC);

			if (!empty($rows2)) {
				echo '<div class="w3-row">';
				echo "<table border='1'>";
				echo "<tr>
						<th>Title</th>
						<th>Customer Name</th>
						<th>Job Type</th>
						<th>City</th>
						<th>Address</th>
						<th>Urgency</th>
						<th>Job Price</th>
						<th>Estimated Completion Date</th>
						<th></th>
						<th></th>
					  </tr>";

				foreach ($rows2 as $row2) {
					$getJobInfo = $conn->prepare("SELECT jobTitle, customerLastName, jobType, jobCity, jobAddress, jobUrgency FROM customerJob WHERE jobID = ?");
					$getJobInfo->bind_param("i", $row2['jobID']);
					$getJobInfo->execute();

					$result3 = $getJobInfo->get_result();
					$row3 = $result3->fetch_assoc();

					echo "<tr>
							<td>{$row3['jobTitle']}</td>
							<td>{$row3['customerLastName']}</td>
							<td>{$row3['jobType']}</td>
							<td>{$row3['jobCity']}</td>
							<td>{$row3['jobAddress']}</td>
							<td>{$row3['jobUrgency']}</td>
							<td>{$row2['quotePrice']}</td>
							<td>{$row2['estimatedCompletionDate']}</td>
							<td><a href='editQuote.php?id={$row2['quoteID']}'><button>Edit</button></a></td>
							<td><form action='#' method='post'><button type='submit' name='deleteQuote'>Delete Quote</button></form></td>
						  </tr>";
						if (isset($_POST['deleteQuote'])) {
							$quoteID = $row2['quoteID'];
							$deleteQuote = "DELETE FROM jobQuote WHERE quoteID = $quoteID";
							$conn->query($deleteQuote);
							echo '<script>window.location="ContractorManageJobs.php"</script>';
							//I abhor header function for causing me a massive headache
							//header("Location: {$_SERVER['PHP_SELF']}");
							exit();
						}
					}
				echo "</table>";
				echo "</div>";
			} else {
				echo "No pending quotes";
			}

			$result2->free();
			
			
		}
		$conn->close();
	?>

	</div>
</body>
</html>