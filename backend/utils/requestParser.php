<?php
function parseRequest()
{
    $method = $_SERVER['REQUEST_METHOD'];
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

    $data = [];

    if ($method === 'GET') 
    {
        parse_str(parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY), $data);
    }
    elseif ($method === 'POST' && strpos($contentType, 'multipart/form-data') !== false)
    {
        // PHP ya parseó $_POST y $_FILES, combinamos todo en $data
        $data = $_POST;

        foreach ($_FILES as $key => $file)
        {
            if ($file['error'] === UPLOAD_ERR_OK)
            {
                $data[$key] = $file;
            }
        }
    }
    elseif (strpos($contentType, 'application/json') !== false)
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
    }
    elseif (in_array($method, ['PUT', 'DELETE']))
    {
        // Para PUT y DELETE con multipart/form-data
        if (strpos($contentType, 'multipart/form-data') !== false)
        {
            $boundary = substr($contentType, strpos($contentType, "boundary=") + 9);
            $rawData = file_get_contents('php://input');
            $data = parseMultipartFormData($rawData, $boundary);
        }
        else
        {
            // PUT/DELETE con x-www-form-urlencoded
            parse_str(file_get_contents('php://input'), $data);
        }
    }
    elseif ($method === 'POST')
    {
        // POST con application/x-www-form-urlencoded
        $data = $_POST;
    }

    return $data;
}

function parseMultipartFormData($rawData, $boundary)
{
    $data = [];
    $blocks = preg_split("/-+$boundary/", $rawData);
    array_pop($blocks); // eliminar el final vacío

    foreach ($blocks as $block)
    {
        if (empty(trim($block))) continue;

        if (preg_match('/name="([^"]+)"; filename="([^"]+)"/', $block, $matches))
        {
            // Es un archivo
            $name = $matches[1];
            $filename = $matches[2];
            $tmpFile = tempnam(sys_get_temp_dir(), 'upload_');

            $content = substr($block, strpos($block, "\r\n\r\n") + 4);
            $content = substr($content, 0, strrpos($content, "\r\n"));

            file_put_contents($tmpFile, $content);
            $data[$name] = [
                'name' => $filename,
                'tmp_name' => $tmpFile,
                'error' => 0,
                'size' => strlen($content),
            ];
        }
        elseif (preg_match('/name="([^"]+)"/', $block, $matches))
        {
            // Es un campo de texto
            $name = $matches[1];
            $value = substr($block, strpos($block, "\r\n\r\n") + 4);
            $value = substr($value, 0, strrpos($value, "\r\n"));
            $data[$name] = $value;
        }
    }

    return $data;
}
