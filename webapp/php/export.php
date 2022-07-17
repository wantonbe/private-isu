<?php

require 'vendor/autoload.php';

$config = [
    'db' => [
        'host' => $_SERVER['ISUCONP_DB_HOST'] ?? 'localhost',
        'port' => $_SERVER['ISUCONP_DB_PORT'] ?? 3306,
        'username' => $_SERVER['ISUCONP_DB_USER'] ?? 'root',
        'password' => $_SERVER['ISUCONP_DB_PASSWORD'] ?? null,
        'database' => $_SERVER['ISUCONP_DB_NAME'] ?? 'isuconp',
    ],
];
$db = new PDO(
    "mysql:dbname={$config['db']['database']};host={$config['db']['host']};port={$config['db']['port']};charset=utf8mb4",
    $config['db']['username'],
    $config['db']['password']
);

$limit = 1000;

$ps = $db->prepare('SELECT COUNT(*) AS count FROM `posts` AS `p` INNER JOIN `users` AS `u` ON `u`.`id` = `p`.`user_id` WHERE `u`.`del_flg` = 0');
$ps->execute();
$posts_count = $ps->fetch()["count"];
for ($offset = 0; $offset < $posts_count; $offset+=$limit) {
    $query =<<<_SQL_
        SELECT
        `p`.`id`, `p`.`imgdata`, `p`.`mime`
        FROM `posts` AS `p`
        INNER JOIN `users` AS `u` ON `u`.`id` = `p`.`user_id`
        WHERE `u`.`del_flg` = 0
        LIMIT {$offset}, {$limit};
    _SQL_
    ;

    $ps = $db->prepare($query);
    $ps->execute();
    $posts = $ps->fetchAll(PDO::FETCH_ASSOC);
    foreach ($posts as $post) {
        switch($post["mime"]) {
            case "image/jpeg": $ext = "jpg"; break;
            case "image/png": $ext = "png"; break;
            case "image/gif": $ext = "gif"; break;
            default: break;
        }
        if (! $ext) {
            continue;
        }

        $filepath = __DIR__ . "/exports/static/image/{$post['id']}.{$ext}";
        echo "output: {$filepath}\n";
        file_put_contents($filepath, $post["imgdata"]);
    }
}
