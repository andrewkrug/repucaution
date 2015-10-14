<form action="" method="POST" id="payform">
    <script src="https://checkout.stripe.com/checkout.js"></script>

    <script>
        var handler = StripeCheckout.configure({
            key: '<?php echo $publishableApiKey;?>',
            image: '<?php echo base_url();?>/public/images/logo.png',
            token: function(token, args) {
                $('#payform')
                    .append($('<input>').attr({ type: 'hidden', name: 'stripeToken', value: token.id }))
                    .submit();
            }
        });

        handler.open({
            name: '<?php echo $site_name ?>',
            description: '<?php echo $transaction->description;?> ($<?php echo $transaction->getFormatedAmount();?>)',
            amount: '<?php echo $transaction->amount;?>'
        });
    </script>
</form>

