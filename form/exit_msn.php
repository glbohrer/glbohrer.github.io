
<?

    # alterar a variavel abaixo colocando o seu email

    $destinatario = "seuemail@seudominio.com.br";

    $nome = $_REQUEST['nome'];
    $email = $_REQUEST['email'];
    $mensagem = $_REQUEST['mensagem'];
    $assunto = $_REQUEST['assunto'];

     // monta o e-mail na variavel $body

    $body = "===================================" . "\n";
    $body = $body . "FALE CONOSCO - TESTE COMPROVATIVO" . "\n";
    $body = $body . "===================================" . "\n\n";
    $body = $body . "Nome: " . $nome . "\n";
    $body = $body . "Email: " . $email . "\n";
    $body = $body . "Mensagem: " . $mensagem . "\n\n";
    $body = $body . "===================================" . "\n";

    // envia o email
    mail($destinatario, $assunto , $body, "From: $email\r\n");

    // redireciona para a página de obrigado
    header("location:obrigado.htm");


    ?>
