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

  .message-notification {
            font-size: 30px;
            font-weight: bold;
            justify-content: center;
            text-align: center;
        }

  </style>
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
		$db = connectToDB();
		$getFNameQuery = "SELECT customerFirstName, customerID FROM customer WHERE customerEmail = '$userEmail'";
		$result = $db->query($getFNameQuery);
		if ($result) {
			$row = $result->fetch_assoc();
			$userFName = $row['customerFirstName'];
      $customerID = $row['customerID'];
      $_SESSION['customerID'] = $customerID;
		} else {
			$userFName = "User";
		}

    $getUnreadMessagesCount = $db->prepare("SELECT COUNT(*) AS unreadCount
      FROM messages 
      WHERE conversationID IN (
          SELECT conversationID 
          FROM conversations 
          WHERE customerEmail = ?
      ) AND sender != ? AND isRead = false;");
    $getUnreadMessagesCount->bind_param("ss", $userEmail, $userEmail);
    if($getUnreadMessagesCount->execute()){
      $result = $getUnreadMessagesCount->get_result();
      $row = $result->fetch_assoc();
      $unreadCount = $row['unreadCount'];
    }

			
		
	?>	
  <header>
    <div class="dropdown">
        <button class="dropbtn">...</button>
        <div class="dropdown-content">
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
    <?php if ($unreadCount > 0): ?>
      <div class="message-notification"><?php echo "$unreadCount unread message(s) - "; ?><a href='CustomerMessageCenter.php'>Click to view</a></div>
    <?php endif; ?>
    <a href="requestservice.php" class="w3-button w3-jumbo">Book Service</a>
    <a href="CustomerManageJobs.php" class="w3-button w3-jumbo">Manage Jobs</a>
    <a href="CustomerMessageCenter.php" class="w3-button w3-jumbo">Message Center</a>
</div>


  <div class="w3-content w3-container w3-padding-64" id="services">
    <h3 class="w3-center">OUR SERVICES</h3>
    <div class="w3-row">
      <div class="w3-col m4 w3-center w3-padding-large">
        <i class="fa fa-television w3-margin-bottom w3-jumbo"></i>
        <h4>TV Mounting</h4>
        <p>We professionally mount your TV on the wall.</p>
      </div>
      <div class="w3-col m4 w3-center w3-padding-large">
        <i class="fa fa-wrench w3-margin-bottom w3-jumbo"></i>
        <h4>Plumbing</h4>
        <p>We fix plumbing issues efficiently.</p>
      </div>
      <div class="w3-col m4 w3-center w3-padding-large">
        <i class="fa fa-bolt w3-margin-bottom w3-jumbo"></i>
        <h4>Electrical Works</h4>
        <p>Professional electrical services for your home.</p>
      </div>
      <div class="w3-col m4 w3-center w3-padding-large">
        <i class="fa fa-wrench w3-margin-bottom w3-jumbo"></i>
        <h4>Handyman</h4>
        <p>General handyman services for various tasks.</p>
      </div>
      <div class="w3-col m4 w3-center w3-padding-large">
        <i class="fa fa-leaf w3-margin-bottom w3-jumbo"></i>
        <h4>Gardening</h4>
        <p>Professional gardening and landscaping services.</p>
      </div>
      <div class="w3-col m4 w3-center w3-padding-large">
        <i class="fa fa-snowflake-o w3-margin-bottom w3-jumbo"></i>
        <h4>HVAC</h4>
        <p>Heating, ventilation, and air conditioning services.</p>
      </div>
    </div>
  </div>
</body>

</html>
