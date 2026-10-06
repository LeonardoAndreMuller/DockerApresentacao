# Pedidos — Apresentação sobre Docker

Projeto criado para uma **apresentação sobre a ferramenta Docker**. A aplicação em si é simples de propósito: um pequeno sistema de **cadastro de produtos e pedidos** feito em Laravel. O foco está na infraestrutura: com um único comando, o Docker Compose sobe todo o ambiente de desenvolvimento (servidor web, banco de dados, build do front-end e ferramentas de administração) sem instalar PHP, Node ou PostgreSQL na máquina.

## O que o `compose.yaml` sobe

O projeto do Compose se chama `pedidos` e tem seis serviços. Só o Traefik (porta `80`) e o PostgreSQL (porta `5432`) publicam portas na máquina; todo o resto é acessado pelo proxy em `http://localhost/...`.

| Serviço     | Imagem                       | Acesso          | Para que serve |
|-------------|------------------------------|-----------------|----------------|
| `traefik`   | `traefik:v3`                 | porta `80`      | Proxy reverso: única porta de entrada, roteia por caminho e aplica rate limiting e headers de segurança. |
| `app`       | `pedidos-app:dev` (construída pelo [Dockerfile](Dockerfile)) | `/`             | Aplicação Laravel servida pelo Apache com PHP 8.5 (mod_php). |
| `postgres`  | `postgres:17-alpine`         | porta `5432`    | Banco de dados PostgreSQL. Os dados ficam no volume `pgdata`. |
| `vite`      | `node:24-slim`               | `/vite`         | Servidor de desenvolvimento do Vite: compila CSS/JS (Tailwind) e recarrega o navegador sozinho a cada alteração. |
| `pgadmin`   | `dpage/pgadmin4`             | `/pgadmin`      | Interface web do PostgreSQL, com o servidor "Pedidos (Docker)" já cadastrado. |
| `portainer` | `portainer/portainer-ce:lts` | `/portainer/`   | Painel web para ver e gerenciar containers, imagens, volumes e redes. |

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
- **Proxy reverso com dois providers**: o Traefik lê o `docker.sock` (somente leitura) e monta as rotas de cada serviço a partir dos `labels` no `compose.yaml` (só entram no proxy os serviços com `traefik.enable=true`). Os middlewares compartilhados e a rota do painel ficam em arquivos, em [docker/traefik/](docker/traefik/), e são usados nos labels com o sufixo `@file` (ex.: `rate-limit@file`).
- **Bind mount para configuração ao vivo**: a pasta `docker/traefik` é montada em `/etc/traefik`. O [traefik.yaml](docker/traefik/traefik.yaml) é a configuração **estática** (entrypoints, providers, painel) e só é relido com `docker compose restart traefik`. Os arquivos em [docker/traefik/dynamic/](docker/traefik/dynamic/) são a configuração **dinâmica**: salvou, o Traefik recarrega na hora, sem reiniciar nada. A pasta é montada inteira (e não arquivo por arquivo) porque editores que salvam substituindo o arquivo quebram a detecção de mudanças num bind mount de arquivo único.
- **Middlewares do Traefik** ([dynamic/middlewares.yaml](docker/traefik/dynamic/middlewares.yaml)):
  - `security-headers`: aplicado a todas as respostas. Adiciona `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy` e `Permissions-Policy`, e remove `Server` e `X-Powered-By`.
  - `rate-limit`: até 50 requisições/s por IP, com picos de 100; acima disso responde `429 Too Many Requests`. Fica de fora do Vite, que carrega dezenas de arquivos por página em desenvolvimento.
  - Ajustes de caminho por serviço, definidos nos labels: `X-Script-Name` para o pgAdmin, `stripPrefix` para o Portainer e `replacePathRegex` para as fontes do Vite.

## Pré-requisitos

- [Docker Engine](https://docs.docker.com/engine/install/) (ou Docker Desktop) com o plugin **Docker Compose v2** (comando `docker compose`).
- Git.
- As portas `80` e `5432` livres (um Apache/nginx ou PostgreSQL instalado na máquina ocupa essas portas).

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
| Aplicação   | http://localhost/        | Abre direto na lista de produtos (`/produtos`); pedidos ficam em `/pedidos`. |
| pgAdmin     | http://localhost/pgadmin | Sem login. O servidor "Pedidos (Docker)" já conecta sem pedir senha. |
| Portainer   | http://localhost/portainer/ | No primeiro acesso, crie o usuário administrador (há um limite de poucos minutos para isso após a subida; se expirar, rode `docker compose restart portainer`). |
| Painel do Traefik | http://localhost/dashboard/ | Rotas, serviços e middlewares ativos. Ele também usa o caminho `/api`, então o Laravel não pode ter rotas em `/api` enquanto o painel estiver exposto. |
| PostgreSQL  | `localhost:5432`         | Acesso direto, sem proxy. Banco `pedidos`, usuário `laravel`, senha `secret`. |

> As credenciais acima são apenas para desenvolvimento local e para a apresentação.

Para ver o rate limit em ação, dispare várias requisições em paralelo e repare nas respostas `429`:

```bash
seq 300 | xargs -P 30 -I{} curl -s -o /dev/null -w "%{http_code}\n" http://localhost/up | sort | uniq -c
```

Para ver o recarregamento ao vivo, mude `average` e `burst` do `rate-limit` em [docker/traefik/dynamic/middlewares.yaml](docker/traefik/dynamic/middlewares.yaml) para `1`, salve e rode o comando acima de novo: quase tudo passa a voltar `429`, sem reiniciar nenhum container.

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
├── pgadmin/
│   └── servers.json       # servidor do PostgreSQL pré-cadastrado no pgAdmin
└── traefik/               # montada em /etc/traefik no container do proxy
    ├── traefik.yaml       # configuração estática (exige restart do traefik)
    └── dynamic/           # configuração dinâmica (recarrega ao salvar)
        ├── middlewares.yaml   # security-headers e rate-limit
        └── dashboard.yaml     # rota do painel do Traefik
```
