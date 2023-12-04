<!DOCTYPE html>
<html lang="en">

<head>
<title>Fixit.Club</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/main.css">
</head>

<body>

<header>
  <!-- Navbar (sit on top) -->
  <div class="w3-top">
    <div class="w3-bar" id="myNavbar">
      <a class="w3-bar-item w3-button w3-hover-black w3-hide-medium w3-hide-large w3-right" href="javascript:void(0);" onclick="toggleFunction()" title="Toggle Navigation Menu">
        <i class="fa fa-bars"></i>
      </a>
      <a href="#home" class="w3-bar-item w3-button">HOME</a>
      <a href="#services" class="w3-bar-item w3-button">SERVICES</a>
      <a href="CustomerLogin.php" class="w3-bar-item w3-button w3-right">CUSTOMER LOG IN</a>
      <a href="ContractorLogin.php" class="w3-bar-item w3-button w3-right">CONTRACTOR LOG IN</a>
    </div>

    <!-- Navbar on small screens -->
    <div id="navDemo" class="w3-bar-block w3-white w3-hide w3-hide-large w3-hide-medium">
      <a href="#services" class="w3-bar-item w3-button" onclick="toggleFunction()">SERVICES</a>
    </div>
  </div>
</header>

<!-- First Parallax Image with Logo Text -->
<div class="bgimg-1 w3-display-container w3-opacity-min" id="home">
  <div class="logo-container">
    <img src="css/Fixitclub.WEBP" class="logo" alt="Company Logo">
  </div>

  <!-- Welcome text -->
  <div class="w3-display-bottommiddle welcome-text" style="white-space:nowrap;">
    <span class="w3-center w3-padding-large w3-black w3-xlarge w3-wide w3-animate-opacity">Welcome to Fixit.club!</span>
  </div>
</div>



<!-- Container (Services Section) -->
<div class="w3-content w3-container w3-padding-64" id="services">
  <h3 class="w3-center">OUR SERVICES</h3>
  <div class="w3-row">
     <div class="w3-col m4 w3-center w3-padding-large">
        <i class="fa fa-television w3-margin-bottom w3-jumbo"></i>
        <h4>TV Mounting</h4>
        <p>We professionally mount your TV on the wall.</p>
      </div>
      <div class="w3-col m4 w3-center w3-padding-large">
        <i class="fa fa-wrench w3-margin-bottom w3-jumbo"></i>
        <h4>Plumbing</h4>
        <p>We fix plumbing issues efficiently.</p>
      </div>
      <div class="w3-col m4 w3-center w3-padding-large">
        <i class="fa fa-bolt w3-margin-bottom w3-jumbo"></i>
        <h4>Electrical Works</h4>
        <p>Professional electrical services for your home.</p>
      </div>
      <div class="w3-col m4 w3-center w3-padding-large">
        <i class="fa fa-wrench w3-margin-bottom w3-jumbo"></i>
        <h4>Handyman</h4>
        <p>General handyman services for various tasks.</p>
      </div>
      <div class="w3-col m4 w3-center w3-padding-large">
        <i class="fa fa-leaf w3-margin-bottom w3-jumbo"></i>
        <h4>Gardening</h4>
        <p>Professional gardening and landscaping services.</p>
      </div>
      <div class="w3-col m4 w3-center w3-padding-large">
        <i class="fa fa-snowflake-o w3-margin-bottom w3-jumbo"></i>
        <h4>HVAC</h4>
        <p>Heating, ventilation, and air conditioning services.</p>
      </div>
  </div>
</div>

</body>

</html>
