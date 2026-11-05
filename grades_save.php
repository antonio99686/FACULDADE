<?php
declare(strict_types=1);
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Método inválido');}
valid_csrf();
$id=(int)($_POST['matricula']??0);$prof=current_link_id();
$grade=one('SELECT m.id_matricula FROM matricula_disciplina m JOIN execucao_disciplina x ON x.id_execucao=m.id_execucao WHERE m.id_matricula=? AND x.id_professor=?',[$id,$prof]);
if(!$grade){http_response_code(403);exit('Esta matrícula não pertence a uma turma sua.');}
try {
 $vals=[];
 foreach(['nota1','nota2'] as $col){$v=trim((string)($_POST[$col]??''));if($v===''){$vals[]=null;continue;}if(!is_numeric($v)|| (float)$v<0||(float)$v>10)throw new RuntimeException('Cada nota deve estar entre 0 e 10.');$vals[]=$v;}
 db()->prepare('UPDATE matricula_disciplina SET nota1=?,nota2=? WHERE id_matricula=?')->execute([$vals[0],$vals[1],$id]);
 flash('success','Notas salvas com sucesso.');
}catch(Throwable $e){flash('error',compact_error($e));}
go('portal',['turma'=>(int)($_POST['turma']??0)]);
