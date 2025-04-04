<?php
// Function to check if all required URL parameters exist
function checkRequiredParams() {
    $required_params = [
        'fullname', 'company', 'email', 'phone', 'address', 'contactMethod',
        'businessDesc', 'products', 'targetAudience', 'website_purpose',
        'features', 'integrations', 'referral_type', 'specialRequirements',
        'hearAboutUs', 'consultation', 'domainName', 'hostingProvider', 'seo', 'maintenance'
    ];

    foreach ($required_params as $param) {
        if (!isset($_GET[$param])) {
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Onboarding Form - Display</title>
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
    </style>
</head>
<body class="p-6 md:p-10 bg-gradient-to-br from-blue-50 to-indigo-50">
    <div class="max-w-4xl mx-auto">
        <div class="glass-effect p-8 rounded-xl shadow-xl">
            <h2 class="text-2xl font-bold mb-6 flex items-center text-indigo-700">
                <i class="fas fa-info-circle mr-2 text-indigo-500"></i>Client Onboarding Summary
            </h2>

            <!-- Existing form sections (unchanged) -->
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-user mr-2"></i> Full Name
                </label>
                <p class="text-gray-700"><?php echo htmlspecialchars($_GET['fullname']); ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-building mr-2"></i> Company
                </label>
                <p class="text-gray-700"><?php echo htmlspecialchars($_GET['company']); ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-envelope mr-2"></i> Email
                </label>
                <p class="text-gray-700"><?php echo htmlspecialchars($_GET['email']); ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-phone mr-2"></i> Phone
                </label>
                <p class="text-gray-700"><?php echo htmlspecialchars($_GET['phone']); ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-globe mr-2"></i> Website
                </label>
                <p class="text-gray-700"><?php echo isset($_GET['website']) && !empty($_GET['website']) ? htmlspecialchars($_GET['website']) : 'Not provided'; ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-map-marker-alt mr-2"></i> Address
                </label>
                <p class="text-gray-700"><?php echo htmlspecialchars($_GET['address']); ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-comments mr-2"></i> Preferred Contact Method
                </label>
                <p class="text-gray-700"><?php echo !empty($_GET['contactMethod']) ? htmlspecialchars($_GET['contactMethod']) : 'Not selected'; ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-briefcase mr-2"></i> Business Description
                </label>
                <p class="text-gray-700"><?php echo htmlspecialchars($_GET['businessDesc']); ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-boxes mr-2"></i> Products/Services
                </label>
                <p class="text-gray-700"><?php echo htmlspecialchars($_GET['products']); ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-users mr-2"></i> Target Audience
                </label>
                <p class="text-gray-700"><?php echo htmlspecialchars($_GET['targetAudience']); ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-chart-line mr-2"></i> Competitor Websites
                </label>
                <p class="text-gray-700"><?php echo isset($_GET['competitors']) && !empty($_GET['competitors']) ? htmlspecialchars($_GET['competitors']) : 'Not provided'; ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-star mr-2"></i> Website Uniqueness
                </label>
                <p class="text-gray-700"><?php echo isset($_GET['uniqueness']) && !empty($_GET['uniqueness']) ? htmlspecialchars($_GET['uniqueness']) : 'Not provided'; ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-globe mr-2"></i> Website Purpose
                </label>
                <p class="text-gray-700"><?php echo htmlspecialchars($_GET['website_purpose']); ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-cogs mr-2"></i> Desired Features
                </label>
                <p class="text-gray-700"><?php echo !empty($_GET['features']) ? htmlspecialchars($_GET['features']) : 'Not selected'; ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-plug mr-2"></i> Integrations
                </label>
                <p class="text-gray-700"><?php echo !empty($_GET['integrations']) ? htmlspecialchars($_GET['integrations']) : 'Not selected'; ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-handshake mr-2"></i> Referral Type
                </label>
                <p class="text-gray-700"><?php echo htmlspecialchars($_GET['referral_type']); ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-link mr-2"></i> Referral URLs
                </label>
                <p class="text-gray-700"><?php echo isset($_GET['referral_urls']) && !empty($_GET['referral_urls']) ? htmlspecialchars($_GET['referral_urls']) : 'Not provided'; ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-globe mr-2"></i> Domain Name
                </label>
                <p class="text-gray-700"><?php echo isset($_GET['domainName']) && !empty($_GET['domainName']) ? htmlspecialchars($_GET['domainName']) : 'Not provided'; ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-server mr-2"></i> Web Hosting
                </label>
                <p class="text-gray-700"><?php echo isset($_GET['hostingProvider']) && !empty($_GET['hostingProvider']) ? htmlspecialchars($_GET['hostingProvider']) : 'Not provided'; ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-search mr-2"></i> SEO Optimization
                </label>
                <p class="text-gray-700"><?php echo htmlspecialchars($_GET['seo']); ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-tools mr-2"></i> Maintenance & Support
                </label>
                <p class="text-gray-700"><?php echo htmlspecialchars($_GET['maintenance']); ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-clipboard-list mr-2"></i> Special Requirements
                </label>
                <p class="text-gray-700"><?php echo isset($_GET['specialRequirements']) && !empty($_GET['specialRequirements']) ? htmlspecialchars($_GET['specialRequirements']) : 'Not provided'; ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-bullhorn mr-2"></i> How Did You Hear About Us?
                </label>
                <p class="text-gray-700"><?php echo !empty($_GET['hearAboutUs']) ? htmlspecialchars($_GET['hearAboutUs']) : 'Not selected'; ?></p>
            </div>
            <div class="form-section">
                <label class="section-title flex items-center">
                    <i class="fas fa-phone mr-2"></i> Free Consultation Call
                </label>
                <p class="text-gray-700"><?php echo htmlspecialchars($_GET['consultation']); ?></p>
            </div>

            <div class="flex justify-end mt-8 space-x-4">
                <form action="cancel.php" method="POST">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                </form>
                <form action="generate_pdf.php" method="POST">
                    <!-- Add hidden inputs for all $_GET parameters -->
                    <?php foreach ($_GET as $key => $value): ?>
                        <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                    <?php endforeach; ?>
                    <button type="submit" name="generate_pdf" class="btn-primary">
                        <i class="fas fa-file-pdf mr-2"></i>Download
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>