<?php
session_start();
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    // Validate inputs
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } elseif (strlen($password) < 12) {
        $error = 'Password must be at least 12 characters long';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        // Connect to database
        $db = connectDB();
        
        // Check if email already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Email already in use';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $success = 'Registration successful! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
        
        $stmt->close();
        $db->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Fortune Quotes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            100: '#121824',
                            200: '#1E293B',
                            300: '#334155',
                        },
                        accent: {
                            100: '#3B82F6',
                            200: '#2563EB',
                            300: '#1D4ED8',
                        }
                    },
                },
            },
        }
    </script>
    <style type="text/css">
        body {
            background-color: #0F172A;
            color: #E5E5E5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        main {
            flex: 1;
            padding-bottom: 80px; /* Space for footer */
        }
        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            z-index: 10;
        }
    </style>
</head>
<body class="min-h-screen bg-dark-100 text-gray-200">
    <header class="bg-dark-200 shadow-lg border-b border-dark-300">
        <nav class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="index.php" class="text-xl font-bold text-blue-300">Fortune Quotes</a>
            <div>
                <a href="login.php" class="text-gray-300 hover:text-blue-300 transition duration-300">Login</a>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-dark-200 rounded-lg shadow-xl p-8 border border-dark-300">
            <h1 class="text-2xl font-bold text-center mb-6 text-blue-300">Register</h1>
        
            <?php if (!empty($error)): ?>
                <div class="bg-red-500 text-white p-3 rounded-lg mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
        
            <?php if (!empty($success)): ?>
                <div class="bg-green-500 text-white p-3 rounded-lg mb-4">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
        
            <form method="POST" action="register.php" id="register-form">
                <div class="mb-4">
                    <label for="username" class="block text-gray-300 mb-2">Username</label>
                    <input type="text" id="username" name="username" required
                           class="w-full px-4 py-3 bg-dark-300 border border-dark-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-100 focus:border-accent-100 transition duration-300">
                </div>
            
                <div class="mb-4">
                    <label for="email" class="block text-gray-300 mb-2">Email</label>
                    <input type="email" id="email" name="email" required
                           class="w-full px-4 py-3 bg-dark-300 border border-dark-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-100 focus:border-accent-100 transition duration-300">
                </div>
            
                <div class="mb-6">
                    <label for="password" class="block text-gray-300 mb-2">Password (min 12 characters)</label>
                    <input type="password" id="password" name="password" required minlength="12"
                           class="w-full px-4 py-3 bg-dark-300 border border-dark-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-100 focus:border-accent-100 transition duration-300">
                    <div id="password-strength" class="mt-2 text-sm"></div>
                </div>
            
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-accent-100 hover:bg-accent-200 text-white font-bold py-2 px-6 rounded-full focus:outline-none focus:ring-2 focus:ring-accent-300 focus:ring-opacity-50 transition duration-300 shadow-lg">
                        Register
                    </button>
                    <a href="login.php" class="text-accent-100 hover:text-accent-200 transition duration-300">
                        Already have an account?
                    </a>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-dark-200 py-4 border-t border-dark-300 shadow-lg">
        <div class="container mx-auto px-4 text-center text-gray-400">
            <p>&copy; <?php echo date('Y'); ?> Fortune Quotes. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Simple password strength indicator
        const passwordInput = document.getElementById('password');
        const strengthIndicator = document.getElementById('password-strength');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 'Weak';
            let color = 'text-red-500';
            
            if (password.length >= 12) {
                if (/[A-Z]/.test(password) && /[a-z]/.test(password) && /[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password)) {
                    strength = 'Strong';
                    color = 'text-green-500';
                } else if ((/[A-Z]/.test(password) || /[a-z]/.test(password)) && (/[0-9]/.test(password) || /[^A-Za-z0-9]/.test(password))) {
                    strength = 'Medium';
                    color = 'text-yellow-500';
                }
            }
            
            strengthIndicator.textContent = `Password strength: ${strength}`;
            strengthIndicator.className = `mt-2 text-sm ${color}`;
        });
        
        // Form validation
        document.getElementById('register-form').addEventListener('submit', function(e) {
            const password = passwordInput.value;
            
            if (password.length < 12) {
                e.preventDefault();
                alert('Password must be at least 12 characters long');
            }
        });
    </script>
</body>
</html>
