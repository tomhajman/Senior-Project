<?php
session_start();

include 'DBCredentials.php';

if (isset($_SESSION['contractorEmail'])) {
    $userEmail = $_SESSION['contractorEmail'];
} else {
    header("Location: ContractorLogin.php?redirect=authFail");
    exit();
}

function connectToDB()
{
    global $HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME;
    $conn = new mysqli($HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME);

    if ($conn->connect_error) {
        die("Connection issue: " . $conn->connect_error);
    }
    return $conn;
}

$db = connectToDB();

$getNameQuery = "SELECT contractorName, contractorID FROM contractor WHERE contractorEmail = '$userEmail'";
$result = $db->query($getNameQuery);

if ($result) {
    $row = $result->fetch_assoc();
    $userName = $row['contractorName'];
    $contractorID = $row['contractorID'];
} else {
    $userName = "Contractor";
    // Handle the case when contractor information is not available
    exit("Contractor information not available.");
}

// Fetch data from the database
$sql = "SELECT CompletionDate, JobTitle, JobCounty, JobCity, JobUrgency FROM customerJob WHERE ContractorID = '$contractorID'";
$result = $db->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Contractor Jobs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/ConPage.css">
    
</head>
<body>
    <header>
        <div class="dropdown">
            <button class="dropbtn">...</button>
            <div class="dropdown-content">
				<a href="CustomerPage.php">Home</a>
                <a href="ContractorMessageCenter.php">Messages</a>
                <a href="AvailableJobs.php">Available Jobs</a>
                <a href="RecentJobs.php">Job History</a>
                <a href="ViewRatings.php">View Ratings</a>
                <a href="ContractorUpdatePage.php">Account Settings</a>
                <a href="Logout.php">Log Out</a>
            </div>
        </div>
        <div class="welcome-contractor">Welcome, <?php echo isset($userName) ? $userName : 'Contractor'; ?></div>
    </header>

    <div class="w3-container">
        <h2>Your recent works</h2>

        <table class="w3-table-all w3-hoverable">
            <thead>
                <tr class="w3-light-grey">
                    <th>Completed Date</th>
                    <th>Title</th>
                    <th>County</th>
                    <th>City</th>
                    <th>Urgency</th>
                </tr>
            </thead>

            <?php
            // Handle SQL errors
            if (!$result) {
                die("Error in SQL query: " . $db->error);
            }

            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['CompletionDate']}</td>
                            <td>{$row['JobTitle']}</td>
                            <td>{$row['JobCounty']}</td>
                            <td>{$row['JobCity']}</td>
                            <td>{$row['JobUrgency']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No Recent Jobs Yet.</td></tr>";
            }

            // Close connection
            $db->close();
            ?>
        </table>
    </div>
</body>
</html>
