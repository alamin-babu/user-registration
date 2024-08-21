<?php
session_start();

if (!isset($_SESSION['USERS'])) {
    $_SESSION['USERS'] = [];
}

if (isset($_COOKIE['USERS'])) {
    $cookie_users = unserialize($_COOKIE['USERS']);
} else {
    $cookie_users = [];
}

function updateCookies($users)
{
    setcookie('USERS', serialize($users), time() + (86400 * 30), "/"); // Set cookie for 30 days
}

//form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    if (!empty($_POST["user_name"]) && !empty($_POST["user_email"]) && !empty($_POST["user_address"])) {
        $user_name = htmlspecialchars(trim($_POST["user_name"]));
        $user_email = htmlspecialchars(trim($_POST["user_email"]));
        $user_address = htmlspecialchars(trim($_POST["user_address"]));

        $user = [
            'name' => $user_name,
            'email' => $user_email,
            'address' => $user_address
        ];

        $_SESSION['USERS'][] = $user;

        $cookie_users[] = $user;
        updateCookies($cookie_users);

        $user_name = "";
        $user_email = "";
        $user_address = "";

        // Redirect to prevent form resubmission on page reload
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $error = "All fields are required.";
    }
}

// Handle user deletion
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["delete"])) {
    $index = (int)$_GET["delete"];

    // Remove user from session 
    if (isset($_SESSION['USERS'][$index])) {
        unset($_SESSION['USERS'][$index]);
    }

    // Re-index session array
    $_SESSION['USERS'] = array_values($_SESSION['USERS']);

    // Remove user from cookie
    if (isset($cookie_users[$index])) {
        unset($cookie_users[$index]);
    }

    // Re-index cookie 
    $cookie_users = array_values($cookie_users);
    updateCookies($cookie_users);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle delete all request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_all"])) {
    $_SESSION['USERS'] = [];
    setcookie('USERS', '', time() - 3600, "/");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Retrieve users 
$USERS = $_SESSION['USERS'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration PHP</title>
    <link rel="stylesheet" href="style.css">

</head>

<body>
    <h1 style="text-align: center;">Welcome to User Registration system</h1>
    <hr>

    <div class="container">
        <!-- Registration -->
        <div class="form-container">
            <h2 style="">Registration</h2>
            <form action="index.php" method="POST">
                <label for="user_name">Name:</label>
                <input type="text" id="user_name" name="user_name" required>

                <label for="user_email">Email:</label>
                <input type="email" id="user_email" name="user_email" required>

                <label for="user_address">Address:</label>
                <input type="text" id="user_address" name="user_address" required>

                <input type="submit" name="submit" value="Register">
            </form>
        </div>

        <!-- Table -->
        <div class="registered-user">
            <h2>Registered Users:</h2>

            <?php if(!empty($_SESSION['USERS'])): ?>
            <form action="index.php" method="POST">

                <input type="submit" name="delete_all" value="Delete All"
                    onclick="return confirm('Are you sure you want to delete all users?');">
            </form>
            <br>
            <?php endif?>


            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($USERS as $index => $user) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['address']); ?></td>
                        <td>
                            <a href="?delete=<?php echo $index; ?>"
                            
                                onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>


            </table>


        </div>
    </div>
    <br>
    <hr>

</body>

</html>