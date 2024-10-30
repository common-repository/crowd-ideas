<script type="text/javascript">

        // rating script
        $(function(){ 
            $('.rate-btn').hover(function(){
                $('.rate-btn').removeClass('rate-btn-hover');
                var therate = $(this).attr('id');
                for (var i = therate; i >= 0; i--) {
                    $('.rate-btn-'+i).addClass('rate-btn-hover');
                };
            });
                            
            $('.rate-btn').click(function(){   
                var therate = $(this).attr('id');
                var dataRate = 'act=rate&post_id=<?php echo $_POST['post_id']?>?>&rate='+therate; //
				//alert(dataRate);
                $('.rate-btn').removeClass('rate-btn-active');
                for (var i = therate; i >= 0; i--) {
                    $('.rate-btn-'+i).addClass('rate-btn-active');
                };
                $.ajax({
                    type : "POST",
                    
					url : myAjax.ajaxurl,
					data: {action: "my_user_vote", dataRate},
                    success:function(){}
                });
                
            });
        });


</script>