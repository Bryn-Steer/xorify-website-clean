<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $consent = isset($_POST["consent"]) && $_POST["consent"] === "yes";

    if (empty($email) || !$consent) {
        http_response_code(400);
        echo "Please enter a valid email and agree to receive emails.";
        exit;
    }

    $api_key = getenv("SENDINBLUE_API_KEY");

    $listId = 9;

    $data = [
        'email' => $email,
        'listIds' => [$listId],
        'updateEnabled' => true
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://api.brevo.com/v3/contacts');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'api-key: ' . $api_key,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 201 || $httpCode == 204) {
        echo "Success! You've been subscribed.";
    } else {
        http_response_code(500);
        echo "Something went wrong. Please try again later.";
    }
} else {
    http_response_code(403);
    echo "Forbidden.";
}
?>
