### Migrador de Emails



###  Dependências que instalei na minha máquina:
###    - PHP 7.4.3;
###      - 1) sudo apt-get update
###      - 2) sudo apt -y install software-properties-common
###      - 3) sudo add-apt-repository ppa:ondrej/php
###      - 4) sudo apt-get update
###      - 5) sudo apt -y install php7.4
###
###    - Apache/2.4.41 (Ubuntu);
###      - 1) sudo apt update
###      - 2) sudo apt install apache2
###
###    - IMAP c-Client Version 2007f.
###      - 1) sudo apt install php-imap
###      - 2) sudo phpenmod imap
###      - 3) sudo systemctl restart apache2
###
###  Como usar:
###    - Ao iniciar o arquivo index.php em um sevidor com as dependências instaladas,
###    é só colocar os dados dos 2 emails no formulário.
###    O email 1 é o email que está sendo migrado (email remetente)
###    e o email 2 é o email para onde vai a migração (email destinatário).
###
###  Como funciona:
###    - O código é baseado na lib IMAP e usa as seguintes funções:
###      - imap_open() -> Acessa o email;
###      - imap_list() -> Mostra todos os diretórios existentes;
###      - imap_createmailbox() -> Cria os diretórios que existem no email remetente, mas não no email destinatário;
###      - imap_subscribe() -> Deixa visível o diretório criado (por padrão vem oculto);
###      - imap_search() -> Lista todas as mensagens do email;
###      - imap_fetchbody() -> Pega as mensagens do email remetente;
###      - imap_append() -> Envia as mensagens para o email destinatário;
###      - imap_close() -> Encerra a conexão com o email;
###      - imap_last_error() -> Mostra ao usuário o erro, caso tenha.
