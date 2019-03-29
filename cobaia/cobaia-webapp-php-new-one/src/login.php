<?php
require 'UsuarioModel.php';
session_start();
$usuario = @$_SESSION['usuario'];

$erro = null;
$info = null;

$url = @$_REQUEST['url'] ?: "/index.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
  $usuarioModel = new UsuarioModel;
    if($user = $usuarioModel->login(@$_POST['email'], @$_POST['senha'])){
      $_SESSION['id'] = $user['id'];
      $_SESSION['usuario'] = $user['nome'];
      $_SESSION['email'] = $user['email'];
      header("Location: {$url}");
      exit;
   }else{
     $erro = "E-mail e/ou senha não encontrados!";
   }   
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Home</title>
<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="/css/style.css">
<style type="text/css">
form.login {
	max-width: 350px;
	margin: 0 auto;
	padding-top: 2em;
}
</style>
<body>

	<nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
    <a class="navbar-brand" href="#">K0B414</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <a class="nav-link" href="/index.php">Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/cursos">Cursos</a>
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

    <form method="post" class="form login">
			<div class="form-group">
				<label for="email">E-mail:</label>
				<input type="text" id="email" autofocus="autofocus"
					name="email" class="form-control" maxlength="100" size="60">
			</div>
			<div class="form-group">
				<label for="senha">Senha:</label>
				<input type="password" id="senha"
					name="senha" class="form-control" maxlength="50" size="50">
			</div>
			<button type="submit" class="btn btn-large btn-outline-primary">
				<i class="fa fa-sign-in" aria-hidden="true"></i>&nbsp; Login
			</button>
			<a href="/registro.php" style="margin-left: 2emx">Cadastre-se</a>
		</form>

  </main>

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
	<script src="/js/bootstrap.bundle.min.js"></script>
</body>
</html>
