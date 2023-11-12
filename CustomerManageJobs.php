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
  </style>
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
  <header>
    <div class="dropdown">
        <button class="dropbtn">...</button>
        <div class="dropdown-content">
          <a href="#">Messages</a>
          <a href="#">Service History</a>
            <a href="#">View Contractors</a>
            <a href="#">Account Settings</a>
            <a href="CustomerLogin.php">Log Out</a>
        </div>
    </div>
    <div class="welcome-user">
        Welcome, <?php echo $userFName; ?><br>
        Email: <?php echo $userEmail; ?>
    </div>
  </header>

  <div class="w3-content w3-container w3-padding-64" id="book-service">
    <a href="CustomerPage.php" class="w3-button">Back</a>
    <h2>Manage your jobs</h2>
  </div>

    <?php
        $getJobs = $conn->prepare("SELECT * FROM customerJob WHERE customerID = ?");
        $getJobs->bind_param("i", $customerID);

        if($getJobs->execute()){
            $result = $getJobs->get_result();
            $getJobs->close();

            if($result->num_rows > 0) {
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
                
                while($row = $result->fetch_assoc()) {
                    $getCoverPicture = $conn->query("SELECT id FROM jobImages WHERE jobID={$row['jobID']} AND isCover = 1 ");
                    $getID = $getCoverPicture->fetch_assoc();
                    echo "<tr>
                            <td><img src='jobImage.php?id={$getID['id']}' width='160px' height='90px' alt='Database Image'></td>
                            <td>{$row['jobTitle']}</td>
                            <td>{$row['jobType']}</td>
                            <td>{$row['jobStatus']}</td>
                            <td>{$row['jobCity']}</td>
                            <td>{$row['jobAddress']}</td>
                            <td>{$row['jobUrgency']}</td>
                            <td><a href='editJob.php?id={$row['jobID']}'><button>Edit</button></a></td>
							<td><a href='viewQuotes.php?id={$row['jobID']}'><button>View Quotes</button></a></td>
                          </tr>";
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
