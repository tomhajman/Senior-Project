<!DOCTYPE html>
<html>
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
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        }
    </style>
</head>

<header>
    <div class="dropdown">
        <button class="dropbtn">...</button>
        <div class="dropdown-content">
            <a href="CustomerPage.php">Home</a>
            <a href="#">Messages</a>
            <a href="#">Service History</a>
            <a href="Contractors.php">View Contractors</a>
            <a href="CustomerUpdatePage.php">Account Settings</a>
            <a href="CustomerLogin.php">Log Out</a>
        </div>
    </div>
    <div class="welcome-user">
        Welcome, <?php echo $contractorName; ?><br>
        Email: <?php echo $contractorEmail; ?>
    </div>
</header>
<?php
    session_start();
    include 'DBCredentials.php';

    // Fetch contractor details
    $contractorEmail = $_SESSION['contractorEmail'];
    function connectToDB() {
        global $HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME, $conn;
        $conn = new mysqli($HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME);
        
        if ($conn->connect_error) {
            die("Connection issue: " . $conn->connect_error);
        }
        return $conn;
    }
    $db = connectToDB();
    $getContractorNameQuery = "SELECT contractorName FROM contractor WHERE contractorEmail = '$contractorEmail'";
    $resultContractor = $db->query($getContractorNameQuery);
    if ($resultContractor) {
        $rowContractor = $resultContractor->fetch_assoc();
        $contractorName = $rowContractor['contractorName'];
    } else {
        $contractorName = "Contractor";
    }

    // Fetch customer details
    $customerEmail = $_SESSION['customerEmail'];
    $getCustomerFNameQuery = "SELECT customerFirstName FROM customer WHERE customerEmail = '$customerEmail'";
    $resultCustomer = $db->query($getCustomerFNameQuery);
    if ($resultCustomer) {
        $rowCustomer = $resultCustomer->fetch_assoc();
        $customerFName = $rowCustomer['customerFirstName'];
    } else {
        $customerFName = "Customer";
    }
?>
<body>
    <div class="centered-content">
        <div class="card">
            <h2>How would you rate <?php echo $customerFName; ?>?</h2>
            Timeliness:
            <div class="stars" id="timelinessStars">
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
            <input type="button" value="Submit" onclick="submitRating()">
        </div>
    </div>
    <script>
        function submitRating() {
            const communicationRating = document.getElementById('communicationStars').getElementsByClassName('fa-star checked').length;
            const timelinessRating = document.getElementById('timelinessStars').getElementsByClassName('fa-star checked').length;

            // For demonstration purposes, you can print the ratings
            console.log('Communication: ' + communicationRating);
            console.log('Timeliness: ' + timelinessRating);

            // Here you can send these ratings to a server or perform other necessary actions
            // For now, just printing in the console for demonstration
        }

        const stars = document.querySelectorAll('.stars');

        stars.forEach(starSet => {
            Array.from(starSet.children).forEach(star => {
                star.addEventListener('click', function() {
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
