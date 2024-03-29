# codejam
PHP software for holding online coding competitions

> [!CAUTION]
> **DO NOT USE THIS PROJECT. It is unmaintained and has known security
> vulnerabilities.** It was originally created in 2015 and contains legacy code
> that in no way reflects my current coding standards and best practices
> (fortunately, since then I've progressed in terms of designing and building
> software having security in mind!).

[![Crowdin](https://d322cqt584bo4o.cloudfront.net/codejam/localized.png)](https://crowdin.com/project/codejam)

## Project status
Unmaintained. Please DO NOT run this in production or anywhere.

## Requirements
* PHP
* Support for the mysqli extension
* Mysql database

## Set-up
1. Clone this repository into your Web Server or download a [zip tarball](https://github.com/avm99963/codejam/archive/master.zip). `git clone https://github.com/avm99963/codejam.git`

2. Open the `config.default.php` file and edit the variables to configure the database connection, random file names length, etc. Then save it as `config.php`

3. Open in your preferred web browser the install.php webpage of the root directory.

4. Fill in the form to create all the database structure and submit the form.

5. That's it! Did you expect this to be so easy?

## Features
* Assign roles to users
 * Hyperadmin, problem writer, judge or contestant
* Allow users to sign up with an email address from only one email domain (for internal contests)
* Create various types of contests
 * Private contests
   * Only invited contestants will be able to view the leaderboard and participate
 * Semiprivate contests
   * All contestants can view the leaderboard, but only invited contestants can view the problem statements and participate
 * Public contests
   * All contestants can view the leaderboard and participate
* Invite contestants to specific contests
* Invite top X contestants from a specific contest to another contest (useful if you want to do many rounds, and you can set various contests as different rounds)
* Write problems and provide input and output files for the small and large datasets
* Choose a contest to be highlighted in the portal of the website (to do)
* Configure 2FA (aka 2-Step Verification) with a U2F Security Key or an auto-generated code
* Participate in contests, with an AJAX-built dashboard to read problem statements, submit solutions and see a summary of your submissions, time left, rank, score and the top 10 contestants.

