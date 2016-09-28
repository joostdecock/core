<?php

/**
 * @file
 * The PHP page that serves all requests to the Freesewing backend.
 *
 * @author Joost De Cock <joost@decock.org>
 * @copyright 2016 Joost De Cock
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License, Version 3
 */

require __DIR__ . '/vendor/autoload.php';

$api = new \Freesewing\ApiHandler($_REQUEST);
$api->handle();
