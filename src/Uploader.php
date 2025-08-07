<?php

namespace Sorfabiosantos\Uploader;
/*
 * Class Uploader
 */
class Uploader {
    private $message;
    // Constantes para controle de tamanho de imagens (em bytes)
    const IMAGE_MAX_SIZE = 5 * 1024 * 1024; // 5MB
    const IMAGE_MIN_SIZE = 10 * 1024; // 10KB

    const IMAGE_DIR = __DIR__ . '/../../storage/images';
    const ALLOWED_IMAGE_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif'
    ];

    const FILE_MAX_SIZE = 5 * 1024 * 1024; // 5MB
    //const FILE_MIN_SIZE = 10 * 1024; // 10KB

    const FILE_DIR = __DIR__ . '/../../storage/files';
    const ALLOWED_FILE_TYPES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain'
    ];

    public function __construct()
    {
        $this->createDirectory(self::IMAGE_DIR);
        $this->createDirectory(self::FILE_DIR);
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
        if ($file['size'] > self::IMAGE_MAX_SIZE || $file['size'] < self::IMAGE_MIN_SIZE) {
            $this->message = "Tamanho inválido para a imagem. Deve ser entre 10KB e 5MB.";
            return false;
        }
        // Verifica tipo
        if (!in_array($file['type'], self::ALLOWED_IMAGE_TYPES)) {
            $this->message = "Tipo de imagem inválido. Apenas JPG, PNG e GIF são permitidos.";
            return false;
        }

        // extrair a extensão do arquivo
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

        $target = self::IMAGE_DIR . '/' . md5(uniqid(rand())) . "." . $extension;
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
        if ($file['size'] > self::FILE_MAX_SIZE) {
            $this->message = "Tamanho inválido para o arquivo. Deve ser até 5MB.";
            return false;
        }
        // Verifica tipo
        if (!in_array($file['type'], self::ALLOWED_FILE_TYPES)) {
            $this->message = "Tipo de arquivo inválido. Apenas PDF, DOC, DOCX, XLS, XLSX e TXT são permitidos.";
            return false;
        }
        // Move arquivo
        $target = self::FILE_DIR . '/' . basename($file['name']);
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            $this->message = "Erro ao mover o arquivo para o diretório de destino.";
            return false;
        }
        return $target;
    }
}
