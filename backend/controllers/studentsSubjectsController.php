<?php
require_once("./models/studentsSubjects.php");
require_once("./utils/requestParser.php");

function handleGet($conn) 
{
    $result = getAllSubjectsStudents($conn);
    $data = [];
    while ($row = $result->fetch_assoc()) 
    {
        $data[] = $row;
    }
    echo json_encode($data);
}

function handlePost($conn) 
{
    $input = parseRequest();

    if (assignSubjectToStudent($conn, $input['student_id'], $input['subject_id'], $input['approved'])) 
    {
        echo json_encode(["message" => "Asignación realizada"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "Error al asignar"]);
    }
}

function handlePut($conn) 
{
    $input = parseRequest();

    if (!isset($input['id'], $input['student_id'], $input['subject_id'], $input['approved'])) 
    {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos"]);
        return;
    }

    if (updateStudentSubject($conn, $input['id'], $input['student_id'], $input['subject_id'], $input['approved'])) 
    {
        echo json_encode(["message" => "Actualización correcta"]);
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

    if (removeStudentSubject($conn, $input['id'])) 
    {
        echo json_encode(["message" => "Relación eliminada"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>
