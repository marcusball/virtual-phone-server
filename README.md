

## Heroku Setup 

1. Create a normal app

2. Add Postgres Addon

```
heroku addons:create heroku-postgresql:hobby-dev
```

3. Add Rust Buildpack (for running migrations)

```
heroku buildpacks:add --index 1 emk/rust
```