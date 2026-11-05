<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
$modules=require __DIR__.'/schema.php';
$page=(string)($_GET['p']??'dashboard');
if($page==='login'){require __DIR__.'/login.php';exit;}
if($page==='logout'){
 if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Método não permitido');}
 valid_csrf();$_SESSION=[];session_regenerate_id(true);go('login');
}
require_auth();
if($page==='save'||$page==='delete'){require __DIR__.'/actions.php';exit;}
if($page==='export'){require __DIR__.'/export.php';exit;}
require __DIR__.'/views.php';
