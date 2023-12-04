<?php
	session_start();
?>
<!DOCTYPE html>
<html lang="en">
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
  
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="css/CusMessage.css">
  
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
    <div class="welcome-user">
        Welcome, <?php echo htmlspecialchars($userFName); ?><br>
        Email: <?php echo htmlspecialchars($userEmail); ?>
    </div>
  </header>

  <div class="w3-content w3-container w3-padding-64" id="book-service">
    <a href="CustomerPage.php" class="w3-button">Back</a>
    <h2>Message Center</h2>
  </div>

    <?php
        $getJobs = $conn->prepare("
          SELECT 
            cj.jobTitle, 
            cj.jobStatus, 
            conv.conversationID, 
            cont.contractorName,
            COUNT(CASE WHEN msg.isRead = false AND msg.sender != ? THEN 1 ELSE NULL END) AS unreadMessagesCount
          FROM 
            customerJob cj
          JOIN 
            conversations conv ON cj.jobID = conv.jobID
          JOIN 
            contractor cont ON conv.contractorEmail = cont.contractorEmail
          LEFT JOIN
            messages msg ON conv.conversationID = msg.conversationID
          WHERE 
            cj.customerID = ?
          GROUP BY
            conv.conversationID;
        ");
        $getJobs->bind_param("si", $userEmail, $customerID);

        if($getJobs->execute()){
            $result = $getJobs->get_result();
            $getJobs->close();

            if($result->num_rows > 0) {
                echo '<div class="w3-row">';
                echo "<table border='1'>";
                echo "<tr>
                        <th>To/From</th>
                        <th>Job Title</th>
                        <th>Job Status</th>
                        <th></th>
                        <th></th>
                      </tr>";
                
                while($row = $result->fetch_assoc()) {
                    $contractorName = htmlspecialchars($row['contractorName']);
                    $title = htmlspecialchars($row['jobTitle']);
                    $status = htmlspecialchars($row['jobStatus']);
                    $unreadMessagesCount = $row['unreadMessagesCount'];
                    echo "<tr". ($unreadMessagesCount > 0 ? " style='font-weight: bold;'" : "") .">";
                    echo "<td>{$contractorName}</td>
                          <td>{$title}</td>
                          <td>{$status}</td>
                          <td><a href='customerConversation.php?id={$row['conversationID']}'><button>Message</button></a></td>";
                    if ($unreadMessagesCount > 0) {
                      echo "<td>New Message(s)</td>";
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
