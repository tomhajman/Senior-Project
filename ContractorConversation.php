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
  <style>
    body,
    h2,
    h3,
    h4,
    h5,
    h6 {
      font-family: "Lato", sans-serif;
    }

    body,
    html {
      height: 100%;
      color: #333;
      line-height: 1.8;
    }

    .bgimg-1,
    .bgimg-2,
    .bgimg-3 {
      background-attachment: fixed;
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;
    }

    .bgimg-1 {
      background-image: url('/w3images/parallax1.jpg');
      min-height: 100%;
    }

    .bgimg-2 {
      background-image: url("/w3images/parallax2.jpg");
      min-height: 400px;
    }

    .bgimg-3 {
      background-image: url("/w3images/parallax3.jpg");
      min-height: 400px;
    }

    .w3-wide {
      letter-spacing: 10px;
    }

    .w3-hover-opacity {
      cursor: pointer;
    }

    @media only screen and (max-device-width: 1600px) {

      .bgimg-1,
      .bgimg-2,
      .bgimg-3 {
        background-attachment: scroll;
        min-height: 400px;
      }
    }

    body {
      font-family: Arial, sans-serif;
      background-color: #f2f2f2;
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
    }

    .center-div{
        align-items: center;
    }

    header {
      background-color: #333;
      color: #fff;
      padding: 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    h1 {
      margin: 0;
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
      left: 0;
      top: 100%;
      z-index: 1;
      text-align: left;
    }

    .dropdown-content a {
      color: gray;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
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

    .welcome-user {
      margin-right: 10px;
      margin-left: auto;
    }

    .w3-content {
      padding: 64px;
    }

    .w3-row {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
    }

    .w3-col {
      margin-bottom: 16px;
      text-align: center;
      width: 25%;
    }

    .w3-col h4 {
      margin: 10px 0;
    }

      #book-service {
          text-align: center;
      }

      #book-service a {
          font-size: 24px; 
          background-color: green;
          padding: 5px 10px; 
          color: white;
          text-decoration: none;
          border-radius: 5px;
      }

      #book-service a:hover {
          background-color: #3e8e41; 
      }
    

    .w3-col p {
      margin: 0;
    }

    .chat-container {
        display: flex;
        flex-direction: column; /* Stack messages vertically */
        align-items: flex-start; /* Align items to the start (left) by default */
        width: 80%;
        max-width: 600px;
        border: 1px solid #ddd;
        padding: 10px;
        margin-bottom: 10px;
        height: 300px; /* Adjust as needed */
        overflow-y: auto;
    }

    .main-content {
        display: flex;
        flex-direction: column; /* Stack children vertically */
        align-items: center; /* Center-align children horizontally */
        justify-content: flex-start; /* Align children to the start of the main axis */
        min-height: 100vh;
        padding-top: 0px; /* Adjust if you have a header */
    }

    .message {
        margin: 5px;
        padding: 10px;
        border-radius: 10px;
        max-width: 70%;
    }

    .sent {
        background-color: #e0f7fa;
        align-self: flex-start;
    }

    .received {
        background-color: #fce4ec;
        align-self: flex-end;
    }

    .message-input {
        width: 130%;
        max-width: 600px;
        height: 50px; /* Adjust as needed */
        margin-bottom: 10px;
    }

    .reply-button {
        width: 100px;
        padding: 10px;
        background-color: blue;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .reply-button:hover {
        background-color: darkblue;
    }

  </style>
</head>

<body>	
    <header>
        <div class="dropdown">
            <button class="dropbtn">...</button>
            <div class="dropdown-content">
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