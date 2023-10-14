<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        .w3-wide {letter-spacing: 10px;}
        .w3-hover-opacity {cursor: pointer;}

        @media only screen and (max-device-width: 1600px) {
          .bgimg-1, .bgimg-2, .bgimg-3 {
            background-attachment: scroll;
            min-height: 400px;
          }
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

        h1 {
            margin: 0;
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
            left: 0; /* Start from the left */
            top: 100%;
            z-index: 1;
        }

        .dropdown-content a {
            color: gray;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left; /* Align the menu items to the left */
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

        .w3-content {
            padding: 64px;
        }

        .w3-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .w3-col {
            margin-bottom: 16px;
            text-align: center;
            width: 25%;
        }

        .w3-col h4 {
            margin: 10px 0;
        }

        .w3-col p {
            margin: 0;
        }
		
		.welcome-contractor {
			margin-right: 10px;
			margin-left: auto;
		}
    </style>
</head>
<body>
    <header>
        <div class="dropdown">
            <button class="dropbtn">...</button>
            <div class="dropdown-content">
                <a href="#">Messages</a>
                <a href="AvailableJobs.php">View Available Jobs</a>
                <a href="#">Job History</a>
                <a href="#">Account Settings</a>
                <a href="ContractorLogin.php">Log Out</a>
            </div>
        </div>
		<div class="welcome-contractor">Welcome Contractor</div> 
    </header>