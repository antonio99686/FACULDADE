<?php
declare(strict_types=1);
$table=(string)($_GET['t']??'');
if(!isset($modules[$table])){http_response_code(400);exit('Tabela inválida');}
$meta=$modules[$table];$columns=[...array_values((array)$meta['pk']),...$meta['columns']];$columns=array_values(array_unique($columns));
if($table==='matricula_disciplina')$columns[]='media';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="faculdade_'.$table.'_'.date('Ymd').'.csv"');
header('Cache-Control: no-store');
$out=fopen('php://output','w');fwrite($out,"\xEF\xBB\xBF");fputcsv($out,$columns,';');
$stmt=db()->query('SELECT '.implode(',',array_map(fn($c)=>'`'.$c.'`',$columns)).' FROM `'.$table.'`');
while($r=$stmt->fetch(PDO::FETCH_NUM)){
  // Evita injeção de fórmulas em planilhas que abrirem os CSVs.
  foreach($r as &$value){if(is_string($value)&&preg_match('/^[\s]*[=+@\-]/u',$value))$value="'".$value;}unset($value);
  fputcsv($out,$r,';');
}
fclose($out);exit;
