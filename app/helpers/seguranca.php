<?php
/** Finalidade: Sanitização de dados para renderização no HTML. **/
declare(strict_types=1);
function e(?string $valor): string { return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }