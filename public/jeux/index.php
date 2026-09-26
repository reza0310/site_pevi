<?php
require_once dirname(__DIR__, 2) . '/includes/utils.php';

$cards = '';
$cards .= file_get_contents('cards/elementalis.html');

$page = file_get_contents('page.html');
$page = str_replace('%cards%', $cards, $page);

echo(addheader($page, 'uno'));
?>
