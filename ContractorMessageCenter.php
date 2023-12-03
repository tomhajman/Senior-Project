<!DOCTYPE html>
<html lang="en">
<?php

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
                die("Connection issue: ".$conn->connect_error);
            }			
        return $conn;
    }

    $conn = connectToDB();
    $getContractorInfo = $conn->prepare("SELECT contractorName, contractorID FROM contractor WHERE contractorEmail = ?");
    $getContractorInfo->bind_param("s", $userEmail);

    if ($getContractorInfo->execute()) {
        $getContractorInfo->bind_result($userFName, $contractorID);
        $getContractorInfo->fetch();
    } else {
        $userFName = "User";
    }
    $getContractorInfo->close();
    

?>	

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> 
  <link rel="stylesheet" href="css/ConCenter.css">

</head>

<body>
  <header>
    <div class="dropdown">
        <button class="dropbtn">...</button>
        <div class="dropdown-content">
         <a href="ContractorPage.php">Home</a>
         <a href="ContractorMessageCenter.php">Messages</a>
         <a href="AvailableJobs.php">Available Jobs</a>
         <a href="#">Job History</a>
         <a href="ContractorUpdatePage.php">Account Settings</a>
         <a href="Logout.php">Log Out</a>
        </div>
    </div>
    <div class="welcome-user">
        Welcome, <?php echo htmlspecialchars($userFName); ?><br>
        Email: <?php echo htmlspecialchars($userEmail); ?>
    </div>
  </header>

  <div class="w3-content w3-container w3-padding-64" id="book-service">
    <a href="ContractorPage.php" class="w3-button">Back</a>
    <h2>Message Center</h2>
  </div>

    <?php
        $getJobs = $conn->prepare("SELECT
            cj.jobTitle, 
            cj.jobStatus, 
            conv.conversationID, 
            cust.customerFirstName,
            cust.customerLastName,
            COUNT(CASE WHEN msg.isRead = false AND msg.sender != ? THEN 1 ELSE NULL END) AS unreadMessagesCount
          FROM 
            conversations conv
          JOIN 
            customerJob cj ON conv.jobID = cj.jobID
          JOIN 
            customer cust ON cust.customerEmail = conv.customerEmail
          LEFT JOIN
            messages msg ON conv.conversationID = msg.conversationID
          WHERE 
            conv.contractorEmail = ?
          GROUP BY
            conv.conversationID;
        ");
        $getJobs->bind_param("ss", $userEmail, $userEmail);

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
                    $customerName = htmlspecialchars($row['customerFirstName'])." ".htmlspecialchars($row['customerLastName']);
                    $title = htmlspecialchars($row['jobTitle']);
                    $status = htmlspecialchars($row['jobStatus']);
                    $unreadMessagesCount = $row['unreadMessagesCount'];
                    echo "<tr". ($unreadMessagesCount > 0 ? " style='font-weight: bold;'" : "") .">";
                    echo "<td>{$customerName}</td>
                            <td>{$title}</td>
                            <td>{$status}</td>
                            <td><a href='contractorConversation.php?id={$row['conversationID']}'><button>Message</button></a></td>";
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
