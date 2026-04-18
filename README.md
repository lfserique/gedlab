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

## Tutorial Didatico: Ubuntu Linux + Apache + PHP + MySQL

Esta secao apresenta um roteiro completo, do ambiente limpo ate a aplicacao em funcionamento.

### Passo 1. Atualizar o sistema

```bash
sudo apt update
sudo apt upgrade -y
```

### Passo 2. Instalar Apache, PHP, MySQL e utilitarios

```bash
sudo apt install -y apache2 mysql-server git unzip
sudo apt install -y php libapache2-mod-php php-mysql php-cli php-common php-mbstring php-xml php-curl
```

### Passo 3. Verificar servicos

```bash
sudo systemctl status apache2 --no-pager
sudo systemctl status mysql --no-pager
```

Se necessario, inicie e habilite os servicos:

```bash
sudo systemctl start apache2 mysql
sudo systemctl enable apache2 mysql
```

### Passo 4. Verificar versoes instaladas

```bash
php -v
mysql --version
apache2 -v
```

### Passo 5. Verificar extensoes PHP obrigatorias

```bash
php -m | grep -E "pdo_mysql|openssl|fileinfo"
```

Caso alguma extensao nao apareca, instale ou habilite e reinicie o Apache.

### Passo 6. Baixar o projeto no servidor

Escolha um diretorio de publicacao. Neste tutorial, sera usado `/var/www/gedlab`.

```bash
cd /var/www
sudo git clone <URL_DO_SEU_REPOSITORIO> gedlab
cd gedlab
```

### Passo 7. Configurar o arquivo de ambiente da aplicacao

Crie `config.php` a partir do modelo:

```bash
sudo cp config.example.php config.php
sudo nano config.php
```

Ajuste obrigatoriamente:

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`
- `APP_URL`
- `DOC_ENCRYPTION_KEY`

Para gerar uma chave segura de 32 bytes (64 caracteres hex):

```bash
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```

### Passo 8. Endurecer instalacao do MySQL (recomendado)

```bash
sudo mysql_secure_installation
```

Siga o assistente para definir senha de `root` e opcoes de seguranca.

### Passo 9. Criar banco e tabelas da aplicacao

Com `root` do MySQL:

```bash
mysql -u root -p < database.sql
```

### Passo 10. Criar usuarios iniciais da aplicacao

```bash
php seed_users.php
```

Usuarios padrao criados:

- `admin` / `admin123`
- `analista` / `analista123`
- `auditor` / `auditor123`

### Passo 11. Ajustar permissoes de arquivos

```bash
sudo chown -R www-data:www-data /var/www/gedlab
sudo find /var/www/gedlab -type d -exec chmod 755 {} \;
sudo find /var/www/gedlab -type f -exec chmod 644 {} \;
```

### Passo 12. Criar VirtualHost do Apache

Crie o arquivo `/etc/apache2/sites-available/gedlab.conf`:

```bash
sudo nano /etc/apache2/sites-available/gedlab.conf
```

Conteudo sugerido:

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

### Passo 13. Ativar site e mod_rewrite

```bash
sudo a2enmod rewrite
sudo a2ensite gedlab.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2
```

### Passo 14. Configurar resolucao local de nome

No servidor local (ou maquina cliente de teste), edite `/etc/hosts`:

```bash
sudo nano /etc/hosts
```

Adicione:

```text
127.0.0.1 gedlab.local
```

### Passo 15. Testar acesso

Abra no navegador:

```text
http://gedlab.local
```

### Passo 16. Checklist de validacao

- login funcionando
- upload de documento funcionando
- assinatura digital funcionando
- verificacao de assinatura funcionando
- auditoria exibindo cadeia integra

### Passo 17. Recomendacoes para producao

- habilitar HTTPS com certificado TLS (por exemplo, Let's Encrypt)
- usar backup periodico do banco de dados
- alterar senhas padrao imediatamente apos a instalacao
- nao versionar `config.php` com credenciais reais
- preservar a mesma `DOC_ENCRYPTION_KEY` para nao invalidar documentos sigilosos antigos

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

## Possiveis Extensoes Futuras

- administracao de usuarios e perfis
- filtros avancados e pesquisa textual
- relatorios exportaveis de auditoria
- armazenamento externo de documentos
- politicas mais granulares de autorizacao

## Licenca

Este projeto esta licenciado sob os termos da licenca MIT. Consulte o arquivo `LICENSE`.