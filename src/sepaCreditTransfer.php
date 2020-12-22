<?php
require_once('vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__, 1));
$dotenv->load();

\Stripe\Stripe::setMaxNetworkRetries(3);

$stripe = new \Stripe\StripeClient($_ENV['STRIPE_API_KEY']);
\Stripe\Stripe::setApiKey($_ENV['STRIPE_API_KEY']);


$source = $stripe->sources->create([
  'type' => 'sepa_credit_transfer',
  'currency' => 'eur',
  'owner' => [
    'name' => 'Jenny Rosen',
    'email' => 'reference_customRef123@example.com',
  ],
]);

$customer = $stripe->customers->create([
  'email' => 'jenny.rosen@example.com',
  'source' => $source->id,
]);


sleep(10);
echo "\n\Source: ";
echo $source;
$charge = $stripe->charges->create([
  'amount' => 1000,
  'currency' => 'eur',
  'customer' => $customer->id,
  'source' => $source->id,
]);

$source = $stripe->sources->update(
  $source->id,
  [
    'owner' => [
      'email' => 'reference_customRef456@example.com',
    ],
  ]
);
sleep(10);

$charge = $stripe->charges->create([
  'amount' => 1000,
  'currency' => 'eur',
  'customer' => $customer->id,
  'source' => $source->id,
]);

// This gets you:
/*
  {
    "id": "srctxn_1I0l7RGvip8Nac8HPJn5Qvos",
    "object": "source_transaction",
    "amount": 1000,
    "created": 1608544557,
    "currency": "eur",
    "livemode": false,
    "sepa_credit_transfer": {
        "reference": "customRef123",
        "sender_iban": "DE89370400440532013000",
        "sender_name": "Jenny Rosen"
    },
    "source": "src_1I0l7PGvip8Nac8HGzYdDPia",
    "status": "succeeded",
    "type": "sepa_credit_transfer"
  }
*/
// You can also get them in real-time as a webhook with the event: 'source.transaction.created', which includes the same fields as above example.
$source_transactions = \Stripe\Source::allSourceTransactions($source->id);

echo "\n\n";
echo "\n\Charge: ";
echo $charge;
echo "\n\n";
echo "\n\source_transactions: ";
echo $source_transactions;
