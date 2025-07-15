<?php
include 'includes/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email    = trim($_POST['email']);

    // Check if username exists
    $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Username already taken.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $role = 'customer';

        $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashed, $email, $role);

        if ($stmt->execute()) {
            $success = "✅ Account created. You can now <a href='login.php'>login</a>.";
        } else {
            $error = "Registration failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body { font-family: Arial; padding: 30px; }
        form { max-width: 400px; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; }
        input { width: 100%; padding: 10px; margin-bottom: 15px; }
        button { padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .msg { text-align: center; margin-top: 10px; }
        .msg.error { color: red; }
        .msg.success { color: green; }
    </style>
</head>
<body>

<h2 style="text-align:center;">📝 Register</h2>

<?php if ($error): ?>
    <p class="msg error"><?= $error ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p class="msg success"><?= $success ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="username" placeholder="Choose Username" required>
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="password" name="password" placeholder="Choose Password" required>
    <button type="submit">Register</button>
</form>

</body>
</html>
