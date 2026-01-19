<?php
class ImageHandler
{
    private static $MAX_FILE_SIZE = 2 * 1024 * 1024;
    private $uploadDir;
    private $allowedMime = ["image/jpeg", "image/png", "image/jpg"];

    public function __construct($uploadDir)
    {
        $this->uploadDir = $uploadDir;
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function validate($fileInput)
    {
        if (!isset($fileInput) || $fileInput["error"] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($fileInput["error"] !== UPLOAD_ERR_OK) {
            return "Errore caricamento codice: " . $fileInput["error"];
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($fileInput["tmp_name"]);
        if (!in_array($mime, $this->allowedMime)) {
            return "Formato non valido (solo JPG/JPEG/PNG)";
        }
        if ($fileInput["size"] > self::$MAX_FILE_SIZE) {
            return "File troppo grande (Max " .
                self::$MAX_FILE_SIZE / 1024 / 1024 .
                "MB)";
        }
        return null;
    }

    public function save($fileInput)
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($fileInput["tmp_name"]);
        $ext = $mime === "image/png" ? "png" : "jpg";

        $fileName = uniqid("img_", true) . "." . $ext;
        $destPath = $this->uploadDir . DIRECTORY_SEPARATOR . $fileName;

        if (move_uploaded_file($fileInput["tmp_name"], $destPath)) {
            return $destPath;
        }
        return false;
    }

    public function delete($oldImagePath)
    {
        if (
            $oldImagePath &&
            file_exists($oldImagePath) &&
            is_file($oldImagePath)
        ) {
            unlink($oldImagePath);
        }
    }
}
?>
