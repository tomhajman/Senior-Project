<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-black.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
    .contractor-card {
      width: 30%;
      margin-bottom: 20px;
      display: inline-block; /* Set contractor cards to be aligned */
    }
    .main-content {
      margin-left: 300px;
    }
  </style>
</head>
<body>

<nav class="w3-sidebar w3-collapse w3-white w3-animate-left" style="z-index:3;width:300px; position: fixed;" id="mySidebar">
  <div class="w3-container">
    <a href="#" onclick="w3_close()" class="w3-hide-large w3-right w3-jumbo w3-padding w3-hover-grey" title="close menu">
      <i class="fa fa-remove"></i>
    </a>
    <img src="#" style="width:45%;" class="w3-round"><br><br>
  </div>
  <div class="w3-bar-block">
    <a href="Contractors.php" onclick="w3_close()" class="w3-bar-item w3-button w3-padding w3-text-teal"><i class="fa fa-briefcase fa-fw w3-margin-right"></i>Available Contractors</a> 
    <a href="CustomerPage.php" onclick="w3_close()" class="w3-bar-item w3-button w3-padding"><i class="fa fa-user fa-fw w3-margin-right"></i>Home</a> 
  </div>
</nav>

<div class="main-content">
  <form class="modal-content" action="" method="post">
    <span class="w3-margin-right">Filter:</span> 
    <button class="w3-button w3-black" name="filterBtn" value="allJobsBtn">ALL</button>
    <button class="w3-button w3-white" name="filterBtn" value="Electrician"><i class="fa fa-wrench w3-margin-right"></i>Electrical</button>
    <button class="w3-button w3-white" name="filterBtn" value="Plumbing"><i class="fa fa-wrench w3-margin-right"></i>Plumbing</button>
    <button class="w3-button w3-white" name="filterBtn" value="Gardening"><i class="fa fa-leaf w3-margin-right"></i>Gardening</button>
    <button class="w3-button w3-white" name="filterBtn" value="HVAC"><i class="fa fa-asterisk w3-margin-right"></i>HVAC</button>
    <button class="w3-button w3-white" name="filterBtn" value="Painting"><i class="fa fa-paint-brush w3-margin-right"></i>Painting</button>
    <button class="w3-button w3-white" name="filterBtn" value="Mounting"><i class="fa fa-wrench w3-margin-right"></i>Mounting</button>
  </form>

  <div class="w3-row-padding w3-center w3-padding-16" id="contractorDetails">
    <?php
      // PHP code to fetch contractor data
      session_start();
      include 'DBCredentials.php';

      function connectToDB() {
        global $HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME, $conn;
        $conn = new mysqli($HOST_NAME, $USERNAME, $PASSWORD, $DB_NAME);

        if ($conn->connect_error) {
          die("Connection issue: ".$conn->connect_error);
        }
        return $conn;
      }

      $db = connectToDB();
      $filter = '';
      
      if (isset($_POST['filterBtn'])) {
        $filter = $_POST['filterBtn'];
      }

      $query = "SELECT ContractorName, ContractorEmail, ContractorPhoneNumber, ContractorExpertise FROM contractor";

      if ($filter !== 'allJobsBtn') {
        $query .= " WHERE ContractorExpertise LIKE '%$filter%'";
      }
      $result = $db->query($query);

      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          $expertise = $row["ContractorExpertise"];
          // Check if the expertise is serialized
          if (is_serialized($expertise)) {
            $expertise = unserialize($expertise);
          }

          echo '<div class="w3-third contractor-card">';
          echo '<ul class="w3-ul w3-border w3-hover-shadow w3-theme-l2">';
          echo '<li class="w3-theme">';
          echo '<p class="w3-large">' . $row["ContractorName"] . '</p>';
          echo '</li>';
          echo '<li><b>Email:</b> ' . $row["ContractorEmail"] . '</li>';
          echo '<li><b>Phone:</b> ' . $row["ContractorPhoneNumber"] . '</li>';
          echo '<li><b>Expertise:</b> ';
          if (is_array($expertise)) {
            $expertiseCount = count($expertise);
            $count = 0;
            foreach ($expertise as $singleExpertise) {
              echo $singleExpertise;
              $count++;
              if ($count < $expertiseCount) {
                echo ', '; // Add a comma if there are more expertise to display
              }
            }
          } else {
            echo $expertise;
          }
          echo '</li>';
          echo '<li class="w3-theme-l5 w3-padding-16">';
          echo '<button class="w3-button w3-teal w3-padding-small"><i class="fa fa-envelope"></i> Message</button>';
          echo '</li>';
          echo '</ul>';
          echo '</div>';
        }
      } else {
        echo "No records found";
      }

      function is_serialized($data) {
        return ($data == serialize(false) || @unserialize($data) !== false);
      }
    ?>
  </div>
</div>

<script>
  function w3_close() {
    document.getElementById("mySidebar").style.display = 'none';
  }

  document.querySelector('.main-content').addEventListener('click', () => {
    document.getElementById("mySidebar").style.display = 'none';
  });
</script>

</body>
</html>



