<!doctype html>
<html>
<head>
	<title>ajaxPHP</title>
	<script src="http://code.jquery.com/jquery-1.9.0rc1.js"></script>
	<script src="http://code.jquery.com/jquery-migrate-1.0.0rc1.js"></script>
	<meta name="name" content="content">	
</head>
<body>
<div id="container">
	<h4>H4</h4>
	<div id="data">
		<ul>
ok
		</ul>
	</div>

</div>
</body>
</html>
<script type="text/javascript" charset="utf-8" async defer>
$(function(){
	console.log($.fn.jquery);

	array = new Array();
	//array = "";

	$('h4').click(function(){
		$ul = $('#data').find('ul');
		$.ajax({
			type: 'get',
			url: 'teste',
			success: function(data){
				//console.log(data.responseText);
				
				array = $.parseJSON('[' + data.responseText + ']');
				//document.console.log(array);
				window.alert(array);
				
				for(var i in array){
					$ul.append('<li>'+array[i]+'</li>');
				}
			},
			error: function(jqxhr){
				//console.log(jqxhr);
			}
		});
	});

});
</script>
