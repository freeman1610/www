<!DOCTYPE html>
<html>
<head>
    <title>Formulario de Cédula</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.4.4/build/qrcode.min.js"></script>
</head>
<body>
<div class="container">
        <h2>Ingrese su cédula</h2>
        <form id="cedulaForm" method="POST">
        <div class="form-group">
            <label for="cedula">Cédula:</label>
            <input type="text" class="form-control" id="cedula" name="cedula" required>
            </div>
            <input type="hidden" id="nombre" name="nombre">
            <input type="hidden" id="apellido" name="apellido">
            <input type="hidden" id="correo" name="correo">
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
        <div id="resultado"></div>
        <button id="generarPDF" class="btn btn-secondary" disabled>Generar PDF</button>
        <!-- Aquí es donde agregas el nuevo div -->
        <div id="pdfLink"></div>
        <div id="qrcode"></div>
    </div>

    <script>
    $('#cedulaForm').on('submit', function(e) {
    e.preventDefault();
    var cedula = $('#cedula').val();
    $.ajax({
        url: 'validar.php',
        type: 'POST',
        dataType: 'json',
        data: {cedula: cedula},
        success: function(data) {
            var html = '<p>Nombre: ' + data[0].nombre + '</p><p>Apellido: ' + data[0].apellido + '</p><p>Correo: ' + data[0].correo + '</p>';
            data.forEach(function(item) {
                html += '<p><input type="radio" id="' + item.nombre_doc + '" name="documento" value="' + item.nombre_doc + '"><label for="' + item.nombre_doc + '"> ' + item.nombre_doc + '</label></p>';
            });
            $('#resultado').html(html);
            $('#generarPDF').prop('disabled', false);
            
            // Almacenar los datos en los campos ocultos
            $('#nombre').val(data[0].nombre);
            $('#apellido').val(data[0].apellido);
            $('#correo').val(data[0].correo);
        }
    });
});

$('#generarPDF').on('click', function() {
    // Obtener el valor del botón de opción seleccionado
    var selected = [$('input[name=documento]:checked').val()];

    // Obtener los datos de los campos ocultos
    var nombre = $('#nombre').val();
    var apellido = $('#apellido').val();
    var correo = $('#correo').val();
	var cedula = $('#cedula').val();  // Obtener el valor de la cédula

	$.ajax({
        url: 'generarPDF.php',
        type: 'POST',
        data: {selected: selected, nombre: nombre, apellido: apellido, correo: correo, cedula: cedula},  // Incluir la cédula
        success: function(token) {
            // Buscar el botón existente
            var link = $('#pdfLink').find('a');

            // Crear un nuevo formulario y botón si no existen
            if (link.length === 0) {
                var form = $('<form>').attr('action', 'mostrarPDF.php').attr('method', 'post').attr('target', '_blank');
                var hiddenField = $('<input>').attr('type', 'hidden').attr('name', 'token');
                form.append(hiddenField);
                
                link = $('<button>').text('Ver PDF').addClass('btn btn-primary');
                form.append(link);
                
                $('#pdfLink').append(form);
            } else {
                var form = link.parent();
            }

            // Actualizar el valor del campo oculto con el token
            form.find('input[type=hidden]').val(token);

            // Agregar una animación al botón
            link.fadeOut(500, function() {
                $(this).fadeIn(500);
            });

            // Generar el código QR
            var href = 'mostrarPDF.php?token=' + token;
            QRCode.toDataURL(href, function(err, url) {
                $('#qrcode').html('<img src="' + url + '">');
            });
        }
    });
});

    </script>
</body>
</html>