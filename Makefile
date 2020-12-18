#!make
include .env
export $(shell sed 's/=.*//' .env)

.PHONY: no3DS

install:
	composer install

sepaCreditTransfer:
	php src/sepaCreditTransfer.php

