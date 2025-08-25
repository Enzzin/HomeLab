<?php
/**
 * Check if user is logged in
 * 
 * @return bool True if user is logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Connect to the database
 * 
 * @return mysqli Database connection
 */
function connectDB() {
    
    $host = ''; // Adicione o nome do host do seu banco de dados (ex: 'localhost' ou o IP do servidor)
    $username = ''; // Adicione o nome de usuário do seu banco de dados
    $password = ''; // Adicione a senha correspondente ao nome de usuário acima
    $database = 'fortune_quotes';

    $db = new mysqli($host, $username, $password, $database);

    if ($db->connect_error) {
        die('Database connection failed: ' . $db->connect_error);
    }

    return $db;
}

/**
 * Get random fortune from the Linux fortune command
 * 
 * @return string Random fortune
 */
function getRandomFortune() {
    // Execute fortune command and capture output
    $fortune = shell_exec('fortune -s');
    
    // If fortune command fails or is not available, return a default quote
    if (empty($fortune)) {
        $default_quotes = [
            "The best way to predict the future is to invent it.",
            "Success is not final, failure is not fatal: It is the courage to continue that counts.",
            "The only way to do great work is to love what you do.",
            "Life is what happens when you're busy making other plans.",
            "The journey of a thousand miles begins with one step."
        ];
        $fortune = $default_quotes[array_rand($default_quotes)];
    }
    
    // Sanitize the output to prevent XSS
    return htmlspecialchars(trim($fortune));
}

/**
 * Get user data by ID
 * 
 * @param int $user_id User ID
 * @return array User data or empty array if user not found
 */
function getUserData($user_id) {
    $db = connectDB();
    
    $stmt = $db->prepare("SELECT id, username, email, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
    } else {
        $user = [];
    }
    
    $stmt->close();
    $db->close();
    
    return $user;
}

/**
 * Get user's favorite quotes
 * 
 * @param int $user_id User ID
 * @return array Array of quotes
 */
function getUserFavoriteQuotes($user_id) {
    $db = connectDB();
    
    $stmt = $db->prepare("SELECT id, quote, created_at FROM favorite_quotes WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $quotes = [];
    while ($row = $result->fetch_assoc()) {
        $quotes[] = $row;
    }
    
    $stmt->close();
    $db->close();
    
    return $quotes;
}

/**
 * Add a quote to user's favorites
 * 
 * @param int $user_id User ID
 * @param string $quote Quote text
 * @return bool True on success, false on failure
 */
function addFavoriteQuote($user_id, $quote) {
    $db = connectDB();
    
    $stmt = $db->prepare("INSERT INTO favorite_quotes (user_id, quote) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $quote);
    $success = $stmt->execute();
    
    $stmt->close();
    $db->close();
    
    return $success;
}

/**
 * Remove a quote from user's favorites
 * 
 * @param int $user_id User ID
 * @param int $quote_id Quote ID
 * @return bool True on success, false on failure
 */
function removeFavoriteQuote($user_id, $quote_id) {
    $db = connectDB();
    
    $stmt = $db->prepare("DELETE FROM favorite_quotes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $quote_id, $user_id);
    $success = $stmt->execute();
    
    $stmt->close();
    $db->close();
    
    return $success;
}
