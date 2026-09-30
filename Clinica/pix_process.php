<?php
require_once 'config.php';
header('Content-Type: application/json');

function crc16(string $str): string
{
    $crc = 0xFFFF;
    for ($i = 0; $i < strlen($str); $i++) {
        $crc ^= (ord($str[$i]) << 8);
        for ($j = 0; $j < 8; $j++) {
            $crc = ($crc & 0x8000) ? (($crc << 1) ^ 0x1021) : ($crc << 1);
            $crc &= 0xFFFF;
        }
    }
    return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
}

function tlv(string $id, string $value): string
{
    $len = str_pad((string) strlen($value), 2, '0', STR_PAD_LEFT);
    return $id . $len . $value;
}

function montarPayloadPix(string $pixKey, string $merchantName, string $merchantCity, float $amount, string $txid): string
{
    $merchantAccount = tlv('00', 'br.gov.bcb.pix') . tlv('01', $pixKey);

    $payload = tlv('00', '01')
             . tlv('26', $merchantAccount)
             . tlv('52', '0000')
             . tlv('53', '986');

    if ($amount > 0) {
        $payload .= tlv('54', number_format($amount, 2, '.', ''));
    }

    $payload .= tlv('58', 'BR')
              . tlv('59', substr($merchantName, 0, 25))
              . tlv('60', substr($merchantCity, 0, 15))
              . tlv('62', tlv('05', substr($txid, 0, 25)));

    $payload .= '6304';
    return $payload . crc16($payload);
}

$valor = isset($_POST['valor']) ? (float) $_POST['valor'] : 0;

if ($valor <= 0) {
    echo json_encode(['ok' => false, 'msg' => 'Informe um valor válido.']);
    exit;
}

$txid = 'CONSULTA' . substr((string) time(), -8) . rand(10, 99);

$payload = montarPayloadPix(
    '+5517988153348',
    'DIEGO GOMES',
    'SAO JOSE DO RIO PRETO',
    $valor,
    $txid
);

$usuarioId = $_SESSION['user_id'] ?? null;

$stmt = $pdo->prepare('INSERT INTO pagamentos (usuario_id, valor, txid, status) VALUES (?, ?, ?, ?)');
$stmt->execute([$usuarioId, $valor, $txid, 'pendente']);

echo json_encode(['ok' => true, 'payload' => $payload]);


