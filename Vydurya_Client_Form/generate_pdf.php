<?php
// Function to check if all required parameters exist in $_POST (since we're using POST from the form)
function checkRequiredParams() {
    $required_params = [
        'fullname', 'company', 'email', 'phone', 'address', 'contactMethod',
        'businessDesc', 'products', 'targetAudience', 'website_purpose',
        'features', 'integrations', 'referral_type', 'specialRequirements',
        'hearAboutUs', 'consultation', 'domainName', 'hostingProvider', 'seo', 'maintenance'
    ];

    foreach ($required_params as $param) {
        if (!isset($_POST[$param])) {
            return false;
        }
    }
    return true;
}

// Redirect to Basic_information.php if any required parameter is missing
if (!checkRequiredParams()) {
    header("Location: Basic_information.php");
    exit();
}

if (isset($_POST['generate_pdf'])) {
    // Require TCPDF library
    require_once('tcpdf/tcpdf.php'); // Adjust path if needed
    
    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor($_POST['fullname']);
    $pdf->SetTitle('Client Onboarding Summary');
    $pdf->SetSubject('Client Information');
    
    // Set margins
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 15);
    
    // Add a page
    $pdf->AddPage();
    
    // Set font
    $pdf->SetFont('helvetica', '', 12);
    
    // Create HTML content for PDF using $_POST
    $html = '
    <h1 style="color: #4f46e5;">Client Onboarding Summary</h1>
    <table cellpadding="5">
        <tr><td style="width: 200px;"><strong>Full Name:</strong></td><td>' . htmlspecialchars($_POST['fullname']) . '</td></tr>
        <tr><td><strong>Company:</strong></td><td>' . htmlspecialchars($_POST['company']) . '</td></tr>
        <tr><td><strong>Email:</strong></td><td>' . htmlspecialchars($_POST['email']) . '</td></tr>
        <tr><td><strong>Phone:</strong></td><td>' . htmlspecialchars($_POST['phone']) . '</td></tr>
        <tr><td><strong>Website:</strong></td><td>' . (isset($_POST['website']) && !empty($_POST['website']) ? htmlspecialchars($_POST['website']) : 'Not provided') . '</td></tr>
        <tr><td><strong>Address:</strong></td><td>' . htmlspecialchars($_POST['address']) . '</td></tr>
        <tr><td><strong>Contact Method:</strong></td><td>' . (!empty($_POST['contactMethod']) ? htmlspecialchars($_POST['contactMethod']) : 'Not selected') . '</td></tr>
        <tr><td><strong>Business Description:</strong></td><td>' . htmlspecialchars($_POST['businessDesc']) . '</td></tr>
        <tr><td><strong>Products/Services:</strong></td><td>' . htmlspecialchars($_POST['businessDesc']) . '</td></tr>
        <tr><td><strong>Target Audience:</strong></td><td>' . htmlspecialchars($_POST['targetAudience']) . '</td></tr>
        <tr><td><strong>Competitor Websites:</strong></td><td>' . (isset($_POST['competitors']) && !empty($_POST['competitors']) ? htmlspecialchars($_POST['competitors']) : 'Not provided') . '</td></tr>
        <tr><td><strong>Website Uniqueness:</strong></td><td>' . (isset($_POST['uniqueness']) && !empty($_POST['uniqueness']) ? htmlspecialchars($_POST['uniqueness']) : 'Not provided') . '</td></tr>
        <tr><td><strong>Website Purpose:</strong></td><td>' . htmlspecialchars($_POST['website_purpose']) . '</td></tr>
        <tr><td><strong>Desired Features:</strong></td><td>' . (!empty($_POST['features']) ? htmlspecialchars($_POST['features']) : 'Not selected') . '</td></tr>
        <tr><td><strong>Integrations:</strong></td><td>' . (!empty($_POST['integrations']) ? htmlspecialchars($_POST['integrations']) : 'Not selected') . '</td></tr>
        <tr><td><strong>Referral Type:</strong></td><td>' . htmlspecialchars($_POST['referral_type']) . '</td></tr>
        <tr><td><strong>Referral URLs:</strong></td><td>' . (isset($_POST['referral_urls']) && !empty($_POST['referral_urls']) ? htmlspecialchars($_POST['referral_urls']) : 'Not provided') . '</td></tr>
        <tr><td><strong>Domain Name:</strong></td><td>' . htmlspecialchars($_POST['domainName']) . '</td></tr>
        <tr><td><strong>Web Hosting:</strong></td><td>' . htmlspecialchars($_POST['hostingProvider']) . '</td></tr>
        <tr><td><strong>SEO Optimization:</strong></td><td>' . htmlspecialchars($_POST['seo']) . '</td></tr>
        <tr><td><strong>Maintenance & Support:</strong></td><td>' . htmlspecialchars($_POST['maintenance']) . '</td></tr>
        <tr><td><strong>Special Requirements:</strong></td><td>' . (isset($_POST['specialRequirements']) && !empty($_POST['specialRequirements']) ? htmlspecialchars($_POST['specialRequirements']) : 'Not provided') . '</td></tr>
        <tr><td><strong>How Heard About Us:</strong></td><td>' . (!empty($_POST['hearAboutUs']) ? htmlspecialchars($_POST['hearAboutUs']) : 'Not selected') . '</td></tr>
        <tr><td><strong>Consultation:</strong></td><td>' . htmlspecialchars($_POST['consultation']) . '</td></tr>
    </table>';
    
    // Write HTML content to PDF
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Clean fullname for filename (remove special characters)
    $cleanFullname = preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['fullname']);
    
    // Get current date and time (e.g., 20250220-143022 for Feb 20, 2025 14:30:22)
    $dateTime = date('Ymd-His');
    
    // Generate unique ID (using uniqid)
    $uniqueId = uniqid();
    
    // Combine into filename
    $filename = "{$cleanFullname}_{$dateTime}_{$uniqueId}.pdf";
    
    // Output PDF with custom filename
    $pdf->Output($filename, 'D');
    exit();
}
?>