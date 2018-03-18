$(function(){
	$(".classes_list").on("click", "span", function(e){
		var clickedObjectName = $(this).text();

		$.ajax({
			type: "GET",
			url: "/documentation/models",
			data: "model_name=" + clickedObjectName,
			success: function(data){
				$(".main").html(data);
			}
		});

	});
});