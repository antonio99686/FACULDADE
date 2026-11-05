<?php
declare(strict_types=1);
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Método não permitido');}
valid_csrf();
$table=(string)($_POST['table']??'');
if(!isset($modules[$table])){http_response_code(400);exit('Módulo inválido');}
$meta=$modules[$table];$pk=$meta['pk'];$isComposite=is_array($pk);
$key=[];foreach((array)$pk as $k){if(isset($_POST['_key_'.$k]) && $_POST['_key_'.$k]!=='')$key[$k]=$_POST['_key_'.$k];}
$isEdit=count($key)===count((array)$pk);
try {
 if($page==='delete'){
  if(!$isEdit)throw new RuntimeException('Identificador ausente');
  $where=implode(' AND ',array_map(fn($k)=>"`$k`=?",array_keys($key)));
  $s=db()->prepare("DELETE FROM `$table` WHERE $where");$s->execute(array_values($key));
  flash('success','Registro excluído com sucesso.');go('list',['t'=>$table]);
 }
 $values=[];
 foreach($meta['columns'] as $col){
  if($isEdit && in_array($col,(array)$pk,true)){ $values[$col]=$key[$col]; continue; }
  $inputType=$meta['inputs'][$col]??'text';
  $value=trim((string)($_POST[$col]??''));
  if($inputType==='boolean')$value=isset($_POST[$col]) && $_POST[$col]==='1'?'1':'0';
  if($value==='' && !in_array($col,$meta['required']??[],true)){$values[$col]=null;continue;}
  if($value==='')throw new RuntimeException('Preencha: '.($meta['labels'][$col]??$col));
  if($inputType==='grade' && (!is_numeric($value)|| (float)$value<0||(float)$value>10))throw new RuntimeException('Notas devem estar entre 0 e 10.');
  if(in_array($inputType,['positive','currency'],true)&&(!is_numeric($value)||(float)$value<($inputType==='positive'?1:0)))throw new RuntimeException('Valor inválido: '.$col);
  if($col==='cpf'){$value=preg_replace('/\D/','',$value);if(strlen($value)!==11)throw new RuntimeException('CPF deve ter 11 dígitos.');}
  if(is_array($inputType)&& !array_key_exists($value,$inputType)&&!in_array($value,$inputType,true))throw new RuntimeException('Opção inválida: '.$col);
  if(isset($meta['refs'][$col]) && (!ctype_digit($value)|| (int)$value<=0))throw new RuntimeException('Selecione um relacionamento válido.');
  $values[$col]=$value;
 }
 if($table==='inscricao'){
  if($values['ano_conclusao']!==null && (int)$values['ano_conclusao']<(int)$values['ano_inicio'])throw new RuntimeException('O ano de conclusão não pode ser anterior ao início.');
 }
 if($table==='execucao_disciplina'){
  $v=one('SELECT 1 FROM curso_disciplina WHERE id_curso=? AND id_disciplina=?',[$values['id_curso'],$values['id_disciplina']]);
  if(!$v)throw new RuntimeException('Vincule primeiro a disciplina ao curso na Grade curricular.');
 }
 if($table==='matricula_disciplina'){
  $v=one('SELECT 1 FROM inscricao i JOIN execucao_disciplina ex ON ex.id_curso=i.id_curso WHERE i.id_aluno=? AND ex.id_execucao=? AND i.ativa=1',[$values['id_aluno'],$values['id_execucao']]);
  if(!$v)throw new RuntimeException('O aluno precisa ter inscrição ativa no curso da oferta.');
 }
 if($isEdit){
  if($isComposite){foreach((array)$pk as $k)unset($values[$k]);}
  $pairs=implode(', ',array_map(fn($k)=>"`$k`=?",array_keys($values)));
  $where=implode(' AND ',array_map(fn($k)=>"`$k`=?",array_keys($key)));
  if($pairs!==''){$s=db()->prepare("UPDATE `$table` SET $pairs WHERE $where");$s->execute([...array_values($values),...array_values($key)]);}
  flash('success','Registro atualizado com sucesso.');
 } else {
  $cols=array_keys($values);$names=implode(', ',array_map(fn($c)=>"`$c`",$cols));$questions=implode(', ',array_fill(0,count($cols),'?'));
  $s=db()->prepare("INSERT INTO `$table` ($names) VALUES ($questions)");$s->execute(array_values($values));
  flash('success','Registro cadastrado com sucesso.');
 }
 go('list',['t'=>$table]);
} catch(Throwable $e){flash('error',compact_error($e));if($page==='delete')go('list',['t'=>$table]);go('form',['t'=>$table]+($isEdit?array_combine(array_map(fn($k)=>'key_'.$k,array_keys($key)),array_values($key)):[]));}
