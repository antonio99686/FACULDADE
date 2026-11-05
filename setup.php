<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
$error='';$installed=false;
try {
 db()->exec("CREATE TABLE IF NOT EXISTS sistema_usuario (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 nome VARCHAR(150) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 senha_hash VARCHAR(255) NOT NULL,
 criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
 $installed=(int)db()->query('SELECT COUNT(*) FROM sistema_usuario')->fetchColumn()>0;
} catch(Throwable $e){$error='Erro de conexão ou de permissão no banco: '.$e->getMessage();}
if ($installed) {header('Location: '.url('login'));exit;}
if ($_SERVER['REQUEST_METHOD']==='POST' && !$error) {
 valid_csrf();
 $name=trim((string)($_POST['nome']??''));$email=trim((string)($_POST['email']??''));$password=(string)($_POST['senha']??'');
 if(mb_strlen($name)<3 || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($password)<10) $error='Informe nome, e-mail válido e senha com pelo menos 10 caracteres.';
 else {try {
  $s=db()->prepare('INSERT INTO sistema_usuario (nome,email,senha_hash) VALUES (?,?,?)');
  $s->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]);
  flash('success','Administrador criado. Entre com suas credenciais.');header('Location: '.url('login'));exit;
 } catch(Throwable $e){$error=compact_error($e);}}
}
?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Instalação | Faculdade</title><link rel="stylesheet" href="assets/style.css"></head><body class="auth-bg"><main class="auth-card"><div class="auth-mark">🎓</div><h1>Configuração inicial</h1><p>Crie o primeiro administrador do sistema acadêmico.</p><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?><form method="post"><input type="hidden" name="_csrf" value="<?=e(csrf())?>"><label>Nome<input name="nome" required minlength="3" autocomplete="name"></label><label>E-mail<input type="email" name="email" required autocomplete="email"></label><label>Senha (mínimo 10 caracteres)<input type="password" name="senha" minlength="10" required autocomplete="new-password"></label><button class="btn primary full">Criar administrador</button></form><p class="hint">Antes disso, importe o arquivo SQL da atividade no phpMyAdmin e revise config.php.</p></main></body></html>
