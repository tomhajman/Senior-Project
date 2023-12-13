<?php
	session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/EditJob.css">
    <title>Edit job</title>

</head>
<body>
	<?php
		//DB connection, session handling.
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
        // Get job info from DB
        if(isset($_GET['id']) && is_numeric($_GET['id'])){
            $stmt = $conn->prepare("SELECT jobType, jobTitle, jobDescription, jobUrgency, customerID FROM customerJob WHERE jobID=?");
            $stmt->bind_param("i", $_GET['id']);
            if(!($stmt->execute())){
                die("job with ID = {$_GET['id']} doesn't exist");
            }
            $result = $stmt->get_result();
            $oldInfo = $result->fetch_assoc();
            $stmt->close();

            $checkAccess = $db->prepare("SELECT customerEmail FROM customer WHERE customerID=?");
            $checkAccess->bind_param("i", $oldInfo['customerID']);
            if($checkAccess->execute()){
                $checkAccess->bind_result($dbEmail);
                $checkAccess->fetch();
                if($dbEmail != $userEmail){
                    header("Location: CustomerManageJobs.php?redirect=accessDenied");
                    exit();
                }
            } else {
                die("Error connecting to database: ". $db->error);
            }
        } else {
            header("Location: CustomerManageJobs.php?redirect=notFound");
            exit();
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
			    echo "<script>alert('Job updated successfully.');</script>";
				echo "<script>window.location='editJob.php?id={$_GET['id']}'</script>";
				//echo "Job updated successfully";
			} else {
			    echo "<script>alert('Error'" . $stmt->error . ");</script>";
				//echo "Error: " . $stmt->error;
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
			<a href="CustomerManageJobs.php">Return to Manage Jobs page</a>
        </form>
    </div>
</body>
</html>
