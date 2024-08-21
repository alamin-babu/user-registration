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
    setcookie('USERS', serialize($users), time() + (86400 * 30), "/"); 
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

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $error = "All fields are required.";
    }
}

// Handle user deletion
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["delete"])) {
    $index = (int)$_GET["delete"];

    if (isset($_SESSION['USERS'][$index])) {
        unset($_SESSION['USERS'][$index]);
    }

    $_SESSION['USERS'] = array_values($_SESSION['USERS']);

    if (isset($cookie_users[$index])) {
        unset($cookie_users[$index]);
    }
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
    <title>Users Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1 style="width:100%;">Welcome to User Registration</h1>

    
    <div class="container">
        
        <!-- Registration Form -->
        <div class="form-container">
            <h2>Registration</h2>
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

        <!-- Registered Users Table -->
        <div class="registered-user">
            <h2>Registered Users:</h2>
            <div style="overflow-x:auto;">
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Action</th>
                    </tr>
                    
                        <?php foreach ($USERS as $index => $user) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['address']); ?></td>
                                <td>
                                    <a href="?delete=<?php echo $index; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <?php if(!empty($_SESSION['USERS'])): ?>
            <form action="index.php" method="POST">

                <input type="submit" name="delete_all" value="Delete All"
                    onclick="return confirm('Are you sure you want to delete all users?');">
            </form>
            <br>
            <?php endif?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
