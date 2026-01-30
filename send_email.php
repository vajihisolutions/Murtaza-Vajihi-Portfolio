<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header
header('Content-Type: application/json');

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $subject = isset($_POST['subject']) ? htmlspecialchars(trim($_POST['subject'])) : 'No Subject';
    $message = htmlspecialchars(trim($_POST['message']));
    $form_type = isset($_POST['form_type']) ? $_POST['form_type'] : 'contact';
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    // If no errors, process the form
    if (empty($errors)) {
        // Email configuration
        $to = "murtazavajihi42@gmail.com"; // Your email address
        $email_subject = $form_type == 'chat' 
            ? "New Chat Message from Portfolio Website" 
            : "New Contact Form Message: " . $subject;
        
        // Email headers
        $headers = "From: Portfolio Website <noreply@murtazavajihi.com>\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        // Email body
        $email_body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: #f9f9f9; border-radius: 10px; overflow: hidden; }
                .header { background-color: #0EA5E9; color: white; padding: 20px; text-align: center; }
                .content { padding: 30px; background-color: white; }
                .field { margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
                .label { font-weight: bold; color: #0EA5E9; display: block; margin-bottom: 5px; }
                .value { color: #333; }
                .footer { margin-top: 30px; padding-top: 20px; border-top: 2px solid #0EA5E9; font-size: 12px; color: #666; text-align: center; }
                .message-box { background: #f5f5f5; padding: 15px; border-radius: 5px; margin-top: 10px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>New Message from Portfolio Website</h2>
                    <p>Form Type: " . ucfirst($form_type) . " Form</p>
                </div>
                <div class='content'>
                    <div class='field'>
                        <span class='label'>Name:</span>
                        <span class='value'>$name</span>
                    </div>
                    <div class='field'>
                        <span class='label'>Email:</span>
                        <span class='value'>$email</span>
                    </div>";
        
        if ($form_type == 'contact') {
            $email_body .= "<div class='field'>
                        <span class='label'>Subject:</span>
                        <span class='value'>$subject</span>
                    </div>";
        }
        
        $email_body .= "<div class='field'>
                        <span class='label'>Message:</span>
                        <div class='message-box'>" . nl2br($message) . "</div>
                    </div>
                </div>
                <div class='footer'>
                    <p>This email was sent from your portfolio contact form at " . date('Y-m-d H:i:s') . "</p>
                    <p>IP Address: " . $_SERVER['REMOTE_ADDR'] . "</p>
                </div>
            </div>
        </body>
        </html>";
        
        // Send email
        if (mail($to, $email_subject, $email_body, $headers)) {
            // Return success response
            echo json_encode([
                'success' => true,
                'message' => 'Thank you! Your message has been sent successfully. I will get back to you soon.'
            ]);
        } else {
            // Email sending failed
            error_log("Failed to send email to: $to, Subject: $email_subject");
            echo json_encode([
                'success' => false,
                'message' => 'Failed to send email. Please try again later or contact me directly at murtazavajihi42@gmail.com'
            ]);
        }
    } else {
        // Return validation errors
        echo json_encode([
            'success' => false,
            'message' => 'Please fix the following errors:',
            'errors' => $errors
        ]);
    }
} else {
    // Invalid request method
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Please use the form to submit your message.'
    ]);
}
?>