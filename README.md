[![Build Status](https://travis-ci.org/WordPress-Coding-Standards/WordPress-Coding-Standards.png?branch=master)](https://travis-ci.org/WordPress-Coding-Standards/WordPress-Coding-Standards)

# WordPress Coding Standards for PHP_CodeSniffer

This project is a collection of [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) rules (sniffs) to validate code developed for WordPress. It ensures code quality and adherence to coding conventions, especially the official [WordPress Coding Standards](http://make.wordpress.org/core/handbook/coding-standards/).

## Project history

 - In April 2009 original project from [Urban Giraffe](http://urbangiraffe.com/articles/wordpress-codesniffer-standard/) was published.
 - In May 2011 the project was forked on GitHub by [Chris Adams](http://chrisadams.me.uk/).
 - In April 2012 [XWP](https://xwp.co/) started to dedicate resources to the development and currently maintains the project, along with [J.D. Grimes](https://github.com/JDGrimes) and [Gary Jones](https://github.com/GaryJones).

## Installation

### Composer

Standards can be installed with [Composer](https://getcomposer.org/) dependency manager:

    composer create-project wp-coding-standards/wpcs:dev-master --no-dev

Running this command will:

1. Install WordPress standards into `wpcs` directory.  
2. Install PHP_CodeSniffer.
3. Register WordPress standards in PHP_CodeSniffer configuration.
4. Make `phpcs` command available from `wpcs/vendor/bin`.

For convenience of using `phpcs` as global command you might want to add path to `wpcs/vendor/bin` directory to a `PATH` environment of your operating system.

### Standalone

1. Install PHP_CodeSniffer by following its [installation instructions](https://github.com/squizlabs/PHP_CodeSniffer#installation) (via Composer, PEAR, or Git checkout).

  Do ensure, if for example you're using [VVV](https://github.com/Varying-Vagrant-Vagrants/VVV), that PHP_CodeSniffer's version matches our requirements (you can check the required version in [composer.json](composer.json#L18)).

2. Clone WordPress standards repository:

        git clone -b master https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards.git wpcs

3. Add its path to PHP_CodeSniffer configuration: 

        phpcs --config-set installed_paths /path/to/wpcs


To summarize:

```bash
cd ~/projects
git clone https://github.com/squizlabs/PHP_CodeSniffer.git phpcs
git clone -b master https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards.git wpcs
cd phpcs
./scripts/phpcs --config-set installed_paths ../wpcs
```

And then add the `~/projects/phpcs/scripts` directory to your `PATH` environment variable via your `.bashrc`.

You should then see `WordPress-Core` et al listed when you run `phpcs -i`.

## How to use

### Command line

Run the `phpcs` command line tool on a given file or directory, for example:

    phpcs --standard=WordPress wp-load.php

Will result in following output:

	--------------------------------------------------------------------------------
	FOUND 13 ERROR(S) AFFECTING 7 LINE(S)
	--------------------------------------------------------------------------------
	  1 | ERROR | End of line character is invalid; expected "\n" but found "\r\n"
	 22 | ERROR | No space after opening parenthesis of function  prohibited
	 22 | ERROR | No space before closing parenthesis of function  prohibited
	 26 | ERROR | No space before closing parenthesis of function  prohibited
	 31 | ERROR | No space after opening parenthesis of function  prohibited
	 31 | ERROR | No space before closing parenthesis of function  prohibited
	 31 | ERROR | No space after opening parenthesis of function  prohibited
	 31 | ERROR | No space before closing parenthesis of function  prohibited
	 34 | ERROR | No space after opening parenthesis of function  prohibited
	 34 | ERROR | No space before closing parenthesis of function  prohibited
	 55 | ERROR | Detected usage of a non-validated input variable: $_SERVER
	 55 | ERROR | Detected usage of a non-sanitized input variable: $_SERVER
	 70 | ERROR | String "Create a Configuration File" does not require double
		|       | quotes; use single quotes instead
	--------------------------------------------------------------------------------

### PhpStorm

Please see “[PHP Code Sniffer with WordPress Coding Standards Integration](https://www.jetbrains.com/phpstorm/help/using-php-code-sniffer-tool.html)” in PhpStorm documentation.

### Sublime Text

Install the [sublime-phpcs package](https://github.com/benmatselby/sublime-phpcs), then use the "Switch coding standard" command in the Command Palette to switch between coding standards.

## Standards subsets

The project encompasses a super–set of the sniffs that the WordPress community may need. If you use the `WordPress` standard you will get all the checks. Some of them might be unnecessary for your environment, for example those specific to WordPress VIP coding requirements.

You can use the following as standard names when invoking `phpcs` to select sniffs, fitting your needs:

 - `WordPress` — complete set with all of the sniffs in the project
  - `WordPress-Core` — main ruleset for [WordPress core coding standards](http://make.wordpress.org/core/handbook/coding-standards/)
  - `WordPress-Docs` — additional ruleset for inline documentation
  - `WordPress-Extra` — extended ruleset for optional best practices sniffs
    - includes `WordPress-Core`
  - `WordPress-VIP` — extended ruleset for [WordPress VIP coding requirements](http://vip.wordpress.com/documentation/code-review-what-we-look-for/)
    - includes `WordPress-Core`


### Using custom ruleset

If you need to further customize selection of sniffs for your project — you can create custom `ruleset.xml` standard. See provided [project.ruleset.xml.example](project.ruleset.xml.example) file and [fully annotated example](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Annotated-ruleset.xml) in PHP_CodeSniffer documentation.

## Contributing

See [CONTRIBUTING](CONTRIBUTING.md), including information about [unit testing](CONTRIBUTING.md#unit-testing).

## License

See [LICENSE](LICENSE) (MIT).
