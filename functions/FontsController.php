<?php 
require_once '../database/Database.php';
require_once 'FontsController.php';

$db = new Database();
$fontController = new FontsController($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['font'])) {        
        echo $fontController->uploadFont($_FILES['font']);
    } elseif (isset($_POST['deleteFontId'])) {        
        echo $fontController->deleteFont($_POST['deleteFontId']);
    }
}elseif (isset($_GET['action']) && $_GET['action'] === 'displayFonts') {
    echo $fontController->displayFonts();
}

class FontsController{

    private $fontDir;
    private $db; 

    public function __construct($db)
    {
        $this->db = $db;
        $this->fontDir = dirname(__DIR__) . '/fonts/';
    }

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
        echo $fontName . ' ' . $fontPath;
        $sql = "INSERT INTO fonts (font_name, font_path, status) VALUES (:font_name, :font_path, 1)";
        $this->db->query($sql, [
            'font_name' => $fontName, 
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
}

?>