<?php
// Check only required fields from URL parameters
$requiredFields = ['fullname', 'company', 'email', 'phone', 'address', 'contactMethod'];
$missingFields = array_filter($requiredFields, fn($field) => empty($_GET[$field]));

if (!empty($missingFields)) {
    error_log('Missing required URL parameters: ' . print_r($missingFields, true));
    header("Location: Basic_information.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    if (
        !empty($_POST['businessDesc']) &&
        !empty($_POST['products']) &&
        !empty($_POST['targetAudience'])
    ) {
        // Prepare query string with data from both pages
        $queryParams = http_build_query([
            'fullname' => $_GET['fullname'],
            'company' => $_GET['company'],
            'email' => $_GET['email'],
            'phone' => $_GET['phone'],
            'website' => $_GET['website'] ?? '',
            'address' => $_GET['address'],
            'contactMethod' => $_GET['contactMethod'],
            'businessDesc' => filter_input(INPUT_POST, 'businessDesc', FILTER_SANITIZE_STRING),
            'products' => filter_input(INPUT_POST, 'products', FILTER_SANITIZE_STRING),
            'targetAudience' => filter_input(INPUT_POST, 'targetAudience', FILTER_SANITIZE_STRING),
            'competitors' => filter_input(INPUT_POST, 'competitors', FILTER_SANITIZE_STRING) ?? '',
            'uniqueness' => filter_input(INPUT_POST, 'uniqueness', FILTER_SANITIZE_STRING) ?? ''
        ]);

        // Redirect to next page with all data
        header("Location: website_goals.php?$queryParams");
        exit();
    } else {
        error_log('Missing required POST data: ' . print_r($_POST, true));
    }
}

// Website is optional, so we'll set it to empty string if not provided
$website = $_GET['website'] ?? '';
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
        .input-container .icon-input-textarea {
            top: 24px;
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
                            40%
                        </span>
                    </div>
                </div>
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-indigo-100">
                    <div id="progress-bar" style="width:40%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-indigo-500 to-indigo-600"></div>
                </div>
            </div>
        </div>

        <div class="glass-effect p-8 rounded-xl shadow-xl hover-scale bg-white bg-opacity-90">
            <form id="onboardingForm" method="POST" class="space-y-6">
                <!-- Page 2: Business Overview -->
                <div id="page2">
                    <h2 class="text-2xl font-bold mb-6 flex items-center text-indigo-700">
                        <i class="fas fa-briefcase mr-2 text-indigo-500"></i>Business Overview
                    </h2>
                    <div class="mb-4 input-container group">
                        <i class="fas fa-info-circle icon-input icon-input-textarea group-hover:text-indigo-700"></i>
                        <textarea class="form-textarea shadow appearance-none border rounded-lg w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none" id="businessDesc" name="businessDesc" rows="4" placeholder="Describe Your Business in a few sentences*" required></textarea>
                    </div>
                    <div class="mb-4 input-container group">
                        <i class="fas fa-boxes icon-input icon-input-textarea group-hover:text-indigo-700"></i>
                        <textarea class="form-textarea shadow appearance-none border rounded-lg w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none" id="products" name="products" rows="4" placeholder="What products/services do you offer?*" required></textarea>
                    </div>
                    <div class="mb-4 input-container group">
                        <i class="fas fa-users icon-input group-hover:text-indigo-700"></i>
                        <input class="form-input shadow appearance-none border rounded-lg w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none" id="targetAudience" name="targetAudience" type="text" placeholder="Who is your target audience?*" required>
                    </div>
                    <div class="mb-4 input-container group">
                        <i class="fas fa-chart-line icon-input icon-input-textarea group-hover:text-indigo-700"></i>
                        <textarea class="form-textarea shadow appearance-none border rounded-lg w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none" id="competitors" name="competitors" rows="4" placeholder="Competitor Websites"></textarea>
                    </div>
                    <div class="mb-4 input-container group">
                        <i class="fas fa-star icon-input icon-input-textarea group-hover:text-indigo-700"></i>
                        <textarea class="form-textarea shadow appearance-none border rounded-lg w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none" id="uniqueness" name="uniqueness" rows="4" placeholder="What should make your website unique?"></textarea>
                    </div>
                    <div class="flex justify-between mt-8">
                        <a href="./Basic_information.php">
                            <button type="button" class="btn-secondary font-bold py-3 px-6 rounded-lg">
                                <i class="fas fa-arrow-left mr-2"></i>Previous
                            </button>
                        </a>
                        <button type="submit" class="btn-primary font-bold py-3 px-6 rounded-lg" id="nextButton" disabled>
                            <i class="fas fa-arrow-right mr-2"></i>Next
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        // Function to check if all required fields are filled
        function checkFormValidity() {
            const businessDesc = document.querySelector('textarea[name="businessDesc"]');
            const products = document.querySelector('textarea[name="products"]');
            const targetAudience = document.querySelector('input[name="targetAudience"]');
            const nextButton = document.getElementById('nextButton');
            
            // Check if all required fields have values
            const isValid = 
                businessDesc.value.trim() !== "" && 
                products.value.trim() !== "" && 
                targetAudience.value.trim() !== "";
            
            // Enable/disable button based on validation
            nextButton.disabled = !isValid;
            nextButton.style.opacity = isValid ? "1" : "0.5";
            nextButton.style.cursor = isValid ? "pointer" : "not-allowed";
        }

        // Add event listeners to all required fields
        document.addEventListener('DOMContentLoaded', function() {
            const requiredFields = [
                'textarea[name="businessDesc"]',
                'textarea[name="products"]',
                'input[name="targetAudience"]'
            ];
            
            requiredFields.forEach(selector => {
                const element = document.querySelector(selector);
                element.addEventListener('input', checkFormValidity);
            });
            
            // Run initial check
            checkFormValidity();
        });
    </script>
</body>
</html>