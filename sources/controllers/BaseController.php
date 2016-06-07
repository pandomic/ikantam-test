<?php
namespace Engine\Controllers;
use Engine\Components\Controller;
use Engine\Components\Application;
use Engine\Exceptions\HttpException;
use Engine\Models\User;
use Engine\Models\File;

/**
 * Main controller class
 * Connects models with views
 */
class BaseController extends Controller
{
    
    /**
     * Starts on download request
     * Finds the file and gives it to download
     * @throws HttpException if file not found
     */
    public function actionDownload() {
        $app = Application::$app;
        $model = new File();
        $model = $model->findFirst(array(
            array(
                'placeholder' => ':id',
                'compare' => '=',
                'value' => $_GET['l'],
                'column' => 'token'
            )
        ));
        
        if (is_null($model->token) || !file_exists($model->location))
            throw new HttpException('File not found', 404);
        
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($model->location));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($model->location));
        
        if ($file = fopen($model->location, 'rb')) {
            while (!feof($file)) {
                print fread($file, 1024);
            }
            fclose($file);
        }
    }
    
    /**
     * Remove file
     * Allows remove only own files
     */
    public function actionRemove() {
        $app = Application::$app;
        if ($app->getAcc()->getAccess()) {
            if (!empty($_GET['id'])) {
                $model = new File();
                $model = $model->findFirst(array(
                        array(
                            'placeholder' => ':id',
                            'compare' => '=',
                            'value' => $_GET['id'],
                            'column' => 'id',
                            'operator' => 'AND'
                        ),
                        array(
                            'placeholder' => ':user',
                            'compare' => '=',
                            'value' => $app->getAcc()->getUserid(),
                            'column' => 'user_id'
                        )
                    ));
                
                if (is_null($model->id)) {
                    $app->getRequest()->setFlash('error', 'You can not remove this file');
                    $app->getRequest()->redirect('/base/index');
                    return;
                }
                
                $model->remove();
                $app->getRequest()->setFlash('info', 'Your file has been successfully removed');
                $app->getRequest()->redirect('/base/index');
                return;
            }
        }
        $app->getRequest()->redirect('/base/index');
    }
    
    /**
     * Log out from the system
     */
    public function actionLogout() {
        $app = Application::$app;
        if ($app->getAcc()->getAccess())
            $app->getAcc()->logout();
        $app->getRequest()->redirect('/base/index');
    }
    
    /**
     * Upload file to server
     * @throws HttpException if CSRF validation fails
     */
    public function actionUpload() {
        $app = Application::$app;
        if ($app->getAcc()->getAccess()) {
            $model = new File($_POST['Upload'] ?? []);
            if ($app->getRequest()->isPostRequest()) {
                if (!$app->getRequest()->checkCSRF())
                    throw new HttpException('It looks like CSRF', 400);
                
                $code = $model->validateFile();
                
                switch ($code) {
                    case File::UPLOAD_TITLE_EMPTY:
                        $app->getRequest()->setFlash('error', 'Empty title given');
                    break;
                    case File::UPLOAD_TITLE_INVALID:
                        $app->getRequest()->setFlash('error', 'Title can not be less than 16 and more than 128 symbols and should start with a letter');
                    break;
                    case File::UPLOAD_FILE_EMPTY:
                        $app->getRequest()->setFlash('error', 'You should select the file to upload');
                    break;
                    case File::UPLOAD_FILE_MULTIPLE:
                        $app->getRequest()->setFlash('error', 'Only one file can be uploaded per once');
                    break;
                    case File::UPLOAD_FILE_INVALID:
                        $app->getRequest()->setFlash('error', 'It is something wrong with the file. It seems like it is too big or unsupported');
                    break;
                    case File::UPLOAD_VALIDATE_OK:
                        $model->uploadFile();
                        $model->user_id = $app->getAcc()->getUserid();
                        $model->uploadtime = time();
                        $model->save();
                        $app->getRequest()->setFlash('info', 'Thank you for your upload!');
                        $app->getRequest()->redirect('/base/index');
                        return;
                    break;
                }
            }
            
            $this->render('upload.php',array(
                'request' => $app->getRequest(),
                'model' => $model
            ));
        } else {
            $app->getRequest()->redirect('/base/index');
        }
    }
    
    /**
     * Login page and files list
     * @throws HttpException if CSRF validation fails
     */
    public function actionIndex() {
        $app = Application::$app;
        if ($app->getAcc()->getAccess()) {
            $model = new File();
            
            $models = $model->findAll(array(
                'params' => 'ORDER BY `id` DESC'
            ));
            
            $this->render('files.php',array(
                'request' => $app->getRequest(),
                'models' => $models,
                'userid' => $app->getAcc()->getUserid()
            ));
        } else {
            $model = new User($_POST['Login'] ?? []);
            
            if ($app->getRequest()->isPostRequest()) {
                if (!$app->getRequest()->checkCSRF())
                    throw new HttpException('It looks like CSRF', 400);
                
                $code = $model->validateLogin();
                
                switch ($code) {
                    case User::ERROR_LOGIN_EMPTY:
                        $app->getRequest()->setFlash('error', 'Email or password is empty');
                    break;
                    case User::ERROR_LOGIN_INVALID:
                        $app->getRequest()->setFlash('error', 'Incorrect email or password');
                    break;
                    case User::LOGIN_VALIDATE_OK:
                        $app->getAcc()->login(
                            $model->email,
                            $model->password
                        );
                        $app->getRequest()->refresh();
                        return;
                    break;
                }
            }
            
            $this->render('base.php',array(
                'request' => $app->getRequest(),
                'model' => $model
            ));
            
        }
    }
    
    /**
     * Base user registration system
     * @throws HttpException
     */
    public function actionSignup() {
        $app = Application::$app;
        $model = new User($_POST['Register'] ?? []);
        
        if ($app->getAcc()->getAccess()) {
            $app->getRequest()->redirect('/base/index');
            return;
        }
        
        if ($app->getRequest()->isPostRequest()) {
            if (!$app->getRequest()->checkCSRF())
                    throw new HttpException('It looks like CSRF', 400);
            
            $code = $model->validateRegister();
            
            switch ($code) {
                case User::REGISTER_EMAIL_EMPTY:
                    $app->getRequest()->setFlash('error', 'Email could not be empty');
                break;
                case User::REGISTER_PASSWORD_EMPTY:
                    $app->getRequest()->setFlash('error', 'Password could not be empty');
                break;
                case User::REGISTER_EMAIL_INVALID:
                    $app->getRequest()->setFlash('error', 'Invalid email given');
                break;
                case User::REGISTER_PASSWORD_NVALID:
                    $app->getRequest()->setFlash('error', 'Password is too short, too long or too weak');
                break;
                case User::REGISTER_REPEAT_INVALID:
                    $app->getRequest()->setFlash('error', 'Password and its repeat should be equal');
                break;
                case User::REGISTER_EMAIL_EXISTS:
                    $app->getRequest()->setFlash('error', 'User with such email already exists');
                break;
                case User::REGISTER_VALIDATE_OK:
                    $model->save();
                    $app->getRequest()->setFlash('info', 'Thank you for registration. Now you can sign in with your email and password');
                    $app->getRequest()->redirect('/base/index');
                    return;
                break;
            }
        }
        
        $this->render('signup.php',array(
            'request' => $app->getRequest(),
            'model' => $model
        ));
    }

}