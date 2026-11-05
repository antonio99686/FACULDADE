<?php
declare(strict_types=1);
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 valid_csrf();
 $action=(string)($_POST['action']??'create');
 try {
  if($action==='toggle') {
   $id=(int)($_POST['id']??0);
   if($id===(int)$_SESSION['user_id'])throw new RuntimeException('Você não pode desativar a própria conta.');
   $s=db()->prepare('UPDATE sistema_usuario SET ativo=1-ativo WHERE id=? AND perfil<>\'admin\'');$s->execute([$id]);
   flash('success','Status da conta atualizado.');
  } elseif($action==='reset') {
   $id=(int)($_POST['id']??0); $pass=(string)($_POST['senha']??'');
   if(strlen($pass)<10)throw new RuntimeException('A nova senha precisa de no mínimo 10 caracteres.');
   $s=db()->prepare('UPDATE sistema_usuario SET senha_hash=? WHERE id=?');$s->execute([password_hash($pass,PASSWORD_DEFAULT),$id]);
   flash('success','Senha alterada.');
  } else {
   $nome=trim((string)($_POST['nome']??''));$email=strtolower(trim((string)($_POST['email']??'')));
   $pass=(string)($_POST['senha']??'');$role=(string)($_POST['perfil']??'');$link=(int)($_POST['vinculo']??0);
   if(mb_strlen($nome)<3 || !filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($pass)<10)throw new RuntimeException('Informe nome, e-mail válido e senha de pelo menos 10 caracteres.');
   if(!in_array($role,['aluno','professor'],true)||$link<1)throw new RuntimeException('Selecione o perfil e o vínculo acadêmico.');
   $table=$role==='aluno'?'aluno':'professor';$key=$role==='aluno'?'id_aluno':'id_professor';
   if(!one("SELECT `$key` FROM `$table` WHERE `$key`=?",[$link]))throw new RuntimeException('Cadastro vinculado não encontrado.');
   db()->prepare("INSERT INTO sistema_usuario (nome,email,senha_hash,perfil,`$key`) VALUES (?,?,?,?,?)")
    ->execute([$nome,$email,password_hash($pass,PASSWORD_DEFAULT),$role,$link]);
   flash('success','Acesso criado.');
  }
  go('users');
 }catch(Throwable $e){$error=compact_error($e);}
}
$users=query('SELECT u.id,u.nome,u.email,u.perfil,u.ativo,a.nome AS aluno_nome,p.nome AS professor_nome FROM sistema_usuario u LEFT JOIN aluno a ON a.id_aluno=u.id_aluno LEFT JOIN professor p ON p.id_professor=u.id_professor ORDER BY u.id DESC');
$alunos=query('SELECT a.id_aluno AS id,a.nome FROM aluno a LEFT JOIN sistema_usuario u ON u.id_aluno=a.id_aluno WHERE u.id IS NULL ORDER BY a.nome');
$professores=query('SELECT p.id_professor AS id,p.nome FROM professor p LEFT JOIN sistema_usuario u ON u.id_professor=p.id_professor WHERE u.id IS NULL ORDER BY p.nome');
?><!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Contas | Faculdade</title><link rel="stylesheet" href="assets/style.css"></head><body><div class="portal-shell"><header class="portal-header"><a class="brand" href="<?=e(url('dashboard'))?>">🎓 FACULDADE</a><strong>Gestão de acessos</strong><a class="btn muted" href="<?=e(url('dashboard'))?>">Voltar ao painel</a></header><main class="portal-content"><h1>Contas de alunos e professores</h1><p>Cadastre o aluno ou professor primeiro, no painel administrativo, e depois vincule a conta de acesso aqui.</p><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?><?php if(!empty($_SESSION['flash'])):?><div class="alert success"><?=e($_SESSION['flash']['message'])?></div><?php unset($_SESSION['flash']);endif;?><section class="panel"><h2>Criar acesso</h2><form method="post" class="portal-form"><input type="hidden" name="_csrf" value="<?=e(csrf())?>"><input type="hidden" name="action" value="create"><label>Perfil<select name="perfil" id="roleSelect" required><option value="">Selecione</option><option value="aluno">Aluno</option><option value="professor">Professor</option></select></label><label>Pessoa cadastrada<select name="vinculo" id="personSelect" required><option value="">Escolha o perfil primeiro</option></select></label><label>Nome de exibição<input name="nome" required minlength="3"></label><label>E-mail de acesso<input type="email" name="email" required></label><label>Senha inicial<input type="password" name="senha" minlength="10" required></label><button class="btn primary">Criar conta</button></form></section><section class="panel"><h2>Usuários existentes</h2><div class="table-scroll"><table><thead><tr><th>Usuário</th><th>Perfil</th><th>Cadastro associado</th><th>Status</th><th>Ações</th></tr></thead><tbody><?php foreach($users as $u):?><tr><td><strong><?=e($u['nome'])?></strong><small style="display:block"><?=e($u['email'])?></small></td><td><?=e($u['perfil'])?></td><td><?=e($u['aluno_nome']??$u['professor_nome']??'—')?></td><td><?= $u['ativo']?'Ativo':'Desativado'?></td><td><div style="display:flex;gap:8px;flex-wrap:wrap"><?php if($u['perfil']!=='admin'):?><form method="post"><input type="hidden" name="_csrf" value="<?=e(csrf())?>"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?=e($u['id'])?>"><button class="btn muted" type="submit"><?= $u['ativo']?'Desativar':'Ativar'?></button></form><?php endif;?><form method="post" class="reset-inline"><input type="hidden" name="_csrf" value="<?=e(csrf())?>"><input type="hidden" name="action" value="reset"><input type="hidden" name="id" value="<?=e($u['id'])?>"><input type="password" name="senha" placeholder="Nova senha (10+ caracteres)" minlength="10" required><button class="btn muted">Alterar senha</button></form></div></td></tr><?php endforeach;?></tbody></table></div></section></main></div><script>
const people={aluno:<?=json_encode($alunos,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)?>,professor:<?=json_encode($professores,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)?>};
const role=document.getElementById('roleSelect'),person=document.getElementById('personSelect');role.addEventListener('change',()=>{person.replaceChildren(new Option('Selecione',''));(people[role.value]||[]).forEach(p=>person.add(new Option(p.nome,p.id)));});
</script></body></html>
