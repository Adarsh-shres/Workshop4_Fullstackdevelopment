<?php
$errors = [];
$success = "";
$name = "";
$email = "";

function validate($n, $e, $p, $c) {
    $err = [];
    if ($n === "") $err['name'] = "Name is required";
    if (!filter_var($e, FILTER_VALIDATE_EMAIL)) $err['email'] = "Invalid email";
    if (strlen($p) < 6) $err['password'] = "Password must be at least 6 characters";
    if ($p !== $c) $err['confirm'] = "Passwords do not match";
    return $err;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];

    $errors = validate($name, $email, $password, $confirm);

    if (empty($errors)) {
        if (!file_exists("users.json")) {
            file_put_contents("users.json", "[]");
        }

        $list = json_decode(file_get_contents("users.json"), true);

        $list[] = [
            "name" => $name,
            "email" => $email,
            "password" => password_hash($password, PASSWORD_DEFAULT)
        ];

        file_put_contents("users.json", json_encode($list, JSON_PRETTY_PRINT));

        $success = "Registration successful!";
        $name = "";
        $email = "";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>User Registration</h2>

    <?php if ($success): ?>
    <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>">
        <div class="error"><?php echo $errors['name'] ?? ""; ?></div>

        <label>Email</label>
        <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <div class="error"><?php echo $errors['email'] ?? ""; ?></div>

        <label>Password</label>
        <input type="password" name="password">
        <div class="error"><?php echo $errors['password'] ?? ""; ?></div>

        <label>Confirm Password</label>
        <input type="password" name="confirm">
        <div class="error"><?php echo $errors['confirm'] ?? ""; ?></div>

        <button type="submit">Register</button>
    </form>
</div>

</body>
</html>
