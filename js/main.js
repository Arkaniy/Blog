$(document).ready(function() {
	$(".slide-down").click(function() {
		var slide = $(this).next();
		var text = $(this).children("p");
		if(slide.css('display') == 'none') {
			slide.slideDown(200);
			text.html('Hide');
		}
		else {
			slide.slideUp(200);
			text.html('New post');
		}
	});

	$('#subscribe').click(function() {
		$.post('', 
			{
				sub: 1,
			},
			function(data) {
				location.reload();
			}
		);
	});

	$('#unsubscribe').click(function() {
		$.post('', 
			{
				sub: 0,
			},
			function(data) {
				location.reload();
			}
		);
	});

	$('.welcome').click(function() {
		$(this).prev().show();
		$(this).prev().focus();
	});

	$('.new-welcome').keypress(function(e) {
		if(e.which == 13) {
			$.post('scripts.php', 
				{
					newwelcome: $(this).val()
				},
				function(data) {
					location.reload();
				}
			);
		}
	});

	$('.new-welcome').blur(function() {
		
		$(this).hide();
	});

	$('#search').click(function() {
		console.log(1);
		if($(this).prev().val() != '') {
			$.post('scripts.php', 
				{
					search: $(this).prev().val()
				},
				function(data) {
					if(data == '') {
						data = "<p>Can't find...</p>";
					}
					$('.find').html(data);
				}
			);
		}
	});

	$('.new-comment').click(function() {
		if($(this).prev().val() != '') {
			$.post('scripts.php', 
				{
					newcomment: $(this).prev().val()
				},
				function(data) {
					location.reload();
				}
			);
		}
	});

	$('.delete-comment').click(function() {
		$.post('scripts.php', 
			{
				delete: $(this).parent().attr('id'),
			},
			function(data) {
				location.reload();
			}
		);
	});

	var arr = $('img');
	for(var i = 0; i < arr.length; i++) {
	 	if(/\/$/.test(arr[i]['src'])) {
	 		$(arr[i]).parent().css('min-height', '0');
	 		arr[i].remove();
	 	}
	 }

	$('.content').css('minHeight', $(document).height()-98);
});
