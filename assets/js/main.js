(function($){

	var ds = {

		open: function(e){
			ds.top = $(document).scrollTop();
			$('#gist-' + $(this).data('id')).addClass('active');
			$('#gistcode').addClass('active');
			$('#gists,h2').addClass('inactive');
			$('body,html').animate(
				{scrollTop: 0},
				{duration: 500}
			);
		},

		close: function(){
			$(this).removeClass('active');
			$('#gistcode').removeClass('active');
			$('#gists,h2').removeClass('inactive');
			$('body,html').animate(
				{scrollTop: ds.top},
				{duration: 500}
			);

			return false;
		},

		onTransitionEnd: function(){
			if($(this).hasClass('active')){
				$('#gistclose').addClass('active');
			}
			else {
				$('#gistcode > code').removeClass('active');
			}
		},

		listen: function(){
			$('li[data-id]').on({
				'click.Gist': ds.open
			});
			$('#gistcode').on({
				'webkitTransitionEnd.Gist transitionend.Gist': ds.onTransitionEnd
			});
			$('#gistclose').on({
				'click.Gist': ds.close
			});

			return false;
		},

		init: function(){
			$('h1').toggleClass('small',$('#gistcode').length > 0);
			ds.listen();
			ds.top = 0;

			return false;
		}

	};

	$.Gist = function(method) {
		if (ds[method]){
			return ds[method].apply(this,Array.prototype.slice.call(arguments,1));
		}
		else if (typeof method === 'object' || ! method){
			return ds.init.apply(this,arguments);
		}
		else {
			$.error('Method ' +  method + ' does not exist on jQuery.Gist');
		}
	};

	$(document).ready(function(){
		$.Gist();
	});

})(jQuery);