<?php
// Function to check if all required URL parameters exist
function checkRequiredParams() {
    $required_params = [
        'fullname',
        'company',
        'email',
        'phone',
        'address',
        'contactMethod',
        'businessDesc',
        'products',
        'targetAudience',
        'website_purpose',
        'features',
        'integrations',
        'referral_type'
    ];

    foreach ($required_params as $param) {
        if (!isset($_GET[$param]) || empty($_GET[$param])) {
            return false;
        }
    }
    return true;
}

// Check if we have all required URL parameters
if (!checkRequiredParams()) {
    // Redirect to Basic_information.php if any required parameter is missing
    header("Location: Basic_information.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $domain = $_POST['domain'] ?? '';
    $domainName = $_POST['domainName'] ?? '';
    $hosting = $_POST['hosting'] ?? '';
    $hostingProvider = $_POST['hostingProvider'] ?? '';
    $seo = $_POST['seo'] ?? '';
    $maintenance = $_POST['maintenance'] ?? '';

    // Combine previous GET data with current POST data
    $queryParams = http_build_query(array_merge($_GET, [
        'domain' => $domain,
        'domainName' => $domainName,
        'hosting' => $hosting,
        'hostingProvider' => $hostingProvider,
        'seo' => $seo,
        'maintenance' => $maintenance
    ]));

    // Redirect to add_info.php with all data in the URL
    header("Location: add_info.php?$queryParams");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Onboarding Form - Technical Requirements</title>
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
        .form-input {
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid rgba(209, 213, 219, 0.5);
            border-radius: 1rem;
            transition: all 0.2s ease;
            height: 3.5rem;
            font-size: 1.1rem;
        }
        .form-input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
            outline: none;
        }
        .icon-input {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #4f46e5;
            opacity: 0.8;
            transition: all 0.2s ease;
            font-size: 1.2rem;
        }
        .input-container {
            position: relative;
            margin: 1rem 0;
        }
        .input-container input {
            padding-left: 48px;
            width: 100%;
        }
        .input-container:hover .icon-input {
            color: #4338ca;
            opacity: 1;
        }
        .custom-radio {
            width: 1.2rem;
            height: 1.2rem;
            border: 2px solid #4f46e5;
            border-radius: 9999px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .custom-radio:checked {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }
        .custom-radio:hover {
            border-color: #4338ca;
        }
        .btn-primary {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #fff;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-secondary {
            background: rgba(79, 70, 229, 0.1);
            color: #4f46e5;
            border: 1px solid rgba(79, 70, 229, 0.3);
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background: rgba(79, 70, 229, 0.2);
            transform: translateY(-2px);
        }
        .hover-scale {
            transition: transform 0.2s ease;
        }
        .hover-scale:hover {
            transform: scale(1.01);
        }
        .form-section {
            background: rgba(238, 242, 255, 0.5);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .section-label {
            font-size: 1.1rem;
            color: #4338ca;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
                            80%
                        </span>
                    </div>
                </div>
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-indigo-100">
                    <div id="progress-bar" style="width:80%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-indigo-500 to-indigo-600"></div>
                </div>
            </div>
        </div>

        <div class="glass-effect p-8 rounded-xl shadow-xl hover-scale">
            <form id="onboardingForm" method="POST" class="space-y-6">
                <div id="page4">
                    <h2 class="text-2xl font-bold mb-6 flex items-center text-indigo-700">
                        <i class="fas fa-cogs mr-2 text-indigo-500"></i>Technical Requirements
                    </h2>

                    <!-- Domain Name Section -->
                    <div class="form-section">
                        <div class="section-label">
                            <i class="fas fa-globe text-indigo-500"></i>
                            <span>Do you already have a domain name?*</span>
                        </div>
                        <div class="mt-2 space-x-6">
                            <label class="inline-flex items-center hover:text-indigo-600">
                                <input type="radio" class="custom-radio" name="domain" value="Yes" required>
                                <span class="ml-2">Yes</span>
                            </label>
                            <label class="inline-flex items-center hover:text-indigo-600">
                                <input type="radio" class="custom-radio" name="domain" value="No">
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                        <div class="mt-4 input-container">
                            <i class="fas fa-link icon-input"></i>
                            <input class="form-input" id="domainName" type="text" name="domainName" placeholder="Enter Domain Name">
                        </div>
                    </div>

                    <!-- Web Hosting Section -->
                    <div class="form-section">
                        <div class="section-label">
                            <i class="fas fa-server text-indigo-500"></i>
                            <span>Do you have web hosting?*</span>
                        </div>
                        <div class="mt-2 space-x-6">
                            <label class="inline-flex items-center hover:text-indigo-600">
                                <input type="radio" class="custom-radio" name="hosting" value="Yes" required>
                                <span class="ml-2">Yes</span>
                            </label>
                            <label class="inline-flex items-center hover:text-indigo-600">
                                <input type="radio" class="custom-radio" name="hosting" value="No">
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                        <div class="mt-4 input-container">
                            <i class="fas fa-building icon-input"></i>
                            <input class="form-input" id="hostingProvider" type="text" name="hostingProvider" placeholder="Hosting Provider's Name">
                        </div>
                    </div>

                    <!-- SEO Section -->
                    <div class="form-section">
                        <div class="section-label">
                            <i class="fas fa-search text-indigo-500"></i>
                            <span>Do you need SEO optimization?*</span>
                        </div>
                        <div class="mt-2 space-x-6">
                            <label class="inline-flex items-center hover:text-indigo-600">
                                <input type="radio" class="custom-radio" name="seo" value="Yes" required>
                                <span class="ml-2">Yes</span>
                            </label>
                            <label class="inline-flex items-center hover:text-indigo-600">
                                <input type="radio" class="custom-radio" name="seo" value="No">
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                    </div>

                    <!-- Maintenance Section -->
                    <div class="form-section">
                        <div class="section-label">
                            <i class="fas fa-tools text-indigo-500"></i>
                            <span>Do you need ongoing maintenance & support?*</span>
                        </div>
                        <div class="mt-2 space-x-6">
                            <label class="inline-flex items-center hover:text-indigo-600">
                                <input type="radio" class="custom-radio" name="maintenance" value="Yes" required>
                                <span class="ml-2">Yes</span>
                            </label>
                            <label class="inline-flex items-center hover:text-indigo-600">
                                <input type="radio" class="custom-radio" name="maintenance" value="No">
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <a href="./website_goals.php">
                            <button type="button" class="btn-secondary font-bold py-3 px-6 rounded-lg">
                                <i class="fas fa-arrow-left mr-2"></i>Previous
                            </button>
                        </a>
                       
                            <button type="submit" class="btn-primary font-bold py-3 px-6 rounded-lg">
                                Next<i class="fas fa-arrow-right ml-2"></i>
                            </button>
                       
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
    // Add this script just before the closing </body> tag
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('onboardingForm');
    const nextButton = form.querySelector('button[type="submit"]');
    const domainNameInput = document.getElementById('domainName');
    const hostingProviderInput = document.getElementById('hostingProvider');
    
    // Initially hide the input fields and disable the next button
    domainNameInput.parentElement.style.display = 'none';
    hostingProviderInput.parentElement.style.display = 'none';
    nextButton.disabled = true;
    nextButton.classList.add('opacity-50', 'cursor-not-allowed');

    // Required fields to check
    const requiredFields = {
        domain: false,
        hosting: false,
        seo: false,
        maintenance: false,
        domainName: false,
        hostingProvider: false
    };

    // Show/hide domain name input based on selection
    document.querySelectorAll('input[name="domain"]').forEach(radio => {
        radio.addEventListener('change', function() {
            requiredFields.domain = true;
            requiredFields.domainName = this.value === 'No';
            domainNameInput.parentElement.style.display = this.value === 'Yes' ? 'block' : 'none';
            if (this.value === 'Yes') {
                domainNameInput.required = true;
                requiredFields.domainName = false;
            } else {
                domainNameInput.required = false;
                requiredFields.domainName = true;
            }
            validateForm();
        });
    });

    // Show/hide hosting provider input based on selection
    document.querySelectorAll('input[name="hosting"]').forEach(radio => {
        radio.addEventListener('change', function() {
            requiredFields.hosting = true;
            requiredFields.hostingProvider = this.value === 'No';
            hostingProviderInput.parentElement.style.display = this.value === 'Yes' ? 'block' : 'none';
            if (this.value === 'Yes') {
                hostingProviderInput.required = true;
                requiredFields.hostingProvider = false;
            } else {
                hostingProviderInput.required = false;
                requiredFields.hostingProvider = true;
            }
            validateForm();
        });
    });

    // Handle SEO radio buttons
    document.querySelectorAll('input[name="seo"]').forEach(radio => {
        radio.addEventListener('change', function() {
            requiredFields.seo = true;
            validateForm();
        });
    });

    // Handle maintenance radio buttons
    document.querySelectorAll('input[name="maintenance"]').forEach(radio => {
        radio.addEventListener('change', function() {
            requiredFields.maintenance = true;
            validateForm();
        });
    });

    // Handle input field changes
    domainNameInput.addEventListener('input', function() {
        requiredFields.domainName = this.value.trim() !== '';
        validateForm();
    });

    hostingProviderInput.addEventListener('input', function() {
        requiredFields.hostingProvider = this.value.trim() !== '';
        validateForm();
    });

    // Validate the entire form
    function validateForm() {
        const isValid = Object.values(requiredFields).every(field => field === true);
        
        if (isValid) {
            nextButton.disabled = false;
            nextButton.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            nextButton.disabled = true;
            nextButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }
});
</script>
</body>
</html>