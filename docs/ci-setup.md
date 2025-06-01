# CI Setup

This guide explains how to set up and use testcontainers-php in various Continuous Integration (CI) environments.

## GitHub Actions

GitHub Actions provides a convenient way to run your tests with testcontainers-php. Since GitHub Actions runners have Docker installed by default, you can use testcontainers-php without additional configuration.

```yaml
name: Tests

on:
  push:
    branches: [ main ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        extensions: pdo, pdo_mysql
        
    - name: Install dependencies
      run: composer install --prefer-dist --no-progress
      
    - name: Run tests
      run: vendor/bin/phpunit
```

## CircleCI with Machine Executor

The Machine Executor in CircleCI provides a complete Linux virtual machine with Docker pre-installed. This is the simplest way to use testcontainers-php in CircleCI.

```yaml
version: 2.1

jobs:
  test:
    machine:
      image: ubuntu-2204:current
    
    steps:
      - checkout
      
      - run:
          name: Install PHP and Composer
          command: |
            sudo apt-get update
            sudo apt-get install -y php php-cli php-curl php-mbstring php-xml php-zip php-pdo php-mysql
            php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
            php composer-setup.php --install-dir=/usr/local/bin --filename=composer
            php -r "unlink('composer-setup.php');"
      
      - run:
          name: Install dependencies
          command: composer install --prefer-dist --no-progress
      
      - run:
          name: Run tests
          command: vendor/bin/phpunit

workflows:
  version: 2
  test:
    jobs:
      - test
```

With the Machine Executor, no special configuration is needed in the test code, as Docker is running directly on the machine.

## CircleCI with Docker Executor

The Docker Executor in CircleCI runs your job in a Docker container. To use testcontainers-php with the Docker Executor, you need to use the "Remote Docker" feature and SSH port forwarding.

```yaml
version: 2.1

jobs:
  test:
    docker:
      - image: cimg/php:8.1
    
    steps:
      - checkout
      
      - setup_remote_docker:
          version: 20.10.14
      
      - run:
          name: Install dependencies
          command: composer install --prefer-dist --no-progress
      
      - run:
          name: Run tests
          environment:
            DOCKER_HOST: tcp://remote-docker:2375
            TESTCONTAINERS_SSH_FEEDFORWARDING: remote-docker
            TESTCONTAINERS_HOST_OVERRIDE: localhost
          command: vendor/bin/phpunit

workflows:
  version: 2
  test:
    jobs:
      - test
```

When using the Docker Executor with Remote Docker, you need to set the following environment variables:

- `DOCKER_HOST`: Connects to the Docker daemon on the remote-docker host
- `TESTCONTAINERS_SSH_FEEDFORWARDING`: Enables SSH port forwarding
- `TESTCONTAINERS_HOST_OVERRIDE`: Makes container ports accessible via localhost

For more details on `TESTCONTAINERS_SSH_FEEDFORWARDING` and related configurations, see the [Environments](environments.md) and [Container Configuration](container-configuration.md#ssh-port-forward-settings) documentation.

If you need to explicitly configure SSH port forwarding in your test code, you can use `withSSHPortForward`:

```php
$instance = Testcontainers::run(
    (new GenericContainer('mysql:8'))
    ->withExposedPort(3306)
    ->withEnvs([
        'MYSQL_ROOT_PASSWORD' => 'test',
    ])
    ->withWaitStrategy(
        (new PDOConnectWaitStrategy())
            ->withDsn(new MySQLDSN())
            ->withUsername('root')
            ->withPassword('test')
    )
    ->withSSHPortForward('remote-docker')
);
```

For more information about environment variables, see the [Environments](environments.md) documentation.
