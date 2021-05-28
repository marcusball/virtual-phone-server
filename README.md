

## Heroku Setup 

1. Create a normal app

2. Add Postgres Addon

```
heroku addons:create heroku-postgresql:hobby-dev
```

3. Add Diesel Buildpack (for running migrations).

```
heroku buildpacks:add --index 1 https://github.com/sgrif/heroku-buildpack-diesel.git
```