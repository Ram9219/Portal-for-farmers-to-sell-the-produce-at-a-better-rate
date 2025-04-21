<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = $_POST['email']; // User's email address
    $subject = "Order Confirmation - Farmer's Market";
    $shippingAddress = htmlspecialchars($_POST['shipping_address']);
    $total = htmlspecialchars($_POST['total']);

    $message = "
        <h1>Thank you for your order!</h1>
        <p>Your order has been placed successfully. Below are the details:</p>
        <p><strong>Shipping Address:</strong> $shippingAddress</p>
        <p><strong>Total Amount:</strong> Rs.$total</p>
        <p>We will notify you once your order is shipped.</p>
    ";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: kashaudhans977@gmail.com" . "\r\n";

    if (mail($to, $subject, $message, $headers)) {
        echo json_encode(['success' => true, 'message' => 'Email sent successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send email.']);
    }
}


?>