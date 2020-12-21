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
    'email' => 'jenny.rosen@example.com',
  ],
]);

$customer = $stripe->customers->create([
  'email' => 'jenny.rosen@example.com',
  'source' => $source->id,
]);



$source = $stripe->sources->update(
  $source->id,
  [
    'owner' => [
      'email' => 'reference_customRef123@example.com',
    ],
  ]
);
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
      'email' => 'amount_4242@example.com',
    ],
  ]
);

sleep(10);

$charge = $stripe->charges->create([
  'amount' => 4242,
  'currency' => 'eur',
  'customer' => $customer->id,
  'source' => $source->id,
]);

$source_transactions = \Stripe\Source::allSourceTransactions($source->id);

echo "\n\n";
echo "\n\Charge: ";
echo $charge;
echo "\n\n";
echo "\n\source_transactions: ";
echo $source_transactions;
