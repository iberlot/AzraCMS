$(function() {
	$(".publicar").click(function() {
		var $button = $('form');
		var contenido = $button.parent().find(".area").html();
		contenido.replace(/<div>/gi,'<br>').replace(/<\/div>/gi,'');
		var titulo = $button.parent().find("input[name=titulo]").val();
		var autor = $button.parent().find("input[name=autor]").val();
		$.ajax({
			type: "POST",
			url:"text/actualizar_articulo.php",
			data: "titulo="+titulo+"&contenido="+contenido+"&autor="+autor,
			success: function(data){
				window.location = "articulos.php?id="+parseInt(data);
			}
		});
	});
});

$(function() {
	$(".actualizar").click(function() {
		var $button = $('form');
		var contenido = $button.parent().find(".area").html();
		contenido.replace(/<div>/gi,'<br>').replace(/<\/div>/gi,'');
		contenido=contenido.replace(/<div>/g,"<br>").replace(/<\/div>/g," ");
		var titulo = $button.parent().find("input[name=titulo]").val();
		var autor = $button.parent().find("input[name=autor]").val();
		var post = $button.parent().find("input[name=post]").val();
		$.ajax({ 
			type: "POST",
			url:"text/actualizar_articulo.php",
			data: "titulo="+titulo+"&contenido="+contenido+"&autor="+autor+"&post="+post,
			success: function(data){}
		});
	});
});
