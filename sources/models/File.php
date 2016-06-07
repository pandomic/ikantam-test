<?php
namespace Engine\Models;
use Engine\Components\Model;
/**
 * Files management model
 * It allows to validate file uploading forms and process
 * file uploading to server
 */
class File extends Model
{
    /**
     * Status codes
     */
    const UPLOAD_TITLE_EMPTY = 0x0;
    const UPLOAD_TITLE_INVALID = 0x1;
    const UPLOAD_FILE_EMPTY = 0x2;
    const UPLOAD_FILE_MULTIPLE = 0x3;
    const UPLOAD_FILE_INVALID = 0x4;
    const UPLOAD_VALIDATE_OK = 0x5;
    
    /**
     * Table primary key
     * @var string
     */
    protected $primaryKey = 'id';
    /**
     * Table name
     * @var string
     */
    protected $tableName = '{{prefix}}files';
    
    private $_filePath;
    private $_maxFileSize = 10485760;
    private $_allowedMimeTypes = array(
        'application/zip',
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'application/x-zip-compressed',
        'multipart/x-zip',
        'application/zip-compressed'
    );
   
    /**
     * Validates input data on fail upload
     * @return int status code
     */
    public function validateFile() {
        if ($this->title == '')
            return self::UPLOAD_TITLE_EMPTY;
        if (!preg_match('(^[[:alnum:]][[:print:][:alnum:]]{16,128}$)iu', $this->title))
            return self::UPLOAD_TITLE_INVALID;
        if (empty($_FILES['Upload']['name']['file']))
            return self::UPLOAD_FILE_EMPTY;
        if (count($_FILES['Upload']['name']) > 1)
            return self::UPLOAD_FILE_MULTIPLE;
        if (
            $_FILES['Upload']['size']['file'] > $this->_maxFileSize
            || !in_array($_FILES['Upload']['type']['file'], $this->_allowedMimeTypes)
        )
            return self::UPLOAD_FILE_INVALID;
        return self::UPLOAD_VALIDATE_OK;
    }
    
    /**
     * Moves uploaded file from tmp directory to uploads folder
     * Generates token and stores it in current model
     * @return string new file name
     */
    public function uploadFile() {
        $this->token = md5(mt_rand(0,5000));
        $fileParts = explode('.', $_FILES['Upload']['name']['file']);
        $fileName = $this->token . '.' . array_pop($fileParts);
        $this->location = UPLOADS_DIR . '/' . $fileName;
        move_uploaded_file(
            $_FILES['Upload']['tmp_name']['file'],
            $this->location
        );
        return $this->_filePath = $fileName;
    }
    
    /**
     * Saves current model
     * Removes 'file' param from model, as it is not a part of db schema
     */
    public function save() {
        if (!empty($this->params['file']))
            unset($this->params['file']);
        parent::save();
    }
    
    /**
     * Removes current model from database
     */
    public function remove() {
        if (file_exists($this->location))
            unlink($this->location);
        parent::remove();
    }
}
