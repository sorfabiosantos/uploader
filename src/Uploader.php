<?php

namespace SorFabioSantos\Uploader;
/*
 * Class Uploader
 */
class Uploader {
    private $message;
    public function __construct()
    {
        $this->createDirectory(IMAGE_DIR);
        $this->createDirectory(FILE_DIR);
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    private function createDirectory($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    /**
     * Formata o tamanho em bytes para KB ou MB
     */
    private function formatSize($bytes)
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        return number_format($bytes / 1024, 2) . ' KB';
    }

    /**
     * Upload de uma imagem
     */
    public function Image($file): bool|string
    {
        // Verifica tamanho
        if ($file['size'] > IMAGE_MAX_SIZE || $file['size'] < IMAGE_MIN_SIZE) {
            $this->message = str_replace([
                IMAGE_MIN_SIZE, IMAGE_MAX_SIZE
            ], [
                $this->formatSize(IMAGE_MIN_SIZE),
                $this->formatSize(IMAGE_MAX_SIZE)
            ], IMAGE_SIZE_ERROR_MESSAGE);
            return false;
        }
        // Verifica tipo
        if (!in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
            $this->message = IMAGE_TYPE_ERROR_MESSAGE;
            return false;
        }
        // extrair a extensão do arquivo
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $target = IMAGE_DIR . '/' . md5(uniqid(rand())) . "." . $extension;
        // Move  arquivo
        if(!move_uploaded_file($file['tmp_name'], $target)) {
            $this->message = IMAGE_MOVE_ERROR_MESSAGE;
            return false;
        }
        return $target;
    }

    /**
     * Upload de um arquivo
     */
    public function File($file): bool|string
    {
        // Verifica tamanho
        if ($file['size'] > FILE_MAX_SIZE || $file['size'] < FILE_MIN_SIZE) {
            $this->message = str_replace(
                FILE_MAX_SIZE,
                $this->formatSize(FILE_MAX_SIZE),
                FILE_SIZE_ERROR_MESSAGE
            );
            return false;
        }
        // Verifica tipo
        if (!in_array($file['type'], ALLOWED_FILE_TYPES)) {
            $this->message = FILE_TYPE_ERROR_MESSAGE;
            return false;
        }
        // Move arquivo
        $target = FILE_DIR . '/' . basename($file['name']);
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            $this->message = FILE_MOVE_ERROR_MESSAGE;
            return false;
        }
        return $target;
    }
}
