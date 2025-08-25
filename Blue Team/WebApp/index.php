<?php
session_start();
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fortune Quotes</title>
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
    <style>
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
                <?php if (isLoggedIn()): ?>
                    <a href="profile.php" class="text-gray-300 hover:text-blue-300 mr-4 transition duration-300">Profile</a>
                    <a href="logout.php" class="text-gray-300 hover:text-blue-300 transition duration-300">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="text-gray-300 hover:text-blue-300 mr-4 transition duration-300">Login</a>
                    <a href="register.php" class="text-gray-300 hover:text-blue-300 transition duration-300">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-dark-200 rounded-lg shadow-lg p-6 mb-8">
            <h1 class="text-2xl font-bold text-center mb-6">Random Fortune Quote</h1>
            <div id="quote-container" class="bg-dark-300 p-8 rounded-lg mb-6 shadow-lg border border-dark-200">
                <p id="quote-text" class="text-lg italic text-center text-blue-100">
                    <?php echo getRandomFortune(); ?>
                </p>
            </div>
            <div class="text-center">
                <button id="new-quote" class="bg-accent-100 hover:bg-accent-200 text-white font-bold py-2 px-6 rounded-full focus:outline-none focus:ring-2 focus:ring-accent-300 focus:ring-opacity-50 transition duration-300 shadow-lg">
                    Get New Quote
                </button>
            </div>
        </div>
    </main>

    <footer class="bg-dark-200 py-4 border-t border-dark-300 shadow-lg">
        <div class="container mx-auto px-4 text-center text-gray-400">
            <p>&copy; <?php echo date('Y'); ?> Fortune Quotes. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.getElementById('new-quote').addEventListener('click', function() {
            fetch('get_quote.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('quote-text').innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>
