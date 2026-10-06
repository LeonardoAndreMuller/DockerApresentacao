# Pedidos — Apresentação sobre Docker

Projeto criado para uma **apresentação sobre a ferramenta Docker**. A aplicação em si é simples de propósito: um pequeno sistema de **cadastro de produtos e pedidos** feito em Laravel. O foco está na infraestrutura: com um único comando, o Docker Compose sobe todo o ambiente de desenvolvimento (servidor web, banco de dados, build do front-end e ferramentas de administração) sem instalar PHP, Node ou PostgreSQL na máquina.

## O que o `compose.yaml` sobe

O projeto do Compose se chama `pedidos` e tem cinco serviços:

| Serviço     | Imagem                       | Porta(s)        | Para que serve |
|-------------|------------------------------|-----------------|----------------|
| `app`       | `pedidos-app:dev` (construída pelo [Dockerfile](Dockerfile)) | `8000` → `80` | Aplicação Laravel servida pelo Apache com PHP 8.5 (mod_php). |
| `postgres`  | `postgres:17-alpine`         | `5432`          | Banco de dados PostgreSQL. Os dados ficam no volume `pgdata`. |
| `vite`      | `node:24-slim`               | `5173`          | Servidor de desenvolvimento do Vite: compila CSS/JS (Tailwind) e recarrega o navegador sozinho a cada alteração. |
| `pgadmin`   | `dpage/pgadmin4`             | `5050` → `80`   | Interface web do PostgreSQL, com o servidor "Pedidos (Docker)" já cadastrado. |
| `portainer` | `portainer/portainer-ce:lts` | `9000`, `9443`  | Painel web para ver e gerenciar containers, imagens, volumes e redes. |

### Conceitos de Docker demonstrados

- **Imagem própria com `Dockerfile`**: o serviço `app` parte da imagem oficial `php:8.5-apache`, instala as extensões do PostgreSQL, copia o Composer de outra imagem (`COPY --from=composer:2`) e configura o Apache para servir a pasta `public/` ([docker/app/vhost.conf](docker/app/vhost.conf)).
- **Build args**: `UID` e `GID` são repassados para o build para que o usuário do Apache tenha o mesmo ID do usuário da máquina. Assim os arquivos criados pelo container continuam sendo seus.
- **Bind mount**: o código do projeto é montado em `/var/www/html`. Ele não é copiado para a imagem, então qualquer alteração no código aparece na hora, sem rebuild.
- **Volumes nomeados**: `pgdata`, `pgadmin_data` e `portainer_data` guardam dados que sobrevivem à remoção dos containers.
- **Rede interna e DNS**: os containers se enxergam pelo nome do serviço. O Laravel conecta em `DB_HOST=postgres`, não em `localhost`.
- **Variáveis de ambiente**: a configuração do banco é injetada pelo `compose.yaml`, sem depender do `.env`.
- **Healthcheck e `depends_on`**: `app` e `pgadmin` só iniciam depois que o PostgreSQL responde ao `pg_isready`.
- **Entrypoint**: na subida, o [entrypoint](docker/app/entrypoint.sh) do `app` instala as dependências PHP (se faltarem), cria o `.env`, gera a `APP_KEY`, roda as migrations e popula o banco (o seed é ignorado se já houver dados).
- **Política de reinício**: todos os serviços usam `restart: unless-stopped`.
- **Acesso ao Docker do host**: o Portainer monta `/var/run/docker.sock` para gerenciar o próprio Docker da máquina.

## Pré-requisitos

- [Docker Engine](https://docs.docker.com/engine/install/) (ou Docker Desktop) com o plugin **Docker Compose v2** (comando `docker compose`).
- Git.
- As portas `8000`, `5173`, `5432`, `5050`, `9000` e `9443` livres.

Não é preciso ter PHP, Composer, Node ou PostgreSQL instalados.

## Passo a passo para executar

### 1. Clonar o repositório

```bash
git clone https://github.com/LeonardoAndreMuller/DockerApresentacao DockerApresentacao
cd DockerApresentacao
```

### 2. (Opcional) Exportar seu UID/GID

O padrão é `1000:1000`, que é o usuário principal na maioria das distribuições Linux. Se o seu for diferente (confira com `id -u` e `id -g`), exporte antes de subir:

```bash
export UID=$(id -u) GID=$(id -g)
```

### 3. Construir a imagem e subir os containers

```bash
docker compose up -d --build
```

Na primeira execução o Docker baixa as imagens, constrói a imagem do `app`, instala as dependências PHP (`composer install`) e Node (`npm install`), roda as migrations e popula o banco. Isso pode levar alguns minutos.

### 4. Acompanhar a inicialização

```bash
docker compose ps            # estado de cada serviço
docker compose logs -f app   # mensagens do entrypoint ([entrypoint] ...)
docker compose logs -f vite  # instalação do npm e servidor do Vite
```

O `app` está pronto quando o log mostra o Apache iniciado, logo depois de "Populando o banco".

### 5. Acessar

| O quê       | Endereço                 | Acesso |
|-------------|--------------------------|--------|
| Aplicação   | http://localhost:8000    | Abre direto na lista de produtos (`/produtos`); pedidos ficam em `/pedidos`. |
| pgAdmin     | http://localhost:5050    | Sem login. O servidor "Pedidos (Docker)" já conecta sem pedir senha. |
| Portainer   | http://localhost:9000    | No primeiro acesso, crie o usuário administrador (há um limite de poucos minutos para isso após a subida; se expirar, rode `docker compose restart portainer`). |
| PostgreSQL  | `localhost:5432`         | Banco `pedidos`, usuário `laravel`, senha `secret`. |

> As credenciais acima são apenas para desenvolvimento local e para a apresentação.

## Comandos úteis

```bash
# Rodar comandos Artisan dentro do container (como o usuário www-data)
docker compose exec -u www-data app php artisan migrate:status
docker compose exec -u www-data app php artisan tinker

# Rodar os testes
docker compose exec -u www-data app php artisan test --compact

# Abrir um shell no container da aplicação
docker compose exec app bash

# Acessar o banco pelo terminal
docker compose exec postgres psql -U laravel -d pedidos

# Recriar o banco do zero com dados de exemplo
docker compose exec -u www-data app php artisan migrate:fresh --seed

# Reconstruir a imagem do app após mudar o Dockerfile ou os arquivos em docker/app
docker compose up -d --build app
```

## Parar e limpar

```bash
docker compose stop      # para os containers, mantendo tudo
docker compose down      # remove containers e rede (os volumes e os dados ficam)
docker compose down -v   # remove também os volumes: apaga o banco e as configs do pgAdmin/Portainer
```

## Estrutura dos arquivos Docker

```
compose.yaml               # definição de todos os serviços, volumes e portas
Dockerfile                 # imagem do app (PHP 8.5 + Apache + Composer)
.dockerignore              # arquivos ignorados no contexto de build
docker/
├── app/
│   ├── entrypoint.sh      # composer install, .env, migrations e seed na subida
│   ├── php.ini            # ajustes de PHP para desenvolvimento (OPcache, limites)
│   └── vhost.conf         # VirtualHost do Apache apontando para public/
└── pgadmin/
    └── servers.json       # servidor do PostgreSQL pré-cadastrado no pgAdmin
```
