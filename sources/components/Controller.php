<?php
namespace Engine\Components;

/**
 * Basic controllers class.
 */
abstract class Controller
{
    
    private $_layout = 'main';
    private $_layoutsPath = 'layouts';
    private $_viewsPath = 'views';
    private $_initContent = '';
    
    /**
     * Basic of index action
     */
    abstract public function actionIndex();
    
    /**
     * Class constructor
     * Lets to init controller with some data (e.g. errors handling)
     * @param mixed $initContent parameters to init the controller
     */
    public function __construct($initContent = '') {
        Application::$app->setController($this);
        $this->_layoutsPath = Application::$app->viewSettings()['layoutsPath'] ?? $this->_layoutsPath;
        $this->_viewsPath = Application::$app->viewSettings()['viewsPath'] ?? $this->_viewsPath;
        $this->_layout = Application::$app->viewSettings()['baseLayout'] ?? $this->_layout;
        $this->_initContent = $initContent;
    }
    
    /**
     * Error page action
     * @return void
     */
    public function actionError() {
        $this->renderPartial('error.php', array('error' => $this->getInitContent()));
    }
    
    /**
     * Render layout with some view file
     * @param string $view view name, e.g. view.php
     * @param array $content array of data that would be extracted in template
     * @return void
     */
    public function render($view, $content = []) {
        $layout = $this->_layoutsPath . '/' . $this->_layout;
        $view = $this->_viewsPath . '/' . $view;
        ob_start();
        $this->_getContent(
                $view,
                $content
        );
        $viewContent = ob_get_contents();
        ob_clean();
        $this->_getContent(
            $layout,
            array('content' => $viewContent, 'application' => Application::$app)
        );
        $layoutContent = ob_get_contents();
        ob_end_clean();
        echo $layoutContent;
    }
    
    /**
     * Render view without layout
     * @param string $view view name, e.g. view.php
     * @param array $content array of data that would be extracted in template
     * @throws \Exception if view not found
     */
    public function renderPartial($view, $content = []) {
        $view = $this->_viewsPath . '/' . $view;
        if (!file_exists($view))
            throw new \Exception ('View file not found');
        echo $this->_getContent(
                $view,
                $content
        );
    }
    
    /**
     * Return configuration given on startup
     * @return mixed init data
     */
    public function getInitContent() {
        return $this->_initContent;
    }
    
    /**
     * Helps to render views and extract data
     * @param string $view view name, e.g. view.php
     * @param array $content array of data that would be extracted in template
     * @throws \Exception if view not found
     */
    private function _getContent($viewPath, $content = []) {
        if (!file_exists($viewPath))
            throw new \Exception ('View file not found');
        if (!empty($content))
            extract($content, EXTR_OVERWRITE);
        include($viewPath);
    }

}