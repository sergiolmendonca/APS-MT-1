<?php
require 'Model.php';
class UsuarioModel extends Model{

    public function login($email, $senha){
        $salt = 'cobaia';
        $sql = <<<SQL
            SELECT id, nome, email, status
            FROM usuarios
            WHERE email = :email AND senha = :senha
SQL;

        $cmd = $this->prepare($sql);
        $cmd->bindValue('email', $email);
        $cmd->bindValue('senha', md5($salt . $senha));
        $result = $cmd->execute();
        if ($usuario = $result->fetchArray(SQLITE3_ASSOC)) {
            return $usuario;
        } else {
            return null;
        }

    }

    public function ativar($email, $token){
        $sql = <<<SQL
            UPDATE usuarios
            SET status = 1, token = NULL
            WHERE token = :token AND email = :email AND status = 0;
SQL;
        $cmd = $this->prepare($sql);
        $cmd->bindValue('token', $token);
        $cmd->bindValue('email', $email);
        $cmd->execute();
        return $this->changes() > 0;
    }
    
}

?>