<?php

use App\Search\Infrastructure\SearchRouter;
use App\Stats\Infrastructure\StatsRouter;
use Slim\App;

return function (App $app) {
    SearchRouter::setup($app);
    StatsRouter::setup($app);
};
