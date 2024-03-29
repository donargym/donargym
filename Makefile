SHELL=/bin/bash

.DEFAULT_GOAL := help

## up: Runs vagrant up
.PHONY: up
up:
	vagrant up

## up: Runs vagrant provision
.PHONY: provision
make provision:
	vagrant provision

## release:	Checkout master, get most recent version, create new (minor) tag, push master and tags
.PHONY: release
release:
	git checkout master
	git pull
	bin/bump-tag.sh
	git push origin master && git push --tags
	./deploy

## branch:	Checkout master, get most recent version, create new branch based on master
.PHONY: branch
branch:
	git checkout master
	git pull
	@read -p "Enter Branch Name: " branchName; \
	git checkout -b $$branchName

## help:		Print this message
.PHONY: help
help: Makefile
	@sed -n 's/^##//p' $<
