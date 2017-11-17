

<?php
// Include config file

require_once 'include/Config.php';
require_once 'include/DB_Connect.php';

// connecting to database
$db = new Db_Connect();
$conn = $db->connect();



// Define variables and initialize with empty values

$staffid = $username = $password = $confirm_password = "";

$staffid_err = $username_err = $password_err = $confirm_password_err = "";



// Processing form data when form is submitted

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    // validate staff id

    if (empty(trim($_POST["staffid"]))) {
        $staffid_err = "Please enter a staff ID.";
    } else {
        $sql = "SELECT staff_id FROM user WHERE staff_id = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {

            // Bind variables to the prepared statement as parameters

            mysqli_stmt_bind_param($stmt, "s", $param_staffid);

            $param_staffid = trim($_POST["staffid"]);

            if (mysqli_stmt_execute($stmt)) {

                /* store result */

                mysqli_stmt_store_result($stmt);



                if (mysqli_stmt_num_rows($stmt) == 1) {

                    $sql = "SELECT username FROM user WHERE staff_id = '$param_staffid'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        // output data of each row
                        while ($row = $result->fetch_assoc()) {
                            if ($row["username"] === null || strlen($row["username"]) > 0) {
                                $staffid_err = "This staff ID is already assigned. Please contact your administrator";
                            } else {
                                $staffid = trim($_POST["staffid"]);
                            }
                            break;
                        }
                    }
                } else {

                    $staffid_err = "This staff ID is invalid or not on the system. Please contact your administrator";
                }
            } else {

                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement

        mysqli_stmt_close($stmt);
    }

    // Validate username

    if (empty(trim($_POST["username"]))) {

        $username_err = "Please enter a username.";
    } else {

        // Prepare a select statement

        $sql = "SELECT id FROM user WHERE username = ?";



        if ($stmt = mysqli_prepare($conn, $sql)) {

            // Bind variables to the prepared statement as parameters

            mysqli_stmt_bind_param($stmt, "s", $param_username);



            // Set parameters

            $param_username = trim($_POST["username"]);



            // Attempt to execute the prepared statement

            if (mysqli_stmt_execute($stmt)) {

                /* store result */

                mysqli_stmt_store_result($stmt);



                if (mysqli_stmt_num_rows($stmt) == 1) {

                    $username_err = "This username is already taken.";
                } else {

                    $username = trim($_POST["username"]);
                }
            } else {

                echo "Oops! Something went wrong. Please try again later.";
            }
        }



        // Close statement

        mysqli_stmt_close($stmt);
    }



    // Validate password

    if (empty(trim($_POST['password']))) {

        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST['password'])) < 6) {

        $password_err = "Password must have atleast 6 characters.";
    } else {

        $password = trim($_POST['password']);
    }



    // Validate confirm password

    if (empty(trim($_POST["confirm_password"]))) {

        $confirm_password_err = 'Please confirm password.';
    } else {

        $confirm_password = trim($_POST['confirm_password']);

        if ($password != $confirm_password) {

            $confirm_password_err = 'Password did not match.';
        }
    }



    // Check input errors before inserting in database

    if (empty($staffid_err) && empty($username_err) && empty($password_err) && empty($confirm_password_err)) {



        // Prepare an insert statement
        //$sql = "INSERT INTO user (staff_id, username, password) VALUES (?, ?)";
        $sql = "UPDATE user set username = ?, password = ? WHERE staff_id = ?";



        if ($stmt = mysqli_prepare($conn, $sql)) {

            // Bind variables to the prepared statement as parameters

            mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_password, $param_staffid);



            // Set parameters

            $param_username = $username;

            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            // Attempt to execute the prepared statement

            if (mysqli_stmt_execute($stmt)) {

                // Redirect to login page

                header("location: Login.php");
            } else {

                echo "Something went wrong. Please try again later.";
            }
        }



        // Close statement

        mysqli_stmt_close($stmt);
    }



    // Close connection

    mysqli_close($conn);
}
?>



<!DOCTYPE html>

<html lang="en">

    <head>

        <meta charset="UTF-8">

        <title>Sign Up</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">

        <style type="text/css">

            body{ font: 14px sans-serif; }

            .wrapper{ width: 350px; padding: 20px; }

        </style>

    </head>

    <body>

        <div class="wrapper">

            <h2>Sign Up</h2>

            <p>Please fill this form to create an account.</p>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                <div class="form-group <?php echo (!empty($staffid_err)) ? 'has-error' : ''; ?>">

                    <label>Staff ID:<sup>*</sup></label>

                    <input type="text" name="staffid"class="form-control" value="<?php echo $staffid; ?>">

                    <span class="help-block"><?php echo $staffid_err; ?></span>

                </div> 

                <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">

                    <label>Username:<sup>*</sup></label>

                    <input type="text" name="username"class="form-control" value="<?php echo $username; ?>">

                    <span class="help-block"><?php echo $username_err; ?></span>

                </div>    

                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">

                    <label>Password:<sup>*</sup></label>

                    <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">

                    <span class="help-block"><?php echo $password_err; ?></span>

                </div>

                <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">

                    <label>Confirm Password:<sup>*</sup></label>

                    <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">

                    <span class="help-block"><?php echo $confirm_password_err; ?></span>

                </div>

                <div class="form-group">

                    <input type="submit" class="btn btn-primary" value="Submit">

                </div>

                <p>Already have an account? <a href="Login.php">Login here</a>.</p>

            </form>

        </div>    

    </body>

</html>

