<?php
// Start the session at the very beginning of the file
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

$getUnreadMessagesCount = $db->prepare("SELECT COUNT(*) AS unreadCount
    FROM messages 
    WHERE conversationID IN (
        SELECT conversationID 
        FROM conversations 
        WHERE contractorEmail = ?
    ) AND sender != ? AND isRead = false;");
$getUnreadMessagesCount->bind_param("ss", $userEmail, $userEmail);
if ($getUnreadMessagesCount->execute()) {
    $result = $getUnreadMessagesCount->get_result();
    $row = $result->fetch_assoc();
    $unreadCount = $row['unreadCount'];
}

$month = date("n"); // Current month
$year = date("Y"); // Current year
$recentWorksQuery = "SELECT jobTitle, jobType, jobCity
                     FROM customerJob
                     WHERE contractorID = ? AND jobStatus = 'completed'
                     ORDER BY jobDate DESC
                     LIMIT 3"; // Updated to fetch only the last 3 completed jobs

$recentWorksQuery = "SELECT jobTitle, jobType, jobCity
                     FROM customerJob
                     WHERE contractorID = ? AND jobStatus = 'completed'
                     ORDER BY jobDate DESC
                     LIMIT 3"; // Updated to fetch only the last 3 completed jobs

$getRecentWorks = $db->prepare($recentWorksQuery);

if ($getRecentWorks) {
    $getRecentWorks->bind_param("s", $userEmail); // Assuming contractorID is stored in $userEmail, replace it with the actual field name
    
    if ($getRecentWorks->execute()) {
        $recentWorksResult = $getRecentWorks->get_result();
        $recentWorks = $recentWorksResult->fetch_all(MYSQLI_ASSOC);
    } else {
        // Handle the error if needed
    }

    $getRecentWorks->close(); // Close the statement
} else {
    // Handle the error if needed
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/ConPage.css">
	<title> Welcome </title>
</head>
<body>
<header>
    <div class="dropdown">
        <button class="dropbtn">...</button>
        <div class="dropdown-content">
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

<div class="w3-content w3-container w3-padding-32">
    <?php if ($unreadCount > 0): ?>
        <div class="message-notification"><?php echo "$unreadCount unread message(s) - "; ?><a
                    href='ContractorMessageCenter.php'>Click to view</a></div>
    <?php endif; ?>
    <div class="w3-row">
        <div class="w3-col manage-jobs-button">
            <a href="ContractorManageJobs.php" class="w3-button w3-jumbo">Manage Jobs</a>
        </div>
        <div class="w3-col available-jobs-button">
            <a href="AvailableJobs.php" class="w3-button w3-jumbo">Find Jobs</a>
        </div>
    </div>

    <div class="calendar-container">
        <div class="calendar">
            <?php
function buildCalendar($month, $year)
{
    $firstDay = mktime(0, 0, 0, $month, 1, $year);
    $daysInMonth = date("t", $firstDay);
    $today = date('j');

    echo "<div class='calendar-controls'>";
    echo "<span>" . date("F Y", $firstDay) . "</span>";
    echo "</div>";

    echo "<table class='calendar-table'>";
    echo "<tr>";
    echo "<th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>";
    echo "</tr>";

    $currentDay = 1;
    $dayOfWeek = date("w", $firstDay);

    echo "<tr>";

    // Output empty cells for days before the first day of the month
    foreach (range(1, $dayOfWeek) as $i) {
        echo "<td></td>";
    }

    // Output the days of the month
    while ($currentDay <= $daysInMonth) {
        if ($dayOfWeek == 7) {
            echo "</tr><tr>";
            $dayOfWeek = 0;
        }

        $class = ($currentDay == $today) ? 'today' : '';
        echo "<td class='calendar-day $class'>$currentDay</td>";

        $currentDay++;
        $dayOfWeek++;
    }

    // Output empty cells for remaining days in the last week
    while ($dayOfWeek < 7) {
        echo "<td></td>";
        $dayOfWeek++;
    }

    echo "</tr>";
    echo "</table>";
}



            buildCalendar($month, $year);
            ?>
        </div>
    </div>

	<div class="recent-works-container">
    <div class="recent-works">
        <h2>Recent Works</h2>
        <?php
        // Check if $recentWorks is set and not empty before trying to count or iterate over it
        if (isset($recentWorks) && is_array($recentWorks) && count($recentWorks) > 0) {
            $counter = 0; // Counter to limit to the last three recent works
            foreach ($recentWorks as $work) {
                if ($counter < 3) {
                    echo "<div class='recent-works-item'>";
					echo "<p><strong>Completed Date:</strong> " . $work['CompletionDate'] . "</p>";
                    echo "<p><strong>Title:</strong> " . $work['jobTitle'] . "</p>";
                    echo "<p><strong>Type:</strong> " . $work['jobType'] . "</p>";
                    echo "<p><strong>City:</strong> " . $work['jobCity'] . "</p>";
                    echo "<a href='recentJobs.php?jobTitle=" . urlencode($work['jobTitle']) . "'>Details</a>";
                    echo "</div>";
                    $counter++;
                } else {
                    break; // Break the loop after showing the last three recent works
                }
            }
        } else {
            echo "<p class='no-recent-works'>No recent works yet..</p>";
        }
        ?>
    </div>
</div>


</div>
</body>
</html>
