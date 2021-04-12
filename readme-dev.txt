== Code Analysis ==

PhpStan is being used for running static code analysis.

Prerequisite:
First run 'composer install' in this repository, as it will install the required package.
It's configuration file 'phpstan.neon' is available is this repository. You can change it according to your need

Now we can run the analysis. To run the analysis run the command given below. 
- 'vendor/phpstan/phpstan/phpstan analyze .'

It'll print out any errors detected by PHPStan.

