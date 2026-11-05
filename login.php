<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
if(!empty($_SESSION['user_id']))go(current_role()==='admin'?'dashboard':'portal');
try{$table=one("SHOW TABLES LIKE 'sistema_usuario'"); if(!$table || count_table('sistema_usuario')===0){header('Location: setup.php');exit;}}catch(Throwable $e){header('Location: setup.php');exit;}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 valid_csrf();
 $email=trim((string)($_POST['email']??''));$password=(string)($_POST['senha']??'');
 $u=one('SELECT * FROM sistema_usuario WHERE email=?',[$email]);
 if($u && password_verify($password,$u['senha_hash'])){
  if(isset($u['ativo']) && !(int)$u['ativo']) {$error='Sua conta está desativada. Contate o administrador.';}
  else { session_regenerate_id(true); $_SESSION['user_id']=(int)$u['id']; $_SESSION['admin_id']=(int)$u['id']; $_SESSION['admin_name']=$u['nome']; $_SESSION['role']=$u['perfil']??'admin'; $_SESSION['link_id']=(int)($u['id_aluno']??$u['id_professor']??0); go($_SESSION['role']==='admin'?'dashboard':'portal'); }
 }
 $error='E-mail ou senha incorretos.';
}
?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Entrar | Faculdade</title><link rel="stylesheet" href="assets/style.css"></head><body class="auth-bg"><main class="auth-card"><div class="auth-mark">🎓</div><h1>Bem-vindo de volta</h1><p>Acesse seu painel de administrador, professor ou aluno.</p><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?><?php if(!empty($_SESSION['flash'])):?><div class="alert success"><?=e($_SESSION['flash']['message'])?></div><?php unset($_SESSION['flash']);endif;?><form method="post"><input type="hidden" name="_csrf" value="<?=e(csrf())?>"><label>E-mail<input type="email" name="email" required autofocus autocomplete="username"></label><label>Senha<input type="password" name="senha" required autocomplete="current-password"></label><button class="btn primary full">Entrar no sistema</button></form><p class="hint">Se for o primeiro acesso, abra <a href="setup.php">configuração inicial</a>.</p></main></body></html>
