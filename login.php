<?php
session_start(); 

// Retrieve errors and active form state from the session before unsetting them
$errors = [
    'login'    => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? ''
];

$activeForm = $_SESSION['active_form'] ?? 'login';

// Clear only the specific session alerts so they don't persist on refresh
unset($_SESSION['login_error']);
unset($_SESSION['register_error']);
unset($_SESSION['active_form']);

function showError($error) {
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
} 

function isActiveForm($formName, $activeForm) {
    return $formName === $activeForm ? 'active' : '';
}
?>  

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Login Page</title>
    <meta name="description" content="A simple HTML login page structure.">
    <meta name="author" content="Eurielle Bognon">
    <link rel="stylesheet" href="login.css">
</head>
<body>

 <div class="container">
    <div class="form-box <?= isActiveForm('login', $activeForm); ?>" id="login-form">
        <form action="config.php" method="post">
            <?= showError($errors['login']); ?>  
            <h2>Login</h2>
            <label>EMAIL: <input type="email" name="email" placeholder="Email" required></label>
            <label>PASSWORD: <input type="password" name="password" placeholder="Password" required></label>
            <button type="submit" name="login">Login</button>
            <p>Don't have an account? <a href="#" onclick="showForm('register-form')">Register</a></p>
        </form>
    </div>

    <div class="form-box <?= isActiveForm('register', $activeForm); ?>" id="register-form">
        <form action="config.php" method="post">
            <?= showError($errors['register']); ?>
            <h2>Register</h2>
            <label>Username: <input type="text" name="name" placeholder="Name" required></label>
            <label>Mail: <input type="email" name="email" placeholder="Email" required></label>
            <label>PASSWORD: <input type="password" name="password" placeholder="Password" required></label>

            <select name="role" required>
                <option value=""> --Select Role--</option>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
            <button type="submit" name="register">Register</button>
            <p>Already have an account? <a href="#" onclick="showForm('login-form')">Login</a></p>
        </form>
    </div>
 </div>

<script src="login.js"></script>
</body>
</html>