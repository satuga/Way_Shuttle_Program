<?php
require_once('lib/Stripe.php');

/*
  $stripe = array(
  "secret_key"      => "sk_test_ZCKFq4iNuiwaLxrWUf44eQgb",
  "publishable_key" => "pk_test_sgyMNRLYJ2gsgzd5UkZMUbe7"
  						
);*/

$stripe = array(
  "secret_key"      => "sk_test_J7yPSc4wORXlfYNaZJki1OPS",
  "publishable_key" => "pk_test_G2SPSwZFMTqi6osW8Hhxs7ZE"
  
);

Stripe::setApiKey($stripe['secret_key']);
?>

