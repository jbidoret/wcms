include .default.env
include .env
export

build_dir  := build
js_src_dir := src

# Misc variables.
export PATH             := vendor/bin:node_modules/.bin:$(PATH)

ifneq ($(OS),Windows_NT) # Not for Windows
UNAME_S := $(shell uname -s)
ifeq ($(UNAME_S),Darwin) # For Mac
SHELL                   := PATH=$(PATH) /bin/bash
else
SHELL                   := /bin/bash
endif
endif
override GIT_VERSION    := $(shell git --no-pager describe --always --tags)
override CUR_VERSION    := $(strip $(shell cat VERSION 2>/dev/null))
override WEBPACK_FLAGS  += $(if $(filter $(ENV),dist),-p)
override COMPOSER_FLAGS += $(if $(filter $(ENV),dist),--no-dev --prefer-dist)
PREV_ENV_FILE           := $(build_dir)/prev_env
PREV_ENV                := $(strip $(shell cat $(PREV_ENV_FILE) 2>/dev/null))
HOST                    := localhost
PORT                    := 8080
PHP_FLAGS               += $(and $(XDEBUG3),-d xdebug.start_with_request=yes)

# Files variables.
js_sources  := $(wildcard $(js_src_dir)/*.js)
js_bundles  := $(js_sources:$(js_src_dir)/%.js=assets/js/%.bundle.js)
js_srcmaps  := $(js_sources:$(js_src_dir)/%.js=assets/js/%.bundle.js.map)
zip_release := dist/w_cms_$(GIT_VERSION).zip
phpcs_dir   := $(build_dir)/phpcs
phpunit_dir := $(build_dir)/phpunit
dirs        := $(phpcs_dir) $(phpunit_dir)

# Default target. This executes everything targets needed to get a fully
# working development environment.
.PHONY: all
all: vendor build

# Build generated files.
.PHONY: build
build: VERSION $(js_bundles)

.PHONY: dev
dev: ENV := dev
dev: MAKEFLAGS += j2
dev:
	$(MAKE) serve .watch ENV=$(ENV)

.PHONY: serve
serve: vendor VERSION
	php -S $(HOST):$(PORT) -t ./ $(PHP_FLAGS)

# Run webpack in watch mode.
.PHONY: watch
watch: ENV := dev
watch:
	$(MAKE) .watch ENV=$(ENV)

.PHONY: .watch
.watch: $(PREV_ENV_FILE) node_modules
	webpack --env $(ENV) --watch

# Create a new release and upload it on GitHub.
.PHONY: release
release: check node_modules
	release-it

# Inform Sentry of a new release.
.PHONY: sentryrelease
sentryrelease: ENV := dist
sentryrelease:
	$(MAKE) .sentryrelease ENV=$(ENV)

.PHONY: .sentryrelease
.sentryrelease: build $(js_srcmaps)
	sentry-cli releases new $(GIT_VERSION)
	sentry-cli releases set-commits $(GIT_VERSION) --auto
	sentry-cli releases files $(GIT_VERSION) upload-sourcemaps assets/js --url-prefix '~/assets/js' --rewrite
	sentry-cli releases finalize $(GIT_VERSION)

# Generate the distribution files.
.PHONY: dist
dist: ENV := dist
dist:
	$(MAKE) .dist ENV=$(ENV)

.PHONY: .dist
.dist: distclean $(zip_release)

dist/w_cms_%.zip: all
	@echo Building Zip release...
	mkdir -p $(dir $@)
# Include git tracked files (everything except ignored) + needed files.
	zip -r $@ \
		$(shell git ls-tree -r HEAD --name-only) \
		VERSION \
		assets/js \
		vendor \
		-x "*test*" \
		-x "*docs*"
# Include non-empty git tracked directories (to keep dir permissions).
	zip $@ $(shell git ls-tree -r HEAD --name-only -d)
# Remove non-useful files.
	zip -d $@ \
		$(js_src_dir)/\* \
		$(js_srcmaps) \
		.github/\* \
		.default.env \
		.gitignore \
		.release-it.json \
		codecov.yaml \
		composer.json \
		composer.lock \
		Makefile \
		package.json \
		package-lock.json \
		phpcs.xml \
		phpunit.xml \
		phpstan.neon \
		tests/\* \
		webpack.config.js

# Generate the js bundles (and sourcemaps).
assets/js/%.bundle.js assets/js/%.bundle.js.map: $(js_src_dir)/%.js node_modules webpack.config.js $(PREV_ENV_FILE) | assets/js
	@echo Building JS Bundles...
	webpack --entry=./$< --output-path=$(@D) --output-filename=$(@F) --env=$(ENV) $(WEBPACK_FLAGS)

assets/js:
	mkdir -p $@

# Generate a .env file if none is present.
.env:
	cp .default.env $@

# Generate the VERSION file.
VERSION: .FORCE
ifneq ($(CUR_VERSION),$(GIT_VERSION))
	echo $(GIT_VERSION) > $@
endif

# Trick to force rebuild if the build environment has changed.
ifneq ($(PREV_ENV),$(ENV))
$(PREV_ENV_FILE): touch
ifdef PREV_ENV
	@echo Build env has changed !
else
	@echo New build env.
endif
	mkdir -p $(dir $@)
	echo $(ENV) > $@
endif

# Install PHP dependencies.
vendor: $(PREV_ENV_FILE) composer.json composer.lock
	@echo Installing PHP dependencies...
	composer install $(COMPOSER_FLAGS)
	touch $@

# Install JS dependencies.
node_modules: package.json package-lock.json
	@echo Installing JS dependencies...
	npm install --no-progress --loglevel=error --also=dev
	touch $@

# Clean files generated by `make all`.
.PHONY: clean
clean: buildclean
	@echo Cleaning make artifacts...
	rm -rf vendor
	rm -rf node_modules
	rm -rf VERSION

# Clean files generated by `make dist`.
.PHONY: distclean
distclean:
	@echo Cleaning dist artifacts...
	rm -rf dist

# Clean files generated by `make build`.
.PHONY: buildclean
buildclean:
	@echo Cleaning build artifacts...
	rm -rf $(js_bundles)
	rm -rf $(js_srcmaps)
	rm -rf $(build_dir)

# Run all checks.
.PHONY: check
check: lint analyse test

# Lint php code with phpcs.
.PHONY: lint
lint: $(phpcs_dir) vendor
	phpcs --report-full --report-summary --cache=$(phpcs_dir)/result.cache || { printf "run 'make fix'\n\n"; exit 1; }

# fix php code with phpcbf.
.PHONY: fix
fix: $(phpcs_dir) vendor
	phpcbf || exit 0

# Analyse php code with phpstan.
.PHONY: analyse
analyse: vendor
	phpstan analyse

# Test php code with phpunit.
.PHONY: test
test: $(phpunit_dir) vendor
	phpunit

# Create dirs if the do not exist
$(dirs):
	mkdir -p $@

# Touch files affected by the build environment to force the execution
# of the corresponding targets.
.PHONY: touch
touch:
	touch composer.json webpack.config.js

# Special (fake) target to always run a target but have Make consider
# this updated if it was actually rewritten (a .PHONY target is always
# considered new).
.FORCE: ;
