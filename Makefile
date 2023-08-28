repoBase=ghcr.io/softfineware/discriminator-default-normalizer/base
repoCI=ghcr.io/softfineware/discriminator-default-normalizer/ci
repoCIDependency=ghcr.io/softfineware/discriminator-default-normalizer/ci/cache
cli=docker compose run -it --rm cli

#composerLockHash := $(shell #echo whatever)
HASH:=$(shell md5 -q composer.lock)
shell:
	docker compose run --rm -it cli sh
github.registry.login:
	cat secrets.json | jq .CR_PAT -r | docker login ghcr.io -u SoftFineWare --password-stdin
docker.build.base:
	docker build . --tag ${repoBase}:${version} -f .docker/Dockerfile.base
docker.push.base:
	docker push ${repoBase}:${version}
docker.build.ci: docker.build.ci.dependency
	docker build . --tag ${repoCI}:${version} -f .docker/Dockerfile.ci \
		--cache-from=${repoCIDependency}:$(HASH)
docker.build.ci.dependency: docker.build.ci.dependency.locked docker.build.ci.dependency.lowest docker.build.ci.dependency.highest
docker.build.ci.dependency.locked:
	docker build . --tag ${repoCIDependency}:$(HASH) -f .docker/Dockerfile.ci \
		--build-arg COMPOSER_LOCK_HASH=$(HASH) \
		--cache-from=${repoCIDependency}/locked:$(HASH)
docker.build.ci.dependency.lowest:
	docker build . --tag ${repoCIDependency}:$(HASH) -f .docker/Dockerfile.ci \
		--build-arg COMPOSER_LOCK_HASH=$(HASH) \
		--build-arg COMPOSER_COMMAND="update --prefer-lowest" \
		--cache-from=${repoCIDependency}/lowest:$(HASH)
docker.build.ci.dependency.highest:
	docker build . --tag ${repoCIDependency}:$(HASH) -f .docker/Dockerfile.ci \
		--build-arg COMPOSER_LOCK_HASH=$(HASH) \
		--build-arg COMPOSER_COMMAND=update \
		--cache-from=${repoCIDependency}/highest:$(HASH)
docker.run.psalm:
	cli psalm
docker.run.phpunit:
	cli vendor/bin/phpunit  --coverage-php .output/coverage.cov
docker.run.coverage.diff: docker.run.phpunit
	docker-compose run -it git diff HEAD^1 -- "**/*.php"  > .output/patch.txt
	docker-compose run -it coverage patch-coverage --path-prefix /app .output/coverage.cov .output/patch.txt

test.example.pet:
	 ${cli} bin/generator create-dto examples/petstore.yaml
