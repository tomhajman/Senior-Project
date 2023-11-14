<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contractor Ratings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
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

        .checked {
            color: orange;
        }

        .stars {
            font-size: 24px;
            cursor: pointer;
        }

        .centered-content {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f2f2f2;
        }

        .card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        }

        .view-ratings {
            text-align: center;
            margin-top: 20px;
        }

        .rating-table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        .rating-table th,
        .rating-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php
    session_start();
    include 'DBCredentials.php';

    // Check if contractor session information exists
    if (!isset($_SESSION['contractorEmail'])) {
        // Redirect to contractors page or another location
        header('Location: Contractors.php');
        exit();
    }

    $userEmail = $_SESSION['contractorEmail'];

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

    $getContractorNameQuery = "SELECT contractorName FROM contractors WHERE contractorEmail = '$userEmail'";
    $contractorResult = $db->query($getContractorNameQuery);
    if ($contractorResult) {
        $contractorRow = $contractorResult->fetch_assoc();
        $contractorName = $contractorRow['contractorName'];
    } else {
        $contractorName = "Contractor";
    }

    function getOverallAverageRating($db, $contractorEmail)
    {
        $overallAvgQuery = "SELECT AVG(rating) AS overall_average FROM rating WHERE contractorID = (SELECT contractorID FROM contractors WHERE contractorEmail = '$contractorEmail')";
        $result = $db->query($overallAvgQuery);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $averageRating = $row['overall_average'];
            // Convert numeric rating to stars
            $stars = str_repeat('<span class="fa fa-star checked"></span>', round($averageRating));
            return $stars;
        } else {
            return str_repeat('<span class="fa fa-star checked"></span>', 5);
        }
    }

    function getRatingTable($db, $contractorEmail)
    {
        $ratingTableQuery = "SELECT category, rating FROM rating WHERE contractorID = (SELECT contractorID FROM contractors WHERE contractorEmail = '$contractorEmail')";
        $result = $db->query($ratingTableQuery);

        if ($result && $result->num_rows > 0) {
            $tableHtml = '<table class="rating-table">
                            <tr>
                                <th>Category</th>
                                <th>Rating</th>
                            </tr>';

            foreach ($result as $row) {
                $stars = str_repeat('<span class="fa fa-star checked"></span>', $row['rating']);
                $tableHtml .= '<tr>
                                  <td>' . $row['category'] . '</td>
                                  <td>' . $stars . '</td>
                                </tr>';
            }

            $tableHtml .= '</table>';
            return $tableHtml;
        } else {
            // No ratings available, display 5 stars
            return '<p>No ratings available. ' . '</p>';
        }
    }
    ?>
    <header>
        <div class="dropdown">
            <button class="dropbtn">...</button>
            <div class="dropdown-content">
                <a href="ContractorPage.php">Home</a>
                <a href="#">Messages</a>
                <a href="AvailableJobs.php">Available Jobs</a>
                <a href="#">Job History</a>
                <a href="ViewContractorRatings.php">View Ratings</a>
                <a href="ContractorUpdatePage.php">Account Settings</a>
                <a href="ContractorLogin.php">Log Out</a>
            </div>
        </div>
        <div class="welcome-user">
            Welcome, <?php echo $userName; ?><br>
        </div>
    </header>

    <div class="centered-content">
        <div class="card">
            <h2><?php echo $contractorName; ?>'s Ratings</h2>

            <div class="view-ratings">
                <h3>Overall Rating</h3>
                <?php echo getOverallAverageRating($db, $userEmail); ?>

                <h3>Category-wise Ratings</h3>
                <?php echo getRatingTable($db, $userEmail); ?>
            </div>
        </div>
    </div>


</body>

</html>


