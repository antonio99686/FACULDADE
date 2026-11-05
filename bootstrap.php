<?php
declare(strict_types=1);
$config = require __DIR__ . '/config.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('faculdade_sess');
    session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off']);
    session_start();
}
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
function db(): PDO {
    static $pdo = null;
    global $config;
    if ($pdo === null) {
        $d = $config['db'];
        $pdo = new PDO('mysql:host='.$d['host'].';port='.$d['port'].';dbname='.$d['name'].';charset=utf8mb4', $d['user'], $d['pass'], [
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES=>false,
        ]);
    }
    return $pdo;
}
function e(mixed $s): string { return htmlspecialchars((string)($s??''), ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $route='', array $params=[]): string {
    global $config;
    return rtrim($config['base_url'], '/') . '/index.php' . ($route !== '' || $params ? '?' . http_build_query(['p'=>$route]+$params) : '');
}
function go(string $route='', array $params=[]): never { header('Location: '.url($route,$params)); exit; }
function csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function valid_csrf(): void {
    if (!hash_equals(csrf(), (string)($_POST['_csrf']??''))) { http_response_code(419); exit('Token CSRF inválido. Atualize a página e tente novamente.'); }
}
function flash(string $type,string $message): void { $_SESSION['flash']=['type'=>$type,'message'=>$message]; }
function require_auth(): void { if (empty($_SESSION['admin_id'])) go('login'); }
function query(string $sql,array $params=[]): array { $s=db()->prepare($sql); $s->execute($params); return $s->fetchAll(); }
function one(string $sql,array $params=[]): ?array { $s=db()->prepare($sql);$s->execute($params); $row=$s->fetch();return $row?:null; }
function count_table(string $name): int { return (int)db()->query('SELECT COUNT(*) FROM `'.$name.'`')->fetchColumn(); }
function money(mixed $v): string { return 'R$ '.number_format((float)$v,2,',','.'); }
function year_options(): array { $now=(int)date('Y');return range($now+2,$now-15); }
function compact_error(Throwable $e): string {
    if ($e instanceof PDOException) {
        $code=(string)($e->errorInfo[1]??'');
        if ($code==='1062') return 'Registro duplicado. Confira os campos que devem ser únicos.';
        if ($code==='1451') return 'Não é possível excluir: existem registros vinculados.';
        if ($code==='1452') return 'Relacionamento inválido. Verifique os dados vinculados.';
        if ($code==='3819') return 'Valor fora das regras permitidas pelo banco.';
    }
    return 'Não foi possível concluir a operação. '. $e->getMessage();
}
