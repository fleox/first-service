![](./docs/img/logo-reparcar-std-400.webp)

# Reparcar Channel

Reparcar channel micro service

# Requirements :

- MS [Traefik-dockerize](https://github.com/restarteco/traefik-dockerized)

# Installation

```bash
cp docker-compose.override.yml.dist docker-compose.override.yml

# run project with docker:
docker-compose up -d

# check if ACCESS_TOKEN_RECTOR secret exit on project or create one
```

update your hosts:

- osx: `nano /etc/hosts`
- Linux (Debian based): `vim /etc/hosts`

and add : `127.0.0.1 local.channel-reparcar.fr`

now you can go to [local.channel-reparcar.fr/channel/docs](http://local.channel-reparcar.fr/channel/docs) and start coding

```bash
# run bash in docker (console command ...) :
docker-compose exec app bash
```

# Run tests

```bash
php vendor/bin/phpunit
```