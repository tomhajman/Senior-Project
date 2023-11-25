<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
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


        .centered-content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f2f2f2;
        }

        .card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        }

        .stars {
            font-size: 24px;
            cursor: pointer;
        }

        .stars .fa-star.checked {
            color: orange;
        }

        .submit-button {
            background-color: #333;
            color: #fff;
            padding: 10px;
            border: none;
            cursor: pointer;
        }

        #thankYouMessage {
            color: #3e8e41;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<?php
session_start();
include 'DBCredentials.php';
$userEmail = $_SESSION['customerEmail'];

function connectToDB()
{
    global $HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME, $conn;
    $conn = new mysqli($HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME);

    if ($conn->connect_error) {
        die("Connection issue: " . $conn->connect_error);
    }
    return $conn;
}

$db = connectToDB();

$getFNameQuery = "SELECT customerFirstName FROM customer WHERE customerEmail = ?";
$stmt = $db->prepare($getFNameQuery);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    $row = $result->fetch_assoc();
    $userFName = $row['customerFirstName'];
} else {
    $userFName = "User";
}

function getCustomerID($db, $userEmail)
{
    $getCustomerIDQuery = "SELECT customerID FROM customer WHERE customerEmail = ?";
    $stmt = $db->prepare($getCustomerIDQuery);
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['customerID'];
    } else {
        return null;
    }
}

$customerID = getCustomerID($db, $userEmail);

function saveRating($qualityRating, $communicationRating, $timelinessRating, $priceRating, $contractorID, $jobID)
{
    global $db;
    // Insert the ratings into the database using prepared statement with bind parameters
    $insertRatingQuery = "INSERT INTO rating (qualityRating, communicationRating, timelinessRating, priceRating, contractorID, jobID)
                        VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($insertRatingQuery);

    if ($stmt === false) {
        die('Error preparing statement: ' . $db->error);
    }

    $stmt->bind_param("iiiiii", $qualityRating, $communicationRating, $timelinessRating, $priceRating, $contractorID, $jobID);

    if ($stmt->execute()) {
        // Ratings saved successfully
        return true;
    } else {
        // Error occurred while saving ratings
        echo 'Error: ' . $stmt->error; // Print the error message for debugging
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['jobID']) && is_numeric($_POST['jobID'])) {
        $jobID = $_POST['jobID'];
        $qualityRating = $_POST['qualityRating'];
        $communicationRating = $_POST['communicationRating'];
        $timelinessRating = $_POST['timelinessRating'];
        $priceRating = $_POST['priceRating'];

        // Fetch the job and contractor information from the database
        $getJobInfoQuery = "SELECT ContractorID FROM customerJob WHERE jobID = ?";
        $stmt = $db->prepare($getJobInfoQuery);
        $stmt->bind_param("i", $jobID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $jobInfoRow = $result->fetch_assoc();
            $contractorID = $jobInfoRow['ContractorID'];

            // Save the ratings to the database
            if (saveRating($qualityRating, $communicationRating, $timelinessRating, $priceRating, $contractorID, $jobID)) {
                // Display a thank you message
                echo '<script>displayThankYouMessage();</script>';
            } else {
                // Handle error if ratings couldn't be saved
                echo '<script>alert("Error: Unable to save ratings.");</script>';
            }
        }
    }
}

$db->close();
?>
<!-- The rest of your HTML code remains unchanged -->

   <header>
    <div class="dropdown">
        <button class="dropbtn">...</button>
        <div class="dropdown-content">
			<a href="CustomerPage.php">Home</a>
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


    <div class="centered-content">
        <div class="card">
            <h2>How would you rate this contractor?</h2>
            Quality of Work:
            <div class="stars" id="qualityStars">
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                    <span class="fa fa-star" onclick="rateStar('qualityStars', <?php echo $i; ?>)"></span>
                <?php endfor; ?>
            </div>
            <br>

            <!-- Repeat similar sections for other rating criteria -->
            <!-- Communication -->
            Communication:
            <div class="stars" id="communicationStars">
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                    <span class="fa fa-star" onclick="rateStar('communicationStars', <?php echo $i; ?>)"></span>
                <?php endfor; ?>
            </div>
            <br>

            <!-- Timeliness -->
            Timeliness:
            <div class="stars" id="timelinessStars">
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                    <span class="fa fa-star" onclick="rateStar('timelinessStars', <?php echo $i; ?>)"></span>
                <?php endfor; ?>
            </div>
            <br>

            <!-- Price -->
            Price:
            <div class="stars" id="priceStars">
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                    <span class="fa fa-star" onclick="rateStar('priceStars', <?php echo $i; ?>)"></span>
                <?php endfor; ?>
            </div>
            <br>

            <form method="post" action="#">
                <input type="hidden" name="qualityRating" id="qualityRating" value="0">
                <input type="hidden" name="communicationRating" id="communicationRating" value="0">
                <input type="hidden" name="timelinessRating" id="timelinessRating" value="0">
                <input type="hidden" name="priceRating" id="priceRating" value="0">
                <input type="hidden" name="jobID" value="<?php echo htmlspecialchars($_GET['jobID']); ?>">
                <input type="button" value="Submit" onclick="submitRating()" class="submit-button">
            </form>

            <div id="thankYouMessage"></div>
        </div>
    </div>

    <script>
        function rateStar(starSetId, rating) {
            const starSet = document.getElementById(starSetId);
            const stars = starSet.getElementsByClassName('fa-star');

            for (let i = 0; i < stars.length; i++) {
                const star = stars[i];
                if (i < rating) {
                    star.classList.add('checked');
                } else {
                    star.classList.remove('checked');
                }
            }

            // Set the corresponding hidden input field value
            const ratingInput = document.getElementById(starSetId.replace('Stars', 'Rating'));
            ratingInput.value = rating;
        }

        function submitRating() {
            // Validate ratings for all criteria
            const qualityRating = document.getElementById('qualityRating').value;
            const communicationRating = document.getElementById('communicationRating').value;
            const timelinessRating = document.getElementById('timelinessRating').value;
            const priceRating = document.getElementById('priceRating').value;

            if (qualityRating === '0' || communicationRating === '0' || timelinessRating === '0' || priceRating === '0') {
                alert('Please rate all criteria before submitting.');
                return;
            }

            // Additional validation logic if needed

            // Submit the form
            document.forms[0].submit();
        }

        function displayThankYouMessage() {
            const thankYouMessage = document.getElementById('thankYouMessage');
            thankYouMessage.textContent = 'Thank you for your rating';
            thankYouMessage.style.color = '#3e8e41'; // Set color to green
        }
    </script>
</body>

</html>
