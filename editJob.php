
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit job</title>
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
        // Get job info from DB
        if(isset($_GET['id']) && is_numeric($_GET['id'])){
            $stmt = $conn->prepare("SELECT jobType, jobTitle, jobDescription, jobUrgency FROM customerJob WHERE jobID=?");
            $stmt->bind_param("i", $_GET['id']);
            if(!($stmt->execute())){
                die("job with ID = {$_GET['id']} doesn't exist");
            }
            $result = $stmt->get_result();
            $oldInfo = $result->fetch_assoc();
            $stmt->close();
        }
        // Sanitize old input
        $title = htmlspecialchars($oldInfo['jobTitle']); 
        $description = htmlspecialchars($oldInfo['jobDescription']);

		$errors = [];
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			//Handle form submission
			$jobType = $_POST['jobType'];
			$jobTitle = $_POST['jobTitle'];
			$jobDescription = $_POST['jobDescription'];
			$jobUrgency = $_POST['jobUrgency'];

            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
            
            if(!(empty($_FILES['coverImage']['tmp_name']))){
                echo "file detected ";
                $coverImage = $_FILES['coverImage']['tmp_name'];
                $mimeType = mime_content_type($coverImage);
			
                // Check if mime type is one of the expected types
                if (!in_array($mimeType, $allowedMimeTypes)) {
                    die("Unsupported image type.");
                }

                $imageData = file_get_contents($coverImage);
            }
		
		
		if (empty($errors)) {
			$db = connectToDB();
			
			// Update data in customerJob table
			$stmt = $conn->prepare("UPDATE customerJob SET jobTitle=?, jobDescription=?, jobType=?, jobUrgency=? WHERE jobID=?");
			$stmt->bind_param("sssii", $jobTitle, $jobDescription, $jobType, $jobUrgency, $_GET['id']);
			
			if ($stmt->execute()) {
				echo "Job updated successfully";
			} else {
				echo "Error: " . $stmt->error;
			}
			$stmt->close();

            
            // Update cover photo
            $stmt = $conn->prepare("UPDATE jobImages SET mimeType=?, imageData=? WHERE jobID=? AND isCover=?");

            if(!(empty($_FILES['coverImage']['tmp_name']))){
            $isCover = 1;
            $stmt->bind_param("ssii", $mimeType, $imageData, $_GET['id'], $isCover);
            $stmt->send_long_data(1, $imageData);
            $stmt->execute();
            $stmt->close();
            }
            
            // Upload other photos

            $isCover = 0;

            $stmt = $conn->prepare("INSERT INTO jobImages (mimeType, imageData, jobID, isCover) VALUES (?, ?, ?, ?)");
            
            if(!(empty($_FILES['otherImages']['tmp_name']))){
            // Loop through each uploaded file using foreach
                foreach ($_FILES['otherImages']['error'] as $i => $err) {
                    if ($err == UPLOAD_ERR_OK) {
                    $imgFile = $_FILES['otherImages']['tmp_name'][$i];
                    $mimeType = mime_content_type($imgFile);
                    // Chceck if the image is of allowed type, if not - skip it
                        if(!in_array($mimeType, $allowedMimeTypes)){
                            echo "wrong type<br>";
                            continue;
                        }
                    $imageData = file_get_contents($imgFile);

                    $stmt->bind_param("ssii", $mimeType, $imageData, $_GET['id'], $isCover);
                    $stmt->execute();
                    }
                }
            }

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
                <input type="radio" id="generalContracting" name="jobType" value="Contracting" required <?php echo $oldInfo['jobType'] == 'Contracting' ? 'checked' : ''; ?>>
                <label for="generalContracting">General Contracting</label>
            </div>
            <div>
                <input type="radio" id="plumbing" name="jobType" value="Plumbing" <?php echo $oldInfo['jobType'] == 'Plumbing' ? 'checked' : ''; ?>>
                <label for="plumbing">Plumbing</label>
            </div>
            <div>
                <input type="radio" id="electrician" name="jobType" value="Electrician" <?php echo $oldInfo['jobType'] == 'Electrician' ? 'checked' : ''; ?>>
                <label for="electrician">Electrician</label>
            </div>
            <div>
                <input type="radio" id="gardening" name="jobType" value="Gardening" <?php echo $oldInfo['jobType'] == 'Gardening' ? 'checked' : ''; ?>>
                <label for="gardening">Gardening</label>
            </div>        
			<div>
                <input type="radio" id="painting" name="jobType" value="Painting" <?php echo $oldInfo['jobType'] == 'Painting' ? 'checked' : ''; ?>>
                <label for="painting">Painting</label>
            </div>    
			<div>
                <input type="radio" id="hvac" name="jobType" value="HVAC" <?php echo $oldInfo['jobType'] == 'HVAC' ? 'checked' : ''; ?>>
                <label for="hvac">HVAC</label>
            </div>
			<label for="jobTitle">Job Title:</label>
			<input type="text" id="jobTitle" name="jobTitle" required <?php echo "value='{$title}'"; ?>>
            <label for="details">Job Details:</label>
            <textarea id="details" name="jobDescription" rows="4" required><?php echo $description; ?></textarea>     

            <label>Job Urgency:</label>
            <div>
                <input type="radio" id="low" name="jobUrgency" value=0 required <?php echo $oldInfo['jobUrgency'] == 0 ? 'checked' : ''?>>
                <label for="low">Low Urgency - "I need it done, but it's not time sensitive"</label>
            </div>
            <div>
                <input type="radio" id="medium" name="jobUrgency" value=1 <?php echo $oldInfo['jobUrgency'] == 1 ? 'checked' : ''?>>
                <label for="medium">Medium Urgency - "I need it done within a month"</label>
            </div>
            <div>
                <input type="radio" id="high" name="jobUrgency" value=2 <?php echo $oldInfo['jobUrgency'] == 2 ? 'checked' : ''?>>
                <label for="high">High Urgency - "I need it done this week"</label>
            </div>
            <div>
                <input type="radio" id="critical" name="jobUrgency" value=3 <?php echo $oldInfo['jobUrgency'] == 3 ? 'checked' : ''?>>
                <label for="critical">Critical Urgency - "I needed it done yesterday!"</label>
            </div>
           
            <label for="coverImage">Change Cover Photo:</label>
            <input type="file" id="coverImage" name="coverImage" accept="image/*">
            
            <label for="otherImage">Upload Additional Photo(s):</label>
            <input type="file" id="otherImages" name="otherImages[]" accept="image/*" multiple>

            <button type="submit">Update Request</button>
        </form>
    </div>
</body>
</html>
