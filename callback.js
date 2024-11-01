jQuery(document).ready(function(){

        // preload
	try{
	        jQuery.preLoadImages(
        	        pluginpath + 'images/free_call_button_2.jpg',
                	pluginpath + 'images/free_call_button_3.jpg'
	        );
	} catch(e){}

        function callSuccess(message){
                jQuery('#acumen_call_container').css('background-image',
                        'url('+pluginpath+'images/free_call_button_3.jpg)'
                );
                jQuery('#acumen_callnumber').hide();
                jQuery('#acumen_callbutton').hide();
                jQuery('#acumen_callfeedback').html('<div class="acumen_success">Connecting...</div><div class="acumen_success_sub">Your phone will ring shortly</div>');
                if(Cufon) Cufon.refresh();
        }

        jQuery(document).ready(function(){

                // trap enter
                jQuery('#acumen_callnumber').keypress(function(e){
                        if(e.which == 13){
                                jQuery('#acumen_callbutton').click();

                                // analytics
                                if(_gaq) _gaq.push(['_trackEvent','Website Callback','Enter Pressed']);
                                return false;
                        }
                });

                function showCallform(message){
                        jQuery('#acumen_call_container').css('background-image',
                                'url('+pluginpath+'images/free_call_button_2.jpg)'
                        );
                        jQuery('#acumen_callnumber').val('');
                        jQuery('#acumen_callnumber').show();
                        jQuery('#acumen_callbutton').show();
                        jQuery('#acumen_callfeedback').html(message);
                        jQuery('#acumen_callfeedback').show();
                }

                // click the container
                var state='unclicked';
                jQuery('#acumen_call_container').click(function(){

                        // arm
                        switch(state){
                                case 'unclicked':
                                        state='armed';
                                        showCallform('Enter your phone number');
                                        // analytics
                                        if(_gaq) _gaq.push(['_trackEvent','Website Callback','Armed']);
                                break;
                        }
                });

                // click the button
                jQuery('#acumen_callbutton').click(function(){

                        // who to call 
                        var toCall = jQuery('#acumen_callnumber').val();

                        // make call
                        jQuery.ajax({
                                cache: false,
                                url: pluginpath+"initiate.php",
                                data: {number: toCall},
                                context: document.body,
                                success: function(data, status, xhr){
                                        if(xhr.status==200){
                                                callSuccess(data);
                                                if(_gaq) _gaq.push(['_trackEvent','Website Callback','Call Success',toCall]);
                                        }
                                        else{
                                                showCallform(data);
                                                // analytics
                                                if(_gaq) _gaq.push(['_trackEvent','Website Callback','Call Failure',toCall]);
                                        }
                                }
                        });
                });

        });
});
