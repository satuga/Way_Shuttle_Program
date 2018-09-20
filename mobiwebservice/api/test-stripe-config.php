<?php
require_once('lib/Stripe.php');

/*
  $stripe = array(
  "secret_key"      => "sk_test_ZCKFq4iNuiwaLxrWUf44eQgb",
  "publishable_key" => "pk_test_sgyMNRLYJ2gsgzd5UkZMUbe7"

);
*/
// /*
//define('STRIPE_PRIVATE_KEY', 'sk_live_PGFjFjH7r9ahiLk49wQiBYwO');
//define('STRIPE_PUBLIC_KEY', 'pk_live_JauicenpYmJxOvXgM6EYQwC7');
$stripe = array(
   "secret_key"      => "sk_live_PGFjFjH7r9ahiLk49wQiBYwO", // Live
   "publishable_key" => "pk_live_JauicenpYmJxOvXgM6EYQwC7" // Live

  //"secret_key"      => "sk_test_J7yPSc4wORXlfYNaZJki1OPS",//nilesh
  //"publishable_key" => "pk_test_G2SPSwZFMTqi6osW8Hhxs7ZE"//nilesh
 // "secret_key"      => "sk_test_ZCKFq4iNuiwaLxrWUf44eQgb",//zeba
 // "publishable_key" => "pk_test_sgyMNRLYJ2gsgzd5UkZMUbe7"//zeba

);
// */

Stripe::setApiKey($stripe['secret_key']);
?>
