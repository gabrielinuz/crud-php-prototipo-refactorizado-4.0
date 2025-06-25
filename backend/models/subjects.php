<?php
function getAllSubjects($conn) 
{
    $sql = "SELECT * FROM subjects";
    return $conn->query($sql);
}

function getSubjectById($conn, $id) 
{
    $sql = "SELECT * FROM subjects WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result();
}

function createSubject($conn, $name, $syllabusPath = null)
{
    $sql = "INSERT INTO subjects (name, syllabus_path) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $name, $syllabusPath);
    return $stmt->execute();
}

function updateSubject($conn, $id, $name, $syllabusPath = null)
{
    if ($syllabusPath) 
    {
        $sql = "UPDATE subjects SET name = ?, syllabus_path = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $syllabusPath, $id);
    }
    else
    {
        $sql = "UPDATE subjects SET name = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $name, $id);
    }
    return $stmt->execute();
}

function deleteSubject($conn, $id) 
{
    $sql = "DELETE FROM subjects WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
?>
