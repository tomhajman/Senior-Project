<!DOCTYPE html>
<html lang="en">

<head>
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
            height: 100vh;
            background-color: #f2f2f2;
        }

        .card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
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

    function saveRating($qualityRating, $communicationRating, $timelinessRating, $priceRating, $contractorID, $customerEmail)
    {
        $db = connectToDB();

        // Insert the ratings into the database
        $insertRatingQuery = "INSERT INTO ratings (QualityRating, CommunicationRating, TimelinessRating, PriceRating, ContractorID, CustomerEmail)
                            VALUES ('$qualityRating', '$communicationRating', '$timelinessRating', '$priceRating', '$contractorID', '$customerEmail')";
        $db->query($insertRatingQuery);

        // Calculate the average rating
        $averageRating = ($qualityRating + $communicationRating + $timelinessRating + $priceRating) / 4;

        // Update the contractor's overall rating
        $updateContractorRatingQuery = "UPDATE contractors SET ContractorRating = '$averageRating' WHERE ContractorID = '$contractorID'";
        $db->query($updateContractorRatingQuery);

        $db->close();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $qualityRating = $_POST['qualityRating'];
        $communicationRating = $_POST['communicationRating'];
        $timelinessRating = $_POST['timelinessRating'];
        $priceRating = $_POST['priceRating'];

        // Fetch the job and contractor information from the database
        $getJobInfoQuery = "SELECT jq.ContractorID
                            FROM JobQuote jq
                            WHERE jq.CustomerEmail = '$userEmail'";
        $jobInfoResult = $db->query($getJobInfoQuery);

        if ($jobInfoResult) {
            $jobInfoRow = $jobInfoResult->fetch_assoc();
            $contractorID = $jobInfoRow['ContractorID'];

            // Save the ratings to the database
            saveRating($qualityRating, $communicationRating, $timelinessRating, $priceRating, $contractorID, $userEmail);
        }
    }

    $db->close();
    ?>
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
            <h2>How would you rate <?php echo $contractorName; ?>?</h2>
            Quality of Work:
            <div class="stars" id="qualityStars">
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>
            </div>
            <br>
            Communication:
            <div class="stars" id="communicationStars">
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>
            </div>
            <br>
            Timeliness:
            <div class="stars" id="timelinessStars">
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>
            </div>
            <br>
            Price:
            <div class="stars" id="priceStars">
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>
            </div>
            <br>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="qualityRating" id="qualityRating" value="0">
                <input type="hidden" name="communicationRating" id="communicationRating" value="0">
                <input type="hidden" name="timelinessRating" id="timelinessRating" value="0">
                <input type="hidden" name="priceRating" id="priceRating" value="0">
                <input type="button" value="Submit" onclick="submitRating()" class="submit-button">
            </form>
            <div id="thankYouMessage"></div>
        </div>
    </div>
    <script>
        function submitRating() {
            const qualityRating = document.getElementById('qualityStars').getElementsByClassName('fa-star checked').length;
            const communicationRating = document.getElementById('communicationStars').getElementsByClassName('fa-star checked').length;
            const timelinessRating = document.getElementById('timelinessStars').getElementsByClassName('fa-star checked').length;
            const priceRating = document.getElementById('priceStars').getElementsByClassName('fa-star checked').length;

            // Set the values of hidden input fields
            document.getElementById('qualityRating').value = qualityRating;
            document.getElementById('communicationRating').value = communicationRating;
            document.getElementById('timelinessRating').value = timelinessRating;
            document.getElementById('priceRating').value = priceRating;

            // Submit the form
            document.forms[0].submit();
        }

        function displayThankYouMessage() {
            const submitButton = document.querySelector('.submit-button');
            const thankYouMessage = document.getElementById('thankYouMessage');
            thankYouMessage.textContent = 'Thank you for your rating';
            thankYouMessage.style.color = '#3e8e41'; // Set color to green
        }

        const stars = document.querySelectorAll('.stars');

        stars.forEach(starSet => {
            Array.from(starSet.children).forEach(star => {
                star.addEventListener('click', function () {
                    const parent = this.parentElement;
                    Array.from(parent.children).forEach((s, i) => {
                        if (i <= Array.from(parent.children).indexOf(this)) {
                            s.classList.add('checked');
                        } else {
                            s.classList.remove('checked');
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>
