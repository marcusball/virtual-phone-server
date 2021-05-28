#!/bin/sh

echo $(which cargo)
echo $(which diesel)

diesel setup
diesel migration run