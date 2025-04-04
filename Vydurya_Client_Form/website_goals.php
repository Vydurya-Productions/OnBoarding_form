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
        'targetAudience'
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
    header("Location: Basic_information.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $website_purpose = $_POST['purpose'] ?? '';
    $features = isset($_POST['features']) ? implode(',', $_POST['features']) : ''; // Convert array to comma-separated string
    $integrations = isset($_POST['integrations']) ? implode(',', $_POST['integrations']) : ''; // Convert array to comma-separated string
    $referral_type = $_POST['referral'] ?? '';
    $referral_urls = null;

    switch ($_POST['referral']) {
        case 'Urls':
            $referral_urls = $_POST['urls'] ?? '';
            break;
        case 'No':
            $referral_urls = ''; // Empty string for URL consistency
            break;
    }

    // Combine previous GET data with current POST data
    $queryParams = http_build_query(array_merge($_GET, [
        'website_purpose' => $website_purpose,
        'features' => $features,
        'integrations' => $integrations,
        'referral_type' => $referral_type,
        'referral_urls' => $referral_urls
    ]));

    // Redirect to the next page with all data in the URL
    header("Location: tech_req.php?$queryParams");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Onboarding Form</title>
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
        .form-input, .form-textarea {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(209, 213, 219, 0.5);
            transition: all 0.2s ease;
            resize: none;
        }
        .form-input:focus, .form-textarea:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
        .icon-input {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #4f46e5;
            opacity: 0.8;
        }
        .input-container {
            position: relative;
        }
        .input-container input, .input-container textarea {
            padding-left: 40px;
        }
        .custom-radio, .custom-checkbox {
            border-radius: 9999px;
            transition: all 0.3s ease;
        }
        .custom-radio:checked, .custom-checkbox:checked {
            background-color: #4f46e5;
            border-color: #4f46e5;
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
        .purpose-option {
            background: rgba(79, 70, 229, 0.05);
            transition: all 0.3s ease;
        }
        .purpose-option:hover {
            background: rgba(79, 70, 229, 0.1);
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
                            60%
                        </span>
                    </div>
                </div>
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-indigo-100">
                    <div id="progress-bar" style="width:60%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-indigo-500 to-indigo-600"></div>
                </div>
            </div>
        </div>

        <div class="glass-effect p-8 rounded-xl shadow-xl hover-scale bg-white bg-opacity-90">
        <form id="onboardingForm" method="POST" enctype="multipart/form-data" class="space-y-6">
                <!-- Page 3: Website Goals & Requirements -->
                <div id="page3">
                    <h2 class="text-2xl font-bold mb-6 flex items-center text-indigo-700">
                        <i class="fas fa-bullseye mr-2 text-indigo-500"></i>Website Goals & Requirements
                    </h2>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-bold mb-2 text-indigo-700">
                            <i class="fas fa-tasks mr-2 text-indigo-500"></i>What is the Purpose of your website?*
                        </label>
                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="purpose-option inline-flex items-center p-4 rounded-lg cursor-pointer">
                                <input type="radio" class="form-radio h-5 w-5 text-indigo-600" name="purpose" value="Business Portfolio" required>
                                <span class="ml-2 text-gray-700">Business Portfolio</span>
                            </label>
                            <label class="purpose-option inline-flex items-center p-4 rounded-lg cursor-pointer">
                                <input type="radio" class="form-radio h-5 w-5 text-indigo-600" name="purpose" value="E-Commerce">
                                <span class="ml-2 text-gray-700">E-Commerce</span>
                            </label>
                            <label class="purpose-option inline-flex items-center p-4 rounded-lg cursor-pointer">
                                <input type="radio" class="form-radio h-5 w-5 text-indigo-600" name="purpose" value="Blog/News">
                                <span class="ml-2 text-gray-700">Blog/News</span>
                            </label>
                            <label class="purpose-option inline-flex items-center p-4 rounded-lg cursor-pointer">
                                <input type="radio" class="form-radio h-5 w-5 text-indigo-600" name="purpose" value="Booking & Appointments">
                                <span class="ml-2 text-gray-700">Booking & Appointments</span>
                            </label>
                            <label class="purpose-option inline-flex items-center p-4 rounded-lg cursor-pointer">
                                <input type="radio" class="form-radio h-5 w-5 text-indigo-600" name="purpose" value="Online Community/Forum">
                                <span class="ml-2 text-gray-700">Online Community/Forum</span>
                            </label>
                            <label class="purpose-option inline-flex items-center p-4 rounded-lg cursor-pointer">
                                <input type="radio" class="form-radio h-5 w-5 text-indigo-600" name="purpose" value="Other">
                                <span class="ml-2 text-gray-700">Other</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold mb-2 text-indigo-700">
                            <i class="fas fa-list-ul mr-2 text-indigo-500"></i>What features do you want on your website?*
                        </label>
                        <div class="mt-2 grid grid-cols-2 md:grid-cols-3 gap-4">
                            <label class="purpose-option inline-flex items-center p-3 rounded-lg cursor-pointer">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-indigo-600" name="features[]" value="Contact Form">
                                <span class="ml-2 text-gray-700">Contact Form</span>
                            </label>
                            <label class="purpose-option inline-flex items-center p-3 rounded-lg cursor-pointer">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-indigo-600" name="features[]" value="Online Payments">
                                <span class="ml-2 text-gray-700">Online Payments</span>
                            </label>
                            <label class="purpose-option inline-flex items-center p-3 rounded-lg cursor-pointer">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-indigo-600" name="features[]" value="Subscription">
                                <span class="ml-2 text-gray-700">Subscription</span>
                            </label>
                            <label class="purpose-option inline-flex items-center p-3 rounded-lg cursor-pointer">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-indigo-600" name="features[]" value="E-commerce Shop">
                                <span class="ml-2 text-gray-700">E-commerce Shop</span>
                            </label>
                            <label class="purpose-option inline-flex items-center p-3 rounded-lg cursor-pointer">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-indigo-600" name="features[]" value="Blog/News">
                                <span class="ml-2 text-gray-700">Blog/News</span>
                            </label>
                            <label class="purpose-option inline-flex items-center p-3 rounded-lg cursor-pointer">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-indigo-600" name="features[]" value="Live Chat">
                                <span class="ml-2 text-gray-700">Live Chat</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold mb-2 text-indigo-700">
                            <i class="fas fa-plug mr-2 text-indigo-500"></i>Do you need any third-party integrations?*
                        </label>
                        <div class="mt-2 grid grid-cols-2 md:grid-cols-3 gap-4">
                            <label class="purpose-option inline-flex items-center p-3 rounded-lg cursor-pointer">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-indigo-600" name="integrations[]" value="Payment Gateway">
                                <span class="ml-2 text-gray-700">Payment Gateway</span>
                            </label>
                            <label class="purpose-option inline-flex items-center p-3 rounded-lg cursor-pointer">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-indigo-600" name="integrations[]" value="Chatbot">
                                <span class="ml-2 text-gray-700">Chatbot</span>
                            </label>
                            <label class="purpose-option inline-flex items-center p-3 rounded-lg cursor-pointer">
                                <input type="checkbox" class="form-checkbox h-5 w-5 text-indigo-600" name="integrations[]" value="Other">
                                <span class="ml-2 text-gray-700">Other</span>
                            </label>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold mb-2 text-indigo-700">
                            <i class="fas fa-link mr-2 text-indigo-500"></i>Do you have any referral links?*
                        </label>
                        <div class="mt-2 flex space-x-6">
                            <label class="purpose-option inline-flex items-center p-3 rounded-lg cursor-pointer">
                                <input type="radio" class="form-radio h-5 w-5 text-indigo-600" name="referral" value="Urls" required>
                                <span class="ml-2 text-gray-700">URLs</span>
                            </label>
                            <label class="purpose-option inline-flex items-center p-3 rounded-lg cursor-pointer">
                                <input type="radio" class="form-radio h-5 w-5 text-indigo-600" name="referral" value="No">
                                <span class="ml-2 text-gray-700">No</span>
                            </label>
                        </div>
                    </div>

                    <div id="urls-container" class="mb-4 input-container hidden">
                        <i class="fas fa-link icon-input"></i>
                        <textarea name="urls" class="form-textarea shadow appearance-none border rounded-lg w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none" id="urls" rows="4" placeholder="Enter URLs/Links"></textarea>
                    </div>

                    <div class="flex justify-between mt-8">
                        <a href="./Business_overview.php">
                            <button type="button" class="btn-secondary font-bold py-3 px-6 rounded-lg">
                                <i class="fas fa-arrow-left mr-2"></i>Previous
                            </button>
                        </a>
                        <button type="submit" class="btn-primary font-bold py-3 px-6 rounded-lg">
                            <i class="fas fa-arrow-right mr-2"></i>Next
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
    // Function to check if any radio button in a group is selected
    function isRadioSelected(name) {
        return document.querySelector(`input[name="${name}"]:checked`) !== null;
    }

    // Function to check if at least one checkbox in a group is checked
    function isAnyCheckboxChecked(name) {
        return document.querySelectorAll(`input[name="${name}"]:checked`).length > 0;
    }

    // Function to validate referral URLs based on selection
    function validateReferral() {
        const referralValue = document.querySelector('input[name="referral"]:checked')?.value;
        if (!referralValue) return true;
        
        if (referralValue === 'Urls') {
            const urlsText = document.querySelector('#urls').value.trim();
            return urlsText !== '';
        }
        return true;
    }

    // Function to validate all required fields
    function validateForm() {
        const isValid = 
            isRadioSelected('purpose') &&
            isAnyCheckboxChecked('features[]') &&
            isAnyCheckboxChecked('integrations[]') &&
            isRadioSelected('referral') &&
            validateReferral();

        const nextButton = document.querySelector('button[type="submit"]');
        nextButton.disabled = !isValid;
        nextButton.classList.toggle('opacity-50', !isValid);
        nextButton.classList.toggle('cursor-not-allowed', !isValid);
    }

    // Toggle URLs input visibility
    function toggleUrlsInput() {
        const referralValue = document.querySelector('input[name="referral"]:checked')?.value;
        const urlsContainer = document.getElementById('urls-container');
        urlsContainer.classList.toggle('hidden', referralValue !== 'Urls');
    }

    document.addEventListener('DOMContentLoaded', function() {
        validateForm();

        const form = document.getElementById('onboardingForm');
        
        form.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.name === 'referral') {
                    toggleUrlsInput();
                }
                validateForm();
            });
        });

        form.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', validateForm);
        });

        form.querySelector('#urls').addEventListener('input', validateForm);
    });
    </script>
</body>
</html>


