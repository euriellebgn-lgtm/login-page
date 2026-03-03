<?php
session_start();

// 1️⃣ Check if user is logged in and is admin
// Updated to match the session keys set in config.php
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2️⃣ Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "users_b";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 3️⃣ Handle delete user action
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']); 
    // Updated redirect from admin.php to admin_page.php
    $stmtDelete = $conn->prepare("DELETE FROM users WHERE id=? AND role!='admin'");
    $stmtDelete->bind_param("i", $delete_id);
    $stmtDelete->execute();
    $stmtDelete->close();

    header("Location: admin_page.php"); 
    exit();
}

// 4️⃣ Fetch all users
$result = $conn->query("SELECT id, name, email, role FROM users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Page</title>
    <link rel="stylesheet" href="login.css">
    <style>
        /* Adding basic table styling since it's used here */
        table { width: 80%; margin: 20px auto; border-collapse: collapse; background: white; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f4f4f4; }
        .delete { color: red; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body style="background-color: #ccc; padding: 20px;">
    <h1 style="text-align:center; text-decoration: underline;">Users Table</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
        <?php while ($user = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td>
                    <?php if ($user['role'] !== 'admin'): ?>
                        <a class="delete" href="admin_page.php?delete_id=<?= $user['id'] ?>" onclick="return confirm('Delete this user?');">Delete</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <div style="text-align: center;">
        <button onclick="window.location.href='logout.php'" style="width: 200px;">Logout</button>
    </div>
</body>
</html>
<?php $conn->close(); ?>