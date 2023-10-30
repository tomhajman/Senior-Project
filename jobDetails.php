<!DOCTYPE html>
<html>
<head>
<title>Job Details</title>
<style>
	body,h1,h2,h3,h4,h5,h6 {font-family: "Lato", sans-serif;}
        body, html {
          height: 100%;
          color: #333;
          line-height: 1.8;
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
            padding: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            left: 0; /* Start from the left */
            top: 100%;
            z-index: 1;
        }

        .dropdown-content a {
            color: gray;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left; /* Align the menu items to the left */
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
		
		.pageInfo {
			margin-right: 10px;
			margin-left: auto;
		}
		
		.jobDetails {
			padding-left: 50px;
			padding-right: 50px;
			background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
		}
		
		.jobImages {
			text-align: center;
		}
		button {
            background-color: #007bff;
            color: white;
            padding: 14px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
			text-align: center;
        }
		button:hover {
			background-color: #0056b3;
		}
		#quote {
			margin-left: 20px;
			float: left;
		}
		
		#info {
			margin-right: 20px;
			float: right;
		}
		
</style>
</head>
<body>



  	<?php
		//Connecting DB, passing contractorEmail, can later change this to boot user to login page if they are not signed in. For now will just throw an error.
		session_start();
		include 'DBCredentials.php';
		$userEmail = $_SESSION['contractorEmail'];
	?>
	
	<header>
	<div class="dropdown">
            <button class="dropbtn">...</button>
            <div class="dropdown-content">
                <a href="#">Messages</a>
                <a href="AvailableJobs.php">Available Jobs</a>
                <a href="#">Job History</a>
                <a href="ContractorUpdatePage.php">Account Settings</a>
                <a href="ContractorLogin.php">Log Out</a>
            </div>
    </div>
	<div class="pageInfo">View Listing Information</div> 
</header>
	
	<?php
		function connectToDB() {
			global $HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME, $conn;
				$conn = new mysqli($HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME);
				
				if ($conn->connect_error) {
					die("Connection issue: ".$conn->connect_error);
				}			
			return $conn;
		}
	
	if (isset($_GET['id'])) {
		$conn = connectToDB();
		$id = $_GET['id'];
		$getJobInfo = "SELECT jobType, jobTitle, jobDescription, jobCounty, jobCity, jobAddress, jobUrgency, customerLastName FROM customerJob WHERE jobID = '$id'";
		$result = $conn->query($getJobInfo);
		$record = mysqli_fetch_assoc($result);
		
		//I tried making these two queries just one through a join, but I kept getting "Trying to access array offset on value of type null error" for every field. This will be fixed after the presentation for bandwidth preservation.
		$getCustInfo = "SELECT customerFirstName, customerEmail, customerPhoneNumber FROM customer WHERE jobID='$id'";
		$result2 = $conn->query($getCustInfo);
		$record2 = mysqli_fetch_assoc($result2);
		
		//Retrieve cover photo
		$getCoverPicture = $conn->query("SELECT id FROM jobImages WHERE jobID=$id AND isCover = 1 ");
		$getID = $getCoverPicture->fetch_assoc();
		
		//Retrieve other photos
		$getPhotos = $conn->query("SELECT id FROM jobImages WHERE jobID=$id AND isCover != 1");
		$getOtherID = $getPhotos->fetch_assoc();
		
		echo '<div class="jobDetails">'.'<h2>'.$record['jobTitle'].'</h1>';
		echo "<img src='jobImage.php?id={$getID['id']}' width='300px' height='120px' alt='Database Image'><br>";
		echo "Customer Name: ".$record2['customerFirstName']." ".$record['customerLastName']."<br>";
		echo "Customer Email: ".$record2['customerEmail']."<br>";
        echo "Customer Phone Number: ".$record2['customerPhoneNumber']."<br>";
		echo "Job Address: ".$record['jobAddress'].', '.$record['jobCity'].', '.$record['jobCounty']."<br>";
		echo "Job Urgency: ".$record['jobUrgency']."<br><br>";
		echo "Job Description: ".$record['jobDescription']."<br>";
		echo '</div>';
		echo '<div class="jobImages">';
		while ($imgs = $getPhotos->fetch_assoc()) {
			echo "<img src='jobImage.php?id={$imgs['id']}' width='400px' height='200px' alt='Database Image'>";
		} 
		echo '</div>';
		
	} else {
		echo "Error retrieving job info.";
	}
	?>
	
	<form action="" method="post">
		<button id="quote">Send Quote</button>
		<button id="info">Request More Information</button>
	</form>
	
</body>
</html>