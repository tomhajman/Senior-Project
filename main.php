<!DOCTYPE html>
<html>
<head>
    <title>HandyMan Services</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body,h1,h2,h3,h4,h5,h6 {font-family: "Lato", sans-serif;}
        body, html {
          height: 100%;
          color: #333;
          line-height: 1.8;
        }

        /* Create a Parallax Effect */
        .bgimg-1, .bgimg-2, .bgimg-3 {
          background-attachment: fixed;
          background-position: center;
          background-repeat: no-repeat;
          background-size: cover;
        }

        /* First image (Logo. Full height) */
        .bgimg-1 {
          background-image: url('/w3images/parallax1.jpg');
          min-height: 100%;
        }

        /* Second image (Services) */
        .bgimg-2 {
          background-image: url("/w3images/parallax2.jpg");
          min-height: 400px;
        }

        /* Third image (Contact) */
        .bgimg-3 {
          background-image: url("/w3images/parallax3.jpg");
          min-height: 400px;
        }

        .w3-wide {letter-spacing: 10px;}
        .w3-hover-opacity {cursor: pointer;}

        /* Turn off parallax scrolling for tablets and phones */
        @media only screen and (max-device-width: 1600px) {
          .bgimg-1, .bgimg-2, .bgimg-3 {
            background-attachment: scroll;
            min-height: 400px;
          }
        }
    </style>
</head>
<body>

<?php 
session_start();
?>

<!-- Navbar (sit on top) -->
<div class="w3-top">
  <div class="w3-bar" id="myNavbar">
    <a class="w3-bar-item w3-button w3-hover-black w3-hide-medium w3-hide-large w3-right" href="javascript:void(0);" onclick="toggleFunction()" title="Toggle Navigation Menu">
      <i class="fa fa-bars"></i>
    </a>
    <a href="#home" class="w3-bar-item w3-button">HOME</a>
    <a href="#services" class="w3-bar-item w3-button">SERVICES</a>
    <a href="#portfolio" class="w3-bar-item w3-button">PORTFOLIO</a>
    <a href="#contact" class="w3-bar-item w3-button">CONTACT</a>
    <a href="CustomerLogin.php" class="w3-bar-item w3-button w3-right">CUSTOMER LOG IN</a>
	<a href="ContractorLogin.php" class="w3-bar-item w3-button w3-right">CONTRACTOR LOG IN</a>
     
  </div>

  <!-- Navbar on small screens -->
  <div id="navDemo" class="w3-bar-block w3-white w3-hide w3-hide-large w3-hide-medium">
    <a href="#services" class="w3-bar-item w3-button" onclick="toggleFunction()">SERVICES</a>
    <a href="#portfolio" class="w3-bar-item w3-button" onclick="toggleFunction()">PORTFOLIO</a>
    <a href="#contact" class="w3-bar-item w3-button" onclick="toggleFunction()">CONTACT</a>
    <a href="#" class="w3-bar-item w3-button">SEARCH</a>
  </div>
</div>

<!-- First Parallax Image with Logo Text -->
<div class="bgimg-1 w3-display-container w3-opacity-min" id="home">
  <div class="w3-display-middle" style="white-space:nowrap;">
    <span class="w3-center w3-padding-large w3-black w3-xlarge w3-wide w3-animate-opacity">HandyMan Services</span>
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

<!-- Second Parallax Image with Portfolio Text -->
<div class="bgimg-2 w3-display-container w3-opacity-min">
  <div class="w3-display-middle">
    <span class="w3-xxlarge w3-text-white w3-wide">PORTFOLIO</span>
  </div>
</div>

<!-- Container (Portfolio Section) -->
<div class="w3-content w3-container w3-padding-64" id="portfolio">
  <h3 class="w3-center">OUR WORK</h3>
  <p class="w3-center"><em>Take a look at our recent projects</em></p><br>

  
  <div class="w3-row-padding w3-center">
    <div class="w3-col m3">
      <img src="/w3images/project1.jpg" style="width:100%" onclick="onClick(this)" class="w3-hover-opacity" alt="Project 1">
    </div>
    <div class="w3-col m3">
      <img src="/w3images/project2.jpg" style
