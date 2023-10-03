<?php
$host = "localhost";
$db   = "pruebas";
$user = "root";
$pass = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $cedula = $_POST['cedula'];
    $stmt = $conn->prepare("SELECT usuario.nombre, usuario.apellido, usuario.correo, documentos.nombre_doc FROM usuario INNER JOIN doc_ref ON usuario.id=doc_ref.user_id INNER JOIN documentos ON doc_ref.document_id=documentos.id_doc WHERE usuario.cedula = :cedula");
    $stmt->execute([':cedula' => $cedula]);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>