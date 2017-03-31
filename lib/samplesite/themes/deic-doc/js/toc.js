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
			event.preventDefault();
			$(this).ekkoLightbox({
				alwaysShowClose: true
			});
		});
});
