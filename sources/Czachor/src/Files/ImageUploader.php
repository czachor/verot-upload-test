<?php
declare(strict_types=1);

namespace Czachor\Files;

class ImageUploader
{
    /**
     * @var string
     */
    private $file_data;

    /**
     * @var string
     */
    private $new_file_name_body;

    /**
     * @var array
     */
    private $allowed_upload_file_types = ['image/*'];

    /**
     * @var bool
     */
    private $convert_to_jpeg = true;

    /**
     * @var \upload
     */
    private $obj_uploader;

    /**
     * @var string
     */
    private $uploaded_file_name;

    /**
     * Default destination
     * @var string
     */
    private $file_destination = 'upload/';

    /**
     * @var string Default language (for verot's class)
     */
    private $lang = 'en_GB';

    /**
     * @var string
     */
    private $uploaded_file_pathname;

    /**
     * ImageUploader constructor.
     * @param $file_data
     * @param string $new_file_name_body New file name, empty if same as uploaded.
     */
    public function __construct($file_data, string $new_file_name_body = '')
    {
        $this->file_data = $file_data;
        $this->new_file_name_body = $new_file_name_body;
        $this->upload();
    }

    public function createUploaderService(): \upload
    {
        $this->obj_uploader = new \upload($this->file_data, $this->lang);

        return $this->obj_uploader;
    }

    private function isFileDataToUpload(): bool
    {
        if (!empty($this->file_data)) {
            return true;
        }

        return false;
    }

    private function checkNewFileName(): void
    {
        if (empty($this->new_file_name_body)) {
            $this->new_file_name_body = $this->obj_uploader->file_src_name_body;
        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function upload(): bool
    {
        if (!$this->isFileDataToUpload()) {
            return true;
        }

        $this->createUploaderService();

        if ($this->obj_uploader->uploaded) {
            $this->checkNewFileName();
            $this->obj_uploader->file_new_name_body = $this->new_file_name_body;
            $this->obj_uploader->file_overwrite = false;
            $this->obj_uploader->jpeg_quality = 85;
            $this->obj_uploader->allowed = $this->allowed_upload_file_types;

            if ($this->isConvertToJpeg()) {
                $this->obj_uploader->image_convert = 'jpg';
            }

            $this->obj_uploader->process($this->getFileDestination());

            if (!$this->obj_uploader->processed) {
                throw new \Exception($this->obj_uploader->error);
            }

            $this->uploaded_file_name = $this->obj_uploader->file_dst_name;
            $this->uploaded_file_pathname = $this->obj_uploader->file_dst_pathname;
        }

        return true;
    }

    /**
     * @param string $file_destination Empty to restore default
     */
    public function setFileDestination(string $file_destination = ''): void
    {
        $this->file_destination = $file_destination;
    }

    /**
     * @return string
     */
    public function getFileDestination(): string
    {
        return $this->file_destination;
    }

    /**
     * @return bool
     */
    public function isConvertToJpeg(): bool
    {
        return $this->convert_to_jpeg;
    }

    /**
     * @param bool $convert_to_jpeg
     */
    public function setConvertToJpeg(bool $convert_to_jpeg = true): void
    {
        $this->convert_to_jpeg = $convert_to_jpeg;
    }

    /**
     * Uploaded file name
     * @return string
     */
    public function getUploadedFileName(): string
    {
        return $this->uploaded_file_name;
    }

    /**
     * Uploaded file name with path
     * @return string
     */
    public function getUploadedFilePathname(): string
    {
        return $this->uploaded_file_pathname;
    }

    /**
     * Delete uploaded file
     * @return bool
     */
    public function delete(): bool
    {
        if (file_exists($this->getUploadedFilePathname()) && unlink($this->getUploadedFilePathname()) === false) {
            throw new \RuntimeException('Cannot delete image file!');
        }

        return true;
    }

    /**
     * Returns uploader log data.
     * @return string
     */
    public function getLog(): string
    {
        return $this->obj_uploader->log ?? '';
    }
}