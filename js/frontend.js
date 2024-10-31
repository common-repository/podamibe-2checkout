(function ($) {
    $(function () {        
        
        var successCallback = function(data) {
            var myForm = document.getElementById('ptcCCForm');
            myForm.ptc_token.value = data.response.token.token;
            myForm.submit();
        };
        
        var errorCallback = function(data) {
            //console.log(data);
            //for(x in data){
            //<span class="ptc-error-code">'+data.errorCode+': </span>
            $('.ptc-append-error').html('<span class="ptc-error-msg">'+data.errorMsg+'</span>')
           // }
            //if (data.errorCode === 200){
//                
//            }else{
//                alert(data.errorMsg);  
//            } 
        };
        
        var tokenRequest = function() {
            var args = {
                sellerId: ptc.seller_id,
                publishableKey: ptc.publishable_key,
                ccNo: $("#ptc_ccNo").val(),
                cvv: $("#ptc_cvv").val(),
                expMonth: $("#ptc_expMonth").val(),
                expYear: $("#ptc_expYear").val()
            };
            TCO.requestToken(successCallback, errorCallback, args);
        };
        
        TCO.loadPubKey('sandbox');
        $(".ptc_submit_button").click( function(e) {
            e.preventDefault();
            tokenRequest();
            return false;
        });
        
        $('#same_as_billing').click(function(){
            if($(this).is(':checked')){
                $('.ptc-shipping-items').hide();
                //$('.ptc-shipping-items :input').prop('disabled', true);
            }else{
                $('.ptc-shipping-items').show();
                //$('.ptc-shipping-items :input').prop('disabled', false);
            }
        });
        
        if($('#same-as-billing').is(':checked')){
            $('.ptc-shipping-items').hide();
            //$('.ptc-shipping-items :input').prop('disabled', true);
        }else{
            $('.ptc-shipping-items').show();
            //$('.ptc-shipping-items :input').prop('disabled', false);
        }
        
        $('.ptc-next-button').click(function(){
            var selector = $(this);
            var values = {};
            var show_div = selector.data('show');
            var formData =  selector.parent().find(":input").serialize();
             $('.validation_message:not(.ptc-append-error)').remove();
            $.ajax({
                method: "POST",
    			url: ptc.ajaxurl,
                dataType: "JSON",
    			data: {"form_data":formData,action:"ptc_form_validation"},
    			success: function(response){
    				//console.log(response);/*
                    if(response.status=='success'){
                        selector.parent().fadeOut(function(){
                             $(show_div).fadeIn();
                        });
                        $.each(selector.parent().find(":input").serializeArray(), function (i, field) {
                            values[field.name] = field.value;
                        });
                    
                        var getValue = function (valueName) {
                            return values[valueName];
                        };
                        if(getValue('billing_details[billing_name]') != undefined){
                            //$('#ptc-paypal-form input[name="li_0_name"]').val(getValue('billing_details[billing_name]'));
                            $('#ptc-paypal-form input[name="card_holder_name"]').val(getValue('billing_details[billing_name]'));
                            $('#ptc-paypal-form input[name="street_address"]').val(getValue('billing_details[billing_address]'));
                            $('#ptc-paypal-form input[name="city"]').val(getValue('billing_details[billing_city]'));
                            $('#ptc-paypal-form input[name="state"]').val(getValue('billing_details[billing_state_province]'));
                            $('#ptc-paypal-form input[name="zip"]').val(getValue('billing_details[billing_postalcode]'));
                            $('#ptc-paypal-form input[name="country"]').val(getValue('billing_details[billing_country]'));
                            $('#ptc-paypal-form input[name="email"]').val(getValue('billing_details[billing_email]'));
                        }
                        //if($('inpt[name="shipping_details[same_as_billing]"]'))
                        if(getValue('shipping_details[same_as_billing]')==1){
                            $('.ptc-shipping-paypal').prop('disabled', true);
                            $('#ptc-paypal-form').submit();
                        }else if(getValue('shipping_details[shipping_name]') != undefined){
                            $('.ptc-shipping-paypal').prop('disabled', false);
                            $('#ptc-paypal-form input[name="ship_name"]').val(getValue('shipping_details[shipping_name]'));
                            $('#ptc-paypal-form input[name="ship_street_address"]').val(getValue('shipping_details[shipping_address]'));
                            $('#ptc-paypal-form input[name="ship_city"]').val(getValue('shipping_details[shipping_city]'));
                            $('#ptc-paypal-form input[name="ship_state"]').val(getValue('shipping_details[shipping_state_province]'));
                            $('#ptc-paypal-form input[name="ship_zip"]').val(getValue('shipping_details[shipping_postalcode]'));
                            $('#ptc-paypal-form input[name="ship_country"]').val(getValue('shipping_details[shipping_country]'));
                            $('#ptc-paypal-form').submit();
                        }
                    }else{
                        var errors = response.errors;
                        for (var x in errors) {
                            //$('#'+x).closest('div.row').find('.validation_message').html(errors[x]);
                            $('#'+x).parent().append('<div class="validation_message">'+errors[x]+'</div>');
                            $('#'+x).addClass('ptc-error');
                        }
                    }
                    /**/
    			} 
            });
           
            
            var $id = selector.parent().attr('id');
            /**
 * var data = $('#ptcCCForm').serializeArray().reduce(function(obj, item) {
 *                 obj[item.name] = item.value;
 *                 return obj;
 *             }, {});
 */
    //        var formData = $('#ptcCCForm').serializeArray();
     //        var formData =  $('#'+$id+' :input').serializeArray();
              
             
        });
        
        $('.ptc-prev-button').click(function(){
            var selector = $(this);
            var show_div = selector.data('show');
            selector.parent().fadeOut(function(){
                $(show_div).fadeIn();
            });
        });
        
    });//document.ready close
}(jQuery));