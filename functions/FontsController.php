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
    }
}elseif (isset($_GET['action']) && $_GET['action'] === 'displayFonts') {
    echo $fontController->displayFonts();
}elseif (isset($_GET['action']) && $_GET['action'] === 'displayAllFonts') {
    echo $fontController->displayAllFonts();
}elseif(isset($_GET['action']) && $_GET['action'] === 'displayAllFontGroups') {
    echo $fontController->displayAllFontGroups();
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

        if (count($fonts) > 0) {
            foreach ($fonts as $font) {

                $font_name_parts = explode('.', $font['font_name']);
                $font_name = $font_name_parts[0];

                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($font_name) . '</td>';
                $html .= '<td><span style="font-family:' . htmlspecialchars($font_name) . ';">Example Style </span></td>';
                $html .= '<td><button class="btn btn-danger btn-sm delete-font" onclick="deleteFont(' . $font['id'] . ')">Delete</button></td>';
                $html .= '</tr>';
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
            
        } catch (PDOException $e) {
            exit('Error: ' . $e->getMessage());
        }
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
                            <button class="btn btn-info btn-sm edit-font-group" onclick="editFontGroup(' . $font['group_id'] . ')">Edit</button>
                            <button class="btn btn-danger btn-sm delete-font-group" onclick="deleteFontGroup(' . $font['group_id'] . ')">Delete</button>
                        </td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr><td colspan="3">No fonts available.</td></tr>';
        }

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

    
}

?>