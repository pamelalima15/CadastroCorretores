<?php
// Conexão com o banco de dados
$servername = "localhost"; // Altere para o seu servidor
$username = "root"; // Altere para o seu usuário
$password = "Pamela100@"; // Altere para sua senha
$dbname = "master"; // Altere para o nome do seu banco de dados

// Criação da conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificação de conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Variáveis para armazenar erros, sucesso e os dados cadastrados
$erro = "";
$sucesso = "";
$corretores = [];
$corretorEditando = null;

// Verifica se o formulário foi enviado para inserir dados
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao']) && $_POST['acao'] == 'inserir') {
    $cpf = $_POST['cpf'];
    $nome = $_POST['nome'];
    $creci = $_POST['creci'];

    if (strlen($cpf) != 11) {
        $erro = "O CPF é inválido.";
    } else if (strlen($nome) < 2) {
        $erro = "O nome não pode ser menor que 2 caracteres.";
    } else if (strlen($creci) < 2) {
        $erro = "O CRECI é inválido.";
    } else {
        // Inserção no banco de dados
        $sql = "INSERT INTO corretores (cpf, nome, creci) VALUES ('$cpf', '$nome', '$creci')";
        if ($conn->query($sql) === TRUE) {
            $sucesso = "Corretor cadastrado com sucesso!";
        } else {
            $erro = "Erro: " . $conn->error;
        }
    }
}

// Verifica se o formulário foi enviado para alterar dados
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['acao']) && $_POST['acao'] == 'alterar') {
    $id = $_POST['id'];
    $cpf = $_POST['cpf'];
    $nome = $_POST['nome'];
    $creci = $_POST['creci'];

    if (strlen($cpf) != 11) {
        $erro = "O CPF é inválido.";
    } else if (strlen($nome) < 2) {
        $erro = "O nome não pode ser menor que 2 caracteres.";
    } else if (strlen($creci) < 2) {
        $erro = "O CRECI o é inválido.";
    } else {
        // Alteração no banco de dados
        $sql = "UPDATE corretores SET cpf='$cpf', nome='$nome', creci='$creci' WHERE id='$id'";
        if ($conn->query($sql) === TRUE) {
            $sucesso = "Corretor alterado com sucesso!";
        } else {
            $erro = "Erro: " . $conn->error;
        }
    }
}

// Verifica se o pedido é para excluir um registro
if (isset($_GET['acao']) && $_GET['acao'] == 'excluir' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Excluir do banco de dados
    $sql = "DELETE FROM corretores WHERE id='$id'";
    if ($conn->query($sql) === TRUE) {
        $sucesso = "Corretor excluído com sucesso!";
    } else {
        $erro = "Erro: " . $conn->error;
    }
}

// Buscar os dados cadastrados no banco de dados
$sql = "SELECT id, cpf, nome, creci FROM corretores";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Armazenando os dados em um array
    while($row = $result->fetch_assoc()) {
        $corretores[] = $row;
    }
}

// Verifica se existe um corretor para edição
if (isset($_GET['acao']) && $_GET['acao'] == 'editar' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM corretores WHERE id='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Obtém o corretor para edição
        $corretorEditando = $result->fetch_assoc();
    }
}

$conn->close();
?>

<style type="text/css">
    input {
        border-radius: 5px;
    }
    button {
        border-radius: 5px;
    }
    th{
        border-radius: 5px;
    }
    tr{
        border-radius: 5px;
    }
    td {
        border-radius: 5px;
    }
    table {
        border-radius: 5px;
	border: 1px solid; 
	width: 620px;
    }
    #divCadastro {
	border-width:2px;
	border-style:solid;
	border-color:black;
        border-radius: 5px;
	width: 650px;	
    }
</style>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Corretor</title>
</head>
<body>
<div align="center" width="500px">
    <h3>Cadastro de Corretor</h3>

    <!-- Formulário de inserção ou alteração de dados -->
    <form method="POST">
        <input type="hidden" name="acao" value="<?php echo ($corretorEditando ? 'alterar' : 'inserir'); ?>">
        <?php if ($corretorEditando): ?>
            <input type="hidden" name="id" value="<?php echo $corretorEditando['id']; ?>">
        <?php endif; ?>
        <div>
            <input type="number" style="width: 176px; margin-bottom: 7px;" id="cpf" name="cpf" placeholder="Digite seu CPF" required value="<?php echo $corretorEditando['cpf'] ?? ''; ?>">
            <input type="number" style="width: 176px; margin-bottom: 7px;" id="creci" name="creci" placeholder="Digite seu Creci" required value="<?php echo $corretorEditando['creci'] ?? ''; ?>">
        </div>
        <div>
            <input type="text" placeholder="Digite seu nome" id="nome" name="nome" style="width: 355px; margin-bottom:20px;" required value="<?php echo $corretorEditando['nome'] ?? ''; ?>">
        </div>
        <div>
            <button type="submit" style="width: 356px; background-color:rgb(44, 43, 43);color: azure;"><?php echo ($corretorEditando ? 'Salvar' : 'Enviar'); ?></button>
        </div>
    </form>

    <!-- Exibindo erros ou sucesso -->
    <?php if ($erro): ?>
        <p style="color: red;"><?php echo $erro; ?></p>
    <?php endif; ?>
    <?php if ($sucesso): ?>
        <p style="color: green;"><?php echo $sucesso; ?></p>
    <?php endif; ?>
</br>
<div id="divCadastro">
    <h3>Lista de Corretores Cadastrados</h3>

    <!-- Exibindo os corretors cadastrados -->
    <?php if (count($corretores) > 0): ?>
	<table border="1">
            <tr>
                <th>ID</th>
                <th>CPF</th>
                <th>Creci</th>
                <th>Nome</th>
                <th>Operação</th>
            </tr>
            <?php foreach ($corretores as $corretor): ?>
                <tr>
                    <td><?php echo $corretor['id']; ?></td>
                    <td><?php echo $corretor['cpf']; ?></td>
                    <td><?php echo $corretor['creci']; ?></td>
                    <td><?php echo $corretor['nome']; ?></td>
                    <td align="center">
                        <!-- Botão de Alterar -->
                        <a href="CadastroCorretores.php?acao=editar&id=<?php echo $corretor['id']; ?>">
                            <button type="button">Editar</button>
                        </a>
			&nbsp;&nbsp;
                        <!-- Botão de Excluir -->
                        <a href="CadastroCorretores.php?acao=excluir&id=<?php echo $corretor['id']; ?>" onclick="return confirm('Deseja excluír esté corretor?');">
                            <button type="button">Excluir</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Nenhum corretor cadastrado.</p>
    <?php endif; ?>
</br>
</div> 
</div>
</body>
</html>