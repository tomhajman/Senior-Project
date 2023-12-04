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
  <link rel="stylesheet" href="css/CusManJobs.css">
  <script src='ImageLoading.js'></script>
</head>

<body>
  <?php
		include 'DBCredentials.php';
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

		$conn = connectToDB();
		$getCustomerInfo = $conn->prepare("SELECT customerFirstName, customerID FROM customer WHERE customerEmail = ?");
        $getCustomerInfo->bind_param("s", $userEmail);

		if ($getCustomerInfo->execute()) {
			$getCustomerInfo->bind_result($userFName, $customerID);
            $getCustomerInfo->fetch();
		} else {
			$userFName = "User";
		}
    $getCustomerInfo->close();

    if(isset($_POST['jobIDforConversation']) && is_numeric($_POST['jobIDforConversation'])){
      $jobIDforConversation = $_POST['jobIDforConversation'];
      
      $findConversation = $conn->prepare("SELECT
        conv.conversationID
      FROM
        conversations conv
      JOIN
        customerJob cj ON conv.jobID = cj.jobID
      JOIN
        contractor cont ON cj.contractorID = cont.contractorID
      WHERE
        cj.customerID=? AND cj.jobID=?;");
      $findConversation->bind_param("ii", $customerID, $jobIDforConversation);
      if($findConversation->execute()){
        $conversation = $findConversation->get_result();
        $findConversation->close();
        if($conversation->num_rows > 0){
          $result = $conversation->fetch_assoc();
          header("Location: CustomerConversation.php?id={$result['conversationID']}");
          exit();
        } else {
          $getContractorEmail = $conn->query("SELECT contractorEmail FROM contractor cont JOIN customerJob cj ON cj.contractorID = cont.contractorID WHERE cj.jobID=$jobIDforConversation");
          $result = $getContractorEmail->fetch_assoc();
          $createConversation = $conn->prepare("INSERT INTO conversations (customerEmail, contractorEmail, jobID) VALUES (?, ?, ?)");
          $createConversation->bind_param("ssi", $userEmail, $result['contractorEmail'], $jobIDforConversation);
          if($createConversation->execute()){
            $lastInsertedId = mysqli_insert_id($conn);
            header("Location: CustomerConversation.php?id={$lastInsertedId}");
            exit();
          } else {
            echo "Something went wrong, try again later";
          }
        }
      }
    }
			
	if (isset($_POST['markCompleted'])) {
		$jobIDforStatus = $_POST['jobIDforStatus'];
		$updateStatus = "UPDATE customerJob SET jobStatus = 'Completed' WHERE jobID = $jobIDforStatus";
		$conn->query($updateStatus);
	}
	
	if (isset($_POST['removeJob'])) {
		$jobIDforStatus = $_POST['jobIDforStatus'];
		$removeJob = "DELETE FROM customerJob WHERE jobID = $jobIDforStatus";
		$conn->query($removeJob);
	}
		
	?>	
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
    <div class="welcome-user">
        Welcome, <?php echo htmlspecialchars($userFName); ?><br>
        Email: <?php echo htmlspecialchars($userEmail); ?>
    </div>
  </header>

  <div class="w3-content w3-container w3-padding-64" id="book-service">
    <a href="CustomerPage.php" class="w3-button">Back</a>
    <h2>Manage your jobs</h2>
  </div>

    <?php
        $getJobs = $conn->prepare("SELECT * FROM customerJob WHERE customerID = ?");
        $getJobs->bind_param("i", $customerID);

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
                        <th>Job Type</th>
                        <th>Job Status</th>
                        <th>City</th>
                        <th>Address</th>
                        <th>Urgency</th>
                        <th></th>
						<th></th>
                      </tr>";

            foreach ($rows as $row) {
                $getCoverPicture = $conn->query("SELECT id FROM jobImages WHERE jobID={$row['jobID']} AND isCover = 1 ");
                $getID = $getCoverPicture->fetch_assoc();
                $jobID = $row['jobID'];

                echo "<tr>
                            <td><img src='assets/loading.png' alt='Loading' class='loading-image' width='160px' height='90px'/>
                            <img src='jobImage.php?id={$getID['id']}' width='160px' height='90px' alt='Job Cover Image' style='display: none;' onload='imageLoaded(this)'></td>
                            <td>{$row['jobTitle']}</td>
                            <td>{$row['jobType']}</td>
                            <td>{$row['jobStatus']}</td>
                            <td>{$row['jobCity']}</td>
                            <td>{$row['jobAddress']}</td>
                            <td>{$row['jobUrgency']}</td>
                            <td><a href='editJob.php?id={$row['jobID']}'><button>Edit</button></a></td>";

                $status = $row['jobStatus'];
                switch ($status) {
                    case 'Pending':
                        echo "<td><a href='viewQuotes.php?id={$row['jobID']}'><button>View Quotes</button></a>
						      <form action='#' method='post'>
							    <input type='hidden' name='jobIDforStatus' value='{$jobID}'>
							    <button type='submit' name='removeJob'>Remove Job</button>
							  </form></td>";
                        break;
                    case 'In Progress':
                        echo "<td><form action='#' method='post'>
                                    <input type='hidden' name='jobIDforConversation' value='{$jobID}'>
                                    <button type='submit'>Message</button>
                                  </form>
                                  <form action='#' method='post'>
                                    <input type='hidden' name='jobIDforStatus' value='{$jobID}'>
                                    <button type='submit' name='markCompleted'>End Job</button>
                                  </form></td>";
                        break;
                    case 'Completed':
                        echo "<td><a href='ContractorRatings.php?jobID={$row['jobID']}'><button>Rate Work</button></a></td>";
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
        $conn->close();
    }
    ?>
</body>

</html>
