<?php
    namespace Application\Controller;
    use Core\Controller;
    use Core\LocationServices;
    use Core\View;
    use Core\FileManager;

    class Pages_Controller extends Controller
    {
        public $geoloc;
        public $view = null;

        public function __construct($controller, $method)
        {
            parent::__construct($controller, $method);
            $this->geoloc = new LocationServices;
            $this->view = new View;
        }

        public function index($params)
        {
            $pages = ["login", "receipt"];

            $path = implode(DS, $params);

            if(\in_array($path, $pages))
            {
                //dnd("here");
                $this->view->setTemplate('');
            }
            else
            {
                $this->view->setTemplate('main_template');
            }
            
            $this->view->setTitle($path);
		    $this->view->pageView($path);
        }
    }
?>