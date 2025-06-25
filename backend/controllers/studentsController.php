<?php
require_once("./models/students.php");
require_once("./utils/requestParser.php");

function handleGet($conn) 
{
    $input = parseRequest();
    
    if (isset($input['id'])) 
    {
        $result = getStudentById($conn, $input['id']);
        echo json_encode($result->fetch_assoc());
    } 
    else 
    {
        $result = getAllStudents($conn);
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

    if (createStudent($conn, $input['fullname'], $input['email'], $input['age'])) 
    {
        echo json_encode(["message" => "Estudiante agregado correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo agregar"]);
    }
}

function handlePut($conn) 
{
    $input = parseRequest();

    if (updateStudent($conn, $input['id'], $input['fullname'], $input['email'], $input['age'])) 
    {
        echo json_encode(["message" => "Actualizado correctamente"]);
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
    
    if (deleteStudent($conn, $input['id'])) 
    {
        echo json_encode(["message" => "Eliminado correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>