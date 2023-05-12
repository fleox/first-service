# Tuto Docker / Traefik / Tests / API Rest - GraphQl
Sample project API rest / graphQL
- [Tuto create dev env with Docker and Traefik](https://medium.com/@fredericleaux/tuto-monter-un-environnement-de-dev-docker-avec-traefik-et-oauth2-pr%C3%AAt-pour-le-micro-service-12f78874d79c)
- [Une Api Rest / GraphQl sans ApiPlatform (qu'il est préférable d'éviter)](https://medium.com/reparcar/comment-bien-supprimer-apiplatform-et-utiliser-des-solutions-alternatives-3661a0460e19)

# Requirements :

- [Dockerize](https://github.com/fleox/dockerized)

# Installation

```bash
cd /first_service

# run project with docker:
docker-compose up -d
docker-compose exec app_firstservice bash
# run fixtures
php bin/console doctrine:fixtures:load
```

update your hosts:

- osx: `nano /etc/hosts`
- Linux (Debian based): `vim /etc/hosts`

and add : `127.0.0.1 local.first-service.fr`

now you can go to [http://local.first-service.fr/](http://local.first-service.fr/) and start coding

Access to [GraphiQl](http://local.first-service.fr/graphql/graphiql)

Access to Rest api doc [NelmioApiDoc](http://local.first-service.fr/api/doc)

# Tests

Run tests :
```bash
cd /first_service
docker-compose exec app_firstservice bash
./bin/phpunit
```
