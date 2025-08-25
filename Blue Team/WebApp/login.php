<?php
session_start();
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        // Connect to database
        $db = connectDB();
        
        // Prepare statement to prevent SQL injection
        $stmt = $db->prepare("SELECT id, username, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Redirect to home page
                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid email or password';
            }
        } else {
            $error = 'Invalid email or password';
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
    <title>Login - Fortune Quotes</title>
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
                <a href="register.php" class="text-gray-300 hover:text-blue-300 transition duration-300">Register</a>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-dark-200 rounded-lg shadow-xl p-8 border border-dark-300">
            <h1 class="text-2xl font-bold text-center mb-6 text-blue-300">Login</h1>
        
            <?php if (!empty($error)): ?>
                <div class="bg-red-500 text-white p-3 rounded-lg mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
        
            <form method="POST" action="login.php">
                <div class="mb-4">
                    <label for="email" class="block text-gray-300 mb-2">Email</label>
                    <input type="email" id="email" name="email" required
                           class="w-full px-4 py-3 bg-dark-300 border border-dark-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-100 focus:border-accent-100 transition duration-300">
                </div>
            
                <div class="mb-6">
                    <label for="password" class="block text-gray-300 mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-3 bg-dark-300 border border-dark-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-100 focus:border-accent-100 transition duration-300">
                </div>
            
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-accent-100 hover:bg-accent-200 text-white font-bold py-2 px-6 rounded-full focus:outline-none focus:ring-2 focus:ring-accent-300 focus:ring-opacity-50 transition duration-300 shadow-lg">
                        Login
                    </button>
                    <a href="register.php" class="text-accent-100 hover:text-accent-200 transition duration-300">
                        Need an account?
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
</body>
</html>
