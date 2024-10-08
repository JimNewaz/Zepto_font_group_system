<?php 
require_once '../database/Database.php';
// require_once 'FontsController.php';

$db = new Database();
$fontController = new FontsController($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['font'])) {        
        echo $fontController->uploadFont($_FILES['font']);
    } elseif (isset($_POST['deleteFontId'])) {        
        echo $fontController->deleteFont($_POST['deleteFontId']);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'createGroup') {
        echo $fontController->createGroup();
    } elseif(isset($_POST['deleteFontGroup'])){
        echo $fontController->deleteFontGroup($_POST['deleteFontGroup']);
    } elseif (isset($_GET['action']) && $_GET['action'] === 'updateFontGroup') {        
        echo $fontController->updateFontGroup($_POST['groupId'], $_POST['groupName'], $_POST['selectedFonts']);
    }
}elseif (isset($_GET['action']) && $_GET['action'] === 'displayFonts') {
    echo $fontController->displayFonts();
}elseif (isset($_GET['action']) && $_GET['action'] === 'displayAllFonts') {
    echo $fontController->displayAllFonts();
}elseif(isset($_GET['action']) && $_GET['action'] === 'displayAllFontGroups') {
    echo $fontController->displayAllFontGroups();
}elseif(isset($_GET['action']) && $_GET['action'] === 'getAllFonts') {
    echo json_encode($fontController->getAllFonts());
}

class FontsController{

    private $fontDir;
    private $db; 

    public function __construct($db)
    {
        $this->db = $db;
        $this->fontDir = dirname(__DIR__) . '/fonts/';
    }

    /*
        FONTS CRUD Functions Started
    */

    public function uploadFont($fontFile){

        if (!is_dir($this->fontDir)) {
            mkdir($this->fontDir, 0777, true); 
        }

        $fileExtension = pathinfo($fontFile['name'], PATHINFO_EXTENSION);

        if(strtolower($fileExtension) !== 'ttf') {
            return json_encode([   
                'status' => 'false', 
                'message' => 'Only TTF files are allowed.'
            ]);
        }

        $fontName = basename($fontFile['name']);
        $targetPath = $this->fontDir . $fontName;

        if(move_uploaded_file($fontFile['tmp_name'], $targetPath)){
            $fontPath = $targetPath; 
            $this->addFontToDB($fontName, $fontPath);

            return $this->displayFonts();
        }
    }

    private function addFontToDB($fontName, $fontPath){

        $font_name_parts = explode('.', $fontName);
        $font_name = $font_name_parts[0];

        $sql = "INSERT INTO fonts (font_name, font_path, status) VALUES (:font_name, :font_path, 1)";
        $this->db->query($sql, [
            'font_name' => $font_name, 
            'font_path' => $fontPath
        ]);
    }

    public function displayFonts(){
        $sql = "SELECT * FROM fonts WHERE status = 1 ORDER BY id DESC";

        $result = $this->db->query($sql);
        $fonts = $result->fetchAll(PDO::FETCH_ASSOC);

        $html = '';
        $google_fonts = [];

        if (count($fonts) > 0) {
            foreach ($fonts as $font) {

                $font_name_parts = explode('.', $font['font_name']);
                $font_name = $font_name_parts[0];

                $is_google_font = strpos($font_name, 'Google') !== false;
                if ($is_google_font) {
                    $google_fonts[] = str_replace(' ', '+', $font_name);
                    continue; // Skip inline font styling for Google Fonts
                }
                
                $font_file_path = './fonts/' . htmlspecialchars($font['font_name']);

                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($font_name) . '</td>';
                $html .= '<style>
                        @font-face {
                            font-family: "' . htmlspecialchars($font_name) . '";
                            src: url("' . $font_file_path.'.ttf") format("truetype");
                        }
                        </style>';
                $html .= '<td><span style="font-family:\'' . htmlspecialchars($font_name) . '\';">Example Style</span></td>';
                $html .= '<td><button class="btn btn-danger btn-sm delete-font" onclick="deleteFont(' . $font['id'] . ')">Delete</button></td>';
                $html .= '</tr>';
            }

            if (!empty($google_fonts)) {
                $google_font_url = 'https://fonts.googleapis.com/css2?family=' . implode(':', $google_fonts) . '&display=swap';
                echo '<link rel="stylesheet" href="' . $google_font_url . '">';
            }
        } else {
            $html .= '<tr><td colspan="3">No fonts available.</td></tr>';
        }

        return $html;
    }

    public function deleteFont($fontId){
        $sql = "UPDATE fonts SET status = 0 WHERE id = :id";
        $this->db->query($sql, [
            'id' => $fontId
        ]);

        return $this->displayFonts();
    }

    /*
        FONTS CRUD FUNCTIONS STARTED
    */


    /*     
        FONT GROUPS RELATED FUNCTIONS STARTED
    */

    public function displayAllFonts()
    {
        $sql = "SELECT * FROM fonts WHERE status = 1 ORDER BY id DESC";
        $result = $this->db->query($sql);
        $fonts = $result->fetchAll(PDO::FETCH_ASSOC);

        $html = '';
        if (count($fonts) > 0) {
            foreach ($fonts as $font) {
                $html .= '<option value="' . $font['id'] . '">' . $font['font_name'] . '</option>';
            }
        } else {
            $html .= '<option value="">No fonts available.</option>';
        }

        return $html;
    }

    public function createGroup() {
        $groupName = $_POST['groupName'] ?? '';
        $fonts = $_POST['fonts'] ?? [];
    
        $total_fonts = count($fonts);
    
        // Validate data
        if (empty($groupName) || $total_fonts < 2) {
            exit('Invalid input: Ensure you have a valid group name and at least two fonts selected.');
        }
    
        try {    
            // Insert 
            $sql = "INSERT INTO font_groups (name, total_fonts, status) VALUES (:group_name, :total_fonts, 1)";
            $this->db->query($sql, [
                'group_name' => $groupName,
                'total_fonts' => $total_fonts,
            ]);
    
            // last inserted group ID
            $groupId = $this->db->lastInsertId();
    
            // Insert into font_group_map
            foreach ($fonts as $fontId) {
                $sql = "INSERT INTO font_group_fonts (group_id, font_id) VALUES (:group_id, :font_id)";
                $this->db->query($sql, [
                    'group_id' => $groupId,
                    'font_id' => $fontId
                ]);
            }

            echo json_encode(['success' => true, 'groupId' => $groupId, 'groupName' => $groupName]);
            
        } catch (PDOException $e) {
            exit('Error: ' . $e->getMessage());
        }
    }

    public function getAllFonts()
    {
        $sql = "SELECT id, font_name FROM fonts WHERE status = 1";
        $result = $this->db->query($sql);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }


    public function displayAllFontGroups()
    {
        $sql = "SELECT font_groups.id AS group_id, font_groups.name AS group_name, font_groups.total_fonts AS count, 
                GROUP_CONCAT(fonts.font_name SEPARATOR ', ') AS font_names 
                FROM font_groups 
                JOIN font_group_fonts ON font_groups.id = font_group_fonts.group_id
                JOIN fonts ON fonts.id = font_group_fonts.font_id
                WHERE font_groups.status = 1 
                GROUP BY font_groups.id 
                ORDER BY font_groups.id DESC";

        $result = $this->db->query($sql);
        $fonts = $result->fetchAll(PDO::FETCH_ASSOC);

        $html = '';

        if (count($fonts) > 0) {
            foreach ($fonts as $font) {
                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($font['group_name']) . '</td>';
                $html .= '<td>' . htmlspecialchars($font['font_names']) . '</td>';
                $html .= '<td>' . $font['count'] . '</td>';
                $html .= '<td>
                            <button class="btn btn-info btn-sm edit-font-group" 
                                data-group-id="' . $font['group_id'] . '" 
                                data-group-name="' . htmlspecialchars($font['group_name']) . '" 
                                data-font-names="' . htmlspecialchars($font['font_names']) . '">Edit</button>
                            <button class="btn btn-danger btn-sm delete-font-group" onclick="deleteFontGroup(' . $font['group_id'] . ')">Delete</button>
                            </td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr><td colspan="4">No font groups available.</td></tr>';
        }
        // echo $html;
        return $html;
    }


    public function deleteFontGroup($groupId)
    {
        $sql = "UPDATE font_groups SET status = 0 WHERE id = :id";
        $this->db->query($sql, [
            'id' => $groupId
        ]);

        return $this->displayAllFontGroups();
    }

    public function updateFontGroup($groupId, $groupName, $selectedFonts)
    {
        try {

            // var_dump($groupId, $groupName, $selectedFonts);
            
            // Update the group name
            $sql = "UPDATE font_groups SET name = :groupName WHERE id = :groupId";
            $stmt = $this->db->prepare($sql);

            if ($stmt === false) {
                throw new Exception("Error preparing SQL: " . implode(":", $this->db->errorInfo()));
            }

            $stmt->bindParam(':groupName', $groupName);
            $stmt->bindParam(':groupId', $groupId);
            $stmt->execute();

            if (is_string($selectedFonts)) {
                $selectedFonts = json_decode($selectedFonts, true);
            }

            if(count($selectedFonts) < 2) {
                throw new Exception("Please select at least two fonts.");
            }

            if (!empty($selectedFonts)) {                
                $sql = "DELETE FROM font_group_fonts WHERE group_id = :groupId";
                $stmt = $this->db->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Error preparing SQL: " . implode(":", $this->db->con->errorInfo()));
                }
                $stmt->bindParam(':groupId', $groupId);
                $stmt->execute();
    
                // var_dump($selectedFonts);
                // Insert into font_group_map
                foreach ($selectedFonts as $fontId) {
                    $sql = "INSERT INTO font_group_fonts (group_id, font_id) VALUES (:groupId, :fontId)";
                    $stmt = $this->db->prepare($sql);
                    if (!$stmt) {
                        throw new Exception("Error preparing SQL: " . implode(":", $this->db->con->errorInfo()));
                    }
                    $stmt->bindParam(':groupId', $groupId);
                    $stmt->bindParam(':fontId', $fontId);
                    $stmt->execute();
                }
            }
    
            // return "Font group updated successfully";
            return $this->displayAllFontGroups();

        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }



    
}

?>