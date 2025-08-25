<?php
session_start();
require_once 'includes/functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = getUserData($_SESSION['user_id']);
$quotes = getUserFavoriteQuotes($_SESSION['user_id']);

$message = '';

// Handle adding a quote to favorites
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_favorite'])) {
    $quote = filter_input(INPUT_POST, 'quote', FILTER_SANITIZE_SPECIAL_CHARS);
    
    if (!empty($quote)) {
        if (addFavoriteQuote($_SESSION['user_id'], $quote)) {
            $message = 'Quote added to favorites!';
            // Refresh quotes list
            $quotes = getUserFavoriteQuotes($_SESSION['user_id']);
        } else {
            $message = 'Failed to add quote to favorites.';
        }
    }
}

// Handle removing a quote from favorites
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_favorite'])) {
    $quote_id = filter_input(INPUT_POST, 'quote_id', FILTER_SANITIZE_NUMBER_INT);
    
    if (!empty($quote_id)) {
        if (removeFavoriteQuote($_SESSION['user_id'], $quote_id)) {
            $message = 'Quote removed from favorites!';
            // Refresh quotes list
            $quotes = getUserFavoriteQuotes($_SESSION['user_id']);
        } else {
            $message = 'Failed to remove quote from favorites.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Fortune Quotes</title>
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
                <a href="index.php" class="text-gray-300 hover:text-blue-300 mr-4 transition duration-300">Home</a>
                <a href="logout.php" class="text-gray-300 hover:text-blue-300 transition duration-300">Logout</a>
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
        <div class="bg-dark-200 rounded-lg shadow-xl p-8 mb-8 border border-dark-300">
            <h1 class="text-2xl font-bold mb-6 text-blue-300">Profile</h1>
            
            <div class="mb-6 bg-dark-300 p-6 rounded-lg shadow-md border border-dark-300">
                <p class="text-lg mb-2"><span class="font-semibold text-blue-200">Username:</span> <?php echo htmlspecialchars($user['username']); ?></p>
                <p class="text-lg mb-2"><span class="font-semibold text-blue-200">Email:</span> <?php echo htmlspecialchars($user['email']); ?></p>
                <p class="text-lg"><span class="font-semibold text-blue-200">Member since:</span> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="bg-accent-100 text-white p-4 rounded-lg mb-6 shadow-md">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <div class="mb-8">
                <h2 class="text-xl font-bold mb-4 text-blue-300">Add Current Quote to Favorites</h2>
                <div id="current-quote" class="bg-dark-300 p-6 rounded-lg mb-4 shadow-md border border-dark-300">
                    <p id="quote-text" class="text-lg italic text-blue-100">
                        <?php echo getRandomFortune(); ?>
                    </p>
                </div>
                <form method="POST" action="profile.php">
                    <input type="hidden" id="quote-input" name="quote" value="">
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" name="add_favorite" class="bg-accent-100 hover:bg-accent-200 text-white font-bold py-2 px-6 rounded-full focus:outline-none focus:ring-2 focus:ring-accent-300 focus:ring-opacity-50 transition duration-300 shadow-lg">
                            Add to Favorites
                        </button>
                        <button type="button" id="new-quote" class="bg-dark-300 hover:bg-dark-200 text-white font-bold py-2 px-6 rounded-full focus:outline-none focus:ring-2 focus:ring-dark-200 focus:ring-opacity-50 transition duration-300 shadow-lg border border-dark-300">
                            Get New Quote
                        </button>
                    </div>
                </form>
            </div>
            
            <div>
                <h2 class="text-xl font-bold mb-4 text-blue-300">Your Favorite Quotes</h2>
                
                <?php if (empty($quotes)): ?>
                    <p class="text-gray-400 italic p-4 bg-dark-300 rounded-lg">You haven't added any favorite quotes yet.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($quotes as $quote): ?>
                            <div class="bg-dark-300 p-6 rounded-lg flex justify-between items-start shadow-md border border-dark-300">
                                <p class="text-lg italic text-blue-100"><?php echo htmlspecialchars($quote['quote']); ?></p>
                                <form method="POST" action="profile.php" class="ml-4">
                                    <input type="hidden" name="quote_id" value="<?php echo $quote['id']; ?>">
                                    <button type="submit" name="remove_favorite" class="text-red-400 hover:text-red-300 transition duration-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    </main>

    <footer class="bg-dark-200 py-4 border-t border-dark-300 shadow-lg">
        <div class="container mx-auto px-4 text-center text-gray-400">
            <p>&copy; <?php echo date('Y'); ?> Fortune Quotes. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Update hidden input with current quote
        function updateQuoteInput() {
            const quoteText = document.getElementById('quote-text').innerText;
            document.getElementById('quote-input').value = quoteText;
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', updateQuoteInput);
        
        // Get new quote
        document.getElementById('new-quote').addEventListener('click', function() {
            fetch('get_quote.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('quote-text').innerHTML = data;
                    updateQuoteInput();
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>
