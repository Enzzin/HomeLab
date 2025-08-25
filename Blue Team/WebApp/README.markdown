# Fortune Quotes Web Application

This directory contains the source code for the **Fortune Quotes** web application, a PHP-based platform hosted on Apache within the `/WebApp` folder of the Blue Team x Red Team project repository. The application allows users to view random fortune quotes, register, log in, and manage favorite quotes, with a strong focus on security to protect against common web vulnerabilities.

## Project Overview

Fortune Quotes is a web application that:
- Displays random fortune quotes to users.
- Allows users to register and log in securely.
- Enables logged-in users to save and manage their favorite quotes.
- Connects to a MySQL database for user authentication and quote storage.

The application is part of the Blue Team's infrastructure, hosted in the DMZ and secured with various measures to ensure robust protection.

## File Structure

```
WebApp/
├── database.sql          # SQL schema for database setup
├── get_quote.php        # Endpoint to fetch random quotes
├── index.php            # Main page displaying random quotes
├── login.php            # User login page
├── logout.php           # User logout functionality
├── profile.php          # User profile and favorite quotes management
├── register.php         # User registration page
└── includes/
    └── functions.php    # Shared functions (e.g., database connection, quote retrieval)
```

## Security Features

The application incorporates multiple security measures to protect against common web vulnerabilities, ensuring a secure user experience.

### 1. **Input Sanitization and Validation**
- **Sanitization**: User inputs (e.g., email, username, and quotes) are sanitized using PHP's `filter_input` with `FILTER_SANITIZE_EMAIL`, `FILTER_SANITIZE_SPECIAL_CHARS`, and `FILTER_SANITIZE_NUMBER_INT` to prevent malicious data from entering the system.
- **Validation**: Client-side and server-side validation ensure inputs meet requirements (e.g., email format, minimum password length of 12 characters).
- **Password Strength**: The registration form includes a client-side password strength indicator, encouraging users to create strong passwords with a mix of uppercase, lowercase, numbers, and special characters.

### 2. **Prepared Statements**
- All database queries use **prepared statements** with parameterized queries via MySQLi to prevent SQL injection attacks. For example:
  - User authentication in `login.php` uses prepared statements to securely query the `users` table.
  - User registration in `register.php` and quote management in `profile.php` use prepared statements for safe database interactions.
  - Functions in `functions.php` (e.g., `getUserData`, `addFavoriteQuote`) use prepared statements for all database operations.

### 3. **Secure Password Handling**
- Passwords are hashed using PHP's `password_hash` function with the `PASSWORD_DEFAULT` algorithm (currently bcrypt) before storage.
- Password verification during login uses `password_verify` to securely compare user input against the stored hash.

### 4. **Session Management**
- Sessions are managed securely using PHP's `session_start()` and `session_destroy()` for login and logout.
- Session variables are unset and destroyed upon logout (`logout.php`) to prevent session fixation or hijacking.
- The `isLoggedIn` function in `functions.php` checks for valid sessions, and users are redirected to appropriate pages (e.g., `index.php` if already logged in) to prevent unauthorized access.

### 5. **HTTP Headers**
- The `Content-Type` header is explicitly set in `get_quote.php` (`header('Content-Type: text/plain')`) to ensure proper content handling and prevent MIME-type confusion attacks.
- The application avoids sending unnecessary headers, reducing the attack surface.

### 6. **Cross-Site Scripting (XSS) Protection**
- User outputs are escaped using `htmlspecialchars` in all PHP files (e.g., displaying usernames, emails, or quotes) to prevent XSS attacks.
- The `getRandomFortune` function in `functions.php` sanitizes fortune command output with `htmlspecialchars` to prevent XSS from external command output.
- The application uses Tailwind CSS via CDN, avoiding local storage of third-party scripts to minimize risks.

### 7. **Database Security**
- The database schema (`database.sql`) uses:
  - Unique constraints on email addresses to prevent duplicate accounts.
  - Foreign key constraints in the `favorite_quotes` table to maintain data integrity.
  - Indexes on frequently queried fields (e.g., `email` and `user_id`) for performance without compromising security.
- Database connections in `functions.php` use MySQLi with error handling to ensure robust connectivity.

### 8. **Secure File Handling**
- The application avoids file uploads, reducing risks associated with file inclusion or execution vulnerabilities.
- All PHP files are stored in a structured directory, with sensitive logic (e.g., database connections) in the `includes/` directory.
- The `fortune` command in `getRandomFortune` is executed securely with `shell_exec`, and its output is sanitized to prevent command injection or XSS.

### 9. **Frontend Security**
- The application uses Tailwind CSS with a custom configuration for consistent styling, served via a trusted CDN (`https://cdn.tailwindcss.com`).
- JavaScript is minimal and client-side only (e.g., fetching new quotes via `fetch` in `index.php` and `profile.php`), reducing the risk of client-side vulnerabilities.
- Forms avoid using `onSubmit` events due to sandbox restrictions, relying on standard POST requests.

### 10. **Command Execution Safety**
- The `getRandomFortune` function in `functions.php` uses the Linux `fortune` command with the `-s` flag to retrieve short quotes. If the command fails, it falls back to a predefined array of safe default quotes.
- The command output is trimmed and sanitized with `htmlspecialchars` to prevent injection of malicious content.

## Deployment Requirements

- **Web Server**: Apache with PHP support (PHP 7.4 or higher recommended).
- **Database**: MySQL or MariaDB for storing user data and favorite quotes.
- **Dependencies**:
  - Tailwind CSS (loaded via CDN).
  - MySQLi extension for PHP.
  - Linux `fortune` command (optional; falls back to default quotes if unavailable).
- **Configuration**:
  - Set up the database using `database.sql`.
  - Update `includes/functions.php` with your database credentials (`$host`, `$username`, `$password`).
  - Ensure Apache is configured with proper permissions and security headers (e.g., CSP, X-Frame-Options).

## Usage

1. **Setup**:
   - Import `database.sql` into your MySQL database.
   - Configure database credentials in `includes/functions.php`.
   - Deploy the application to an Apache server in the DMZ.

2. **Access**:
   - Visit `index.php` to view random quotes.
   - Register at `register.php` or log in at `login.php`.
   - Manage favorite quotes at `profile.php` (requires login).

3. **Security Considerations**:
   - Ensure the Apache server is hardened (e.g., disable directory listing, restrict PHP execution in public directories).
   - Use HTTPS to encrypt traffic.
   - Regularly update PHP, MySQL, and the `fortune` command to patch known vulnerabilities.
   - Monitor `fortune` command usage with Wazuh to detect anomalies.