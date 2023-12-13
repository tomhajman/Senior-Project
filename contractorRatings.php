<?php
//DB connection, session handling
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

$jobID = isset($_GET['jobID']) ? $_GET['jobID'] : null;

$checkRatingQuery = "SELECT * FROM rating WHERE jobID = ? AND customerID = ?";
// Moved outside of the if statement
$stmtCheckRating = $db->prepare($checkRatingQuery);
$stmtCheckRating->bind_param("ii", $jobID, $_SESSION['customerID']);
$stmtCheckRating->execute();
$resultCheckRating = $stmtCheckRating->get_result();

if ($resultCheckRating) {
    $rowCheckRating = $resultCheckRating->fetch_assoc();

    if (!empty($rowCheckRating)) {
        $prefilledQualityRating = $rowCheckRating['qualityRating'];
        $prefilledCommunicationRating = $rowCheckRating['communicationRating'];
        $prefilledTimelinessRating = $rowCheckRating['timelinessRating'];
        $prefilledPriceRating = $rowCheckRating['priceRating'];
    } else {
        $prefilledQualityRating = 0;
        $prefilledCommunicationRating = 0;
        $prefilledTimelinessRating = 0;
        $prefilledPriceRating = 0;
    }
} else {
    $prefilledQualityRating = 0;
    $prefilledCommunicationRating = 0;
    $prefilledTimelinessRating = 0;
    $prefilledPriceRating = 0;
}

function saveRating($qualityRating, $communicationRating, $timelinessRating, $priceRating, $contractorID, $jobID, $customerID)
{
    global $db, $checkRatingQuery;
    $insertRatingQuery = "INSERT INTO rating (qualityRating, communicationRating, timelinessRating, priceRating, contractorID, jobID, customerID)
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($insertRatingQuery);

    if ($stmt === false) {
        die('Error preparing statement: ' . $db->error);
    }

    $stmt->bind_param("iiiiiii", $qualityRating, $communicationRating, $timelinessRating, $priceRating, $contractorID, $jobID, $customerID);

    if ($stmt->execute()) {
        // Fetch the updated ratings
        $stmt->close();
        $stmtCheckRating = $db->prepare($checkRatingQuery);
        $stmtCheckRating->bind_param("ii", $jobID, $_SESSION['customerID']);
        $stmtCheckRating->execute();
        $resultCheckRating = $stmtCheckRating->get_result();

        if ($resultCheckRating) {
            $row = $resultCheckRating->fetch_assoc();

            if (!empty($row)) {
                return $row;
            }
        }
    }

    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['jobID']) && is_numeric($_POST['jobID'])) {
        $jobID = $_POST['jobID'];
        $qualityRating = $_POST['qualityRating'];
        $communicationRating = $_POST['communicationRating'];
        $timelinessRating = $_POST['timelinessRating'];
        $priceRating = $_POST['priceRating'];

        $getJobInfoQuery = "SELECT ContractorID, customerID FROM customerJob WHERE jobID = ?";
        $stmt = $db->prepare($getJobInfoQuery);
        $stmt->bind_param("i", $jobID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            $jobInfoRow = $result->fetch_assoc();

            if (!empty($jobInfoRow)) {
                $contractorID = $jobInfoRow['ContractorID'];
                $customerID = $jobInfoRow['customerID'];

                $row = saveRating($qualityRating, $communicationRating, $timelinessRating, $priceRating, $contractorID, $jobID, $customerID);

                if ($row) {
                    $prefilledQualityRating = $row['qualityRating'];
                    $prefilledCommunicationRating = $row['communicationRating'];
                    $prefilledTimelinessRating = $row['timelinessRating'];
                    $prefilledPriceRating = $row['priceRating'];
                } else {
                    echo '<script>alert("Error: Unable to save ratings.");</script>';
                }
            }
        }
    }
}

$db->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/ratings.css">
</head>

<body>
    <header>
        <div class="dropdown">
            <button class="dropbtn">...</button>
            <div class="dropdown-content">
                <a href="CustomerPage.php">Home</a>
                <a href="CustomerMessageCenter.php">Messages</a>
                <a href="CustomerManageJobs.php">Service History</a>
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
            Communication:
            <div class="stars" id="communicationStars">
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                    <span class="fa fa-star" onclick="rateStar('communicationStars', <?php echo $i; ?>)"></span>
                <?php endfor; ?>
            </div>
            <br>

            Timeliness:
            <div class="stars" id="timelinessStars">
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                    <span class="fa fa-star" onclick="rateStar('timelinessStars', <?php echo $i; ?>)"></span>
                <?php endfor; ?>
            </div>
            <br>

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
            if (ratingInput) {
                ratingInput.value = rating;
            }
        }

        function submitRating() {
            // Validate ratings for all criteria
            const qualityRating = document.getElementById('qualityRating') ? document.getElementById('qualityRating').value : 0;
            const communicationRating = document.getElementById('communicationRating') ? document.getElementById('communicationRating').value : 0;
            const timelinessRating = document.getElementById('timelinessRating') ? document.getElementById('timelinessRating').value : 0;
            const priceRating = document.getElementById('priceRating') ? document.getElementById('priceRating').value : 0;

            if (qualityRating === '0' || communicationRating === '0' || timelinessRating === '0' || priceRating === '0') {
                alert('Please rate all criteria before submitting.');
                return;
            }

            // Additional validation logic if needed

            // Display thank-you message
            displayThankYouMessage();

            // Submit the form
            document.forms[0].submit();
        }

        function displayThankYouMessage() {
            const thankYouMessage = document.getElementById('thankYouMessage');
            if (thankYouMessage) {
                thankYouMessage.innerHTML = 'Thank you for your rating';
                thankYouMessage.style.color = '#3e8e41'; // Set color to green
            }
        }
		const jobID = <?php echo json_encode(htmlspecialchars($_GET['jobID'])); ?>;
    const updatedRatings = <?php echo json_encode([
        'qualityRating' => $prefilledQualityRating,
        'communicationRating' => $prefilledCommunicationRating,
        'timelinessRating' => $prefilledTimelinessRating,
        'priceRating' => $prefilledPriceRating,
    ]); ?>;

    // Prefill the star ratings based on the retrieved data
    function prefillRatings() {
        rateStar('qualityStars', updatedRatings.qualityRating);
        rateStar('communicationStars', updatedRatings.communicationRating);
        rateStar('timelinessStars', updatedRatings.timelinessRating);
        rateStar('priceStars', updatedRatings.priceRating);
    }

    // Call the prefillRatings function after the page loads
    window.onload = prefillRatings;
</script>
</body>

</html>
