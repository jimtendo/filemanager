<?php namespace Jimtendo\FileManager;

/**
* A terribly useful class
*
* See: http://knpuniversity.com/screencast/question-answer-day
*/
class FileManager
{
    /**
    * Configuration settings
    *
    * Valid array options are:
    * ['disk'] = Disk to use (as defined in config/filesystems.php
    * ['rootDir'] = Root path for file manager
    */
    protected $config;
    
    /**
    * Action to perform
    */
    protected $action;
    
    function __construct($fmid, $config = array())
    {
        // Get configuration
        $this->config = config('filemanager');
    
        // Merge defaults with user specified settings
        $this->config = array_merge($this->config, $config);
        
        // Set the name of this JazzyCRUD instance
        $this->config['fmid'] = $fmid;
        
        // Convert this to a javascript-safe variable name
        $this->config['jsVarName'] = str_replace('-', '_', $this->config['fmid']);
    }
    
    public function getFMID()
    {
        return $this->config['fmid'];
    }
    
    public function getRootDir()
    {
        return $this->config['rootDir'];
    }
    
    public function getCurrDir()
    {
        return \Input::get('_directory');
    }
    
    public function getFullDirPath()
    {
        return $this->getRootDir() . $this->getCurrDir();
    }
    
    public function getJsVarName()
    {
        return $this->config['jsVarName'];
    }
    
    public function setRootPath($rootPath)
    {
        $this->config['rootPath'] = $rootPath;
    }
    
    public function onRender($function)
    {
        // If it's directed to this file manager widget
        if (\Input::get('_fmid') == $this->config['fmid']) {
            $function();
            $this->render();
        }
    }
    
    public function container()
    {
        return '<div id="' . $this->config['fmid'] . '" data-ajax="' . \URL::current() . '?_fmid=' . $this->config['fmid'] . '&_action=list"></div>' .
               '<script>' .
               '     var ' . $this->config['jsVarName'] . ' = $("#' . $this->config['fmid'] . '").ajaxBox();' .
               '</script>';
    }
    
    /**
    * Render the CRUD code
    *
    * Returns the final CRUD code for output to the screen
    *
    * @param  string  $action
    * @return string
    */
    public function render()
    {
        // Clean up rootDir directory by adding ending slash
        if (strlen($this->config['rootDir']) && substr($this->config['rootDir'], -1) != '/') {
            $this->config['rootDir'] .= '/';
        }
        
        // Get the current directory
        $this->currDir = \Input::get('_directory');
    
        // Get the action to perform
        $this->action = \Input::get('_action');
        
        // Switch statement to handle correct function
        switch ($this->action) {
            case 'list':
                return $this->performList();
        
            case 'download':
                return $this->performDownload();
        
            case 'upload':
                return $this->performUpload();
                
            case 'delete':
                return $this->performDelete();
                
            case 'rename':
                return $this->performRename();
            
            case 'directory-create':
                return $this->performDirectoryCreate();
                
            case 'directory-delete':
                return $this->performDirectoryDelete();
        }
        
        
    }
    
    private function performList()
    {
        // Get directories and files
        $directories = [];
        $directoriesDirty = \Storage::disk($this->config['disk'])->directories($this->getFullDirPath());
        foreach ($directoriesDirty as $directory) {
        
            // Directory name
            $dirName = basename($directory);
        
            // Determine path (make relative - remove root path from url)
            $dir = substr($directory, strlen($this->config['rootDir']));
        
            $directories[$dirName] = [ 'dir'=>$dir,
                                       'size'=>\Storage::disk($this->config['disk'])->size($directory),
                                       'date'=>\Storage::disk($this->config['disk'])->lastModified($directory) ];
        }
        
        $files = [];
        $filesDirty = \Storage::disk($this->config['disk'])->files($this->getFullDirPath());
        foreach ($filesDirty as $file) {
        
            // Get filename
            $filename = basename($file);
            
            // Determine path (make relative - remove root path from url)
            $dir = dirname(substr($file, strlen($this->config['rootDir'])));
        
            $files[$filename] = [ 'dir'=>$dir,
                                  'size'=>$this->formatBytes(\Storage::disk($this->config['disk'])->size($file), 0),
                                  'date'=>\Storage::disk($this->config['disk'])->lastModified($file) ];
        }
        
        return $this->outputView('filemanager::list', ['fm'=>$this, 'directories'=>$directories, 'files'=>$files]);
    }
    
    private function performDownload()
    {
        $file = \Storage::disk($this->config['disk'])->get($this->getFullDirPath() . '/' . \Input::get('_file'));
        
        $quoted = sprintf('"%s"', addcslashes(\Input::get('_file'), '"\\'));

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $quoted); 
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . count($file));
        
        echo $file;
                                
        exit();
    }
    
    private function performUpload()
    {
        $files = \Input::file('files');
        
        foreach ($files as $file) {
        
            if ($file->isValid()) {
                $filename = $file->getClientOriginalName();
                $file->move(storage_path(), $filename);
                
                $contents = file_get_contents(storage_path() . '/' . $filename);
                \Storage::disk($this->config['disk'])->put($this->getFullDirPath() . '/' . $filename, $contents);
            }
        }
        
        return $this->redirectToAction('list');
    }
    
    private function performDelete()
    {
        \Storage::disk($this->config['disk'])->delete($this->getFullDirPath() . '/'. \Input::get('_file'));
        
        return $this->redirectToAction('list');
    }
    
    private function performRename()
    {
        $from = \Input::get('_from');
        $to = \Input::get('_to');
        \Storage::disk($this->config['disk'])->move($this->getFullDirPath() . '/' . $from, $this->getFullDirPath() . '/' . $to);
        
        return $this->redirectToAction('list');
    }
    
    private function performDirectoryCreate()
    {
        $name = \Input::get('_name');
        \Storage::disk($this->config['disk'])->makeDirectory($this->getFullDirPath() . '/' . $name);
        
        return $this->redirectToAction('list');
    }
    
    private function performDirectoryDelete()
    {
        $name = \Input::get('_name');
        \Storage::disk($this->config['disk'])->deleteDirectory($this->getFullDirPath() . '/' . $name);
        
        return $this->redirectToAction('list');
    }
    
    private function outputView($view, $data)
    {
        exit(view($view, $data)->render());
    }
    
    private function redirectToAction($action)
    {
        die(header('Location: ' . \URL::current() . '?_fmid=' . $this->config['fmid'] . '&_action=' . $action . '&_directory=' . $this->getCurrDir()));
    }
    
    private function formatBytes($size, $precision = 2)
    {
        $base = log($size, 1024);
        $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');   

        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
    }
    
    public function actionUrl($action, $directory, $extra = [])
    {
        $url = \URL::current() . '?_fmid=' . $this->config['fmid'] . '&_action=' . $action . '&_directory=' . $directory;
        
        foreach ($extra as $key=>$value) {
            $url .= '&' . $key . '=' . $value;
        }
        
        return $url;
    }
}
