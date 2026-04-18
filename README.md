# GEDLab

## Resumo

O GEDLab e uma aplicacao web desenvolvida em PHP para demonstrar um fluxo de gestao eletronica de documentos com mecanismos de autenticacao, criptografia de arquivos sigilosos, assinatura digital baseada em certificados no formato `.p12` ou `.pfx` e trilha de auditoria com encadeamento criptografico de eventos.

O projeto pode ser empregado como base didatica, prototipo academico ou demonstracao tecnica de conceitos relacionados a integridade de dados, rastreabilidade de operacoes e protecao de documentos digitais.

## Objetivo

O objetivo central deste projeto e integrar, em uma unica aplicacao, um conjunto de mecanismos frequentemente discutidos em contextos de seguranca da informacao e sistemas documentais, a saber:

- controle de acesso autenticado
- armazenamento de documentos comuns e sigilosos
- criptografia simetrica para protecao de conteudo sensivel
- assinatura digital com certificados pessoais
- verificacao posterior de autenticidade
- auditoria imutavel por encadeamento de hashes

## Problema Abordado

Em ambientes organizacionais, a gestao de documentos exige nao apenas armazenamento e consulta, mas tambem mecanismos que permitam:

- restringir acesso a conteudos sensiveis
- garantir a integridade dos arquivos armazenados
- identificar quem realizou determinada operacao
- registrar evidencias de visualizacao, upload e assinatura
- permitir validacao posterior de autenticidade e rastreabilidade

O GEDLab foi estruturado para abordar esses aspectos de forma integrada e com implementacao direta em PHP e MySQL.

## Funcionalidades Implementadas

- autenticacao de usuarios
- painel central com listagem de documentos
- upload de arquivos com metadados
- classificacao de documentos sigilosos
- criptografia de documentos sigilosos com AES-256-GCM
- assinatura digital de documentos com certificado `.p12` ou `.pfx`
- verificacao de assinaturas registradas no sistema
- trilha de auditoria com cadeia de hashes para validacao de integridade

## Arquitetura Geral

O sistema foi organizado em arquivos PHP com responsabilidades bem definidas.

- `login.php`: autentica o usuario
- `dashboard.php`: apresenta o painel principal da aplicacao
- `upload.php`: processa o envio de documentos
- `sign_document.php`: realiza a assinatura digital
- `verify_signature.php`: valida assinaturas previamente armazenadas
- `audit.php`: exibe os eventos registrados na cadeia de auditoria
- `blockchain.php`: concentra a logica de hash, verificacao e reparo da cadeia
- `crypto.php`: implementa a criptografia e a descriptografia dos documentos
- `database.sql`: contem a estrutura inicial do banco de dados

## Tecnologias Utilizadas

- PHP 8.2 ou superior
- MySQL 8.x ou MariaDB compativel
- HTML e CSS para a interface
- OpenSSL para operacoes criptograficas e assinatura digital

## Requisitos de Execucao

- PHP 8.2 ou superior
- MySQL 8.x ou MariaDB compativel
- extensoes PHP habilitadas:
  - `pdo_mysql`
  - `openssl`
  - `fileinfo`

## Ambiente Ubuntu Linux

O projeto pode ser executado em distribuicoes Ubuntu com uma pilha baseada em PHP CLI ou com servidor web dedicado. Para fins didaticos e de desenvolvimento local, recomenda-se inicialmente a execucao com o servidor embutido do PHP.

### Dependencias recomendadas no Ubuntu

Em versoes recentes do Ubuntu, instale:

```bash
sudo apt update
sudo apt install -y git php php-cli php-mysql php-openssl php-common php-mbstring php-xml php-curl mariadb-server
```

Observacao:

- em algumas versoes do Ubuntu, `php-openssl` pode ja estar incluido no pacote principal do PHP
- caso utilize MySQL Community Server em vez de MariaDB, os comandos SQL permanecem equivalentes para este projeto

### Verificacao das extensoes PHP

Confirme se as extensoes necessarias estao ativas:

```bash
php -m | grep -E "pdo_mysql|openssl|fileinfo"
```

Se necessario, habilite os modulos instalados e reinicie o servico PHP ou o servidor web correspondente.

## Procedimento de Instalacao

### 1. Obter o codigo-fonte

No Windows PowerShell:

```powershell
git clone <URL_DO_SEU_REPOSITORIO>
cd ged
```

No Ubuntu Linux:

```bash
git clone <URL_DO_SEU_REPOSITORIO>
cd ged
```

### 2. Criar o arquivo de configuracao local

Utilize `config.example.php` como modelo.

No Windows PowerShell:

```powershell
Copy-Item config.example.php config.php
```

No Ubuntu Linux:

```bash
cp config.example.php config.php
```

Em seguida, ajuste os seguintes parametros no arquivo `config.php`:

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`
- `APP_URL`
- `DOC_ENCRYPTION_KEY`

Para gerar uma chave hexadecimal de 32 bytes:

```bash
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```

### 3. Criar a base de dados

Importe o arquivo `database.sql` no servidor MySQL.

Em ambientes Linux:

```bash
sudo systemctl start mariadb
sudo systemctl enable mariadb
mysql -u root -p < database.sql
```

Em ambientes com redirecionamento tradicional:

```bash
mysql -u root -p < database.sql
```

Alternativamente, no PowerShell:

```powershell
mysql -u root -p -e "source C:/caminho/para/o/projeto/database.sql"
```

### 4. Popular usuarios iniciais

No Ubuntu Linux:

```bash
php seed_users.php
```

No Windows PowerShell:

```powershell
php seed_users.php
```

Usuarios criados pelo script de seed:

- `admin` / `admin123`
- `analista` / `analista123`
- `auditor` / `auditor123`

### 5. Verificar extensoes obrigatorias do PHP

No arquivo `php.ini`, habilite:

```ini
extension=pdo_mysql
extension=openssl
extension=fileinfo
```

No Ubuntu, o arquivo costuma estar em caminhos semelhantes a:

- `/etc/php/8.2/cli/php.ini`
- `/etc/php/8.2/apache2/php.ini`

O caminho exato depende da versao instalada e do modo de execucao.

### 6. Executar a aplicacao localmente

No Ubuntu Linux:

```bash
php -S 127.0.0.1:8000 -t .
```

No Windows PowerShell:

```powershell
php -S 127.0.0.1:8000 -t .
```

A aplicacao podera ser acessada em:

```text
http://127.0.0.1:8000
```

### 7. Permissoes e observacoes para Linux

Em cenarios com Apache ou Nginx, verifique:

- permissao de leitura do projeto pelo usuario do servidor web
- configuracao correta do `DocumentRoot` ou do diretio servido
- disponibilidade das extensoes `openssl`, `fileinfo` e `pdo_mysql` no SAPI utilizado pelo servidor

Caso utilize Apache no Ubuntu, uma configuracao inicial comum envolve:

```bash
sudo apt install -y apache2 libapache2-mod-php
sudo systemctl restart apache2
```

Para demonstracoes, entretanto, o servidor embutido do PHP continua sendo a alternativa mais simples.

## Exemplo de Deploy em Ubuntu com Apache

Esta secao apresenta um exemplo simplificado de publicacao da aplicacao em um servidor Ubuntu com Apache.

### 1. Instalar os componentes necessarios

```bash
sudo apt update
sudo apt install -y apache2 php libapache2-mod-php php-mysql php-mbstring php-xml php-curl mariadb-server git
```

### 2. Publicar o codigo no diretorio do servidor

Um caminho comum de implantacao e `/var/www/gedlab`.

```bash
cd /var/www
sudo git clone <URL_DO_SEU_REPOSITORIO> gedlab
cd gedlab
```

### 3. Criar o arquivo de configuracao da aplicacao

```bash
sudo cp config.example.php config.php
sudo nano config.php
```

Defina corretamente:

- credenciais do banco de dados
- `APP_URL`
- `DOC_ENCRYPTION_KEY`

### 4. Criar a base de dados

```bash
sudo systemctl start mariadb
sudo systemctl enable mariadb
mysql -u root -p < database.sql
php seed_users.php
```

### 5. Ajustar permissao de leitura do projeto

```bash
sudo chown -R www-data:www-data /var/www/gedlab
sudo find /var/www/gedlab -type d -exec chmod 755 {} \;
sudo find /var/www/gedlab -type f -exec chmod 644 {} \;
```

### 6. Criar o VirtualHost do Apache

Crie o arquivo `/etc/apache2/sites-available/gedlab.conf` com o conteudo abaixo:

```apache
<VirtualHost *:80>
  ServerName gedlab.local
  DocumentRoot /var/www/gedlab

  <Directory /var/www/gedlab>
    AllowOverride All
    Require all granted
  </Directory>

  ErrorLog ${APACHE_LOG_DIR}/gedlab_error.log
  CustomLog ${APACHE_LOG_DIR}/gedlab_access.log combined
</VirtualHost>
```

### 7. Habilitar o site e reiniciar o Apache

```bash
sudo a2ensite gedlab.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2
```

Se desejar testar localmente com nome amigavel, adicione no arquivo `/etc/hosts` da propria maquina cliente:

```text
127.0.0.1 gedlab.local
```

### 8. Acessar a aplicacao

Depois da configuracao, a aplicacao podera ser acessada por:

```text
http://gedlab.local
```

### Observacoes sobre o deploy com Apache

- em ambiente de producao, recomenda-se uso de HTTPS com certificado TLS
- o arquivo `config.php` deve permanecer fora do versionamento com credenciais reais
- a chave `DOC_ENCRYPTION_KEY` deve ser preservada entre deploys para nao invalidar documentos sigilosos ja armazenados
- caso a versao do PHP instalada seja diferente, o caminho do `php.ini` e dos modulos habilitados pode variar

## Fluxo Operacional

1. O usuario realiza autenticacao no sistema.
2. Um documento pode ser enviado com ou sem classificacao de sigilo.
3. Documentos sigilosos sao criptografados antes do armazenamento.
4. O documento pode ser assinado digitalmente com certificado pessoal.
5. A assinatura armazenada pode ser verificada posteriormente.
6. Eventos relevantes sao registrados na trilha de auditoria.

## Consideracoes de Seguranca

- o arquivo `config.php` nao deve ser publicado com credenciais reais
- certificados `.p12` e `.pfx` utilizados em testes nao devem ser enviados ao repositorio
- a chave `DOC_ENCRYPTION_KEY` deve ser unica e mantida em sigilo
- a alteracao da chave de criptografia inviabiliza a abertura de documentos sigilosos ja armazenados
- recomenda-se a alteracao das senhas padrao apos a instalacao inicial

## Documentacao Complementar

A pasta `docs/` foi criada para concentrar material complementar, como:

- capturas de tela da aplicacao
- diagramas de arquitetura
- relatorios tecnicos
- instrucoes de demonstracao

Consulte [docs/README.md](docs/README.md) para a organizacao sugerida desse material.

## Publicacao no GitHub

Para versionar e publicar o projeto:

```powershell
git init
git branch -M main
git add .
git commit -m "Initial commit"
git remote add origin <URL_DO_SEU_REPOSITORIO>
git push -u origin main
```

## Possiveis Extensoes Futuras

- administracao de usuarios e perfis
- filtros avancados e pesquisa textual
- relatorios exportaveis de auditoria
- armazenamento externo de documentos
- politicas mais granulares de autorizacao

## Licenca

Este projeto esta licenciado sob os termos da licenca MIT. Consulte o arquivo `LICENSE`.