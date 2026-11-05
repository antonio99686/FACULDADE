<?php
declare(strict_types=1);
// Campos são allowlisted: nunca usamos identificadores SQL vindos diretamente de GET ou POST.
return [
 'aluno'=>[
  'title'=>'Alunos','singular'=>'Aluno','icon'=>'🎓','pk'=>'id_aluno','columns'=>['matricula','nome','cpf','data_nascimento','sexo','status_aluno'],
  'labels'=>['matricula'=>'Matrícula','nome'=>'Nome completo','cpf'=>'CPF','data_nascimento'=>'Nascimento','sexo'=>'Sexo','status_aluno'=>'Situação'],
  'required'=>['matricula','nome','cpf'],
  'inputs'=>['data_nascimento'=>'date','sexo'=>['Feminino','Masculino','Outro','Prefiro não informar'],'status_aluno'=>['matrícula ativa','evadido','egresso']],
  'search'=>['nome','matricula','cpf'],
 ],
 'telefone_aluno'=>[
  'title'=>'Telefones','singular'=>'Telefone','icon'=>'📞','pk'=>'id_telefone','columns'=>['id_aluno','telefone'],
  'labels'=>['id_aluno'=>'Aluno','telefone'=>'Telefone'],'required'=>['id_aluno','telefone'],'refs'=>['id_aluno'=>['aluno','id_aluno','nome']], 'search'=>['telefone'],
 ],
 'curso'=>[
  'title'=>'Cursos','singular'=>'Curso','icon'=>'📚','pk'=>'id_curso','columns'=>['nome','carga_horaria_total','perfil_profissional'],
  'labels'=>['nome'=>'Nome','carga_horaria_total'=>'Carga horária total','perfil_profissional'=>'Perfil profissional'],
  'required'=>['nome','carga_horaria_total','perfil_profissional'],'inputs'=>['carga_horaria_total'=>'positive','perfil_profissional'=>'textarea'],'search'=>['nome'],
 ],
 'disciplina'=>[
  'title'=>'Disciplinas','singular'=>'Disciplina','icon'=>'📖','pk'=>'id_disciplina','columns'=>['nome','carga_horaria_semanal','ementa'],
  'labels'=>['nome'=>'Nome','carga_horaria_semanal'=>'Carga horária semanal','ementa'=>'Ementa'],
  'required'=>['nome','carga_horaria_semanal','ementa'],'inputs'=>['carga_horaria_semanal'=>'positive','ementa'=>'textarea'],'search'=>['nome'],
 ],
 'curso_disciplina'=>[
  'title'=>'Grade curricular','singular'=>'Vínculo curso-disciplina','icon'=>'🧩','pk'=>['id_curso','id_disciplina'],'columns'=>['id_curso','id_disciplina'],
  'labels'=>['id_curso'=>'Curso','id_disciplina'=>'Disciplina'],'required'=>['id_curso','id_disciplina'],
  'refs'=>['id_curso'=>['curso','id_curso','nome'],'id_disciplina'=>['disciplina','id_disciplina','nome']],
 ],
 'professor'=>[
  'title'=>'Professores','singular'=>'Professor','icon'=>'👨‍🏫','pk'=>'id_professor','columns'=>['nome','titulacao'],
  'labels'=>['nome'=>'Nome','titulacao'=>'Titulação'],'required'=>['nome','titulacao'],
  'inputs'=>['titulacao'=>['graduação','especialização','mestrado','doutorado']], 'search'=>['nome'],
 ],
 'area_professor'=>[
  'title'=>'Áreas de atuação','singular'=>'Área de atuação','icon'=>'🔬','pk'=>'id_area','columns'=>['id_professor','area_atuacao'],
  'labels'=>['id_professor'=>'Professor','area_atuacao'=>'Área de atuação'],'required'=>['id_professor','area_atuacao'],
  'refs'=>['id_professor'=>['professor','id_professor','nome']], 'search'=>['area_atuacao'],
 ],
 'inscricao'=>[
  'title'=>'Inscrições em cursos','singular'=>'Inscrição','icon'=>'📝','pk'=>'numero_inscricao','columns'=>['id_aluno','id_curso','ano_inicio','ano_conclusao','ativa'],
  'labels'=>['id_aluno'=>'Aluno','id_curso'=>'Curso','ano_inicio'=>'Ano de início','ano_conclusao'=>'Ano de conclusão','ativa'=>'Inscrição ativa'],
  'required'=>['id_aluno','id_curso','ano_inicio'],
  'refs'=>['id_aluno'=>['aluno','id_aluno','nome'],'id_curso'=>['curso','id_curso','nome']],
  'inputs'=>['ano_inicio'=>'year','ano_conclusao'=>'year','ativa'=>'boolean'],
 ],
 'execucao_disciplina'=>[
  'title'=>'Ofertas de disciplinas','singular'=>'Oferta','icon'=>'📅','pk'=>'id_execucao','columns'=>['id_curso','id_disciplina','ano','semestre','id_professor'],
  'labels'=>['id_curso'=>'Curso','id_disciplina'=>'Disciplina','ano'=>'Ano letivo','semestre'=>'Semestre','id_professor'=>'Professor'],
  'required'=>['id_curso','id_disciplina','ano','semestre','id_professor'],
  'refs'=>['id_curso'=>['curso','id_curso','nome'],'id_disciplina'=>['disciplina','id_disciplina','nome'],'id_professor'=>['professor','id_professor','nome']],
  'inputs'=>['ano'=>'year','semestre'=>['1'=>'1º semestre','2'=>'2º semestre']],
 ],
 'matricula_disciplina'=>[
  'title'=>'Matrículas e notas','singular'=>'Matrícula em disciplina','icon'=>'🧮','pk'=>'id_matricula','columns'=>['id_aluno','id_execucao','nota1','nota2'],
  'labels'=>['id_aluno'=>'Aluno','id_execucao'=>'Oferta de disciplina','nota1'=>'Nota 1','nota2'=>'Nota 2'],
  'required'=>['id_aluno','id_execucao'],
  'refs'=>['id_aluno'=>['aluno','id_aluno','nome'],'id_execucao'=>['execucao_disciplina','id_execucao','id_execucao']],
  'inputs'=>['nota1'=>'grade','nota2'=>'grade'],
 ],
 'auxilio_estudantil'=>[
  'title'=>'Auxílios estudantis','singular'=>'Auxílio','icon'=>'💰','pk'=>'id_auxilio','columns'=>['id_aluno','nome_programa','valor_beneficio'],
  'labels'=>['id_aluno'=>'Aluno','nome_programa'=>'Programa','valor_beneficio'=>'Valor do benefício'],
  'required'=>['id_aluno','nome_programa','valor_beneficio'],'refs'=>['id_aluno'=>['aluno','id_aluno','nome']],
  'inputs'=>['valor_beneficio'=>'currency'],'search'=>['nome_programa'],
 ],
];
