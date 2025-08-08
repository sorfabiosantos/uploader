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
     * Upload de uma imagem
     */
    public function Image($file): bool|string
    {
        // Verifica tamanho
        if ($file['size'] > IMAGE_MAX_SIZE || $file['size'] < IMAGE_MIN_SIZE) {
            $this->message = "Tamanho inválido para a imagem. Deve ser entre 10KB e 5MB.";
            return false;
        }
        // Verifica tipo
        if (!in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
            $this->message = "Tipo de imagem inválido. Apenas JPG, PNG e GIF são permitidos.";
            return false;
        }

        // extrair a extensão do arquivo
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

        $target = IMAGE_DIR . '/' . md5(uniqid(rand())) . "." . $extension;
        // Move  arquivo
        if(!move_uploaded_file($file['tmp_name'], $target)) {
            $this->message = "Erro ao mover o arquivo para o diretório de destino.";
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
        if ($file['size'] > FILE_MAX_SIZE) {
            $this->message = "Tamanho inválido para o arquivo. Deve ser até 5MB.";
            return false;
        }
        // Verifica tipo
        if (!in_array($file['type'], ALLOWED_FILE_TYPES)) {
            $this->message = "Tipo de arquivo inválido. Apenas PDF, DOC, DOCX, XLS, XLSX e TXT são permitidos.";
            return false;
        }
        // Move arquivo
        $target = FILE_DIR . '/' . basename($file['name']);
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            $this->message = "Erro ao mover o arquivo para o diretório de destino.";
            return false;
        }
        return $target;
    }
}
