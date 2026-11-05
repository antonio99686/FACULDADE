<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
// Migração protegida: somente usuário autenticado como administrador pode executar.
if (empty($_SESSION['admin_id'])) { http_response_code(403); exit('Entre como administrador antes de executar upgrade.php.'); }
// O sistema anterior guardava apenas administradores e não utilizava coluna perfil.
$cols=query('SHOW COLUMNS FROM sistema_usuario');
$names=array_column($cols,'Field');
if (!in_array('perfil',$names,true)) db()->exec("ALTER TABLE sistema_usuario ADD COLUMN perfil ENUM('admin','professor','aluno') NOT NULL DEFAULT 'admin'");
if (!in_array('id_aluno',$names,true)) db()->exec('ALTER TABLE sistema_usuario ADD COLUMN id_aluno INT UNSIGNED NULL UNIQUE');
if (!in_array('id_professor',$names,true)) db()->exec('ALTER TABLE sistema_usuario ADD COLUMN id_professor INT UNSIGNED NULL UNIQUE');
if (!in_array('ativo',$names,true)) db()->exec('ALTER TABLE sistema_usuario ADD COLUMN ativo TINYINT(1) NOT NULL DEFAULT 1');
// Evita permitir a migração por sessão antiga com admin_id forjado de outro perfil.
$auth=one('SELECT perfil FROM sistema_usuario WHERE id=?',[(int)$_SESSION['admin_id']]);
if (!$auth || $auth['perfil']!=='admin') { http_response_code(403); exit('Sem permissão.'); }
$_SESSION['user_id']=(int)$_SESSION['admin_id']; $_SESSION['role']='admin'; $_SESSION['link_id']=0;
flash('success','Atualização aplicada. Agora crie os acessos de professores e alunos.');
header('Location: '.url('users')); exit;
