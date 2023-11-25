<?php
// Start the session at the very beginning of the file
session_start();

include 'DBCredentials.php';
$userEmail = $_SESSION['contractorEmail'] ?? '';

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
    <style>
        body, h1, h2, h3, h4, h5, h6 {
            font-family: "Lato", sans-serif;
        }

        body, html {
            height: 100%;
            color: #333;
            line-height: 1.8;
        }

        .bgimg-1, .bgimg-2, .bgimg-3 {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .bgimg-1 {
            background-image: url('/w3images/parallax1.jpg');
            min-height: 100%;
        }

        .bgimg-2 {
            background-image: url("/w3images/parallax2.jpg");
            min-height: 400px;
        }

        .bgimg-3 {
            background-image: url("/w3images/parallax3.jpg");
            min-height: 400px;
        }

        .w3-wide {
            letter-spacing: 10px;
        }

        .w3-hover-opacity {
            cursor: pointer;
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
        }

        .dropdown-content a {
            color: gray;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
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

        .welcome-contractor {
            margin-right: 10px;
            margin-left: auto;
        }

        .w3-content {
            padding: 64px;
        }

        .w3-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .w3-col {
            text-align: center;
            margin-bottom: 16px;
        }

        .w3-col h4 {
            margin: 10px 0;
        }

        .w3-col p {
            margin: 0;
        }

        .manage-jobs-button,
        .available-jobs-button {
            width: 48%;
            margin-top: 20px;
            display: inline-block;
        }

        .manage-jobs-button a,
        .available-jobs-button a {
            font-size: 24px;
            background-color: green;
            padding: 5px 10px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            display: block;
        }

        .manage-jobs-button a:hover,
        .available-jobs-button a:hover {
            background-color: #3e8e41;
        }

        .calendar-container,
        .recent-works-container {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }

        .calendar {
            text-align: center;
        }

        .calendar table {
            width: 100%;
        }

        .calendar table th,
        .calendar table td {
            text-align: center;
            padding: 10px;
        }

        .calendar table td.today {
            background-color: #ddd;
        }

        .calendar-controls {
            text-align: center;
            margin-top: 10px;
        }

        .calendar-controls button {
            padding: 5px 10px;
            font-size: 16px;
            margin: 0 5px;
        }

        .recent-works {
            text-align: center;
        }

        .recent-works h2 {
            margin-bottom: 10px;
        }

        .recent-works-item {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
        }

        .message-notification {
            font-size: 30px;
            font-weight: bold;
            justify-content: center;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <div class="dropdown">
            <button class="dropbtn">...</button>
            <div class="dropdown-content">
                <a href="ContractorMessageCenter.php">Messages</a>
                <a href="AvailableJobs.php">Available Jobs</a>
                <a href="#">Job History</a>
                <a href="ViewContractorRatings.php">View Ratings</a>
                <a href="ContractorUpdatePage.php">Account Settings</a>
                <a href="ContractorLogin.php">Log Out</a>
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
