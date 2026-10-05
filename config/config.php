<?php
session_start();
const DB_HOST='localhost'; const DB_NAME='rishtewale'; const DB_USER='root'; const DB_PASS='';
const ADMIN_PASSWORD_HASH='$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC4l7z3z5gXh4eJ7u7g6'; // replace via setup or admin password below
function db(){ static $pdo; if(!$pdo){$pdo=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASS,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);} return $pdo; }
function setting($k,$d=''){ try{$s=db()->prepare('SELECT value FROM settings WHERE `key`=?');$s->execute([$k]);$v=$s->fetchColumn();return $v===false?$d:$v;}catch(Throwable $e){return $d;} }
function h($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function admin(){return !empty($_SESSION['admin']);}
function require_admin(){if(!admin()){header('Location: admin/login.php');exit;}}
function telegram_notify($text){$token=setting('telegram_bot_token','');$chat=setting('telegram_chat_id','');if(!$token||!$chat)return false;$url='https://api.telegram.org/bot'.$token.'/sendMessage';$ch=curl_init($url);curl_setopt_array($ch,[CURLOPT_POST=>1,CURLOPT_POSTFIELDS=>['chat_id'=>$chat,'text'=>$text],CURLOPT_RETURNTRANSFER=>1,CURLOPT_TIMEOUT=>10]);$r=curl_exec($ch);curl_close($ch);return $r!==false;}
?>

