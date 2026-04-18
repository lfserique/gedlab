# GEDLab

## 📘 Resumo

O GEDLab é uma aplicação web desenvolvida em PHP para demonstrar um fluxo de gestão eletrônica de documentos com mecanismos de autenticação, criptografia de arquivos sigilosos, assinatura digital baseada em certificados no formato `.p12` ou `.pfx` e trilha de auditoria com encadeamento criptográfico de eventos.

O projeto pode ser empregado como base didática, protótipo acadêmico ou demonstração técnica de conceitos relacionados à integridade de dados, rastreabilidade de operações e proteção de documentos digitais.

## 🎯 Objetivo

O objetivo central deste projeto é integrar, em uma única aplicação, um conjunto de mecanismos frequentemente discutidos em contextos de segurança da informação e sistemas documentais, a saber:

- controle de acesso autenticado
- armazenamento de documentos comuns e sigilosos
- criptografia simétrica para proteção de conteúdo sensível
- assinatura digital com certificados pessoais
- verificação posterior de autenticidade
- auditoria imutável por encadeamento de hashes

## 🧩 Problema Abordado

Em ambientes organizacionais, a gestão de documentos exige não apenas armazenamento e consulta, mas também mecanismos que permitam:

- restringir acesso a conteúdos sensíveis
- garantir a integridade dos arquivos armazenados
- identificar quem realizou determinada operação
- registrar evidências de visualização, upload e assinatura
- permitir validação posterior de autenticidade e rastreabilidade

O GEDLab foi estruturado para abordar esses aspectos de forma integrada e com implementação direta em PHP e MySQL.

## ✅ Funcionalidades Implementadas

- autenticação de usuários
- painel central com listagem de documentos
- upload de arquivos com metadados
- classificação de documentos sigilosos
- criptografia de documentos sigilosos com AES-256-GCM
- assinatura digital de documentos com certificado `.p12` ou `.pfx`
- verificação de assinaturas registradas no sistema
- trilha de auditoria com cadeia de hashes para validação de integridade

## 🏗️ Arquitetura Geral

O sistema foi organizado em arquivos PHP com responsabilidades bem definidas.

- `login.php`: autentica o usuário
- `dashboard.php`: apresenta o painel principal da aplicação
- `upload.php`: processa o envio de documentos
- `sign_document.php`: realiza a assinatura digital
- `verify_signature.php`: valida assinaturas previamente armazenadas
- `audit.php`: exibe os eventos registrados na cadeia de auditoria
- `blockchain.php`: concentra a lógica de hash, verificação e reparo da cadeia
- `crypto.php`: implementa a criptografia e a descriptografia dos documentos
- `database.sql`: contém a estrutura inicial do banco de dados

## 🛠️ Tecnologias Utilizadas

- PHP 8.2 ou superior
- MySQL 8.x ou MariaDB compatível
- HTML e CSS para a interface
- OpenSSL para operações criptográficas e assinatura digital

## 📋 Requisitos de Execução

- PHP 8.2 ou superior
- MySQL 8.x ou MariaDB compatível
- extensões PHP habilitadas:
  - `pdo_mysql`
  - `openssl`
  - `fileinfo`

## 🐧 Tutorial de Instalação: Ubuntu Linux + Apache + PHP + MySQL

Esta seção apresenta um roteiro completo, do ambiente limpo até a aplicação em funcionamento.

### 1. Atualizar o sistema

```bash
sudo apt update
sudo apt upgrade -y
```

### 2. Instalar Apache, PHP, MySQL e utilitários

```bash
sudo apt install -y apache2 mysql-server git unzip
sudo apt install -y php libapache2-mod-php php-mysql php-cli php-common php-mbstring php-xml php-curl
```

### 3. Verificar serviços

```bash
sudo systemctl status apache2
sudo systemctl status mysql
```

Se necessário, inicie e habilite os serviços:

```bash
sudo systemctl start apache2 mysql
sudo systemctl enable apache2 mysql
```

### 4. Verificar versões instaladas

```bash
php -v
mysql --version
apache2 -v
```

### 5. Verificar extensões PHP obrigatórias

```bash
php -m | grep -E "pdo_mysql|openssl|fileinfo"
```

Caso alguma extensão não apareça, instale ou habilite e reinicie o Apache.

### 6. Baixar o projeto no servidor

Escolha um diretório de publicação. Neste tutorial, será usado `/var/www/gedlab`.

```bash
cd /var/www
sudo git clone https://github.com/lfserique/gedlab gedlab
cd gedlab
```

### 7. Configurar o arquivo de ambiente da aplicação

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

Significado de cada parâmetro:

- `DB_HOST`: endereço do servidor MySQL. Em instalação local no mesmo servidor da aplicação, normalmente `127.0.0.1`.
- `DB_NAME`: nome do banco de dados da aplicação. Neste projeto, o padrão utilizado é `gedlab`.
- `DB_USER`: usuário MySQL que a aplicação vai usar para conectar no banco (exemplo: `geduser`).
- `DB_PASS`: senha do usuário informado em `DB_USER`.
- `APP_URL`: URL base da aplicação. Deve refletir como o sistema será acessado no navegador (exemplo: `http://gedlab.local` ou `http://SEU_IP`).
- `DOC_ENCRYPTION_KEY`: chave de criptografia dos documentos sigilosos, em hexadecimal, com 64 caracteres (32 bytes).

Exemplo didático de configuração:

```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'gedlab');
define('DB_USER', 'geduser');
define('DB_PASS', 'Z9#vL2!qR7@pX4$mT8&c');

define('APP_URL', 'http://gedlab.local');
define('DOC_ENCRYPTION_KEY', hex2bin('0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef'));
```

Importante sobre a chave `DOC_ENCRYPTION_KEY`:

- não altere a chave após iniciar o uso em produção, pois documentos sigilosos antigos podem se tornar ilegíveis
- mantenha essa chave em sigilo e fora de versionamento público

Para gerar uma chave segura de 32 bytes (64 caracteres hex):

```bash
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```

### 8. Fortalecer a instalação do MySQL (recomendado)

```bash
sudo mysql_secure_installation
```

Siga o assistente para definir senha de `root` e opções de segurança.

### 9. Criar banco e tabelas da aplicação

Com `root` do MySQL:

```bash
mysql -u root -p < database.sql
```

### 10. Criar usuários iniciais da aplicação

```bash
php seed_users.php
```

Usuários padrão criados:

- `admin` / `admin123`
- `analista` / `analista123`
- `auditor` / `auditor123`

### 11. Ajustar permissões de arquivos

```bash
sudo chown -R www-data:www-data /var/www/gedlab
sudo find /var/www/gedlab -type d -exec chmod 755 {} \;
sudo find /var/www/gedlab -type f -exec chmod 644 {} \;
```

### 12. Criar VirtualHost do Apache

Crie o arquivo `/etc/apache2/sites-available/gedlab.conf`:

```bash
sudo nano /etc/apache2/sites-available/gedlab.conf
```

Conteúdo sugerido:

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

### 13. Ativar site e mod_rewrite

```bash
sudo a2enmod rewrite
sudo a2ensite gedlab.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2
```

### 14. Configurar resolução local de nome

No servidor local (ou máquina cliente de teste), edite `/etc/hosts`:

```bash
sudo nano /etc/hosts
```

Adicione:

```text
127.0.0.1 gedlab.local
```

### 15. Testar acesso

Abra no navegador:

```text
http://IP-DO-SERVIDOR
```

### 16. ✅ Checklist rápido de validação (sem upload)

- login funcionando com usuário válido
- acesso ao painel principal (`dashboard.php`)
- acesso à área de auditoria (`audit.php`)
- acesso à tela de assinatura (`sign_document.php`)
- acesso à tela de verificação (`verify_signature.php`)

### 17. 🚀 Recomendações para produção

- habilitar HTTPS com certificado TLS (por exemplo, Let's Encrypt)
- usar backup periódico do banco de dados
- alterar senhas padrão imediatamente após a instalação
- não versionar `config.php` com credenciais reais
- preservar a mesma `DOC_ENCRYPTION_KEY` para não invalidar documentos sigilosos antigos

## 🔄 Fluxo Operacional

1. O usuário realiza autenticação no sistema.
2. Um documento pode ser enviado com ou sem classificação de sigilo.
3. Documentos sigilosos são criptografados antes do armazenamento.
4. O documento pode ser assinado digitalmente com certificado pessoal.
5. A assinatura armazenada pode ser verificada posteriormente.
6. Eventos relevantes são registrados na trilha de auditoria.

## 🧪 Atividades

Os testes abaixo podem ser usados como roteiro de validação funcional e de segurança em contexto acadêmico.

### Tarefa 1. 🔐 Geração de chave pessoal em `.p12` com OpenSSL

Objetivo: gerar um certificado de teste para assinatura digital sem erro de permissão em `/var/www/gedlab`.

Passo 1. Ir para o diretório pessoal do usuário Ubuntu:

```bash
cd ~
mkdir -p gedlab-cert
cd gedlab-cert
```

Passo 2. Gerar chave privada, certificado e arquivo `.p12`:

```bash
openssl genrsa -out chave_privada.pem 2048
openssl req -new -x509 -key chave_privada.pem -out certificado.pem -days 365 -subj "/C=BR/ST=SP/L=SaoPaulo/O=GEDLab/OU=Teste/CN=UsuarioTeste"
openssl pkcs12 -export -out usuario_teste.p12 -inkey chave_privada.pem -in certificado.pem -name "Usuario Teste"
```

Observação: o último comando solicitará uma senha para proteger o arquivo `.p12`.

Passo 3. Copiar o `.p12` para a pasta do projeto (se necessário):

```bash
cp ~/gedlab-cert/usuario_teste.p12 /var/www/gedlab/
```

Se houver erro de permissão nesse passo, use:

```bash
sudo cp ~/gedlab-cert/usuario_teste.p12 /var/www/gedlab/
```

### Tarefa 2. 📄 Envio, assinatura e criptografia de documentos

Objetivo: verificar o fluxo completo da aplicação para documentos comuns e sigilosos.

Procedimento sugerido:

1. Fazer login com usuário válido.
2. Enviar um documento sem marcar sigilo e confirmar exibição no painel.
3. Enviar outro documento marcando a opção de sigilo.
4. Assinar os dois documentos com certificado `.p12`.
5. Validar assinatura na tela de verificação.
6. Conferir os eventos na tela de auditoria.

Resultados esperados:

- upload concluído nos dois cenários
- documento sigiloso marcado com indicador de sigilo
- assinatura registrada como válida
- eventos de upload, assinatura e visualização presentes na auditoria

### Tarefa 3. 🧬 Corrupção controlada de registro da auditoria

Objetivo: demonstrar que alterações indevidas em registros comprometem a integridade da cadeia.

Passo 1. Anotar o valor original (exemplo com ID 1):

```bash
mysql -u root -p -D gedlab -e "SELECT id, event_time FROM audit_chain WHERE id = 1;"
```

Passo 2. Corromper o horário de forma controlada:

```bash
mysql -u root -p -D gedlab -e "UPDATE audit_chain SET event_time = '2030-01-01 00:00:00' WHERE id = 1;"
```

Passo 3. Abrir a tela de auditoria e verificar o resultado esperado:

- status da cadeia deve indicar inconsistência/corrupção

Passo 4. Restaurar o valor original anotado no Passo 1:

```bash
mysql -u root -p -D gedlab -e "UPDATE audit_chain SET event_time = 'VALOR_ORIGINAL_AQUI' WHERE id = 1;"
```

Importante: em ambiente de teste, depois da restauração, execute o procedimento de reparo da cadeia já implementado no projeto para normalizar os hashes registrados.

### Tarefa 4. 🌐 Implementação de HTTPS com Let's Encrypt

Objetivo: reforçar a segurança do acesso web com criptografia TLS.

Pré-requisitos obrigatórios:

- VM em nuvem com IP público (AWS, Azure, GCP, OCI, etc.)
- domínio apontando para o IP público da VM (pode usar No-IP)
- Apache ativo
- portas 80 e 443 liberadas no firewall/security group

Comandos de exemplo no Ubuntu:

```bash
sudo apt update
sudo apt install -y certbot python3-certbot-apache
sudo certbot --apache -d seu-dominio.com -d www.seu-dominio.com
```

Renovação automática (verificação):

```bash
sudo certbot renew --dry-run
```

Resultados esperados:

- acesso via `https://seu-dominio.com`
- redirecionamento HTTP -> HTTPS ativo
- certificado válido emitido por Let's Encrypt

## 💻 Transferir o `.p12` para estações Windows sem WinSCP

Se os alunos não tiverem WinSCP, é possível usar o comando `scp` no PowerShell.

Exemplo (executado na estação Windows):

```powershell
scp -i C:\caminho\para\chave-da-vm.pem usuario@IP_DA_VM:~/gedlab-cert/usuario_teste.p12 C:\Users\Public\Downloads\usuario_teste.p12
```

Observações:

- substitua `usuario` pelo usuário da VM (exemplo: `ubuntu`)
- substitua `IP_DA_VM` pelo IP público da VM
- ajuste o caminho da chave privada `.pem`
- no Windows 10/11, o `scp` já vem no cliente OpenSSH na maioria dos casos

## 🔒 Considerações de Segurança

- o arquivo `config.php` não deve ser publicado com credenciais reais
- certificados `.p12` e `.pfx` utilizados em testes não devem ser enviados ao repositório
- a chave `DOC_ENCRYPTION_KEY` deve ser única e mantida em sigilo
- a alteração da chave de criptografia inviabiliza a abertura de documentos sigilosos já armazenados
- recomenda-se a alteração das senhas padrão após a instalação inicial

## 📚 Documentação Complementar

A pasta `docs/` foi criada para concentrar material complementar, como:

- capturas de tela da aplicação
- diagramas de arquitetura
- relatórios técnicos
- instruções de demonstração

Consulte [docs/README.md](docs/README.md) para a organização sugerida desse material.

## 🔭 Possíveis Extensões Futuras

- administração de usuários e perfis
- filtros avançados e pesquisa textual
- relatórios exportáveis de auditoria
- armazenamento externo de documentos
- políticas mais granulares de autorização

## 📄 Licença

Este projeto está licenciado sob os termos da licença MIT. Consulte o arquivo `LICENSE`.
