<?
require('class/class.dbMySQL.php');
require "banco.config.php";

global $db;
global $link_id;
$db  = new dbMySQL();
$link_id  = $db->Connect($_host, $_user, $_senha);
$db->dbSelect($_banco);
?>