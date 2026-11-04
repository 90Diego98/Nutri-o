<?php
require_once 'config.php';
header('Content-Type: application/json');

const MAX_TENTATIVAS = 5;   // erros seguidos permitidos
// Ao errar a 5ª vez, a pessoa fica bloqueada por 1 hora (veja INTERVAL 1 HOUR abaixo)

$data  = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? '');
$senha = $data['senha'] ?? '';

if (!$email || !$senha) {
    echo json_encode(['ok' => false, 'msg' => 'Preencha e-mail e senha.']);
    exit;
}

// O limite vale pra combinação e-mail + IP (assim uma escola inteira, que
// compartilha o mesmo IP, não é bloqueada por causa de um aluno só).
$ip    = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$chave = mb_strtolower(mb_substr($email, 0, 150));

try {
    // 1) Essa pessoa está bloqueada agora?
    $stmt = $pdo->prepare(
        'SELECT tentativas,
                TIMESTAMPDIFF(SECOND, NOW(), bloqueado_ate) AS seg_bloqueio,
                TIMESTAMPDIFF(SECOND, ultima_tentativa, NOW()) AS seg_ultima
         FROM login_tentativas WHERE email = ? AND ip = ?'
    );
    $stmt->execute([$chave, $ip]);
    $reg = $stmt->fetch();

    $tentativas = $reg ? (int) $reg['tentativas'] : 0;

    if ($reg && $reg['seg_bloqueio'] !== null) {
        if ((int) $reg['seg_bloqueio'] > 0) {
            $min = (int) ceil($reg['seg_bloqueio'] / 60);
            echo json_encode([
                'ok'  => false,
                'msg' => 'Muitas tentativas incorretas. Tente novamente em ' . $min . ' minuto' . ($min > 1 ? 's' : '') . '.'
            ]);
            exit;
        }
        $tentativas = 0; // o bloqueio acabou: recomeça a contagem
    }
    // Erros antigos (mais de 1 hora atrás) não contam
    if ($reg && (int) $reg['seg_ultima'] > 3600) {
        $tentativas = 0;
    }

    // 2) Confere e-mail e senha
    $stmt = $pdo->prepare('SELECT id, nome, senha_hash FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
        $tentativas++;
        $bloquear = $tentativas >= MAX_TENTATIVAS;
        $bloqueadoAte = $bloquear ? 'DATE_ADD(NOW(), INTERVAL 1 HOUR)' : 'NULL';

        $pdo->prepare(
            "REPLACE INTO login_tentativas (email, ip, tentativas, ultima_tentativa, bloqueado_ate)
             VALUES (?, ?, ?, NOW(), $bloqueadoAte)"
        )->execute([$chave, $ip, $tentativas]);

        // Limpeza: apaga registros com mais de 1 dia
        $pdo->exec('DELETE FROM login_tentativas WHERE ultima_tentativa < (NOW() - INTERVAL 1 DAY)');

        $msg = 'E-mail ou senha incorretos.';
        if ($bloquear) {
            $msg .= ' Acesso bloqueado por 1 hora.';
        }
        echo json_encode(['ok' => false, 'msg' => $msg]);
        exit;
    }

    // 3) Acertou: zera o contador
    $pdo->prepare('DELETE FROM login_tentativas WHERE email = ? AND ip = ?')->execute([$chave, $ip]);

} catch (PDOException $e) {
    error_log('Erro no login: ' . $e->getMessage());
    echo json_encode(['ok' => false, 'msg' => 'Erro interno no login. Confira se a tabela login_tentativas foi criada no banco.']);
    exit;
}

session_regenerate_id(true); // novo ID de sessão a cada login (evita roubo de sessão)
$_SESSION['user_id']   = (int) $usuario['id'];
$_SESSION['user_nome'] = $usuario['nome'];

echo json_encode(['ok' => true, 'nome' => $usuario['nome']]);
