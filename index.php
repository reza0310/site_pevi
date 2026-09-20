<?php
include "INCLUDES/utils.php";

$cards = "";
$cards .= file_get_contents("jeux/cards/elementalis.html");

$page = file_get_contents("page.html");
$page = str_replace("%cards%", $cards, $page);

echo(addheader($page, "uno"));
?>