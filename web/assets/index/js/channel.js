
	$(function () {
		$('#bb').on('click','li',function(){
			$(this).appendTo('#aa')
		})

		$('#aa').on('click','li',function(){
		
			$(this).appendTo('#bb')
			
		})

	})
	