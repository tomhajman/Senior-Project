<?php

		session_start();
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

    // Check if user has permission to access the conversation
    if(isset($_GET['id'])){
      $getMatchingConversation = $conn->prepare("SELECT customerEmail FROM conversations WHERE conversationID=?");
      $getMatchingConversation->bind_param("i", $_GET['id']);
      if($getMatchingConversation->execute()){
        $getMatchingConversation->bind_result($dbEmail);
        $getMatchingConversation->fetch();
        $getMatchingConversation->close();
        if($dbEmail != $userEmail){
          header("Location: ContractorMessageCenter.php?redirect=accessDenied");
          exit();
        }
      }
    }


        if(isset($_POST['messageContent']) && isset($_GET['id']) && is_numeric($_GET['id'])){
            $messageContent = $_POST['messageContent'];
            $sendMessage = $conn->prepare("INSERT INTO messages (content, conversationID, sender) VALUES (?, ?, ?)");
            $sendMessage->bind_param("sis", $messageContent, $_GET['id'], $userEmail);
            $sendMessage->execute();
            $sendMessage->close();

            header("Location: CustomerConversation.php?id={$_GET['id']}");
            exit();
        }

        if(isset($_GET['id']) && is_numeric($_GET['id'])){
            $conversationID = $_GET['id'];

            // Mark messages as read
            markMessagesAsRead($conversationID, $userEmail);

            $getMessages = $conn->prepare("-- First part: LEFT JOIN
            SELECT 
                m.msgID, 
                m.content, 
                m.sender, 
                m.sentAt, 
                c.contractorName
            FROM 
                messages m
            LEFT JOIN 
                contractor c 
            ON 
                m.sender = c.contractorEmail 
            WHERE 
                m.conversationID = ? 
            
            UNION
            
            -- Second part: RIGHT JOIN
            SELECT 
                m.msgID, 
                m.content, 
                m.sender, 
                m.sentAt, 
                c.contractorName
            FROM 
                messages m
            RIGHT JOIN 
                contractor c 
            ON 
                m.sender = c.contractorEmail 
            WHERE 
                m.conversationID = ?;
            ");
            $getMessages->bind_param("ii", $conversationID, $conversationID);
            if($getMessages->execute()){
                $result = $getMessages->get_result();
            }
            $getMessages->close();

            $getJobTitle = $conn->prepare("SELECT jobTitle FROM customerJob cj JOIN conversations conv ON conv.jobID = cj.jobID WHERE conv.conversationID = ?");
            $getJobTitle->bind_param("i", $conversationID);
            if($getJobTitle->execute()){
              $getJobTitle->bind_result($jobTitle);
              $getJobTitle->fetch();
            } else {
              $jobTitle = "Error fetching job title.";
            }
            $getJobTitle->close();
        }

        function markMessagesAsRead($conversationID, $userEmail) {
          global $conn;
      
          $query = "UPDATE messages SET isRead = true WHERE conversationID = ? AND isRead = false AND sender != ?";
          $stmt = $conn->prepare($query);
          $stmt->bind_param("is", $conversationID, $userEmail);
          $stmt->execute();
          $stmt->close();
        }
      
			
		
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> 
  <link rel="stylesheet" href="css/CustCon.css">
  
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
    <a href="CustomerMessageCenter.php" class="w3-button">Back</a>
    <h3>Conversation about:</h3>
    <h4><?php echo htmlspecialchars($jobTitle) ?></h4>
  </div>

  <div class="main-content">
    <div class="chat-container">
        <?php
            while($row = $result->fetch_assoc()){
                $content = htmlspecialchars($row['content']);
                $sentAt = $row['sentAt'];
                $date = new DateTime($sentAt, new DateTimeZone('UTC')); // Assuming your timestamp is in UTC

                // Convert the timezone to Eastern Time (ET)
                $date->setTimezone(new DateTimeZone('America/New_York'));

                // Format the date
                $formattedDate = $date->format('m-d-Y h:i A');

                // Display the messages
                if($row['sender'] == $userEmail){
                    echo "<div class='message sent'>
                        {$userFName}<br>
                        {$formattedDate}<br>
                        {$content}
                    </div>";
                } else {
                    $sender = htmlspecialchars($row['contractorName']);
                    echo "<div class='message received'>
                        {$sender}<br>
                        {$formattedDate}<br>
                        {$content}
                    </div>";
                }
            }
        ?>
        
    </div>
    <form action="#" method="post">
    <textarea class="message-input" placeholder="Type your message..." name="messageContent" required></textarea>
    <button type="submit" class="reply-button">Send</button>
    </form>
  </div>
</body>
</html>