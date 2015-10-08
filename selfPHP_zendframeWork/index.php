<?php
require './lib/core/Init.class.php';
\lib\core\Init::run();
\lib\core\Router::run('Index', \lib\core\Input::get("method", "index"));