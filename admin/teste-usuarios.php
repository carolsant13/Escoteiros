<?php
// admin/teste-usuarios.php
session_start();

// Simulação de usuário logado
$nomeUsuario = "Administrador";
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body{
            background:#f5f5f5;
            color:#1f2937;
        }

        /* TOPO */
        .topo{
            width:100%;
            background:#0d4d2b;
            padding:18px 40px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            border-bottom:4px solid #d4a017;
        }

        .logo{
            color:white;
        }

        .logo h1{
            font-size:28px;
        }

        .logo p{
            font-size:14px;
            opacity:.8;
        }

        .usuario{
            display:flex;
            align-items:center;
            gap:15px;
            color:white;
        }

        .btn-sair{
            padding:10px 18px;
            border:1px solid rgba(255,255,255,.3);
            border-radius:10px;
            text-decoration:none;
            color:white;
            transition:.3s;
        }

        .btn-sair:hover{
            background:white;
            color:#0d4d2b;
        }

        /* CONTEUDO */
        .container{
            width:90%;
            max-width:1200px;
            margin:50px auto;
        }

        .titulo{
            margin-bottom:35px;
        }

        .titulo h2{
            font-size:42px;
            color:#0d4d2b;
            margin-bottom:10px;
        }

        .titulo p{
            color:#6b7280;
            font-size:18px;
        }

        /* CARDS */
        .cards{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(300px,1fr));
            gap:25px;
        }

        .card{
            background:white;
            border-radius:18px;
            padding:30px;
            box-shadow:0 5px 20px rgba(0,0,0,.08);
            border-left:6px solid #0d4d2b;
            transition:.3s;
        }

        .card:hover{
            transform:translateY(-5px);
        }

        .icone{
            font-size:55px;
            margin-bottom:20px;
        }

        .card h3{
            font-size:28px;
            margin-bottom:10px;
            color:#0d4d2b;
        }

        .card p{
            color:#6b7280;
            margin-bottom:25px;
            line-height:1.5;
        }

        .btn{
            display:inline-block;
            padding:12px 20px;
            border-radius:10px;
            text-decoration:none;
            font-weight:bold;
            transition:.3s;
        }

        .btn-add{
            background:#0d4d2b;
            color:white;
        }

        .btn-add:hover{
            background:#146c3d;
        }

        .btn-lista{
            background:#d4a017;
            color:white;
        }

        .btn-lista:hover{
            background:#b78a12;
        }

        .btn-delete{
            background:#b91c1c;
            color:white;
        }

        .btn-delete:hover{
            background:#991b1b;
        }

        .voltar{
            display:inline-block;
            margin-top:40px;
            color:#0d4d2b;
            text-decoration:none;
            font-weight:bold;
        }

        @media(max-width:700px){

            .topo{
                padding:20px;
                flex-direction:column;
                gap:20px;
                text-align:center;
            }

            .titulo h2{
                font-size:32px;
            }
        }
    </style>
</head>

<body>

    <header class="topo">
        <div class="logo">
            <h1>7º Grupo de Escoteiros Minuano</h1>
            <p>Área Restrita</p>
        </div>

        <div class="usuario">
            <span>👤 <?= $nomeUsuario ?></span>
            <a href="../index.php" class="btn-sair">Sair</a>
        </div>
    </header>

    <main class="container">

        <div class="titulo">
            <h2>Gerenciar Usuários 👋</h2>
            <p>Teste os códigos antigos de cadastro, edição e exclusão.</p>
        </div>

        <div class="cards">

            <div class="card">
                <div class="icone">➕</div>

                <h3>Adicionar Usuário</h3>

                <p>
                    Testar a tela de cadastro de novos usuários.
                </p>

                <a href="usuario-insere.php" class="btn btn-add">
                    Abrir Tela
                </a>
            </div>

            <div class="card">
                <div class="icone">📋</div>

                <h3>Listar Usuários</h3>

                <p>
                    Visualizar usuários cadastrados no sistema.
                </p>

                <a href="usuarios.php" class="btn btn-lista">
                    Abrir Lista
                </a>
            </div>

            <div class="card">
                <div class="icone">🗑️</div>

                <h3>Excluir Usuário</h3>

                <p>
                    Testar exclusão de usuários existentes.
                </p>

                <a href="usuario-exclui.php" class="btn btn-delete">
                    Abrir Tela
                </a>
            </div>

        </div>

        <a href="../index.php" class="voltar">
            ← Voltar para o site
        </a>

    </main>

</body>

</html>