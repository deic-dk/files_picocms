$(function(){

    $('a[href*="#"]').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'')
     && location.hostname == this.hostname) {
            var $target = $(this.hash);
            $target = $target.length && $target || $('[name=' + this.hash.slice(1) +']');
            if ($target.length) {
                var targetOffset = $target.offset().top;
                $('html,body').animate({scrollTop: targetOffset}, 1000);
                return false;
            }
        }
    });
		
		$('a[href*="#top"]').click(function(ev) {
			ev.stopPropagation();
			ev.preventDefault();
			//var aTag = $("#toc-nav");
			var aTag = $("body");
			 $('html,body').animate({scrollTop: aTag.offset().top},'slow');
		});
		
		$(document).on('click', 'img', function(event) {
			if($(event.target).parent().hasClass('ekko-lightbox-item')){
				return false;
			}
			event.preventDefault();
			$(this).ekkoLightbox({
				alwaysShowClose: true
			});
		});
});

// If a folder is accessed with no trailing slash, relative img src'es are not working
$(document).ready(function() {
	if(window.location.pathname.slice(-1)!='/' && $('meta[name=indexinferred]') && $('meta[name=indexinferred]').attr('content')=='yes'){
		window.location = window.location.pathname+'/';
	}
});