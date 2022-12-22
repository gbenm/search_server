<?php

use App\Search\Infrastructure\SearchRouter;
use Slim\App;

return function (App $app) {
  SearchRouter::setup($app);
};
