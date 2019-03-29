<?php

session_start();
$usuario = @$_SESSION['usuario'];

$db = new SQLite3('../db/mochinho.sqlite3');
$erro = null;
$info = null;

$id = @$_GET['id'] ?: 0;

$sql = <<<SQL
SELECT c.*, a.nome AS area,
  (SELECT COUNT(*) FROM inscricoes WHERE id_curso = c.id) AS inscritos
FROM cursos AS c
  JOIN areas AS a
  ON c.id_area = a.id
WHERE c.id = {$id}
SQL;

$result = $db->query($sql);

if (count($result) === 0) {
  header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
  die();
}

$curso = $result->fetchArray(SQLITE3_ASSOC);
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
<script type="text/javascript" src="js/holder.min.js"></script>
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
        <li class="nav-item active">
          <a class="nav-link" href="#">Cursos <span class="sr-only">(current)</span></a>
        </li>
      </ul>
      <form class="form-inline mt-2 mt-md-0">
      	<?php if (@$usuario): ?>
      	<a class="btn btn-primary mr-3" href="/perfil.php">
      		<i class="fa fa-user-circle-o" aria-hidden="true"></i>
      		<?= @$usuario ?>
      	</a>
      	<a href="/logout.php" class="btn btn-outline-danger my-2 my-sm-0 mr-1">
        	Logout
        </a>
        <?php else: ?>
        <a href="/login.php?url=<?= $_SERVER['REQUEST_URI'] ?>" class="btn btn-outline-success my-2 my-sm-0 mr-1">
        	Login
        </a>
        <a href="/registro.php" class="btn btn-outline-info my-2 my-sm-0">
        	Registrar-se
        </a>
        <?php endif ?>
      </form>
    </div>
  </nav>

  <main role="main" class="container">
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

    <h3><?= $curso['nome'] ?></h3>

    <?php if (@$usuario) : ?>
      <a href="/inscrever.php?curso=<?= $curso['id'] ?>" class="btn btn-outline-success">
        Inscrever-se
      </a>
    <?php endif ?>

		<hr>

    <div class="row">
    	<div class="col-md-3"><?= $curso['vagas'] - $curso['inscritos'] ?> vagas disponíveis de <?= $curso['vagas'] ?></div>
    	<div class="col-md-3"><?= $curso['carga_horaria'] ?> horas</div>
    	<div class="col-md-3">Início em <?= $curso['data_inicio'] ?></div>
    	<div class="col-md-3">
    		<?= $curso['dias'] ?> das <?= $curso['horario_inicio'] ?> às <?= $curso['horario_termino'] ?>
    	</div>
    </div>

    <br>

    <div class="row description">
    	<div class="col-md-6 col-xs-12">
    		<h4>Resumo</h4>
    	</div>
    	<div class="col-md-6 col-xs-12">
	    	<p><?= $curso['resumo'] ?></p>
	  	</div>
    </div>

    <br>

    <div class="row description">
    	<div class="col-md-6 col-xs-12">
    		<h4>Programa completo</h4>
    	</div>
    	<div class="col-md-6 col-xs-12">
    		<p><?= $curso['programa'] ?></p>
    	</div>
    </div>

  </main>

</body>
</html>
