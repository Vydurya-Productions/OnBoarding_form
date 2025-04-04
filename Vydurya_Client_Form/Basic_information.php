<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING);
    $company = filter_input(INPUT_POST, 'company', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $website = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_STRING); // Sanitize website
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $contactMethod = isset($_POST['contactMethod']) ? implode(',', $_POST['contactMethod']) : ''; // Convert array to string

    // Debug output
    error_log('Form submitted: ' . print_r($_POST, true));

    // Prepare query string with form data
    $queryParams = http_build_query([
        'fullname' => $fullname,
        'company' => $company,
        'email' => $email,
        'phone' => $phone,
        'website' => $website,
        'address' => $address,
        'contactMethod' => $contactMethod
    ]);

    // Redirect to the next page with query parameters
    header("Location: Business_overview.php?$queryParams");
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
        /* Your existing CSS styles (unchanged) */
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
            pointer-events: none;
        }
        .input-container {
            position: relative;
            height: 56px;
        }
        .input-container.textarea-container {
            height: auto;
        }
        .input-container input {
            padding-left: 40px;
            height: 100%;
            width: 100%;
            box-sizing: border-box;
        }
        .input-container textarea {
            padding-left: 40px;
            padding-top: 12px;
            width: 100%;
            box-sizing: border-box;
        }
        .error-message {
            position: absolute;
            top: 90%;
            left: 0;
            width: 100%;
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 4px;
        }
        .custom-radio, .custom-checkbox {
            border-radius: 9999px;
            transition: all 0.3s ease;
        }
        .custom-radio:checked, .custom-checkbox:checked {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }
        .custom-checkbox {
            border-radius: 6px;
        }
        .form-radio, .form-checkbox {
            width: 1.2rem;
            height: 1.2rem;
            border: 2px solid #4f46e5;
            transition: all 0.3s ease;
        }
        .form-radio:checked, .form-checkbox:checked {
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
        <!-- Progress bar (unchanged) -->
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
                            20%
                        </span>
                    </div>
                </div>
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-indigo-100">
                    <div id="progress-bar" style="width:20%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-indigo-500 to-indigo-600"></div>
                </div>
            </div>
        </div>

        <div class="glass-effect p-8 rounded-xl shadow-xl hover-scale bg-white bg-opacity-90">
            <form id="onboardingForm" method="POST" class="space-y-6">
                <!-- Page 1: Basic Information -->
                <div id="page1" class="form-page">
                    <h2 class="text-2xl font-bold mb-6 flex items-center text-indigo-700">
                        <i class="fas fa-user-circle mr-2 text-indigo-500"></i>Basic Information
                    </h2>
                    <div class="mb-4 input-container group">
                        <i class="fas fa-user icon-input group-hover:text-indigo-700"></i>
                        <input class="form-input shadow appearance-none border rounded-lg text-gray-700 leading-tight focus:outline-none" name="fullname" id="fullname" type="text" placeholder="Full Name*" required>
                    </div>
                    <div class="mb-4 input-container group">
                        <i class="fas fa-building icon-input group-hover:text-indigo-700"></i>
                        <input class="form-input shadow appearance-none border rounded-lg text-gray-700 leading-tight focus:outline-none" name="company" id="company" type="text" placeholder="Company Name*" required>
                    </div>
                    <div class="mb-4 input-container group">
                        <i class="fas fa-envelope icon-input group-hover:text-indigo-700"></i>
                        <input class="form-input shadow appearance-none border rounded-lg text-gray-700 leading-tight focus:outline-none" id="email" name="email" type="email" placeholder="Email*" required>
                    </div>
                    <div class="mb-4 input-container group">
                        <i class="fas fa-phone icon-input group-hover:text-indigo-700"></i>
                        <input class="form-input shadow appearance-none border rounded-lg text-gray-700 leading-tight focus:outline-none" id="phone" type="tel" name="phone" placeholder="Phone Number*" required>
                    </div>
                    <div class="mb-4 input-container group">
                        <i class="fas fa-globe icon-input group-hover:text-indigo-700"></i>
                        <input class="form-input shadow appearance-none border rounded-lg text-gray-700 leading-tight focus:outline-none" id="website" type="text" name="website" placeholder="Company Website">
                    </div>
                    <div class="mb-4 input-container textarea-container group">
                        <i class="fas fa-map-marker-alt icon-input group-hover:text-indigo-700" style="top: 24px;"></i>
                        <textarea class="form-textarea shadow appearance-none border rounded-lg text-gray-700 leading-tight focus:outline-none" id="address" name="address" rows="4" placeholder="Business Location/Address*" required></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2 text-indigo-700">
                            <i class="fas fa-comments mr-2 text-indigo-500"></i>Preferred Contact Method*
                        </label>
                        <div class="mt-2 space-x-6">
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="form-checkbox custom-checkbox h-5 w-5 text-indigo-600" name="contactMethod[]" value="Phone">
                                <span class="ml-2">Phone</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="form-checkbox custom-checkbox h-5 w-5 text-indigo-600" name="contactMethod[]" value="Email">
                                <span class="ml-2">Email</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="form-checkbox custom-checkbox h-5 w-5 text-indigo-600" name="contactMethod[]" value="WhatsApp">
                                <span class="ml-2">WhatsApp</span>
                            </label>
                        </div>
                    </div>
                    <button type="submit" id="nextButton" class="btn-primary font-bold py-3 px-6 rounded-lg">
                        <i class="fas fa-arrow-right mr-2"></i>Next
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>