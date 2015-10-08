<?php
require './lib/core/Init.class.php';
\lib\core\Init::run();

\lib\core\Router::run('Oauth', \lib\core\Input::get("method", "qqlogin"));