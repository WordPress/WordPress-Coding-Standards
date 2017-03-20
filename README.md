[![Build Status](https://travis-ci.org/WordPress-Coding-Standards/WordPress-Coding-Standards.png?branch=master)](https://travis-ci.org/WordPress-Coding-Standards/WordPress-Coding-Standards)
[![Total Downloads](https://poser.pugx.org/wp-coding-standards/wpcs/downloads)](https://packagist.org/packages/wp-coding-standards/wpcs)

# WordPress Coding Standards for PHP_CodeSniffer

* [Introduction](#introduction)
* [Project history](#project-history)
* [Installation](#installation)
    + [Requirements](#requirements)
    + [Composer](#composer)
    + [Standalone](#standalone)
* [Rulesets](#rulesets)
    + [Standards subsets](#standards-subsets)
    + [Using a custom ruleset](#using-a-custom-ruleset)
    + [Customizing sniff behaviour](#customizing-sniff-behaviour)
    + [Recommended additional rulesets](#recommended-additional-rulesets)
* [How to use](#how-to-use)
    + [Command line](#command-line)
    + [PhpStorm](#phpstorm)
    + [Sublime Text](#sublime-text)
    + [Atom](#atom)
    + [Visual Studio](#visual-studio)
* [Running your code through WPCS automatically using CI tools](#running-your-code-through-wpcs-automatically-using-ci-tools)
    + [[Travis CI](https://travis-ci.org/)](#-travis-ci--https---travis-ciorg--)
* [Fixing errors or whitelisting them](#fixing-errors-or-whitelisting-them)
* [Contributing](#contributing)
* [License](#license)

## Introduction

This project is a collection of [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) rules (sniffs) to validate code developed for WordPress. It ensures code quality and adherence to coding conventions, especially the official [WordPress Coding Standards](http://make.wordpress.org/core/handbook/coding-standards/).

## Project history

 - In April 2009 original project from [Urban Giraffe](http://urbangiraffe.com/articles/wordpress-codesniffer-standard/) was published.
 - In May 2011 the project was forked on GitHub by [Chris Adams](http://chrisadams.me.uk/).
 - In April 2012 [XWP](https://xwp.co/) started to dedicate resources to development and lead creation of the the sniffs and rulesets for `WordPress-Core`, `WordPress-VIP` (WordPress.com VIP), and `WordPress-Extra`.
 - In 2015, [J.D. Grimes](https://github.com/JDGrimes) began significant contributions, along with maintanance from [Gary Jones](https://github.com/GaryJones).
 - In 2016, [Juliette Reinders Folmer](https://github.com/jrfnl) began contributing heavily, adding more commits in a year than anyone else in 5 years previous since the project's inception.

## Installation

### Requirements

The WordPress Coding Standards require PHP 5.2 or higher and the [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) version **2.8.1** or higher.
The WordPress Coding Standards are currently [not compatible with the upcoming PHPCS 3 release](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/718).

### Composer

Standards can be installed with [Composer](https://getcomposer.org/) dependency manager:

    composer create-project wp-coding-standards/wpcs --no-dev

Running this command will:

1. Install WordPress standards into `wpcs` directory.
2. Install PHP_CodeSniffer.
3. Register WordPress standards in PHP_CodeSniffer configuration.
4. Make `phpcs` command available from `wpcs/vendor/bin`.

For convenience of using `phpcs` as global command you might want to add path to `wpcs/vendor/bin` directory to a `PATH` environment of your operating system.

### Standalone

1. Install PHP_CodeSniffer by following its [installation instructions](https://github.com/squizlabs/PHP_CodeSniffer#installation) (via Composer, PEAR, or Git checkout).

   Do ensure, if for example you're using [VVV](https://github.com/Varying-Vagrant-Vagrants/VVV), that PHP_CodeSniffer's version matches our [requirements](#requirements).

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

##  Rulesets

### Standards subsets

The project encompasses a super–set of the sniffs that the WordPress community may need. If you use the `WordPress` standard you will get all the checks. Some of them might be unnecessary for your environment, for example those specific to WordPress VIP coding requirements.

You can use the following as standard names when invoking `phpcs` to select sniffs, fitting your needs:

* `WordPress` — complete set with all of the sniffs in the project
  - `WordPress-Core` — main ruleset for [WordPress core coding standards](http://make.wordpress.org/core/handbook/coding-standards/)
  - `WordPress-Docs` — additional ruleset for [WordPress inline documentation standards](https://make.wordpress.org/core/handbook/best-practices/inline-documentation-standards/)
  - `WordPress-Extra` — extended ruleset for recommended best practices, not sufficiently covered in the WordPress core coding standards
    - includes `WordPress-Core`
  - `WordPress-VIP` — extended ruleset for [WordPress VIP coding requirements](http://vip.wordpress.com/documentation/code-review-what-we-look-for/)
    - includes `WordPress-Core`

### Using a custom ruleset

If you need to further customize the selection of sniffs for your project — you can create a custom `phpcs.xml` standard. See provided [project.ruleset.xml.example](project.ruleset.xml.example) file and [fully annotated example](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Annotated-ruleset.xml) in PHP_CodeSniffer documentation.

### Customizing sniff behaviour

The WordPress Coding Standard contains a number of sniffs which are configurable. This means that you can turn parts of the sniff on or off, or change the behaviour by setting a property for the sniff in your custom `ruleset.xml` file.

You can find a complete list of all the properties you can change in the [wiki](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki/Customizable-sniff-properties).

### Recommended additional rulesets

The [PHPCompatibility](https://github.com/wimg/PHPCompatibility) ruleset comes highly recommended.
The [PHPCompatibility](https://github.com/wimg/PHPCompatibility) sniffs are designed to analyse your code for cross-PHP version compatibility.
Install it as a separate ruleset and either run it separately against your code or add it to your custom ruleset.

Whichever way you run it, do make sure you set the `testVersion` to run the sniffs against. The `testVersion` determines for which PHP versions you will received compatibility information. The recommended setting for this at this moment is  `5.2-7.1` to support the same PHP versions as WordPress Core supports.

For more information about setting the `testVersion`, see:
* [PHPCompatibility: Using the compatibility sniffs](https://github.com/wimg/PHPCompatibility#using-the-compatibility-sniffs)
* [PHPCompatibility: Using a custom ruleset](https://github.com/wimg/PHPCompatibility#using-a-custom-ruleset)

## How to use

### Command line

Run the `phpcs` command line tool on a given file or directory, for example:

    phpcs --standard=WordPress wp-load.php

Will result in following output:

	--------------------------------------------------------------------------------
	FOUND 8 ERRORS AND 2 WARNINGS AFFECTING 7 LINES
	--------------------------------------------------------------------------------
	  1 | ERROR   | [x] End of line character is invalid; expected "\n" but found "\r\n"
	 36 | ERROR   | [x] Expected 1 spaces before closing bracket; 0 found
	 41 | WARNING | [ ] Silencing errors is discouraged
	 41 | WARNING | [ ] Silencing errors is discouraged
	 48 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or
	    |         |     question marks
	 48 | ERROR   | [x] There must be no blank line following an inline comment
	 76 | ERROR   | [ ] Inline comments must end in full-stops, exclamation marks, or
	    |         |     question marks
	 92 | ERROR   | [x] String "Create a Configuration File" does not require double
	    |         |     quotes; use single quotes instead
	 94 | ERROR   | [ ] Expected next thing to be an escaping function (see Codex for
	    |         |     'Data Validation'), not '$die'
	 94 | ERROR   | [ ] Expected next thing to be an escaping function (see Codex for
	    |         |     'Data Validation'), not '__'
	--------------------------------------------------------------------------------
	PHPCBF CAN FIX THE 4 MARKED SNIFF VIOLATIONS AUTOMATICALLY
	--------------------------------------------------------------------------------

### PhpStorm

Please see “[PHP Code Sniffer with WordPress Coding Standards Integration](https://www.jetbrains.com/phpstorm/help/using-php-code-sniffer-tool.html)” in PhpStorm documentation.

### Sublime Text

##### sublime-phpcs package
Install the [sublime-phpcs package](https://github.com/benmatselby/sublime-phpcs), then use the "Switch coding standard" command in the Command Palette to switch between coding standards.

##### SublimeLinter-phpcs
sublime-phpcs is insanely powerful, but if you'd prefer automatic linting, [SublimeLinter-phpcs](https://github.com/SublimeLinter/SublimeLinter-phpcs/) can do that.

- Install PHP Sniffer and WordPress Coding Standards per above.
- Use [Package Control](https://packagecontrol.io/) to search for and install [SublimeLinter](http://www.sublimelinter.com) then [SublimeLinter-phpcs](https://github.com/SublimeLinter/SublimeLinter-phpcs/).
- From the command palette, select `Preferences: SublimeLinter Settings - User` and change `user.linters.phpcs.standard` to the phpcs standard of your choice (e.g. `WordPress`, `WordPress-VIP`, etc.).

![SublimeLinter-phpcs user settings](https://cloud.githubusercontent.com/assets/224636/12946250/068776ba-cfc1-11e5-816b-109e4e32d21b.png)

- You may need to restart Sublime for these settings to take effect. Error messages appear in the bottom of the editor.

![SublimeLinter-phpcs linting](https://cloud.githubusercontent.com/assets/224636/12946326/75986c3a-cfc1-11e5-8537-1243554bbab6.png)

![SublimeLinter-phpcs error](https://cloud.githubusercontent.com/assets/224636/12946335/8bee5a30-cfc1-11e5-8b5f-b10e8e4a4909.png)

### Atom

- Install PHP Sniffer and WordPress Coding Standards per above.
- Install [linter-phpcs](https://atom.io/packages/linter-phpcs) via Atom's package manager.
- Run `which phpcs` to get your `phpcs` executable path.
- Enter your `phpcs` executable path and one of the coding standards specified above (e.g. `WordPress`, `WordPress-VIP`, etc.).

![Atom Linter WordPress Coding Standards configuration](https://cloud.githubusercontent.com/assets/224636/12740504/ce4e97b8-c941-11e5-8d83-c77a2470d58e.png)

![Atom Linter in action using WordPress Coding Standards](https://cloud.githubusercontent.com/assets/224636/12740542/131c5894-c942-11e5-9e31-5e020c993224.png)

### Visual Studio

Please see “[Setting up PHP CodeSniffer in Visual Studio Code](https://tommcfarlin.com/php-codesniffer-in-visual-studio-code/)”, a tutorial by Tom McFarlin.


## Running your code through WPCS automatically using CI tools

### [Travis CI](https://travis-ci.org/)

To integrate PHPCS with WPCS with Travis CI, you'll need to install both `before_install` and add the run command to the `script`.
If your project uses Composer, the typical instructions might be different.

If you use a matrix setup in Travis to test your code against different PHP and/or WordPress versions, you don't need to run PHPCS on each variant of the matrix as the results will be same.
You can set an environment variable in the Travis matrix to only run the sniffs against one setup in the matrix.

#### Travis CI example
```yaml
language: php

matrix:
  include:
    # Arbitrary PHP version to run the sniffs against.
    - php: '7.0'
      env: SNIFF=1

before_install:
  - if [[ "$SNIFF" == "1" ]]; export PHPCS_DIR=/tmp/phpcs; fi
  - if [[ "$SNIFF" == "1" ]]; export SNIFFS_DIR=/tmp/sniffs; fi
  # Install PHP CodeSniffer.
  - if [[ "$SNIFF" == "1" ]]; then git clone -b master --depth 1 https://github.com/squizlabs/PHP_CodeSniffer.git $PHPCS_DIR; fi
  # Install WordPress Coding Standards.
  - if [[ "$SNIFF" == "1" ]]; then git clone -b master --depth 1 https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards.git $SNIFFS_DIR; fi
  # Set install path for WordPress Coding Standards.
  - if [[ "$SNIFF" == "1" ]]; then $PHPCS_DIR/scripts/phpcs --config-set installed_paths $SNIFFS_DIR; fi
  # After CodeSniffer install you should refresh your path.
  - if [[ "$SNIFF" == "1" ]]; then phpenv rehash; fi

script:
  # Run against WordPress Coding Standards.
  # If you use a custom ruleset, change `--standard=WordPress` to point to your ruleset file,
  # for example: `--standard=wpcs.xml`.
  # You can use any of the normal PHPCS command line arguments in the command:
  # https://github.com/squizlabs/PHP_CodeSniffer/wiki/Usage
  - if [[ "$SNIFF" == "1" ]]; then $PHPCS_DIR/scripts/phpcs -p . --standard=WordPress; fi
```


## Fixing errors or whitelisting them

You can find information on how to deal with some of the more frequent issues in the [wiki](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/wiki).


## Contributing

See [CONTRIBUTING](CONTRIBUTING.md), including information about [unit testing](CONTRIBUTING.md#unit-testing).

## License

See [LICENSE](LICENSE) (MIT).
