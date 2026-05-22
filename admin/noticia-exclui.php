<?php

require "../includes/funcoes-noticias.php";
require "../includes/funcoes-controle-de-acesso.php";


//Verificar se o usuario está logado
verificarAcesso();

//Capturando o id da noticia que será DELETADA 
$idNoticia = $_GET['id'];

//Capturando o id do usuário logado na sessão
$idUsuario =$_SESSION['id'];

//Capturando o tipo de usuario logado na sessão 
$tipoUsuario = $_SESSION['tipo'];

//Execultando o delete da noticia no banco de dados
excluirNoticia($conexao, $idNoticia, $idUsuario, $tipoUsuario);
//Redirecionando para a pagina com a lista de noticias
header("location:noticias.php");                   