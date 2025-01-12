# Projeto Despesas de Cartão por Usuário 

Este projeto é uma aplicação Laravel para gerenciamento de despesas com autenticação de usuário utilizando Sanctum. O sistema permite criar, ler, atualizar e excluir despesas e inclui notificações para o usuário quando uma nova despesa é criada.

Este projeto foi desenvolvido em uma aplicação Laravel para gerenciamento e cadastro de despesas de um usuário a partir de um cartão. O sistema permite criar, atualizar, excluir, listar e realizar envio de notificacaoes via email dos dados dos usuários cadastrados, dados de cartões inseridos e informações de despesas lançadas.

## Tecnologias e Ferramentas 💡

- **PHP:** 8.2.12
- **Laravel:** 10.48.22
- **Composer:** 2.7.7
- **Mailtrap:** Para teste de envio de e-mails
- **Insomnia:** Para testar as rotas da API
- **Swagger:** Para a documentação da API

## Requisitos

- PHP 8.2.12
- Composer 2.7.7

## Configuração do Projeto 🛠️

1. **Clonar o Repositório:**

   Clone o repositório do projeto e entre no diretório do projeto.
   `git clone https://github.com/Ricardov7070/despesas_cartao.git`

2. **Instalar as Dependências:**

   Instale as dependências do projeto usando o Composer.
   `Composer install`
   `Composer require phpmailer/phpmailer`

3. **Configurar o Ambiente:**

   Renomeie o arquivo `.env.example` para `.env` e ajuste as variáveis de ambiente conforme necessário, incluindo configurações para o banco de dados e Mailtrap.

4. **Gerar a Chave de Aplicação:**

   Gere uma chave de aplicação Laravel para configurar a criptografia.
   `php artisan key:generate`

5. **Limpar os Caches do Laravel:**

   Limpe os caches do framework para garantir que todas as configurações sejam aplicadas corretamente.
   `php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    `

6. **Rodar as Migrações:**

   Execute as migrações para criar as tabelas no banco de dados.
    `php artisan migrate`

   Não esqueça de realizar as configurações do banco de dados desejado no arquivo .env antes de executar o comando!
    
7. **Testar as Rotas da API:**
    
   Utilize o Insomnia para testar as rotas da API. As rotas principais incluem:

   ** Gerenciamento de Usuário:
   - **GET** `/api/users/` - Lista todos os usuários cadastrados no sistema.
   - **GET** `/api/users/{id_user}` - Lista os dados somente do usuário selecionado.
   - **POST** `/api/users/` - Insere na base de dados os registros do novo usuário.
   - **PUT** `/api/users/{id_user}` - Atualiza os dados somente do usuário selecionado já cadastrado.
   - **DELETE** `/api/users/{id_user}` - Exclui na base de dados os registros do usuário selecionado.


   ** Gerenciamento de Cartão:
   - **GET** `/api/cards_users/` - Lista todos os cartões cadastrados no sistema.
   - **GET** `/api/cards_users/{id_user}` - Lista todos os cartões, somente do usuário selecionado.
   - **POST** `/api/cards_users/` - Insere na base de dados os registros do novo cartão.
   - **PUT** `/api/cards_users/{id_card}` - Atualiza os dados somente do cartão selecionado já cadastrado.
   - **DELETE** `/api/cards_users/{id_card}` - Exclui na base de dados os registros do cartão selecionado.


   ** Gerenciamento de Despesa:
   - **GET** `/api/expenses_users/` - Lista todas as despesas cadastradas no sistema.
   - **GET** `/api/expenses_users/{id_user}` - Lista todas as despesas, somente do usuário selecionado.
   - **POST** `/api/expenses_users/` - Insere na base de dados os registros da nova despesa.
   - **PUT** `/api/expenses_users/{id_expense}` - Atualiza os dados somente da despesa selecionada já cadastrada.
   - **DELETE** `/api/expenses_users/{id_expense}` - Exclui na base de dados os registros da despesa selecionada.

   ** Categoria de Usuários:
   - **GET** `/api/type_users/` - Lista todas as categorias de usuários cadastradas.

9. **Parâmetros:**

  As rotas abaixo recebem os seguintes parâmetros:

- **POST** `/api/users/`:
   'name' => Nome do Usuário;
   'email' => Email do Usuário;
   'password' => Senha do usuário;
   'type' => Id de Identificação do Tipo de Categoria do Usuário;

- **PUT** `/api/users/{id_user}`:
   'name' => Nome do Usuário;
   'email' => Email do Usuário;
   'password' => Senha do usuário;
   'type' => Tipo de Categoria do Usuário;

- **POST** `/api/cards_users/`:
   'number' => Número do Cartão;
   'balance' => Saldo do Cartão;
   'user' => Id de Identificação do Usuário;

- **PUT** `/api/cards_users/{id_card}`;
   'number' => Número do Cartão;
   'balance' => Saldo do Cartão;

- **POST** `/api/expenses_users/`:
   'id_card' => Id de Identificação do Cartão;
   'description' => Descrição da Despesa;
   'expense' => Valor da Despesa;

- **PUT** `/api/expenses_users/{id_expense}`:
   'id_card' => Id de Identificação do Cartão;
   'description' => Descrição da Despesa;
   'expense' => Valor da Despesa;

## Emails ✉️

O projeto utiliza uma funcionalidade de envio de emails para cada interação que o usuário solicitar como:
 - Novas despesas criadas;
 - Atualização de despesas já existentes;
 - Exclusão de despesas dos usuários.

- **Email:** Os emails são enviados utilizando um serviço do Laravel (PHPMailer) e pode ser configurado para usar o Mailtrap durante o desenvolvimento.

Não esqueça de realizar as configurações do seu provedor de email SMTP no arquivo .env antes de usar essa funcionalidade!

## Roles e Permissões 🤝

O projeto inclui dois tipos de categoria de usuários:

- **Administrator:** Possui permissão para visualizar suas informações e também a de todos os usuários cadastrados. Esta classificação recebe todos os emails enviados do sistema. 
- **Standard:** Pode visualizar somente suas informações cadastradas no sistema.

## Ferramentas 🪛

- **Mailtrap:** Usado para testar o envio de e-mails durante o desenvolvimento.
- **Insomnia:** Utilizado para testar e documentar as rotas da API.
- **Swagger:** Utilizado para documentar as rotas da APIResources.
  `php artisan l5-swagger:generate`

## Contribuição 🤲

Contribuições são bem-vindas! Se você encontrar problemas ou tiver sugestões, sinta-se à vontade para abrir uma issue ou enviar um pull request.

## Licença

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
