<?php
require '../vendor/autoload.php';
use Ramsey\Uuid\Uuid;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
$usuario = @$_SESSION['usuario'];

$db = new SQLite3('../db/mochinho.sqlite3');

$email = @$_REQUEST["email"];
$info = null;
$erro = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  if (@preg_match($email, "^[\w._]+@\w+(\.\w+)+$")) {
    $erro = "E-mail inválido, ele deve ter o formato de usuario@provedor";
  }
  $uid = explode('-', Uuid::uuid4())[0];
  $sql = <<<SQL
    SELECT status, nome FROM usuarios WHERE email = :email
SQL;

  $cmd = $db->prepare($sql);
  $cmd->bindValue('email', $email);
  $rs = $cmd->execute();
  $row = $rs->fetchArray(SQLITE3_ASSOC);

  if ($row) {
    $nome = $row['nome'];
    if ($row['status'] > 0) {
      $info = 'Esta conta já está ativada, você pode fazer o login em <a href="/login.php?url=<?= $_SERVER[\'REQUEST_URI\'] ?>">login</a>';
    }
  } else {
    $erro = "Este e-mail não existe no nosso sistema, você pode fazer o cadastro.";
  }
  $mail = new PHPMailer(true);
  try {
    $mail->isSMTP();
    $mail->Host = 'smtp.googlemail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'nao.responda.ifrs.riogrande@gmail.com';
    $mail->Password = $_ENV['COBAIA_MAIL_PASSWORD']; //'ifrsrgIFRSRG';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    $mail->setFrom('nao.responda.ifrs.riogrande@gmail.com', 'Cobaia Mailer');
    $mail->addAddress($email, $nome);
    $mail->isHTML(true);
    $mail->Subject = '[Cobaia] Confirmar seu registro';
    $mail->Body = "Olá {$nome}<br><br>Confirme sua conta com esse código: {$uid} ou, se preferir, clique nesse link: <a href=\"http://localhost:4567/ativar.php?codigo={$uid}\">http://localhost:9000/ativar.php?codigo={$uid}</a> para direcioná-lo diretamente";
    $mail->send();
    header("Location: /ativar.php");
    exit;
  } catch (Exception $e) {
      $erro = 'O provedor deste e-mail não foi encontrado, confira o endereço por favor';
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
form.ativar {
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


    <form method="post" class="form ativar">

      <h3>Re-enviar código de acesso</h3>

			<div class="form-group">
				<label for="email">E-mail:</label>
				<input type="text" id="email" autofocus="autofocus" value="<?= @$email ?>"
					name="email" class="form-control" maxlength="100" size="60">
			</div>
			<button type="submit" class="btn btn-large btn-outline-primary">
				<i class="fa fa-sign-in" aria-hidden="true"></i>&nbsp; Re-enviar
			</button>
		</form>
  </main>

  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
  <script src="/js/bootstrap.bundle.min.js"></script>
</body>
</html>
