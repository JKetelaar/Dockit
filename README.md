# Dockit
Dockerize it

This is still Work in Progress

## What is this for?
This is a command line tool that allows you to create and manage PHP projects.  
It allows you to install a local development environment, without being stricted to your local environment.

A real use-case scenario would be a development agency that has multiple websites with different requirements;   
Such as project X with PHP 7.0, MySQL and Solr; but also having project Y with PHP 7.1, and ElasticSearch.  
Yet you want to work on these projects simultaneously, without having to switch the FPM instance all the time.

## How to install?
1.) First ensure you have the latest php (>= 7.1) installed, preferably the latest stable:
```bash
brew install php@7.2
```

2.) Also ensure you have composer installed:
```bash
brew install composer
```

3.) Now install Dockit globally:
```bash
composer global require jketelaar/dockit
```

### `dockit config`
This creates a configuration setup for your current project, based on the arguments you give in the CLI.  
Simply type in `dockit config` and answer what the CLI requests.

### `dockit start`
Starts the Docker containers for the current project.

### `dockit stop`
Stops the Docker containers for the current project.

### `dockit restart`
Restarts the Docker containers for the current project.

### `dockit open`
Opens the current project in your browser.

### `dockit haproxy`
Opens the HAProxy in your browser.