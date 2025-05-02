<?php
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name, $hashed_password);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["name"] = $name;
            header("Location: dashboard.html"); // or home.php
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="bg-gradient-to-r from-gray-900 to-gray-800 flex items-center justify-center min-h-screen text-white">
  <div class="bg-gray-900 p-8 rounded-xl shadow-xl w-full max-w-md border border-gray-700">
    <h2 class="text-3xl font-bold mb-6 text-center text-indigo-400">Login</h2>

    <?php if (isset($error)): ?>
      <p class="mb-4 text-center text-red-400"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-5">
      <div>
        <label class="block mb-1 font-medium text-gray-300">Email</label>
        <input type="email" name="email" required class="w-full px-4 py-2 rounded-lg bg-gray-800 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>
      <div>
        <label class="block mb-1 font-medium text-gray-300">Password</label>
        <input type="password" name="password" required class="w-full px-4 py-2 rounded-lg bg-gray-800 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>
      <button type="submit" class="w-full py-2 px-4 bg-indigo-500 hover:bg-indigo-600 rounded-lg font-semibold">Login</button>
    </form>
    <p class="mt-4 text-center text-sm text-gray-400">
      Don't have an account? <a href="signup.php" class="text-indigo-400 hover:underline">Signup here</a>
    </p>
  </div>
</body>
</html>
