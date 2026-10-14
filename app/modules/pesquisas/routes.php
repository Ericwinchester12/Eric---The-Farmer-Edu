<?php

require_once __DIR__ . '/../../core/router.php';
require_once __DIR__ . '/Controller.php';

Router::get('/pesquisas', [PesquisasController::class, 'mostrarResultados']);
Router::get('/search', [PesquisasController::class, 'mostrarResultados']);
