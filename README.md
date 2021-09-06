# PHP DTO code generator form OpenAPI spect

## To set up 
```bash
./php composer i
```
## To use 
```shell
./generator create-dto ./examples/publisher.json  
```




# Development Tools
## To fix code style issues 
`vendor/bin/php-cs-fixer fix`
## To run test
`vendor/bin/phpunit`
## To run static analizer
`vendor/bin/psalm --no-cache`
# TODO
- [ ] Support allOf, onOff, anyOf
- [ ] SDK client generation
- [ ] Image tag generation automation
- [ ] Extention for codeception (in separate repository)
