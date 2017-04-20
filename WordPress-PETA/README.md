# PETA Coding Standards for PHP_CodeSniffer



## Installation

### Standalone

1. Install PHP_CodeSniffer by following its [installation instructions](https://github.com/squizlabs/PHP_CodeSniffer#installation) (via Composer, PEAR, or Git checkout).

2. Clone WordPress standards repository:

        git clone -b develop https://github.com/PETAF/WordPress-Coding-Standards.git petacs

3. Add its path to PHP_CodeSniffer configuration:

        phpcs --config-set installed_paths /path/to/petacs


To summarize:

```bash
cd ~/projects
git clone https://github.com/squizlabs/PHP_CodeSniffer.git phpcs
git clone -b develop https://github.com/PETAF/WordPress-Coding-Standards.git petacs
cd phpcs
./scripts/phpcs --config-set installed_paths ../petacs
```

And then add the `~/projects/phpcs/scripts` directory to your `PATH` environment variable via your `.bashrc`.

You should then see `WordPress-PETA` et al listed when you run `phpcs -i`.

## How to use

See the [How to use section here](/README.md#how-to-use) for instructions. Use the `WordPress-PETA` standard when configuring your IDE/command line for phpcs parsing.
