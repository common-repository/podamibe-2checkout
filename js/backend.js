(function ($) {
    $(function () {
        
        /**
    	 * Scripts for tab view
    	 * Preview the leaved tab if browser is not closed
    	 */
    
    	jQuery('ul.ptc-tabs li').click(function(){
    		var tab_id = jQuery(this).attr('data-tab');
    		sessionStorage.setItem("ptcActiveTab",tab_id);
    		jQuery('ul.ptc-tabs li').removeClass('ptc-current');
    		jQuery('.ptc-tab-content').removeClass('ptc-current');
    
    		jQuery(this).addClass('ptc-current');
    		jQuery("#"+tab_id).addClass('ptc-current');
    	});
    
    	var active_tab = sessionStorage.getItem("ptcActiveTab");
    
    	if(!active_tab){
    		jQuery('ul.ptc-tabs li[data-tab="ptc-tab-1"]').trigger('click');
    	}
    
    	if(active_tab){
    		jQuery('ul.ptc-tabs li[data-tab='+active_tab+']').trigger("click");
    	}
        
        
        if($('#ptc-sandbox-power').is(':checked')){
            $('.ptc-sandbox tbody').show();
        }else{
            $('.ptc-sandbox tbody').hide();
        }
        
        $('#ptc-sandbox-power').click(function(){
          
            if($(this).is(':checked')){
                $('.ptc-sandbox tbody').show();
            }else{
                $('.ptc-sandbox tbody').hide();
            }
          
        });
        
    });//document.ready close
}(jQuery));