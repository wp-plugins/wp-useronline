jQuery(document).ready(function($){
	var timeout = parseInt(useronlineL10n.timeout);

	var get_data = function(mode) {
		var data = {
			'action': 'useronline',
			'mode': mode
		};

		$.post(useronlineL10n.ajax_url, data, function(response){
			$('#useronline-' + mode).html(response);
		});
	}

	if ( $('#useronline-count').length )
		setInterval("get_data('count')", timeout);

	if ( $('#useronline-browsing-site').length )
		setInterval("get_data('browsing-site')", timeout);

	if ( $('#useronline-browsing-page').length )
		setInterval("get_data('browsing-page')", timeout);
});
