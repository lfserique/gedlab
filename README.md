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

## Procedimento de Instalacao

### 1. Obter o codigo-fonte

```powershell
git clone <URL_DO_SEU_REPOSITORIO>
cd ged
```

### 2. Criar o arquivo de configuracao local

Utilize `config.example.php` como modelo.

```powershell
Copy-Item config.example.php config.php
```

Em seguida, ajuste os seguintes parametros no arquivo `config.php`:

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`
- `APP_URL`
- `DOC_ENCRYPTION_KEY`

Para gerar uma chave hexadecimal de 32 bytes:

```powershell
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```

### 3. Criar a base de dados

Importe o arquivo `database.sql` no servidor MySQL.

```powershell
mysql -u root -p < database.sql
```

Alternativamente, no PowerShell:

```powershell
mysql -u root -p -e "source C:/caminho/para/o/projeto/database.sql"
```

### 4. Popular usuarios iniciais

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

### 6. Executar a aplicacao localmente

```powershell
php -S 127.0.0.1:8000 -t .
```

A aplicacao podera ser acessada em:

```text
http://127.0.0.1:8000
```

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