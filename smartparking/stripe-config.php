<?php
require_once('lib/Stripe.php');
// Test credentials
// /*
$stripe = array(
  "secret_key"      => "sk_test_ZCKFq4iNuiwaLxrWUf44eQgb",
  "publishable_key" => "pk_test_sgyMNRLYJ2gsgzd5UkZMUbe7"

);
// */
/* Live credentials
$stripe = array(
   "secret_key"      => "sk_live_PGFjFjH7r9ahiLk49wQiBYwO", // Live
   "publishable_key" => "pk_live_JauicenpYmJxOvXgM6EYQwC7" // Live
);
*/
Stripe::setApiKey($stripe['secret_key']);
?>
