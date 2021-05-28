

## Heroku Setup 

1. Create a normal app

2. Add Postgres Addon

```
heroku addons:create heroku-postgresql:hobby-dev
```

3. Add Rust Buildpack (for running migrations). Note: See [this issue](https://github.com/emk/heroku-buildpack-rust/issues/40) 
for why we're not using `emk/rust` buildpack. 

```
heroku buildpacks:add --index 1 https://github.com/emk/heroku-buildpack-rust
```