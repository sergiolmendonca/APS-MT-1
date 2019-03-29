<?php
require '../vendor/autoload.php';

session_start();
$usuario = @$_SESSION['usuario'];

$erro = null;
$info = null;

$db = new SQLite3('../db/mochinho.sqlite3');
$nome = null;
$cargaHoraria = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome = @$_POST["nome"];
  if (strlen($nome) === 0) {
    $erro = "Nome não informado";
  }
  $resumo = @$_POST["resumo"];
  if (strlen($resumo) === 0) {
    $erro = "Resumo não informado";
  }
  $vagas = @$_POST["vagas"] ?: 0;
  if ($vagas < 1) {
    $erro = "Quantitativo de vagas não informado";
  }
  $cargaHoraria = @$_POST["carga_horaria"] ?: 0;
  if ($cargaHoraria < 1) {
    $erro = "Carga horária não informada";
  }
  if (strlen(@$_POST["data_inicio"]) === 0) {
    $erro = "Dada de início não informada";
  }
  $dataInicio = $_POST["data_inicio"];
  if (strlen(@$_POST["data_termino"]) === 0) {
    $erro = "Dada de término não informada";
  }
  $dataTermino = $_POST["data_termino"];
  $dias = join(',', @$_POST["dias"]);
  if (strlen($dias) === 0) {
    $erro = "Dias não informados";
  }
  if (strlen(@$_POST["horario_inicio"]) === 0) {
    $erro = "Horário de início não informado";
  }
  $horaInicio = $_POST["horario_inicio"];
  if (strlen(@$_POST["horario_termino"]) === 0) {
    $erro = "Horário de término não informado";
  }
  $horaTermino = $_POST["horario_termino"];
  $programa = $_POST["programa"];
  $arquivo = $_FILES["imagem"]["tmp_name"];
  $tamanho = $_FILES['imagem']['size'];
  $conteudo = null;
  $tipoImagem = null;
  if ($arquivo != 'none') {
    $fp = fopen($arquivo, 'rb');
    $conteudo = fread($fp, $tamanho);
    fclose($fp);
    $tipoImagem = explode('/', $_FILES['imagem']['type'])[1];
  }

  if (strlen(@$_POST["area"]) === 0) {
    $erro = "Área não selecionada de término não informado";
  }

  $idArea = $_POST["area"];

  if (! $erro) {

    $sql = <<<SQL
      INSERT INTO cursos (nome, resumo, vagas, carga_horaria, data_inicio,
      data_termino, dias, horario_inicio, horario_termino, programa,
      imagem, tipo_imagem, id_area)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
SQL;
    $cmd = $db->prepare($sql);

    $cmd->bindValue(1, $nome);
    $cmd->bindValue(2, $resumo);
    $cmd->bindValue(3, $vagas);
    $cmd->bindValue(4, $cargaHoraria);
    $cmd->bindValue(5, $dataInicio);
    $cmd->bindValue(6, $dataTermino);
    $cmd->bindValue(7, $dias);
    $cmd->bindValue(8, $horaInicio);
    $cmd->bindValue(9, $horaTermino);
    $cmd->bindValue(10, $programa);
    $cmd->bindValue(11, $conteudo);
    $cmd->bindValue(12, $tipoImagem);
    $cmd->bindValue(13, $idArea);

    $cmd->execute();
  }
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>K0B414</title>
<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="/css/style.css">
<body>
	<nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
    <a class="navbar-brand" href="#">K0B414</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          <a class="nav-link" href="/index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/cursos">Cursos</a>
        </li>
      </ul>
      <form class="form-inline mt-2 mt-md-0">
      	{% if usuario is not null %}
      	<a class="btn btn-primary mr-3" href="/index.php">
      		<i class="fa fa-user-circle-o" aria-hidden="true"></i>
      		{{ usuario }}
      	</a>
      	<a href="/logout.php" class="btn btn-outline-danger my-2 my-sm-0 mr-1">
        	Logout
        </a>
      	{% else %}
        <a href="/login.php?url=<?= $_SERVER['REQUEST_URI'] ?>" class="btn btn-outline-success my-2 my-sm-0 mr-1">
        	Login
        </a>
        <a href="/registro.php" class="btn btn-outline-info my-2 my-sm-0">
        	Registrar-se
        </a>
        {% endif %}
      </form>
    </div>
  </nav>


  <main role="main" class="container">

    <h3><a href="/admin.php">Área Administrativa</a> &gt; Novo Curso</h3>

    <hr>

    <?php if ($erro): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Erro:</strong> <?= $erro ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php endif ?>

    <?php if ($info): ?>
      <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?= $info ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php endif ?>

    <form method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" class="form-control"
          value="<?= $nome ?>" maxlength="40" size="40" autofocus required>
      </div>
      <div class="form-group">
        <label for="area">Área:</label>
        <select id="area" name="area" class="form-control" required>
          <option value="">Selecione uma área</option>
          <?php foreach ($areas as $a): ?>
            <option value="$a['id']" <?= $a['nome']= area ? "selected" : "" ?>>
              <?= $a['nome'] ?>
            </option>
          <?php endforeach ?>
        </select>
      </div>
      <div class="form-group">
        <label for="resumo">Resumo:</label>
        <textarea id="resumo" name="resumo" rows="2" maxlength="100"
          value="<?= $resumo ?>" class="form-control" required></textarea>
      </div>
      <div class="form-group">
        <label for="vagas">Número de vagas:</label>
        <input type="number" id="vagas" name="vagas" class="form-control"
          value="<?= $vagas ?>" step="1" min="1" required>
      </div>
      <div class="form-group">
        <label for="carga_horaria">Carga horária (horas):</label>
        <input type="number" id="carga_horaria" name="carga_horaria" class="form-control"
          value="<?= $carga_horaria ?>" step="1" min="1" required>
      </div>
      <div class="form-group">
        <label for="data_inicio">Data de início:</label>
        <input type="date" id="data_inicio" name="data_inicio" class="form-control"
          value="<?= $data_inicio ?>" required>
      </div>
      <div class="form-group">
        <label for="data_termino">Data de término:</label>
        <input type="date" id="data_termino" name="data_termino" class="form-control"
          value="<?= $data_termino ?>" required>
      </div>
      <div class="form-group form-check form-check-inline">
        <label class="form-check-label">
          Dias:
        </label>
        <label class="form-check-label">
          <input type="checkbox" name="dias[]"
            class="form-check-input" value="seg">
          Segundas
        </label>
        <label class="form-check-label">
          <input type="checkbox" name="dias[]" class="form-check-input" value="ter">
          Terças
        </label>
        <label class="form-check-label">
          <input type="checkbox" name="dias[]" class="form-check-input" value="qua">
          Quartas
        </label>
        <label class="form-check-label">
          <input type="checkbox" name="dias[]" class="form-check-input" value="qui">
          Quintas
        </label>
        <label class="form-check-label">
          <input type="checkbox" name="dias[]" class="form-check-input" value="sex">
          Sextas
        </label>
        <label class="form-check-label">
          <input type="checkbox" name="dias[]" class="form-check-input" value="sab">
          Sábados
        </label>
      </div>
      <div class="form-inline">
        Horário das
        <input type="time" name="horario_inicio" class="form-control"
          value="<?= $horario_inicio ?>" style="margin: 0 0.5em" required>
        às
        <input type="time" name="horario_termino" class="form-control"
          value="<?= $horario_termino ?>" style="margin: 0 0.5em" required>
      </div>
      <div class="form-group">
        <label for="programa">Programa: <small>(opcional)</small></label>
        <textarea id="programa" name="programa" rows="5" maxlength="500"
          class="form-control"><?= $programa ?></textarea>
      </div>
      <div class="form-group">
        <label for="imagem">Imagem: <small>(opcional)</small></label>
        <input type="file" name="imagem" accept="image/png image/jpg image/jpeg">
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-large btn-outline-primary">
          <i class="fa fa-save" aria-hidden="true"></i>&nbsp;
          Salvar
        </button>
      </div>
    </form>

  </main>

</body>
</html>
