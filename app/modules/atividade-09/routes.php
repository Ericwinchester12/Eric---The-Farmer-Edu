<?php

require_once __DIR__ . '/../../core/router.php';
require_once __DIR__ . '/Controller.php';

Router::get('/atividade-09', [UsuarioController::class, 'show']);
Router::get('/atividade-09/buscarUsuario', [UsuarioController::class, 'showBuscarUsuario']);
Router::post('/atividade-09/buscarUsuario', [UsuarioController::class, 'showBuscarUsuario']);
Router::get('/atividade-09/novoUsuario', [UsuarioController::class, 'showNovoUsuario']);
Router::post('/atividade-09/novoUsuario', [UsuarioController::class, 'showNovoUsuario']);
