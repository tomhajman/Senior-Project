<?php

		session_start();
		include 'DBCredentials.php';
		$userEmail = $_SESSION['contractorEmail'];
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
    $userName = htmlspecialchars($userName);

        if(isset($_POST['messageContent']) && isset($_GET['id']) && is_numeric($_GET['id'])){
            $messageContent = $_POST['messageContent'];
            $sendMessage = $conn->prepare("INSERT INTO messages (content, conversationID, sender) VALUES (?, ?, ?)");
            $sendMessage->bind_param("sis", $messageContent, $_GET['id'], $userEmail);
            $sendMessage->execute();
            $sendMessage->close();

            header("Location: ContractorConversation.php?id={$_GET['id']}");
            exit();
        }

        if(isset($_GET['id']) && is_numeric($_GET['id'])){
            $conversationID = $_GET['id'];

            $getMessages = $conn->prepare("-- First part: LEFT JOIN
            SELECT 
                m.msgID, 
                m.content, 
                m.sender, 
                m.sentAt, 
                c.customerFirstName, 
                c.customerLastName 
            FROM 
                messages m
            LEFT JOIN 
                customer c 
            ON 
                m.sender = c.customerEmail 
            WHERE 
                m.conversationID = ?
            
            UNION
            
            -- Second part: RIGHT JOIN
            SELECT 
                m.msgID, 
                m.content, 
                m.sender, 
                m.sentAt, 
                c.customerFirstName, 
                c.customerLastName 
            FROM 
                messages m
            RIGHT JOIN 
                customer c 
            ON 
                m.sender = c.customerEmail 
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
			
		
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="css/convo.css">
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
                <a href="ContractorLogin.php">Log Out</a>
            </div>
        </div>
		<div class="welcome-contractor">Welcome, <?php echo $userName; ?></div> 
    </header>

  <div class="w3-content w3-container w3-padding-64" id="book-service">
    <a href="ContractorMessageCenter.php" class="w3-button">Back</a>
    <h3>Conversation about:</h3>
    <h4><?php echo htmlspecialchars($jobTitle) ?></h4>
  </div>

  <div class="main-content">
    <div class="chat-container">
        <?php
            while($row = $result->fetch_assoc()){
                $content = htmlspecialchars($row['content']);
                $sender = htmlspecialchars($row['sender']);
                $sentAt = $row['sentAt'];
                $date = new DateTime($sentAt, new DateTimeZone('UTC')); // Assuming your timestamp is in UTC

                // Convert the timezone to Eastern Time (ET)
                $date->setTimezone(new DateTimeZone('America/New_York'));

                // Format the date
                $formattedDate = $date->format('m-d-Y h:i A');

                // Display the messages
                if($row['sender'] == $userEmail){
                    echo "<div class='message sent'>
                        {$userName}<br>
                        {$formattedDate}<br>
                        {$content}
                    </div>";
                } else {
                    $customerName = htmlspecialchars($row['customerFirstName'])." ".htmlspecialchars($row['customerLastName']);
                    echo "<div class='message received'>
                        {$customerName}<br>
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