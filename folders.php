<?php
header("Access-Control-Allow-Origin: http://34.203.214.162");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$db_host = "34.238.176.97"; // Replace with actual private IP
$db_user = "api_user";
$db_pass = "api_password";
$db_name = "spotify_folders";

try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                $id = $conn->real_escape_string($_GET['id']);
                $sql = "SELECT * FROM folders WHERE id = $id";
                $result = $conn->query($sql);
                echo json_encode($result->fetch_assoc());
            } else {
                $sql = "SELECT * FROM folders";
                $result = $conn->query($sql);
                $folders = array();
                while ($row = $result->fetch_assoc()) {
                    $folders[] = $row;
                }
                echo json_encode($folders);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $name = $conn->real_escape_string($data['name']);
            $image_url = $conn->real_escape_string($data['image_url']);
            $description = $conn->real_escape_string($data['description']);
            
            $sql = "INSERT INTO folders (name, image_url, description) VALUES ('$name', '$image_url', '$description')";
            if ($conn->query($sql)) {
                echo json_encode(['id' => $conn->insert_id]);
            } else {
                throw new Exception("Insert failed: " . $conn->error);
            }
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $conn->real_escape_string($data['id']);
            $name = $conn->real_escape_string($data['name']);
            $image_url = $conn->real_escape_string($data['image_url']);
            $description = $conn->real_escape_string($data['description']);
            
            $sql = "UPDATE folders SET name='$name', image_url='$image_url', description='$description' WHERE id=$id";
            if ($conn->query($sql)) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception("Update failed: " . $conn->error);
            }
            break;
            
        case 'DELETE':
            $id = $conn->real_escape_string($_GET['id']);
            $sql = "DELETE FROM folders WHERE id=$id";
            if ($conn->query($sql)) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception("Delete failed: " . $conn->error);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
    
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
