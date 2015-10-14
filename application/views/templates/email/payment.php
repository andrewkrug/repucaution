<h2>Congratulations, <?php echo $user->username;?>!</h2>
<p>Your payment was successfully completed.</p>
<p>Your subscription is active during the period from <?php echo $subscription->start_date.' to '.$subscription->end_date;?></p>
<p>PaymentId: <?php echo $transaction->payment_id;?></p>