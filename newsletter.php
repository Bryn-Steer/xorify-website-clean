<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);

    if (empty($email)) {
        http_response_code(400);
        echo "Please enter a valid email.";
        exit;
    }

    $apiKey = "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"; 
    $listId = 9; // Xorify Newsletter list ID

    $data = [
        "email" => $email,
        "listIds" => [$listId],
        "updateEnabled" => true
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.brevo.com/v3/contacts');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'api-key: ' . $apiKey,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 201 || $httpCode == 204) {
        echo '<div class="success-message">';
        echo '<i class="fas fa-check-circle"></i> Success! You\'re subscribed.<br>';
        echo 'Please check your inbox for a confirmation email and further instructions.';
        echo '</div>';
    } else {
        http_response_code(500);
        echo '<div class="error-message">';
        echo '<i class="fas fa-exclamation-triangle"></i> Failed. Brevo API responded with status code: ' . $httpCode . '<br><br>';
        echo '<strong>Response:</strong><br>';
        echo '<pre>';
        print_r($response);
        echo '</pre>';
        echo '</div>';
    }
} else {
    http_response_code(403);
    echo "Forbidden.";
}
?>
