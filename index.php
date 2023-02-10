<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Migrador de Emails</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>

<body>

  <div class="container">

    <?php

      if (!isset($_POST['submitBtn'])) {

    ?>

    <form class="formularioMigracao" action="" method="post">
      <h1>Migrador de Emails</h1>
      <div class="emailWrapper">
        <h2>Email 1</h2>
        <div class="campo">
          <label for="host1">Host</label>
          <input type="text" id="host1" name="host1">
        </div>
        <div class="campo">
          <label for="porta1">Porta</label>
          <input type="text" id="porta1" name="porta1">
        </div>
        <div class="campo">
          <select name="criptografia1">
            <option value="">Selecione a Criptografia</option>
            <option value="SSL">SSL</option>
            <option value="TLS">TLS</option>
            <option value="notls">Nenhuma</option>
          </select>
        </div>
        <div class="campo">
          <label for="usuario1">Usuário</label>
          <input type="text" id="usuario1" name="usuario1">
        </div>
        <div class="campo">
          <label for="senha1">senha</label>
          <input type="password" id="senha1" name="senha1">
        </div>
      </div>

      <div class="emailWrapper">
        <h2>Email 2</h2>
        <div class="campo">
          <label for="host2">Host</label>
          <input type="text" id="host2" name="host2">
        </div>
        <div class="campo">
          <label for="porta2">Porta</label>
          <input type="text" id="porta2" name="porta2">
        </div>
        <div class="campo">
          <select name="criptografia2">
            <option value="">Selecione a Criptografia</option>
            <option value="SSL">SSL</option>
            <option value="TLS">TLS</option>
            <option value="notls">Nenhuma</option>
          </select>
        </div>
        <div class="campo">
          <label for="usuario2">Usuário</label>
          <input type="text" id="usuario2" name="usuario2">
        </div>
        <div class="campo">
          <label for="senha2">Senha</label>
          <input type="password" id="senha2" name="senha2">
        </div>
      </div>

      <input id="submitBtn" type="submit" value="Migrar" name="submitBtn">
    </form>

    <?php
    
      }

    ?>

  </div>

  <?php

      if (isset($_POST['submitBtn'])) {

        $host1 = $_POST['host1'];
        $porta1 = $_POST['porta1'];
        $criptografia1 = $_POST['criptografia1']; 

        $hostname1 = '{'.$host1.':'.$porta1.'/imap/'.$criptografia1.'}'; //'{mail.example.com.br:993/imap/ssl}'
        $username1 = $_POST['usuario1'];
        $password1 = $_POST['senha1'];



        $host2 = $_POST['host2'];
        $porta2 = $_POST['porta2'];
        $criptografia2 = $_POST['criptografia2']; 

        $hostname2 = '{'.$host2.':'.$porta2.'/imap/'.$criptografia2.'}'; //'{mail.example.com.br:993/imap/ssl}'
        $username2 = $_POST['usuario2'];
        $password2 = $_POST['senha2'];


        // Conecta no email
        $inbox1 = imap_open($hostname1, $username1, $password1) or die('Falha ao se conectar com o email 1: ' . imap_last_error());
        $inbox2 = imap_open($hostname2, $username2, $password2) or die('Falha ao se conectar com o email 2: ' . imap_last_error());
        
        // Pega a lista de diretórios
        $folders1 = imap_list($inbox1, $hostname1, '*');
        $folders2 = imap_list($inbox2, $hostname2, '*');

        $arrayComparacao = array();
        foreach ($folders1 as $folder) {
          $arr = explode("}", $folder);
          $last_string = end($arr);
          $arr2 = explode(".", $last_string);
          $last_string2 = end($arr2);
          $arrayComparacao[] = $last_string2;
        }

        $arrayComparacao2 = array();
        foreach ($folders2 as $folder) {
          $arr = explode("}", $folder);
          $last_string = end($arr);
          $arr2 = explode(".", $last_string);
          $last_string2 = end($arr2);
          $arrayComparacao2[] = $last_string2;
        }

        // Compara diferença de diretórios existentes
        $diff = array_diff($arrayComparacao, $arrayComparacao2);

        // Cria, no destinatário, diretórios que tem no remetente, mas não no destinatário
        foreach ($diff as $dif) {
          $pastaPraCriar = $hostname2.$dif;
          $result = imap_createmailbox($inbox2, imap_utf7_encode($pastaPraCriar));
          imap_subscribe($inbox2, $pastaPraCriar);
          if ($result) {
          } else {
            echo "Erro ao criar diretório: " . imap_last_error() . "\n";
          }
        }

        // Loop para migrar diretório por diretório
        foreach ($arrayComparacao as $folder) {

          if (strstr($folder, "spam") == false && strstr($folder, "Trash") == false){
            // Conecta no email que vai pegar os dados
            $source_imap = imap_open($hostname1."INBOX.".$folder, $username1, $password1);
            
            // Conecta no email para onde vão os dados
            $destination_imap = imap_open($hostname2.$folder, $username2, $password2);
            
            // Pega a lista de mensagens desse email
            $messages = imap_search($source_imap, 'ALL');
            
            // Faz a cópia e envia cada mensagem para o email que queremos migrar
            if ($messages) {
              foreach ($messages as $message_id) {
                  // Pega a mensagem do email que queremos migrar
                  $message = imap_fetchbody($source_imap, $message_id, '');
                  
                  // Envia a mensagem para o email do destinatário
                  imap_append($destination_imap, $hostname2.$folder, $message);
              }
            }

            // Fecha a conexão com os emails
            imap_close($source_imap);
            imap_close($destination_imap);

          }
          
        }

        // Fecha a conexão com os emails
        imap_close($inbox1);
        imap_close($inbox2);

        echo 'Migração Finalizada!';
      }

    ?>
</body>

</html>