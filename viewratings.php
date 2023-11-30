<?php
session_start();

include 'DBCredentials.php';
if(isset($_SESSION['contractorEmail'])){
    $userEmail = $_SESSION['contractorEmail'];
  } else {
    header("Location: ContractorLogin.php?redirect=authFail");
    exit();
  }

function connectToDB() {
    global $HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME, $conn;
    $conn = new mysqli($HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME);

    if ($conn->connect_error) {
        die("Connection issue: " . $conn->connect_error);
    }
    return $conn;
}

$db = connectToDB();

$getNameQuery = "SELECT contractorName FROM contractor WHERE contractorEmail = '$userEmail'";
$result = $db->query($getNameQuery);

if ($result) {
    $row = $result->fetch_assoc();
    $userName = $row['contractorName'];
} else {
    $userName = "Contractor";
}


$overallRating = 0;
$overallQualityRating = 0;
$overallCommunicationRating = 0;
$overallTimelinessRating = 0;
$overallPriceRating = 0;
$ratingCount = 0;

$getRatingsQuery = "SELECT * FROM rating WHERE contractorID = ?";
$stmt = $db->prepare($getRatingsQuery);
$stmt->bind_param("i", $_SESSION['contractorID']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $overallRating += ($row['qualityRating'] + $row['communicationRating'] + $row['timelinessRating'] + $row['priceRating']);
        $overallQualityRating += $row['qualityRating'];
        $overallCommunicationRating += $row['communicationRating'];
        $overallTimelinessRating += $row['timelinessRating'];
        $overallPriceRating += $row['priceRating'];
        $ratingCount++;
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
    <link rel="stylesheet" href="css/ContractorRatings.css">
    <title>View Ratings</title>
</head>

<body>
   <header>
        <div class="dropdown">
            <button class="dropbtn">...</button>
            <div class="dropdown-content">
				<a href="ContractorPage.php">Home</a>
                <a href="#">Messages</a>
                <a href="AvailableJobs.php">Available Jobs</a>
                <a href="#">Job History</a>
                <a href="ViewRatings.php">View Ratings</a>
                <a href="ContractorUpdatePage.php">Account Settings</a>
                <a href="ContractorLogin.php">Log Out</a>
            </div>
        </div>
		<div class="welcome-contractor">Welcome, <?php echo isset($userName) ? $userName : 'Contractor'; ?></div>
    </header>

    <div class="centered-content">
        <div class="card">
            <h2>Your Overall Rating</h2>

            <?php
            if ($ratingCount > 0) {
                $finalOverallRating = $ratingCount > 0 ? ($overallRating / ($ratingCount * 4)) : 0;
            ?>

                <div class="stars" id="overallStars">
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <span class="fa fa-star <?php echo $i <= ($finalOverallRating * 5) ? 'checked' : ''; ?>"></span>
                    <?php endfor; ?>
                </div>

                <div class="rating-item">
                    <p>Quality : <?php echo number_format($overallQualityRating / $ratingCount, 2); ?></p>
                </div>

                <div class="rating-item">
                    <p>Communication: <?php echo number_format($overallCommunicationRating / $ratingCount, 2); ?></p>
                </div>

                <div class="rating-item">
                    <p>Timeliness: <?php echo number_format($overallTimelinessRating / $ratingCount, 2); ?></p>
                </div>

                <div class="rating-item">
                    <p>Price: <?php echo number_format($overallPriceRating / $ratingCount, 2); ?></p>
                </div>

            <?php
            } else {
                // Display full stars for each category if no ratings are available
                echo '<div class="stars" id="overallStars">';
                for ($i = 1; $i <= 5; $i++) {
                    echo '<span class="fa fa-star checked"></span>';
                }
                echo '</div>';              
                echo '<div class="rating-item"><p>Quality: &#9733;&#9733;&#9733;&#9733;&#9733;</p></div>';
                echo '<div class="rating-item"><p>Communication: &#9733;&#9733;&#9733;&#9733;&#9733;</p></div>';
                echo '<div class="rating-item"><p>Timeliness: &#9733;&#9733;&#9733;&#9733;&#9733;</p></div>';
                echo '<div class="rating-item"><p>Price: &#9733;&#9733;&#9733;&#9733;&#9733;</p></div>';
            }
            ?>

        </div>
    </div>
</body>

</html>
