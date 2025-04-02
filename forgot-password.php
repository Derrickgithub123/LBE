<?php
session_start();
require_once 'db_connect.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Configuration - should be moved to a separate config file in production
$config = [
    'mail' => [
        'username' => 'kanyokoderrick@gmail.com',
        'password' => 'zqdflfxhesfrykht', // App password without spaces
        'from_email' => 'noreply@rollbackbets.top', // Changed to domain email
        'from_name' => 'Lightning Bolt Electronics',
        'smtp_host' => 'smtp.gmail.com',
        'smtp_port' => 587,
        'smtp_secure' => 'tls',
        'reply_to' => 'support@rollbackbets.top'
    ],
    'website' => [
        'url' => 'https://lbe.rollbackbets.top', // No trailing slash
        'name' => 'Lightning Bolt Electronics'
    ],
    'security' => [
        'token_expiry' => 3600 // 1 hour in seconds
    ]
];

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid request");
    }

    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: forgot-password.php?error=invalid_email");
        exit;
    }

    try {
        $conn->begin_transaction();

        // Check if user exists
        $stmt = $conn->prepare("SELECT user_id, first_name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $token = bin2hex(random_bytes(32));
            $expires = date("Y-m-d H:i:s", time() + $config['security']['token_expiry']);

            // Clear any existing tokens first
            $stmt = $conn->prepare("UPDATE users SET reset_token = NULL, reset_expires = NULL WHERE user_id = ?");
            $stmt->bind_param("i", $user['user_id']);
            $stmt->execute();

            // Set new token
            $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE user_id = ?");
            $stmt->bind_param("ssi", $token, $expires, $user['user_id']);
            $stmt->execute();

            $reset_link = $config['website']['url'] . "/reset-password.html?token=" . urlencode($token);

            // Configure PHPMailer
            $mail = new PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = $config['mail']['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['mail']['username'];
            $mail->Password = $config['mail']['password'];
            $mail->SMTPSecure = $config['mail']['smtp_secure'];
            $mail->Port = $config['mail']['smtp_port'];
            $mail->Timeout = 30;
            
            // DKIM Configuration (requires DNS setup)
            $mail->DKIM_domain = 'rollbackbets.top';
            $mail->DKIM_private = '/path/to/your/private.key'; // Set this path
            $mail->DKIM_selector = 'phpmailer';
            $mail->DKIM_passphrase = ''; // If your key is encrypted
            $mail->DKIM_identity = $mail->From;
            
            // Email headers for better deliverability
            $mail->Priority = 1; // Highest priority
            $mail->addCustomHeader('List-Unsubscribe: <mailto:' . $config['mail']['reply_to'] . '>');
            $mail->addCustomHeader('X-Mailer: ' . $config['website']['name'] . ' Mailer');
            $mail->addCustomHeader('X-Priority: 1 (Highest)');
            $mail->addCustomHeader('X-MSMail-Priority: High');
            $mail->addCustomHeader('Importance: High');
            
            // Recipients
            $mail->setFrom($config['mail']['from_email'], $config['mail']['from_name']);
            $mail->addAddress($email);
            $mail->addReplyTo($config['mail']['reply_to'], 'Support Team');
            
            // Content
            $mail->Subject = "Password Reset Request - " . $config['website']['name'];
            $mail->isHTML(true);
            
            // Personalize email if first name is available
            $greeting = isset($user['first_name']) ? "Hello " . htmlspecialchars($user['first_name']) . "," : "Hello,";
            
            // HTML email template
            $mail->Body = "
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
    <title>Password Reset</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { color: #007BFF; font-size: 24px; font-weight: bold; }
        .button { 
            background-color: #007BFF; 
            color: white; 
            padding: 12px 24px; 
            text-decoration: none; 
            border-radius: 4px; 
            font-weight: bold;
            display: inline-block;
            margin: 20px 0;
        }
        .footer { 
            margin-top: 40px; 
            padding-top: 20px; 
            border-top: 1px solid #eee; 
            font-size: 12px; 
            color: #777;
        }
    </style>
</head>
<body>
    <div class=\"container\">
        <div class=\"header\">
            <div class=\"logo\">" . htmlspecialchars($config['website']['name']) . "</div>
        </div>
        
        <p>$greeting</p>
        
        <p>We received a request to reset your password for your " . htmlspecialchars($config['website']['name']) . " account.</p>
        
        <p style=\"text-align: center;\">
            <a href=\"$reset_link\" class=\"button\">
                Reset Password
            </a>
        </p>
        
        <p>This link will expire in " . ($config['security']['token_expiry'] / 3600) . " hour(s).</p>
        
        <p>If you didn't request this password reset, please ignore this email or contact our support team if you have any concerns.</p>
        
        <div class=\"footer\">
            <p>© " . date('Y') . " " . htmlspecialchars($config['website']['name']) . ". All rights reserved.</p>
            <p>Our mailing address is:<br>" . htmlspecialchars($config['mail']['reply_to']) . "</p>
            <p><small>This is an automated message - please do not reply directly to this email.</small></p>
        </div>
    </div>
</body>
</html>
            ";
            
            // Plain text version for non-HTML email clients
            $mail->AltBody = "Password Reset Request\n\n" .
                "$greeting\n\n" .
                "We received a request to reset your password for your " . $config['website']['name'] . " account.\n\n" .
                "Please visit the following link to reset your password:\n" .
                "$reset_link\n\n" .
                "This link will expire in " . ($config['security']['token_expiry'] / 3600) . " hour(s).\n\n" .
                "If you didn't request this password reset, please ignore this email.\n\n" .
                "© " . date('Y') . " " . $config['website']['name'] . "\n" .
                "Support: " . $config['mail']['reply_to'];

            if($mail->send()) {
                $conn->commit();
                header("Location: forgot-password.php?status=sent");
                exit;
            } else {
                throw new Exception('Mailer Error: ' . $mail->ErrorInfo);
            }
        }

        // Always show success message (security measure)
        $conn->commit();
        header("Location: forgot-password.php?status=sent");
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Password Reset Error [" . date('Y-m-d H:i:s') . "]: " . $e->getMessage());
        // For debugging, include the error - remove in production
        header("Location: forgot-password.php?error=system_error&debug=" . urlencode($e->getMessage()));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Recovery - <?php echo htmlspecialchars($config['website']['name']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #007BFF;
            --primary-hover: #0056b3;
            --error-color: #d32f2f;
            --error-bg: #fff3f3;
            --success-color: #2e7d32;
            --success-bg: #f0fff4;
            --text-color: #333;
            --light-text: #777;
            --border-color: #ddd;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
            color: var(--text-color);
            line-height: 1.6;
        }
        
        .auth-card {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        
        .logo {
            margin-bottom: 20px;
            color: var(--primary-color);
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .input-group {
            display: flex;
            align-items: center;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 8px;
            margin-bottom: 20px;
            transition: border-color 0.3s;
        }
        
        .input-group:focus-within {
            border-color: var(--primary-color);
        }
        
        .input-group i {
            color: var(--light-text);
            margin: 0 10px;
        }
        
        .input-group input {
            border: none;
            outline: none;
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }
        
        .auth-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 14px;
            width: 100%;
            cursor: pointer;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .auth-btn:hover {
            background: var(--primary-hover);
        }
        
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            text-align: left;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        
        .error-message {
            background-color: var(--error-bg);
            color: var(--error-color);
            border-left: 4px solid var(--error-color);
        }
        
        .success-message {
            background-color: var(--success-bg);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }
        
        .back-link {
            display: block;
            margin-top: 20px;
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        
        @media (max-width: 480px) {
            .auth-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="logo">
            <i class="fas fa-bolt"></i>
            <?php echo htmlspecialchars($config['website']['name']); ?>
        </div>
        <h2>Reset Your Password</h2>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="message error-message">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <?php 
                    switch($_GET['error']) {
                        case 'invalid_email': 
                            echo 'Please enter a valid email address.'; 
                            break;
                        case 'system_error': 
                            echo 'We encountered an error processing your request. Please try again later.'; 
                            // Remove debug info in production
                            if (isset($_GET['debug'])) {
                                echo '<br><small>Debug: ' . htmlspecialchars($_GET['debug']) . '</small>';
                            }
                            break;
                        default: 
                            echo 'An error occurred. Please try again.';
                    }
                    ?>
                </div>
            </div>
        <?php elseif (isset($_GET['status']) && $_GET['status'] === 'sent'): ?>
            <div class="message success-message">
                <i class="fas fa-check-circle"></i>
                <div>
                    If an account exists with this email, you'll receive a password reset link shortly. 
                    Please check your inbox and spam folder.
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Your email address" required>
            </div>
            
            <button type="submit" class="auth-btn">
                <i class="fas fa-paper-plane"></i> Send Reset Link
            </button>
            
            <a href="login.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        </form>
    </div>
</body>
</html>