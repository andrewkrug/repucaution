jQuery(function($){

    $(document).ready(function(){
        var stars = g_settings.stars;
        if(stars > 0){
            $('.rating-box').each(function(){
                var $this = $(this);

                $this.raty({
                    numberMax: stars,
                    score: $this.data('rank'),
                    path:g_settings.base_url+'public/images/raty/',
                    readOnly: true,
                    hints: _.times(stars, function(){ return null; } )
                });
            });
        }


    });

});