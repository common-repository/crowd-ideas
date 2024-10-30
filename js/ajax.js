// rating script
        jQuery(function(){ 
            jQuery('.rate-btn').hover(function(){
                jQuery('.rate-btn').removeClass('rate-btn-hover');
                var therate = jQuery(this).attr('id');
				var thepostid = jQuery('#normalp').text();
				var theuserid = jQuery('#normaluserid').text();
				var destinationurl = jQuery('#destinationurl').text();
                for (var i = therate; i >= 0; i--) {
                    jQuery('.rate-btn-'+i).addClass('rate-btn-hover');
                };
            });
                            
            jQuery('.rate-btn').click(function(){   
                var therate = jQuery(this).attr('id');
				var thepostid = jQuery('#normalp').text();
				var theuserid = jQuery('#normaluserid').text();
				var destinationurl = jQuery('#destinationurl').text();
                var dataRate = '&act=rate&post_id='+thepostid+'&rate='+therate+'&theuserid='+theuserid; //
				//alert(dataRate);
                jQuery('.rate-btn').removeClass('rate-btn-active');
                for (var i = therate; i >= 0; i--) {
                    jQuery('.rate-btn-'+i).addClass('rate-btn-active');
                };
                jQuery.ajax({
                    type : "POST",
                    
					//url : myAjax.ajaxurl,
					url: destinationurl,
					data: dataRate,
                    success:function(){}
                });
                
            });
        });