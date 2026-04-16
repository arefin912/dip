<?php
require_once('vendor/autoload.php'); // Path to your autoloader
include 'includes/db_config.php';

$stripeSecretKey = getenv('STRIPE_SECRET_KEY');
if (!$stripeSecretKey) {
    echo json_encode(['success' => false, 'error' => 'Stripe secret key is not configured.']);
    exit;
}

\Stripe\Stripe::setApiKey($stripeSecretKey);

$data = json_decode(file_get_contents('php://input'), true);

if ($data && isset($data['amount'], $data['shipmentId'])) {
    $amount = $data['amount'];
    $shipmentId = $data['shipmentId'];

    try {
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $amount * 100, // Stripe uses cents
            'currency' => 'usd',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);


        $sql = "INSERT INTO payments (shipment_id, amount, method, transaction_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("idss", $shipmentId, $amount, 'stripe', $paymentIntent->id);
        $stmt->execute();
        $stmt->close();
        $conn->close();


        echo json_encode(['success' => true, 'clientSecret' => $paymentIntent->client_secret]);

    } catch (\Stripe\Exception\CardException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Server Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid Request']);
}
?>
