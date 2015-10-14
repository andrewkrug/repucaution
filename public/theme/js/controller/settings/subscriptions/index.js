$(window).ready(function() {
    $('#cancel_subscription').on('click', function() {
        return question('Are you sure you would like to cancel your subscription? ' +
            'NOTICE: you will be able to access the service until your latest payment expires. ' +
            'Then your account and its data will be deleted entirely.',
            $(this).data('href'));
    });
});