<?php
// Include TCPDF library
require_once 'tcpdf/tcpdf.php'; // Adjust path if needed

// Function to check if all required URL parameters exist
function checkRequiredParams() {
    $required_params = [
        'fullname', 'company', 'email', 'phone', 'address', 'contactMethod',
        'businessDesc', 'products', 'targetAudience', 'website_purpose',
        'features', 'integrations', 'referral_type'
    ];

    foreach ($required_params as $param) {
        if (!isset($_GET[$param]) || empty($_GET[$param])) {
            return false;
        }
    }
    return true;
}

// SMTP email sending function with fallback to mail()
// Updated SMTP email sending function
function sendSmtpEmail($to, $from, $fromName, $subject, $message, $attachmentContent, $attachmentName) {
    $smtpHost = 'smtp.gmail.com';
    $smtpPort = 587;
    $smtpUsername = 'your gmail';
    $smtpPassword = 'your gmail app password';
    $timeout = 30;

    // Create socket connection
    $smtpConn = fsockopen('tcp://'.$smtpHost, $smtpPort, $errno, $errstr, $timeout);
    if (!$smtpConn) {
        error_log("SMTP Connection failed: $errstr ($errno)");
        return false;
    }

    // Read initial response
    $response = fgets($smtpConn, 515);
    if (substr($response, 0, 3) != '220') {
        error_log("Invalid SMTP response: $response");
        fclose($smtpConn);
        return false;
    }

    // Send EHLO
    fputs($smtpConn, "EHLO localhost\r\n");
    $response = '';
    while ($str = fgets($smtpConn, 515)) {
        $response .= $str;
        if (substr($str, 3, 1) == ' ') break;
    }
    if (substr($response, 0, 3) != '250') {
        error_log("EHLO failed: $response");
        fclose($smtpConn);
        return false;
    }

    // Start TLS
    fputs($smtpConn, "STARTTLS\r\n");
    $response = fgets($smtpConn, 515);
    if (substr($response, 0, 3) != '220') {
        error_log("STARTTLS failed: $response");
        fclose($smtpConn);
        return false;
    }
    
    // Enable crypto
    if (!stream_socket_enable_crypto($smtpConn, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)) {
        error_log("TLS negotiation failed");
        fclose($smtpConn);
        return false;
    }

    // Re-send EHLO after TLS
    fputs($smtpConn, "EHLO localhost\r\n");
    $response = '';
    while ($str = fgets($smtpConn, 515)) {
        $response .= $str;
        if (substr($str, 3, 1) == ' ') break;
    }
    if (substr($response, 0, 3) != '250') {
        error_log("Post-TLS EHLO failed: $response");
        fclose($smtpConn);
        return false;
    }

    // Authenticate
    fputs($smtpConn, "AUTH LOGIN\r\n");
    $response = fgets($smtpConn, 515);
    if (substr($response, 0, 3) != '334') {
        error_log("AUTH LOGIN failed: $response");
        fclose($smtpConn);
        return false;
    }

    fputs($smtpConn, base64_encode($smtpUsername)."\r\n");
    $response = fgets($smtpConn, 515);
    if (substr($response, 0, 3) != '334') {
        error_log("Username failed: $response");
        fclose($smtpConn);
        return false;
    }

    fputs($smtpConn, base64_encode($smtpPassword)."\r\n");
    $response = fgets($smtpConn, 515);
    if (substr($response, 0, 3) != '235') {
        error_log("SMTP Authentication failed: $response");
        fclose($smtpConn);
        return false;
    }

    // Send email
    fputs($smtpConn, "MAIL FROM:<$from>\r\n");
    $response = fgets($smtpConn, 515);
    if (substr($response, 0, 3) != '250') {
        error_log("MAIL FROM failed: $response");
        fclose($smtpConn);
        return false;
    }

    fputs($smtpConn, "RCPT TO:<$to>\r\n");
    $response = fgets($smtpConn, 515);
    if (substr($response, 0, 3) != '250') {
        error_log("RCPT TO failed: $response");
        fclose($smtpConn);
        return false;
    }

    fputs($smtpConn, "DATA\r\n");
    $response = fgets($smtpConn, 515);
    if (substr($response, 0, 3) != '354') {
        error_log("DATA failed: $response");
        fclose($smtpConn);
        return false;
    }

    // Compose email
    $boundary = md5(uniqid(time()));
    $headers = "From: $fromName <$from>\r\n";
    $headers .= "To: $to\r\n";
    $headers .= "Subject: $subject\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    $body = "--$boundary\r\n";
    $body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
    $body .= $message."\r\n";
    $body .= "--$boundary\r\n";
    $body .= "Content-Type: application/pdf; name=\"$attachmentName\"\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= "Content-Disposition: attachment; filename=\"$attachmentName\"\r\n\r\n";
    $body .= chunk_split(base64_encode($attachmentContent))."\r\n";
    $body .= "--$boundary--\r\n";

    fputs($smtpConn, "$headers\r\n$body\r\n.\r\n");
    $response = fgets($smtpConn, 515);
    if (substr($response, 0, 3) != '250') {
        error_log("Message send failed: $response");
        fclose($smtpConn);
        return false;
    }

    fputs($smtpConn, "QUIT\r\n");
    fclose($smtpConn);
    return true;
}

// Fallback using mail()
function sendMailFallback($to, $from, $fromName, $subject, $message, $attachmentContent, $attachmentName) {
    $boundary = md5(uniqid(time()));
    $headers = "From: $fromName <$from>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    $body = "--$boundary\r\n";
    $body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
    $body .= $message . "\r\n";
    $body .= "--$boundary\r\n";
    $body .= "Content-Type: application/pdf; name=\"$attachmentName\"\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= "Content-Disposition: attachment; filename=\"$attachmentName\"\r\n\r\n";
    $body .= chunk_split(base64_encode($attachmentContent)) . "\r\n";
    $body .= "--$boundary--\r\n";

    // Attempt to send via mail()
    if (mail($to, $subject, $body, $headers)) {
        error_log("Email sent successfully to $to via mail()");
        return true;
    } else {
        error_log("Email sending failed via mail(): " . error_get_last()['message']);
        return false;
    }
}

// Check required parameters
if (!checkRequiredParams()) {
    header("Location: Basic_information.php");
    exit();
}

// Database connection
$db_host = 'localhost';
$db_user = 'root'; // Your MySQL username
$db_pass = ''; // Your MySQL password
$db_name = 'form'; // Your database name
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Directory to save PDFs
$pdf_dir = 'uploads/';
if (!file_exists($pdf_dir)) {
    mkdir($pdf_dir, 0777, true);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data from POST
    $specialRequirements = strip_tags($_POST['specialRequirements'] ?? '');
    $hearAboutUs = isset($_POST['hearAboutUs']) ? implode(',', array_map('strip_tags', $_POST['hearAboutUs'])) : '';
    $consultation = strip_tags($_POST['consultation'] ?? '');

    // Combine all data for PDF generation
    $formData = array_merge($_GET, [
        'specialRequirements' => $specialRequirements,
        'hearAboutUs' => $hearAboutUs,
        'consultation' => $consultation,
        'website' => $_GET['website'] ?? '',
        'competitors' => $_GET['competitors'] ?? '',
        'uniqueness' => $_GET['uniqueness'] ?? '',
        'referral_urls' => $_GET['referral_urls'] ?? '',
        'domainName' => $_GET['domainName'] ?? '',
        'hostingProvider' => $_GET['hostingProvider'] ?? '',
        'seo' => $_GET['seo'] ?? '',
        'maintenance' => $_GET['maintenance'] ?? ''
    ]);

    error_log("formData: " . print_r($formData, true));

    // Generate PDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor($formData['fullname']);
    $pdf->SetTitle('Client Onboarding Summary');
    $pdf->SetSubject('Client Information');
    $pdf->SetMargins(15, 15, 15);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    $html = '
    <h1 style="color: #4f46e5;">Client Onboarding Summary</h1>
    <table cellpadding="5">
        <tr><td style="width: 200px;"><strong>Full Name:</strong></td><td>' . htmlspecialchars($formData['fullname']) . '</td></tr>
        <tr><td><strong>Company:</strong></td><td>' . htmlspecialchars($formData['company']) . '</td></tr>
        <tr><td><strong>Email:</strong></td><td>' . htmlspecialchars($formData['email']) . '</td></tr>
        <tr><td><strong>Phone:</strong></td><td>' . htmlspecialchars($formData['phone']) . '</td></tr>
        <tr><td><strong>Company Website:</strong></td><td>' . (empty($formData['website']) ? 'Not selected' : htmlspecialchars($formData['website'])) . '</td></tr>
        <tr><td><strong>Address:</strong></td><td>' . htmlspecialchars($formData['address']) . '</td></tr>
        <tr><td><strong>Contact Method:</strong></td><td>' . htmlspecialchars($formData['contactMethod']) . '</td></tr>
        <tr><td><strong>Business Description:</strong></td><td>' . htmlspecialchars($formData['businessDesc']) . '</td></tr>
        <tr><td><strong>Products/Services:</strong></td><td>' . htmlspecialchars($formData['products']) . '</td></tr>
        <tr><td><strong>Target Audience:</strong></td><td>' . htmlspecialchars($formData['targetAudience']) . '</td></tr>
        <tr><td><strong>Competitor Websites:</strong></td><td>' . (empty($formData['competitors']) ? 'Not selected' : htmlspecialchars($formData['competitors'])) . '</td></tr>
        <tr><td><strong>Uniqueness:</strong></td><td>' . (empty($formData['uniqueness']) ? 'Not provided' : htmlspecialchars($formData['uniqueness'])) . '</td></tr>
        <tr><td><strong>Website Purpose:</strong></td><td>' . htmlspecialchars($formData['website_purpose']) . '</td></tr>
        <tr><td><strong>Desired Features:</strong></td><td>' . htmlspecialchars($formData['features']) . '</td></tr>
        <tr><td><strong>Integrations:</strong></td><td>' . htmlspecialchars($formData['integrations']) . '</td></tr>
        <tr><td><strong>Referral Type:</strong></td><td>' . htmlspecialchars($formData['referral_type']) . '</td></tr>
        <tr><td><strong>Referral URLs:</strong></td><td>' . (empty($formData['referral_urls']) ? 'Not selected' : htmlspecialchars($formData['referral_urls'])) . '</td></tr>
        <tr><td><strong>Domain Name:</strong></td><td>' . (empty($formData['domainName']) ? 'Not selected' : htmlspecialchars($formData['domainName'])) . '</td></tr>
        <tr><td><strong>Hosting Provider:</strong></td><td>' . (empty($formData['hostingProvider']) ? 'Not selected' : htmlspecialchars($formData['hostingProvider'])) . '</td></tr>
        <tr><td><strong>SEO:</strong></td><td>' . (empty($formData['seo']) ? 'Not provided' : htmlspecialchars($formData['seo'])) . '</td></tr>
        <tr><td><strong>Maintenance:</strong></td><td>' . (empty($formData['maintenance']) ? 'Not provided' : htmlspecialchars($formData['maintenance'])) . '</td></tr>
        <tr><td><strong>Special Requirements:</strong></td><td>' . (empty($formData['specialRequirements']) ? 'Not provided' : htmlspecialchars($formData['specialRequirements'])) . '</td></tr>
        <tr><td><strong>How Heard About Us:</strong></td><td>' . (empty($formData['hearAboutUs']) ? 'Not selected' : htmlspecialchars($formData['hearAboutUs'])) . '</td></tr>
        <tr><td><strong>Consultation:</strong></td><td>' . htmlspecialchars($formData['consultation']) . '</td></tr>
    </table>';

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdfContent = $pdf->Output('', 'S'); // Get PDF as string

    // Generate filename
    $cleanFullname = preg_replace('/[^A-Za-z0-9\-]/', '', $formData['fullname']);
    $dateTime = date('Ymd-His');
    $uniqueId = uniqid();
    $filename = "{$cleanFullname}_{$dateTime}_{$uniqueId}.pdf";
    $filePath = $pdf_dir . $filename;

    // Save PDF locally
    if (file_put_contents($filePath, $pdfContent) === false) {
        error_log("Failed to save PDF locally at $filePath");
    } else {
        error_log("PDF saved locally at $filePath");
    }

    // Save PDF to database
    $stmt = $conn->prepare("INSERT INTO form_data (pdf_file) VALUES (?)");
    $stmt->bind_param("s", $pdfContent);
    if ($stmt->execute()) {
        error_log("PDF saved to database successfully");
    } else {
        error_log("Failed to save PDF to database: " . $stmt->error);
    }
    $stmt->close();

    // Send email with PDF attachment
    $to = 'php2.vydurya@gmail.com';
    $from = 'php2.vydurya@gmail.com';
    $fromName = 'Your Company Name';
    $subject = 'Your Client Onboarding Summary';
    $message = "Dear " . htmlspecialchars($formData['fullname']) . ",\n\n";
    $message .= "Thank you for completing the onboarding form. Please find your summary attached as a PDF.\n\n";
    $message .= "Best regards,\nYour Team";

    $emailSent = sendSmtpEmail($to, $from, $fromName, $subject, $message, $pdfContent, $filename);
    if ($emailSent) {
        error_log("Email process completed successfully");
        echo "<h1>Success!</h1><p>Your form has been submitted, and the summary has been emailed to $to.</p>";
    } else {
        error_log("Email sending failed");
        echo "<h1>Error!</h1><p>Failed to send the email. Please check the server logs for more details or try again later.</p>";
    }

    $conn->close();
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Onboarding Form - Additional Information</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            min-height: 100vh;
            color: #374151;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        .form-textarea {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(209, 213, 219, 0.5);
            border-radius: 12px;
            color: #374151;
            transition: all 0.3s ease;
            min-height: 120px;
            padding: 1rem;
            width: 100%;
            resize: none;
        }
        .form-textarea:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            outline: none;
        }
        .form-textarea::placeholder {
            color: #6b7280;
        }
        .form-checkbox {
            width: 1.2rem;
            height: 1.2rem;
            border: 2px solid rgba(79, 70, 229, 0.5);
            border-radius: 6px;
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
            background: transparent;
        }
        .form-checkbox:checked {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }
        .form-checkbox:hover {
            border-color: #4f46e5;
        }
        .form-radio {
            width: 1.2rem;
            height: 1.2rem;
            border: 2px solid rgba(79, 70, 229, 0.5);
            border-radius: 50%;
            transition: all 0.3s ease;
            cursor: pointer;
            background: transparent;
        }
        .form-radio:checked {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }
        .form-radio:hover {
            border-color: #4f46e5;
        }
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #4f46e5;
            margin-bottom: 1rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-secondary {
            background: rgba(79, 70, 229, 0.1);
            color: #4f46e5;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 600;
            border: 1px solid rgba(79, 70, 229, 0.3);
        }
        .btn-secondary:hover {
            background: rgba(79, 70, 229, 0.2);
            transform: translateY(-2px);
        }
        .form-section {
            background: rgba(255, 255, 255, 0.9);
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(209, 213, 219, 0.5);
            transition: transform 0.2s ease;
        }
        .form-section:hover {
            transform: scale(1.01);
        }
        .hover-scale {
            transition: transform 0.2s ease;
        }
        .hover-scale:hover {
            transform: scale(1.01);
        }
    </style>
</head>
<body class="p-6 md:p-10 bg-gradient-to-br from-blue-50 to-indigo-50">
    <div class="max-w-4xl mx-auto">
        <!-- Progress bar -->
        <div class="mb-8 glass-effect p-6 rounded-xl shadow-lg hover-scale">
            <div class="relative pt-1">
                <div class="flex mb-2 items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-indigo-600 bg-indigo-100">
                            <i class="fas fa-chart-line mr-1"></i>Progress
                        </span>
                    </div>
                    <div class="text-right">
                        <span id="progress-percentage" class="text-xs font-semibold inline-block text-indigo-600">
                            100%
                        </span>
                    </div>
                </div>
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-indigo-100">
                    <div id="progress-bar" style="width:100%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-indigo-500 to-indigo-600"></div>
                </div>
            </div>
        </div>

        <div class="glass-effect p-8 rounded-xl shadow-xl hover-scale">
            <form id="onboardingForm" method="POST" class="space-y-6">
                <div id="page5">
                    <h2 class="text-2xl font-bold mb-6 flex items-center text-indigo-700">
                        <i class="fas fa-info-circle mr-2 text-indigo-500"></i>Additional Information
                    </h2>

                    <div class="form-section">
                        <label class="section-title flex items-center">
                            <i class="fas fa-clipboard-list mr-2"></i>
                            Special Requirements
                        </label>
                        <textarea 
                            class="form-textarea"
                            id="specialRequirements" 
                            placeholder="Tell us about any special requirements or additional features you need..."
                            rows="4" name="specialRequirements"
                        ></textarea>
                    </div>

                    <div class="form-section">
                        <label class="section-title flex items-center">
                            <i class="fas fa-bullhorn mr-2"></i>
                            How did you hear about us?*
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="inline-flex items-center space-x-3 text-gray-600 hover:text-indigo-600">
                                <input type="checkbox" class="form-checkbox" name="hearAboutUs[]" value="Google">
                                <span>Google</span>
                            </label>
                            <label class="inline-flex items-center space-x-3 text-gray-600 hover:text-indigo-600">
                                <input type="checkbox" class="form-checkbox" name="hearAboutUs[]" value="Social Media">
                                <span>Social Media</span>
                            </label>
                            <label class="inline-flex items-center space-x-3 text-gray-600 hover:text-indigo-600">
                                <input type="checkbox" class="form-checkbox" name="hearAboutUs[]" value="Someone Referred">
                                <span>Someone Referred</span>
                            </label>
                            <label class="inline-flex items-center space-x-3 text-gray-600 hover:text-indigo-600">
                                <input type="checkbox" class="form-checkbox" name="hearAboutUs[]" value="Other">
                                <span>Other</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-section">
                        <label class="section-title flex items-center">
                            <i class="fas fa-phone mr-2"></i>
                            Would you like a free consultation call?*
                        </label>
                        <div class="flex space-x-6">
                            <label class="inline-flex items-center space-x-3 text-gray-600 hover:text-indigo-600">
                                <input type="radio" class="form-radio" name="consultation" value="Yes" required>
                                <span>Yes</span>
                            </label>
                            <label class="inline-flex items-center space-x-3 text-gray-600 hover:text-indigo-600">
                                <input type="radio" class="form-radio" name="consultation" value="No">
                                <span>No</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <a href="./tech_req.html">
                            <button type="button" class="btn-secondary">
                                <i class="fas fa-arrow-left mr-2"></i>Previous
                            </button>
                        </a>
                        <button type="submit" id="submitButton" class="btn-primary">
                            <i class="fas fa-paper-plane mr-2"></i>Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('onboardingForm');
            const submitButton = document.getElementById('submitButton');
            const requiredFields = form.querySelectorAll('[required]');
            const textarea = document.getElementById('specialRequirements');
            const checkboxes = form.querySelectorAll('input[type="checkbox"]');
            const radios = form.querySelectorAll('input[type="radio"]');

            function checkFormValidity() {
                let isFormValid = true;

                requiredFields.forEach(field => {
                    if (!field.value) {
                        isFormValid = false;
                    }
                });

                const isCheckboxChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
                const isRadioSelected = Array.from(radios).some(radio => radio.checked);

                submitButton.disabled = !(isFormValid && isCheckboxChecked && isRadioSelected);
            }

            form.addEventListener('input', checkFormValidity);
            form.addEventListener('change', checkFormValidity);
            checkFormValidity();
        });
    </script>
</body>
</html>
