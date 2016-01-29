<script>

(function ( $ ) {

    $.fn.ajaxBox = function(options) {
    
        var settings = $.extend({
            container: this,
            ajaxLoader: "/img/ajax-loader.gif",
        }, options );
      
        $.ajax(
        {
            url : this.data('ajax'),
            type: "GET",
            success:function(data, textStatus, jqXHR) 
            {
                $(settings.container).html(data);
            },
            error: function(jqXHR, textStatus, errorThrown) 
            {
                $(settings.container).html('<center><img src="' + settings.ajaxLoader + '" /><br/><br/>' + errorThrown + '</center>');
            }
        });
      
        this.on("submit", "form", function(e) {
            
            if ($(document.activeElement).attr('formtarget')) {
                return true;
            }
          
            e.preventDefault();
            
            console.log(e);

            var formURL = $(this).attr("action");
            var method = $(this).attr("method");
            
            if ($(document.activeElement).attr('formaction')) {
                formURL = $(document.activeElement).attr('formaction');
            }
            
            $(settings.container).html('<center><img src="' + settings.ajaxLoader + '" /></center>');
            
            $.ajax(
            {
                url : formURL,
                type: method,
                data: new FormData( this ),
                processData: false,
                contentType: false,
                success:function(data, textStatus, jqXHR) 
                {
                    $(settings.container).html(data);
                },
                error: function(jqXHR, textStatus, errorThrown) 
                {
                    alert(textStatus);
                    $(settings.container).html('<center><img src="' + settings.ajaxLoader + '" /><br/><br/>' + errorThrown + '</center>');
                }
            });
        });

        this.on("click", "a[href]", function(e) {
          
            if ($(this).attr('target')) {
                return true;
            }
          
            e.preventDefault();
        
            var linkUrl = $(this).attr("href");
            
            $(settings.container).html('<center><img src="' + settings.ajaxLoader + '" /></center>');
            
            $.ajax(
            {
                url : linkUrl,
                type: "GET",
                success:function(data, textStatus, jqXHR) 
                {
                    $(settings.container).html(data);
                },
                error: function(jqXHR, textStatus, errorThrown) 
                {
                    $(settings.container).html('<center><img src="' + settings.ajaxLoader + '" /><br/><br/>' + errorThrown + '</center>');
                }
            });
        });
        
        return this;
    };

}( jQuery ));

</script>