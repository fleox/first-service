![](./docs/img/logo-reparcar-std-400.webp)

# Reparcar Channel

[Tuto create dev env with Docker and Traefik](https://medium.com/@fredericleaux/tuto-monter-un-environnement-de-dev-docker-avec-traefik-et-oauth2-pr%C3%AAt-pour-le-micro-service-12f78874d79c)

# Requirements :

- [Dockerize](https://github.com/fleox/dockerized)

# Installation

```bash
cp docker-compose.override.yml.dist docker-compose.override.yml

# run project with docker:
docker-compose up -d
```

update your hosts:

- osx: `nano /etc/hosts`
- Linux (Debian based): `vim /etc/hosts`

and add : `127.0.0.1 http://local.first-service.fr/`

now you can go to [http://local.first-service.fr/](http://local.first-service.fr/) and start coding