
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Service</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        h1 {
            margin: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        select {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #555;
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
		$db = connectToDB();
		
		//Functions used to pull values from the database, I don't know if this is the most optimal way of doing it, but this is the way I found that works.
		function getLName() {
			global $userEmail, $db;
			$getLNameQuery = "SELECT customerLastName FROM customer WHERE customerEmail = '$userEmail'";
			$result = $db->query($getLNameQuery);
			if ($result) {
				$row = $result->fetch_assoc();
				$userLName = $row['customerLastName'];
			} else {
				$userLName = "User";
			}
			return $userLName;
		}
		
		function getCounty() {
			global $userEmail, $db;
			$getCountyQuery = "SELECT customerCounty FROM customer WHERE customerEmail = '$userEmail'";
			$result = $db->query($getCountyQuery);
			if ($result) {
				$row = $result->fetch_assoc();
				$userCounty = $row['customerCounty'];
			} else {
				$userCounty = "N/A";
			}
			return $userCounty;
		}
		
		function getCity() {
			global $userEmail, $db;
			$getCityQuery = "SELECT customerCity FROM customer WHERE customerEmail = '$userEmail'";
			$result = $db->query($getCityQuery);
			if ($result) {
				$row = $result->fetch_assoc();
				$userCity = $row['customerCity'];
			} else {
				$userCity = "N/A";
			}
			return $userCity;
		}
		
		function getAddress() {
			global $userEmail, $db;
			$getAddressQuery = "SELECT customerStreetAddress FROM customer WHERE customerEmail = '$userEmail'";
			$result = $db->query($getAddressQuery);
			if ($result) {
				$row = $result->fetch_assoc();
				$userAddress = $row['customerStreetAddress'];
			} else {
				$userAddress = "N/A";
			}
			return $userAddress;
		}
		
		//Assign variables to pulled values from DB for use in this form.
		$userLName = getLName();
		$userCounty = getCounty();	
		$userCity = getCity();
		$userAddress = getAddress();
		
		$errors = [];
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			//Handle form submission
			$jobType = $_POST['jobType'];
			$jobTitle = $_POST['jobTitle'];
			$jobDescription = $_POST['jobDescription'];
			$jobPrice = $_POST['jobPrice'];
            
            $coverImage = $_FILES['coverImage']['tmp_name'];
            $mimeType = mime_content_type($coverImage);
			
            // Check if mime type is one of the expected types
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($mimeType, $allowedMimeTypes)) {
                die("Unsupported image type.");
            }

            $imageData = file_get_contents($coverImage);
		
		
		if (empty($errors)) {
			$db = connectToDB();
			
			//Insert data to customerJob table
			$stmt = $conn->prepare("INSERT INTO customerJob (jobPrice, jobTitle, jobDescription, jobType, customerLastName, jobCounty, jobCity, jobAddress) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
			$stmt->bind_param("ssssssss", $jobPrice, $jobTitle, $jobDescription, $jobType, $userLName, $userCounty, $userCity, $userAddress);
			
			if ($stmt->execute()) {
                $lastInsertedId = mysqli_insert_id($conn);
				echo "Job posted successfully";
			} else {
				echo "Error: " . $stmt->error;
			}
			$stmt->close();
            // Upload cover photo
            $isCover = 1;
            $stmt = $conn->prepare("INSERT INTO jobImages (mimeType, imageData, jobID, isCover) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssii", $mimeType, $imageData, $lastInsertedId, $isCover);
            $stmt->send_long_data(1, $imageData);
            $stmt->execute();
            $stmt->close();

            // TODO Upload other photos

            $conn->close(); 
		}
	}
	?>
    <header>
        <h1>Select Job Category</h1>
    </header>
    <div class="container">
        <form action="#" method="post" enctype="multipart/form-data">
            <label>Select Service:</label>
            <div>
                <input type="radio" id="generalContracting" name="jobType" value="Contracting" required>
                <label for="generalContracting">General Contracting</label>
            </div>
            <div>
                <input type="radio" id="plumbing" name="jobType" value="Plumbing">
                <label for="plumbing">Plumbing</label>
            </div>
            <div>
                <input type="radio" id="electrician" name="jobType" value="Electrician">
                <label for="electrician">Electrician</label>
            </div>
            <div>
                <input type="radio" id="gardening" name="jobType" value="Gardening">
                <label for="gardening">Gardening</label>
            </div>        
			<div>
                <input type="radio" id="painting" name="jobType" value="Painting">
                <label for="painting">Painting</label>
            </div>    
			<div>
                <input type="radio" id="hvac" name="jobType" value="HVAC">
                <label for="hvac">HVAC</label>
            </div>
			<label for="jobTitle">Job Title:</label>
			<input type="text" id="jobTitle" name="jobTitle" required>
            <label for="details">Job Details:</label>
            <textarea id="details" name="jobDescription" rows="4" required></textarea>     
			<label for="price">Your Asking Price $:</label>
            <input type="price" id="price" name="jobPrice" required>

            <label>Job Urgency:</label>
            <div>
                <input type="radio" id="low" value=0 required>
                <label for="low">Low Urgency - "I need it done, but it's not time sensitive"</label>
            </div>
            <div>
                <input type="radio" id="medium" value=1>
                <label for="medium">Medium Urgency - "I need it done within a month"</label>
            </div>
            <div>
                <input type="radio" id="high" value=2>
                <label for="high">High Urgency - "I need it done this week"</label>
            </div>
            <div>
                <input type="radio" id="critical" value=3>
                <label for="critical">Critical Urgency - "I needed it done yesterday!"</label>
            </div>
           
            <label for="coverImage">Upload Cover Photo:</label>
            <input type="file" id="coverImage" name="coverImage" accept="image/*" required>
            
            <label for="otherImage">Upload Other Photo(s):</label>
            <input type="file" id="otherImages" name="otherImage[]" accept="image/*" multiple>

            <button type="submit">Submit Request</button>
        </form>
    </div>
</body>
</html>
