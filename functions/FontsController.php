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
        $sql = "SELECT * FROM fonts WHERE status = 1";

        $result = $this->db->query($sql);
        $fonts = $result->fetchAll(PDO::FETCH_ASSOC);

        $html = '';

        foreach($fonts as $font){
            $html .= '<li class="list-group-item d-flex justify-content-between align-items-center">';
            $html .= '<span style="font-family:' . $font['font_name'] . ';" class="font-preview">Example Style (' . $font['font_name'] . ')</span>';
            $html .= '<span>Path: ' . $font['font_path'] . '</span>';
            $html .= '<button class="btn btn-danger btn-sm delete-font" data-id="' . $font['id'] . '">Delete</button>';
            $html .= '</li>';
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