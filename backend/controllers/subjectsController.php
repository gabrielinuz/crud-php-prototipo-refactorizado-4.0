<?php
require_once("./models/subjects.php");
require_once("./utils/requestParser.php");

function handleGet($conn) 
{
    $input = parseRequest();
    if (isset($input['id']))
    {
        $result = getSubjectById($conn, $input['id']);
        echo json_encode($result->fetch_assoc());
    }
    else
    {
        $result = getAllSubjects($conn);
        $data = [];
        while ($row = $result->fetch_assoc())
        {
            $data[] = $row;
        }
        echo json_encode($data);
    }
}

function handlePost($conn)
{
    $input = parseRequest();

    file_put_contents("debug.log", print_r([
        'method' => $_SERVER['REQUEST_METHOD'],
        'content_type' => $_SERVER['CONTENT_TYPE'] ?? '',
        'data' => $input
    ], true));

    $name = $input['name'] ?? null;
    $syllabus = $input['syllabus'] ?? null;
    $filePath = null;

    if ($syllabus && isset($syllabus['tmp_name']))
    {
        $uploadDir = './uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $filename = basename($syllabus['name']);
        $targetPath = $uploadDir . uniqid() . "_" . $filename;

        if (move_uploaded_file($syllabus['tmp_name'], $targetPath))
        {
            $filePath = $targetPath;
        }
    }

    if (createSubject($conn, $name, $filePath))
    {
        echo json_encode(["message" => "Materia creada correctamente"]);
    }
    else
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo crear"]);
    }
}

function handlePut($conn)
{
    $input = parseRequest();
    $id = $input['id'] ?? null;
    $name = $input['name'] ?? null;
    $syllabus = $input['syllabus'] ?? null;
    $filePath = null;

    // Si se sube un nuevo archivo, eliminar el anterior
    if ($syllabus && isset($syllabus['tmp_name'])) 
    {
        $old = getSubjectById($conn, $id)->fetch_assoc();
        if ($old && $old['syllabus_path'] && file_exists($old['syllabus_path'])) {
            unlink($old['syllabus_path']);
        }

        $uploadDir = './uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $filename = basename($syllabus['name']);
        $targetPath = $uploadDir . uniqid() . "_" . $filename;

        if (move_uploaded_file($syllabus['tmp_name'], $targetPath))
        {
            $filePath = $targetPath;
        }
    }

    if (updateSubject($conn, $id, $name, $filePath))
    {
        echo json_encode(["message" => "Materia actualizada correctamente"]);
    }
    else
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar"]);
    }
}

function handleDelete($conn)
{
    $input = parseRequest();
    $id = $input['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(["error" => "ID no proporcionado"]);
        return;
    }

    // Recuperar la materia para obtener la ruta del archivo
    $result = getSubjectById($conn, $id);
    $subject = $result->fetch_assoc();

    if (!$subject) {
        http_response_code(404);
        echo json_encode(["error" => "Materia no encontrada"]);
        return;
    }

    // Borrar el archivo si existe
    $filePath = $subject['syllabus_path'] ?? null;
    if ($filePath && file_exists($filePath)) {
        unlink($filePath);
    }

    if (deleteSubject($conn, $id))
    {
        echo json_encode(["message" => "Materia eliminada correctamente"]);
    }
    else
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>
