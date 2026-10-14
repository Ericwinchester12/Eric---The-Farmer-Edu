<?php
require_once __DIR__ . '/../../core/database.php';

header('Content-Type: application/json; charset=utf-8');

// regra de negocio: 100 XP por nivel, todo mundo comeca no nivel 1
const XP_POR_NIVEL = 100;

function calcularNivel(int $xpTotal): int
{
    return intdiv($xpTotal, XP_POR_NIVEL) + 1;
}

try {
    $pdo = iniciarPDO();

    $idAluno = 1;
    $xpGanho = 50;
    $motivo  = 'Atividade concluida (teste)';

    $pdo->beginTransaction();

    $sqlTotal = 'SELECT COALESCE(SUM(quantidade), 0) FROM xp_log WHERE id_aluno = :id_aluno';

    $stmt = $pdo->prepare($sqlTotal);
    $stmt->execute([':id_aluno' => $idAluno]);
    $xpAntes = (int) $stmt->fetchColumn();

    $stmt = $pdo->prepare(
        'INSERT INTO xp_log (id_aluno, quantidade, motivo) VALUES (:id_aluno, :quantidade, :motivo)'
    );
    $stmt->execute([
        ':id_aluno'   => $idAluno,
        ':quantidade' => $xpGanho,
        ':motivo'     => $motivo,
    ]);
    $idXp = (int) $pdo->lastInsertId();

    $stmt = $pdo->prepare($sqlTotal);
    $stmt->execute([':id_aluno' => $idAluno]);
    $xpTotal = (int) $stmt->fetchColumn();

    $pdo->commit();

    $nivelAntes = calcularNivel($xpAntes);
    $nivelAtual = calcularNivel($xpTotal);

    echo json_encode([
        'status' => 'sucesso',
        'squad'  => 'Gamificação',
        'dados'  => [
            'id_xp'             => $idXp,
            'id_aluno'          => $idAluno,
            'xp_ganho'          => $xpGanho,
            'motivo'            => $motivo,
            'xp_antes'          => $xpAntes,
            'xp_total'          => $xpTotal,
            'nivel_antes'       => $nivelAntes,
            'nivel_atual'       => $nivelAtual,
            'subiu_de_nivel'    => $nivelAtual > $nivelAntes,
            'xp_proximo_nivel'  => $nivelAtual * XP_POR_NIVEL - $xpTotal,
        ],
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'status'   => 'erro',
        'squad'    => 'Gamificação',
        'mensagem' => 'Falha no banco: ' . $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
