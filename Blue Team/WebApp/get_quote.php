<?php
require_once 'includes/functions.php';

// Set content type to plain text
header('Content-Type: text/plain');

// Get and output a random fortune
echo getRandomFortune();
