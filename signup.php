<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = "Email already registered!";
    } else {
        $stmt->close(); // Close previous statement before preparing the new one.

        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            $message = "Signup successful! <a href='login.php' class='text-blue-400 underline'>Login here</a>";
        } else {
            $message = "Error: " . $stmt->error;
        }
    }

    $stmt->close(); // Close statement after all operations are done.
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Signup</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="bg-gradient-to-r from-gray-900 to-gray-800 flex items-center justify-center min-h-screen text-white">
  <div class="bg-gray-900 p-8 rounded-xl shadow-xl w-full max-w-md border border-gray-700">
    <h2 class="text-3xl font-bold mb-6 text-center text-indigo-400">Create Account</h2>

    <?php if (isset($message)): ?>
      <p class="mb-4 text-center text-yellow-400"><?php echo $message; ?></p>
      
      <?php if (strpos($message, 'Signup successful!') !== false): ?>
        <script>
          setTimeout(() => {
            window.location.href = 'login.php';
          }, 2000); // 2-second delay before redirect
        </script>
      <?php endif; ?>
    <?php endif; ?>

    <form method="POST" class="space-y-5 animate-slideInUp">
      <div>
        <label class="block mb-1 font-medium text-gray-300">Name</label>
        <input type="text" name="name" required class="w-full px-4 py-2 rounded-lg bg-gray-800 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>
      <div>
        <label class="block mb-1 font-medium text-gray-300">Email</label>
        <input type="email" name="email" required class="w-full px-4 py-2 rounded-lg bg-gray-800 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>
      <div>
        <label class="block mb-1 font-medium text-gray-300">Password</label>
        <div class="relative">
          <input type="password" name="password" id="password" required class="w-full px-4 py-2 rounded-lg bg-gray-800 border border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
          <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3 top-2 text-gray-400 hover:text-white text-sm">👁️</button>
        </div>
        <p id="passwordStrength" class="text-xs mt-1 font-medium text-white"></p>
      </div>
      <button type="submit" class="w-full py-2 px-4 bg-indigo-500 hover:bg-indigo-600 rounded-lg font-semibold">Signup</button>
    </form>
    <p class="mt-4 text-center text-sm text-gray-400">
      Already have an account? <a href="login.php" class="text-indigo-400 hover:underline">Login here</a>
    </p>
  </div>

  <script>
    function togglePasswordVisibility() {
      const passwordField = document.getElementById('password');
      passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
    }

    // Optional: Password strength checker
    const passwordInput = document.getElementById("password");
    const strengthText = document.getElementById("passwordStrength");

    if (passwordInput) {
      passwordInput.addEventListener("input", () => {
        const password = passwordInput.value;
        let strength = "";
        let color = "text-gray-400";

        if (password.length < 6) {
          strength = "Too short";
          color = "text-red-500";
        } else if (/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\d]).{8,}$/.test(password)) {
          strength = "Strong password";
          color = "text-green-400";
        } else if (/^(?=.*[a-zA-Z])(?=.*\d).{6,}$/.test(password)) {
          strength = "Moderate password";
          color = "text-yellow-400";
        } else {
          strength = "Weak password";
          color = "text-orange-400";
        }

        strengthText.textContent = strength;
        strengthText.className = `text-xs mt-1 font-medium ${color}`;
      });
    }
  </script>

  <style>
    @keyframes slideInUp {
      0% { opacity: 0; transform: translateY(30px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    .animate-slideInUp {
      animation: slideInUp 0.8s ease-out forwards;
    }
  </style>
</body>
</html>
