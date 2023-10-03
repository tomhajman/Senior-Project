<!DOCTYPE html>
<html>
<style>
body {font-family: Arial, Helvetica, sans-serif;}
* {box-sizing: border-box;}

input[type=text], input[type=password] {
  width: 100%;
  padding: 15px;
  margin: 5px 0 22px 0;
  display: inline-block;
  border: none;
  background: #f1f1f1;
}

input[type=text]:focus, input[type=password]:focus {
  background-color: #ddd;
  outline: none;
}

button {
  color: darkolivegreen;
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  cursor: pointer;
  width: 100%;
  opacity: 0.9;
}

button:hover {
  opacity: 1;
}

.cancelbtn {
  padding: 14px 20px;
  background-color: #f44336;
}

.signupbtn:last-child {
  background-color: #86cc74;
}

.container {
  padding: 16px;
}

.modal {
  display: none;
  position: fixed;
  z-index: 1;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: #474e5d;
  padding-top: 50px;
}

.modal-content {
  background-color: #fefefe;
  margin: 5% auto 15% auto;
  border: 1px solid #888;
  width: 80%;
}

hr {
  border: 1px solid #f1f1f1;
  margin-bottom: 25px;
}

.close {
  position: absolute;
  right: 35px;
  top: 15px;
  font-size: 40px;
  font-weight: bold;
  color: #f1f1f1;
}

.close:hover, .close:focus {
  color: #f44336;
  cursor: pointer;
}

.clearfix::after {
  content: "";
  clear: both;
  display: table;
}

@media screen and (max-width: 300px) {
  .cancelbtn, .signupbtn {
     width: 100%;
  }
}

.error {
  color: red;
  text-align: center;
}

.success {
  color: green;
  text-align: center;
}
</style>
<body>



<button onclick="document.getElementById('id01').style.display='block'" style="width:auto;">Sign Up</button>

<div id="id01" class="modal">
  <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">&times;</span>
  <form class="modal-content" action="/action_page.php" onsubmit="return validateForm()">
    <div class="container">
      <h1>Sign Up</h1>
      <p>Please fill in this form to create an account.</p>
      <hr>
      <label for="first_name"><b>First Name</b></label>
      <input type="text" placeholder="Enter First Name" name="first_name" required>

      <label for="last_name"><b>Last Name</b></label>
      <input type="text" placeholder="Enter Last Name" name="last_name" required>

      <label for="street_address"><b>Street Address</b></label>
      <input type="text" placeholder="Enter Street Address" name="street_address" required>

      <label for="floor_apt"><b>Floor/Apt</b></label>
      <input type="text" placeholder="Enter Floor/Apt" name="floor_apt">

      <label for="city"><b>City</b></label>
      <input type="text" placeholder="Enter City" name="city" required>

      <label for="zip"><b>Zip</b></label>
      <input type="text" placeholder="Enter Zip" name="zip" required>

      <label for="email_phone"><b>Email/Phone Number</b></label>
      <input type="text" placeholder="Enter Email or Phone Number" name="email_phone" required>

      <label for="password"><b>Password</b></label>
      <input type="password" placeholder="Enter Password" name="password" required>

      <label for="confirm_password"><b>Confirm Password</b></label>
      <input type="password" placeholder="Confirm Password" name="confirm_password" required>
      
      <label>
        <input type="checkbox" checked="checked" name="remember" style="margin-bottom:15px"> Remember me
      </label>

      <p>By creating an account you agree to our <a href="#" style="color:dodgerblue">Terms & Privacy</a>.</p>

      <div class="clearfix">
        <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn">Cancel</button>
        <button type="submit" class="signupbtn">Sign Up</button>
      </div>
      <div id="error-message" class="error"></div>
      <div id="success-message" class="success"></div>
    </div>
  </form>
</div>

<script>
var modal = document.getElementById('id01');

window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}

function validateForm() {
  var firstName = document.forms[0]["first_name"].value;
  var lastName = document.forms[0]["last_name"].value;
  var streetAddress = document.forms[0]["street_address"].value;
  var city = document.forms[0]["city"].value;
  var zip = document.forms[0]["zip"].value;
  var emailPhone = document.forms[0]["email_phone"].value;
  var password = document.forms[0]["password"].value;
  var confirmPassword = document.forms[0]["confirm_password"].value;

  var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

  if (firstName === "" || lastName === "" || streetAddress === "" || city === "" || zip === "" || emailPhone === "" || !password.match(passwordRegex) || confirmPassword === "") {
    document.getElementById('error-message').innerHTML = "Please complete all fields and ensure the password is more than 8 digits, contains both uppercase and lowercase letters, numbers, and at least one special character.";
    return false;
  } else {
    document.getElementById('error-message').innerHTML = "";
    document.getElementById('success-message').innerHTML = "Thank you for signing up";
    return true;
  }
}
</script>

</body>
</html>
