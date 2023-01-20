<?php
// primeiro vamos realizar a leitura
// simples do INI, cada chave � transformada
// em um �ndice do array unidimensional
$ini = parse_ini_file('exemplo.ini');
print $ini['temp'] . '<br>';
print $ini['fonte'] . '<br>';

// agora iremos respeitar a hierarquia das
// se��es do INI. O segundo par�metro faz
// com que as se��es sejam as chaves de acesso
// para este array multi-dimensional
$ini = parse_ini_file('exemplo.ini', true);
print $ini['paths']['temp'] . '<br>';
print $ini['layout']['fonte'] . '<br>';
?>