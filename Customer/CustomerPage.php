<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
 <link rel="stylesheet" href="css/CustomerMessageCenter.css">
</head>

<body>
  <?php

		session_start();
		include 'DBCredentials.php';
		$userEmail = $_SESSION['customerEmail'];
		function connectToDB() {
			global $HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME, $conn;
				$conn = new mysqli($HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME);
				
				if ($conn->connect_error) {
					die("Connection issue: ".$conn->connect_error);
				}			
			return $conn;
		}
		$db = connectToDB();
		$getFNameQuery = "SELECT customerFirstName FROM customer WHERE customerEmail = '$userEmail'";
		$result = $db->query($getFNameQuery);
		if ($result) {
			$row = $result->fetch_assoc();
			$userFName = $row['customerFirstName'];
		} else {
			$userFName = "User";
		}
			
		
	?>	
  <header>
    <div class="dropdown">
        <button class="dropbtn">...</button>
        <div class="dropdown-content">
			<a href="CustomerMessageCenter.php">Messages</a>
			<a href="#">Service History</a>
            <a href="Contractors.php">View Contractors</a>
            <a href="CustomerUpdatePage.php">Account Settings</a>
            <a href="CustomerLogin.php">Log Out</a>
        </div>
    </div>
    <div class="welcome-user">
        Welcome, <?php echo $userFName; ?><br>
        Email: <?php echo $userEmail; ?>
    </div>
  </header>
  <div class="w3-content w3-container w3-padding-64" id="book-service">
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
