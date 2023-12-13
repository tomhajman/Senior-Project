<?php
// Start the session at the very beginning of the file, DB connection
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
//Displays user's name in the corner.
$getNameQuery = "SELECT contractorName FROM contractor WHERE contractorEmail = '$userEmail'";
$result = $db->query($getNameQuery);

if ($result) {
    $row = $result->fetch_assoc();
    $userName = $row['contractorName'];
} else {
    $userName = "Contractor";
}
//Alert for new messages.
$getUnreadMessagesCount = $db->prepare("SELECT COUNT(*) AS unreadCount
    FROM messages 
    WHERE conversationID IN (
        SELECT conversationID 
        FROM conversations 
        WHERE contractorEmail = ?
    ) AND sender != ? AND isRead = false;");
$getUnreadMessagesCount->bind_param("ss", $userEmail, $userEmail);
if($getUnreadMessagesCount->execute()){
$result = $getUnreadMessagesCount->get_result();
$row = $result->fetch_assoc();
$unreadCount = $row['unreadCount'];
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
  
</head>
<body>
    <header>
        <div class="dropdown">
            <button class="dropbtn">...</button>
            <div class="dropdown-content">
                <a href="ContractorMessageCenter.php">Messages</a>
                <a href="AvailableJobs.php">Available Jobs</a>
                <a href="ContractorManageJobs.php">Job History</a>
                <a href="ViewRatings.php">View Ratings</a>
                <a href="ContractorUpdatePage.php">Account Settings</a>
                <a href="Logout.php">Log Out</a>
            </div>
        </div>
        <div class="welcome-contractor">Welcome, <?php echo isset($userName) ? $userName : 'Contractor'; ?></div>
    </header>

    <div class="w3-content w3-container w3-padding-64">
        <?php if ($unreadCount > 0): ?>
        <div class="message-notification"><?php echo "$unreadCount unread message(s) - "; ?><a href='ContractorMessageCenter.php'>Click to view</a></div>
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
                   $month = date("n"); // Current month
                    $year = date("Y"); // Current year

                    function buildCalendar($month, $year) {
                        $firstDay = mktime(0, 0, 0, $month, 1, $year);
                        $daysInMonth = date("t", $firstDay);
                        $today = date('j');

                        echo "<div class='calendar-controls'>";
                        echo "<span>" . date("F Y", $firstDay) . "</span>";
                        echo "</div>";

                        echo "<table>";
                        echo "<tr>";
                        echo "<th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>";
                        echo "</tr>";

                        $currentDay = 1;
                        $dayOfWeek = date("w", $firstDay);

                        echo "<tr>";

                        foreach (range(1, $dayOfWeek - 1) as $i) {
                            echo "<td></td>";
                        }

                        foreach (range(1, $daysInMonth) as $currentDay) {
                            if ($dayOfWeek == 7) {
                                echo "</tr><tr>";
                                $dayOfWeek = 0;
                            }

                            $class = ($currentDay == $today) ? 'today' : '';
                            echo "<td class='$class'>$currentDay</td>";

                            $dayOfWeek++;
                        }

                        foreach (range($dayOfWeek, 6) as $i) {
                            echo "<td></td>";
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
              <?php /*
                    if (count($recentWorks) > 0) {
                        foreach ($recentWorks as $work) {
                            echo "<div class='recent-works-item'>";
                            echo "<p><strong>Job Title:</strong> " . $work['jobTitle'] . "</p>";
                            echo "<p><strong>Job Date:</strong> " . $work['jobDate'] . "</p>";
                            echo "<p><strong>Job Description:</strong> " . $work['jobDescription'] . "</p>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p>No recent jobs.</p>";
                    }*/
                ?>
            </div>
        </div>
    </div>
</body>
</html>
