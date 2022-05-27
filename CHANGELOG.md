# Changelog

## [3.0.2](https://github.com/sifophp/sifo/tree/3.0.2) (2022-05-27)

[Full Changelog](https://github.com/sifophp/sifo/compare/3.0.1...3.0.2)

**Merged pull requests:**

- Add symfony security checker action to pipeline [\#142](https://github.com/sifophp/SIFO/pull/142) ([kpicaza](https://github.com/kpicaza))
- Add easy way to instantiate domains [\#141](https://github.com/sifophp/SIFO/pull/141) ([kpicaza](https://github.com/kpicaza))
- allow to put argv via $\_SERVER [\#140](https://github.com/sifophp/SIFO/pull/140) ([kpicaza](https://github.com/kpicaza))
- AD-231 Add secure and http\_only parameters support for cookies [\#139](https://github.com/sifophp/SIFO/pull/139) ([destebang](https://github.com/destebang))

## [3.0.1](https://github.com/sifophp/sifo/tree/3.0.1) (2022-03-14)

[Full Changelog](https://github.com/sifophp/sifo/compare/3.0.0...3.0.1)

**Merged pull requests:**

- fix: default parameters for query debug call [\#138](https://github.com/sifophp/SIFO/pull/138) ([kpicaza](https://github.com/kpicaza))

## [3.0.0](https://github.com/sifophp/sifo/tree/3.0.0) (2022-02-25)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.32.0...3.0.0)

**Closed issues:**

- Unit testing for Sifo classes [\#57](https://github.com/sifophp/SIFO/issues/57)
- Change getClass\(\) to support the new autoloader and inheritance [\#40](https://github.com/sifophp/SIFO/issues/40)
- Adapt existing code to PSR-4 [\#39](https://github.com/sifophp/SIFO/issues/39)
- Create new PSR directory structure with Composer [\#38](https://github.com/sifophp/SIFO/issues/38)
- Composer dependencies and repositories definition [\#36](https://github.com/sifophp/SIFO/issues/36)
- Analysis of migration needed in Sifo 2.xx instances [\#32](https://github.com/sifophp/SIFO/issues/32)

**Merged pull requests:**

- Prepare release [\#137](https://github.com/sifophp/SIFO/pull/137) ([kpicaza](https://github.com/kpicaza))
- Trigger deprecation warning when fixing query params [\#136](https://github.com/sifophp/SIFO/pull/136) ([kpicaza](https://github.com/kpicaza))
- Remove Bash scripts folder [\#135](https://github.com/sifophp/SIFO/pull/135) ([kpicaza](https://github.com/kpicaza))
- Fix: improve query fixer regex by Edu Fabra [\#134](https://github.com/sifophp/SIFO/pull/134) ([kpicaza](https://github.com/kpicaza))
- Use maintained version of PHPThumb library [\#133](https://github.com/sifophp/SIFO/pull/133) ([kpicaza](https://github.com/kpicaza))
- Remove unused & deprecated google translate feature [\#132](https://github.com/sifophp/SIFO/pull/132) ([kpicaza](https://github.com/kpicaza))
- MDI-1466: support official adodb5 library [\#131](https://github.com/sifophp/SIFO/pull/131) ([kpicaza](https://github.com/kpicaza))
- MDI-1435 Removed mysql deprecated files [\#129](https://github.com/sifophp/SIFO/pull/129) ([seergiue](https://github.com/seergiue))
- MDI-1433: drop support for adodb-sybase [\#128](https://github.com/sifophp/SIFO/pull/128) ([kpicaza](https://github.com/kpicaza))
- MDI-1445: drop support for adodb-encrypt-mcrypt [\#127](https://github.com/sifophp/SIFO/pull/127) ([kpicaza](https://github.com/kpicaza))
- MDI-1439: set addo cache dir default as if safe mode disabled [\#126](https://github.com/sifophp/SIFO/pull/126) ([kpicaza](https://github.com/kpicaza))
- MDI-1438: drop support for adodb.mysql [\#125](https://github.com/sifophp/SIFO/pull/125) ([kpicaza](https://github.com/kpicaza))
- MDI-1437: drop support for adodb.mssql [\#124](https://github.com/sifophp/SIFO/pull/124) ([kpicaza](https://github.com/kpicaza))
- MDI-1436: drop support for adodb.ibase [\#123](https://github.com/sifophp/SIFO/pull/123) ([kpicaza](https://github.com/kpicaza))
- MDI-1440 Removed deprecated get/set magic\_quotes methods [\#122](https://github.com/sifophp/SIFO/pull/122) ([seergiue](https://github.com/seergiue))
- MDI-1442 Removed deprecated "is\_dst" parameter [\#121](https://github.com/sifophp/SIFO/pull/121) ([seergiue](https://github.com/seergiue))
- MDI-1443 Removed global var variable [\#120](https://github.com/sifophp/SIFO/pull/120) ([seergiue](https://github.com/seergiue))
- MDI-1444 Removed deprecated session\_register\(\) function [\#119](https://github.com/sifophp/SIFO/pull/119) ([seergiue](https://github.com/seergiue))
- MDI-1441 Removed/replaced deprecated unset\($this\) [\#118](https://github.com/sifophp/SIFO/pull/118) ([seergiue](https://github.com/seergiue))
- MDI-1432 Delete class using extension 'fbsql' because it is removed since PHP 5.3 [\#117](https://github.com/sifophp/SIFO/pull/117) ([antoniorova](https://github.com/antoniorova))
- MDI-1431 Fix WARNING: INI directive 'magic\_quotes\_sybase' is deprected since PHP 5.3 [\#116](https://github.com/sifophp/SIFO/pull/116) ([antoniorova](https://github.com/antoniorova))
- MDI-1428 Deleted all functions that are using get\_magic\_quotes\_gpc [\#115](https://github.com/sifophp/SIFO/pull/115) ([antoniorova](https://github.com/antoniorova))
- MDI-1425 Fix warning: Function each\(\) is deprecated since PHP 7.2 [\#114](https://github.com/sifophp/SIFO/pull/114) ([antoniorova](https://github.com/antoniorova))
- MDI-1434 Replaced $php\_errormsg with error\_get\_last\(\) [\#113](https://github.com/sifophp/SIFO/pull/113) ([seergiue](https://github.com/seergiue))
- MDI-1430 removed/replaced deprecated OCI functions [\#112](https://github.com/sifophp/SIFO/pull/112) ([seergiue](https://github.com/seergiue))
- MDI-1429 Rename constructor class name to \_\_construct [\#111](https://github.com/sifophp/SIFO/pull/111) ([seergiue](https://github.com/seergiue))
- MDI-1427 Remove dl\(\) function as it is deprecated [\#110](https://github.com/sifophp/SIFO/pull/110) ([seergiue](https://github.com/seergiue))
- MDI-1426 Replace deprecated function split with explode [\#109](https://github.com/sifophp/SIFO/pull/109) ([seergiue](https://github.com/seergiue))
- MDI-1424 Fix error remove $this on anonymous function [\#108](https://github.com/sifophp/SIFO/pull/108) ([seergiue](https://github.com/seergiue))
- MDI-1421 Delete classes that use extensions deleted since PHP 5.4 [\#107](https://github.com/sifophp/SIFO/pull/107) ([antoniorova](https://github.com/antoniorova))
- MDI-1420 Remove continue in switch statement [\#106](https://github.com/sifophp/SIFO/pull/106) ([seergiue](https://github.com/seergiue))
- MDI-1418 Fix error:  can no longer be used in a plain function or method since PHP 7.1 [\#105](https://github.com/sifophp/SIFO/pull/105) ([antoniorova](https://github.com/antoniorova))
- MDI-1419: Change create\_function by anon function [\#104](https://github.com/sifophp/SIFO/pull/104) ([kpicaza](https://github.com/kpicaza))
- MDI-1417 Ignore warning debug\_backtrace\(\) [\#103](https://github.com/sifophp/SIFO/pull/103) ([seergiue](https://github.com/seergiue))
- MDI-1415 Fix warning: Passing the  and  parameters in reverse order to implode [\#102](https://github.com/sifophp/SIFO/pull/102) ([antoniorova](https://github.com/antoniorova))
- MDI-1414: Install code-sniffer php compatibility checker [\#101](https://github.com/sifophp/SIFO/pull/101) ([kpicaza](https://github.com/kpicaza))
- MDI-1416 Rename method with PHP-reserved name [\#100](https://github.com/sifophp/SIFO/pull/100) ([seergiue](https://github.com/seergiue))

## [v2.32.0](https://github.com/sifophp/sifo/tree/v2.32.0) (2021-04-28)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.31.3...v2.32.0)

**Merged pull requests:**

- Read .env file of environment [\#99](https://github.com/sifophp/SIFO/pull/99) ([noelamaya](https://github.com/noelamaya))

## [v2.31.3](https://github.com/sifophp/sifo/tree/v2.31.3) (2021-03-29)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.31.2...v2.31.3)

**Merged pull requests:**

- Reverts temporarilly the change of the featured images since it's not working in LIVE. [\#98](https://github.com/sifophp/SIFO/pull/98) ([emartos](https://github.com/emartos))

## [v2.31.2](https://github.com/sifophp/sifo/tree/v2.31.2) (2021-03-18)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.31.1...v2.31.2)

**Merged pull requests:**

- Adds compatibility with media:content tags. It is required for retrie… [\#97](https://github.com/sifophp/SIFO/pull/97) ([emartos](https://github.com/emartos))

## [v2.31.1](https://github.com/sifophp/sifo/tree/v2.31.1) (2021-02-26)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.31.0...v2.31.1)

## [v2.31.0](https://github.com/sifophp/sifo/tree/v2.31.0) (2021-01-20)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.30.2...v2.31.0)

**Merged pull requests:**

- Generate new PHP session name with vertical and environment to avoid wrong behaviors [\#96](https://github.com/sifophp/SIFO/pull/96) ([noelamaya](https://github.com/noelamaya))

## [v2.30.2](https://github.com/sifophp/sifo/tree/v2.30.2) (2020-10-08)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.30.1...v2.30.2)

**Merged pull requests:**

- Return content from var\_export in DataBaseHandler [\#95](https://github.com/sifophp/SIFO/pull/95) ([p0lemic](https://github.com/p0lemic))

## [v2.30.1](https://github.com/sifophp/sifo/tree/v2.30.1) (2020-10-08)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.30.0...v2.30.1)

**Merged pull requests:**

- Fix error printing on DataBaseHandler [\#94](https://github.com/sifophp/SIFO/pull/94) ([p0lemic](https://github.com/p0lemic))

## [v2.30.0](https://github.com/sifophp/sifo/tree/v2.30.0) (2020-09-22)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.29.2...v2.30.0)

**Merged pull requests:**

- Print errorInfo with a var\_export [\#93](https://github.com/sifophp/SIFO/pull/93) ([p0lemic](https://github.com/p0lemic))

## [v2.29.2](https://github.com/sifophp/sifo/tree/v2.29.2) (2020-09-07)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.29.1...v2.29.2)

**Merged pull requests:**

- Update Symfony/Yaml to 4.4 version [\#92](https://github.com/sifophp/SIFO/pull/92) ([p0lemic](https://github.com/p0lemic))

## [v2.29.1](https://github.com/sifophp/sifo/tree/v2.29.1) (2020-08-07)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.29.0...v2.29.1)

**Merged pull requests:**

- Set DependencyInjector::getImportedServices as static to be able to obtain all services without any instance of DependencyInjector [\#91](https://github.com/sifophp/SIFO/pull/91) ([xserrat](https://github.com/xserrat))

## [v2.29.0](https://github.com/sifophp/sifo/tree/v2.29.0) (2020-07-31)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.28.0...v2.29.0)

**Merged pull requests:**

- add changelog [\#90](https://github.com/sifophp/SIFO/pull/90) ([kpicaza](https://github.com/kpicaza))
- TARC-885: allow using ContainerInterface instance as sifo container in DependencyInjector class [\#89](https://github.com/sifophp/SIFO/pull/89) ([kpicaza](https://github.com/kpicaza))

## [v2.28.0](https://github.com/sifophp/sifo/tree/v2.28.0) (2020-07-06)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.27.3...v2.28.0)

**Merged pull requests:**

- Injectable container [\#88](https://github.com/sifophp/SIFO/pull/88) ([p0lemic](https://github.com/p0lemic))

## [v2.27.3](https://github.com/sifophp/sifo/tree/v2.27.3) (2020-05-25)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.27.2...v2.27.3)

**Merged pull requests:**

- Update dotenv to version 5.0 [\#87](https://github.com/sifophp/SIFO/pull/87) ([p0lemic](https://github.com/p0lemic))

## [v2.27.2](https://github.com/sifophp/sifo/tree/v2.27.2) (2020-04-06)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.27.1...v2.27.2)

**Merged pull requests:**

- Controller instance var [\#86](https://github.com/sifophp/SIFO/pull/86) ([p0lemic](https://github.com/p0lemic))

## [v2.27.1](https://github.com/sifophp/sifo/tree/v2.27.1) (2020-03-06)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.27.0...v2.27.1)

**Merged pull requests:**

- Fix Registry::invalidate method to invalidate a key that contains a NULL value [\#85](https://github.com/sifophp/SIFO/pull/85) ([xserrat](https://github.com/xserrat))

## [v2.27.0](https://github.com/sifophp/sifo/tree/v2.27.0) (2020-02-19)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.26.2...v2.27.0)

## [v2.26.2](https://github.com/sifophp/sifo/tree/v2.26.2) (2020-02-14)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.26.1...v2.26.2)

## [v2.26.1](https://github.com/sifophp/sifo/tree/v2.26.1) (2020-02-13)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.26.0...v2.26.1)

## [v2.26.0](https://github.com/sifophp/sifo/tree/v2.26.0) (2019-08-05)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.25.0...v2.26.0)

**Merged pull requests:**

- Allow arguments with variables in services definition [\#83](https://github.com/sifophp/SIFO/pull/83) ([p0lemic](https://github.com/p0lemic))
- Allow arguments with variables in services definition [\#82](https://github.com/sifophp/SIFO/pull/82) ([p0lemic](https://github.com/p0lemic))

## [v2.25.0](https://github.com/sifophp/sifo/tree/v2.25.0) (2019-06-11)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.24.1...v2.25.0)

## [v2.24.1](https://github.com/sifophp/sifo/tree/v2.24.1) (2019-06-11)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.24.0...v2.24.1)

**Merged pull requests:**

- Change error type to E\_WARNING when try to store a null value in session [\#81](https://github.com/sifophp/SIFO/pull/81) ([p0lemic](https://github.com/p0lemic))

## [v2.24.0](https://github.com/sifophp/sifo/tree/v2.24.0) (2019-05-30)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.23.0...v2.24.0)

**Merged pull requests:**

- Load dotenv file from root folder [\#80](https://github.com/sifophp/SIFO/pull/80) ([p0lemic](https://github.com/p0lemic))

## [v2.23.0](https://github.com/sifophp/sifo/tree/v2.23.0) (2019-05-28)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.22.4...v2.23.0)

**Merged pull requests:**

- TARC-237 Configure Sifo Dependency Injector to be able to locate service definition files outside instances folders [\#79](https://github.com/sifophp/SIFO/pull/79) ([xserrat](https://github.com/xserrat))

## [v2.22.4](https://github.com/sifophp/sifo/tree/v2.22.4) (2019-03-21)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.22.3...v2.22.4)

## [v2.22.3](https://github.com/sifophp/sifo/tree/v2.22.3) (2019-03-21)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.22.2...v2.22.3)

## [v2.22.2](https://github.com/sifophp/sifo/tree/v2.22.2) (2019-03-21)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.22.1...v2.22.2)

## [v2.22.1](https://github.com/sifophp/sifo/tree/v2.22.1) (2019-03-13)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.22.0...v2.22.1)

**Merged pull requests:**

- Changes for generate definition services files complying with PSR-2. [\#77](https://github.com/sifophp/SIFO/pull/77) ([mangasf](https://github.com/mangasf))

## [v2.22.0](https://github.com/sifophp/sifo/tree/v2.22.0) (2019-02-07)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.21.0...v2.22.0)

**Merged pull requests:**

- Uvinum/allow load controller from di container [\#76](https://github.com/sifophp/SIFO/pull/76) ([p0lemic](https://github.com/p0lemic))

## [v2.21.0](https://github.com/sifophp/sifo/tree/v2.21.0) (2019-02-05)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.20.1...v2.21.0)

**Merged pull requests:**

- Uvinum/add factory option to dependency injection [\#74](https://github.com/sifophp/SIFO/pull/74) ([p0lemic](https://github.com/p0lemic))

## [v2.20.1](https://github.com/sifophp/sifo/tree/v2.20.1) (2019-01-23)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.20.0...v2.20.1)

## [v2.20.0](https://github.com/sifophp/sifo/tree/v2.20.0) (2018-10-02)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.19.0...v2.20.0)

## [v2.19.0](https://github.com/sifophp/sifo/tree/v2.19.0) (2018-08-27)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.18.0...v2.19.0)

**Merged pull requests:**

- Allowing Twig plugins to be parsed by Sifo [\#73](https://github.com/sifophp/SIFO/pull/73) ([marcossegovia](https://github.com/marcossegovia))

## [v2.18.0](https://github.com/sifophp/sifo/tree/v2.18.0) (2018-08-01)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.17.3...v2.18.0)

## [v2.17.3](https://github.com/sifophp/sifo/tree/v2.17.3) (2018-07-30)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.17.2...v2.17.3)

## [v2.17.2](https://github.com/sifophp/sifo/tree/v2.17.2) (2018-06-21)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.17.1...v2.17.2)

## [v2.17.1](https://github.com/sifophp/sifo/tree/v2.17.1) (2018-05-28)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.17.0...v2.17.1)

## [v2.17.0](https://github.com/sifophp/sifo/tree/v2.17.0) (2018-05-08)

[Full Changelog](https://github.com/sifophp/sifo/compare/v3.0.0-beta.8...v2.17.0)

## [v3.0.0-beta.8](https://github.com/sifophp/sifo/tree/v3.0.0-beta.8) (2018-04-12)

[Full Changelog](https://github.com/sifophp/sifo/compare/v3.0.0-beta.7...v3.0.0-beta.8)

## [v3.0.0-beta.7](https://github.com/sifophp/sifo/tree/v3.0.0-beta.7) (2018-04-11)

[Full Changelog](https://github.com/sifophp/sifo/compare/v3.0.0-beta.6...v3.0.0-beta.7)

## [v3.0.0-beta.6](https://github.com/sifophp/sifo/tree/v3.0.0-beta.6) (2018-04-11)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.16.4...v3.0.0-beta.6)

## [v2.16.4](https://github.com/sifophp/sifo/tree/v2.16.4) (2018-04-05)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.16.3...v2.16.4)

## [v2.16.3](https://github.com/sifophp/sifo/tree/v2.16.3) (2017-11-07)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.16.2...v2.16.3)

## [v2.16.2](https://github.com/sifophp/sifo/tree/v2.16.2) (2017-10-03)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.16.1...v2.16.2)

## [v2.16.1](https://github.com/sifophp/sifo/tree/v2.16.1) (2017-09-19)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.16.0...v2.16.1)

## [v2.16.0](https://github.com/sifophp/sifo/tree/v2.16.0) (2017-08-24)

[Full Changelog](https://github.com/sifophp/sifo/compare/elastic02...v2.16.0)

## [elastic02](https://github.com/sifophp/sifo/tree/elastic02) (2017-07-26)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.15.2...elastic02)

## [v2.15.2](https://github.com/sifophp/sifo/tree/v2.15.2) (2017-07-14)

[Full Changelog](https://github.com/sifophp/sifo/compare/elastic01...v2.15.2)

## [elastic01](https://github.com/sifophp/sifo/tree/elastic01) (2017-07-12)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.15.1...elastic01)

## [v2.15.1](https://github.com/sifophp/sifo/tree/v2.15.1) (2017-06-27)

[Full Changelog](https://github.com/sifophp/sifo/compare/v3.0.0-beta.5...v2.15.1)

## [v3.0.0-beta.5](https://github.com/sifophp/sifo/tree/v3.0.0-beta.5) (2017-05-17)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.15.0...v3.0.0-beta.5)

## [v2.15.0](https://github.com/sifophp/sifo/tree/v2.15.0) (2017-05-17)

[Full Changelog](https://github.com/sifophp/sifo/compare/v3.0.0-beta.4...v2.15.0)

## [v3.0.0-beta.4](https://github.com/sifophp/sifo/tree/v3.0.0-beta.4) (2017-05-15)

[Full Changelog](https://github.com/sifophp/sifo/compare/v3.0.0-beta.3...v3.0.0-beta.4)

## [v3.0.0-beta.3](https://github.com/sifophp/sifo/tree/v3.0.0-beta.3) (2017-05-11)

[Full Changelog](https://github.com/sifophp/sifo/compare/v3.0.0-beta.2...v3.0.0-beta.3)

## [v3.0.0-beta.2](https://github.com/sifophp/sifo/tree/v3.0.0-beta.2) (2017-05-11)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.14.1...v3.0.0-beta.2)

## [v2.14.1](https://github.com/sifophp/sifo/tree/v2.14.1) (2017-05-11)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.13.4...v2.14.1)

**Closed issues:**

- Add SIFO to packagist [\#45](https://github.com/sifophp/SIFO/issues/45)

## [v2.13.4](https://github.com/sifophp/sifo/tree/v2.13.4) (2017-04-22)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.13.3...v2.13.4)

**Merged pull requests:**

- Multiple improvements from Uvinum's fork [\#72](https://github.com/sifophp/SIFO/pull/72) ([obokaman-com](https://github.com/obokaman-com))

## [v2.13.3](https://github.com/sifophp/sifo/tree/v2.13.3) (2017-04-12)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.13.1...v2.13.3)

## [v2.13.1](https://github.com/sifophp/sifo/tree/v2.13.1) (2017-03-31)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.13.2...v2.13.1)

## [v2.13.2](https://github.com/sifophp/sifo/tree/v2.13.2) (2017-03-31)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.12.2...v2.13.2)

## [v2.12.2](https://github.com/sifophp/sifo/tree/v2.12.2) (2016-11-14)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.12.1...v2.12.2)

## [v2.12.1](https://github.com/sifophp/sifo/tree/v2.12.1) (2016-11-14)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.11.5...v2.12.1)

## [v2.11.5](https://github.com/sifophp/sifo/tree/v2.11.5) (2016-05-05)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.11.4...v2.11.5)

## [v2.11.4](https://github.com/sifophp/sifo/tree/v2.11.4) (2016-05-04)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.11.3...v2.11.4)

## [v2.11.3](https://github.com/sifophp/sifo/tree/v2.11.3) (2016-05-04)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.11.2...v2.11.3)

## [v2.11.2](https://github.com/sifophp/sifo/tree/v2.11.2) (2016-05-03)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.11.1...v2.11.2)

## [v2.11.1](https://github.com/sifophp/sifo/tree/v2.11.1) (2016-05-03)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.11.0...v2.11.1)

## [v2.11.0](https://github.com/sifophp/sifo/tree/v2.11.0) (2016-05-03)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.10.3...v2.11.0)

## [v2.10.3](https://github.com/sifophp/sifo/tree/v2.10.3) (2016-04-26)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.10.2...v2.10.3)

## [v2.10.2](https://github.com/sifophp/sifo/tree/v2.10.2) (2016-04-20)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.10.1...v2.10.2)

## [v2.10.1](https://github.com/sifophp/sifo/tree/v2.10.1) (2016-04-12)

[Full Changelog](https://github.com/sifophp/sifo/compare/v2.10.0...v2.10.1)

## [v2.10.0](https://github.com/sifophp/sifo/tree/v2.10.0) (2016-04-12)

[Full Changelog](https://github.com/sifophp/sifo/compare/v3.0.0-beta.1...v2.10.0)

**Closed issues:**

- Move FilterException to correct location in travis-integration branch [\#67](https://github.com/sifophp/SIFO/issues/67)
- Make the "common" instance run [\#59](https://github.com/sifophp/SIFO/issues/59)
- Helper script to transition 2.xx to 3.0 [\#33](https://github.com/sifophp/SIFO/issues/33)

**Merged pull requests:**

- Merge pull request \#61 from sifophp/devel [\#62](https://github.com/sifophp/SIFO/pull/62) ([alombarte](https://github.com/alombarte))
- Devel [\#61](https://github.com/sifophp/SIFO/pull/61) ([alombarte](https://github.com/alombarte))

## [v3.0.0-beta.1](https://github.com/sifophp/sifo/tree/v3.0.0-beta.1) (2014-10-14)

[Full Changelog](https://github.com/sifophp/sifo/compare/sifo-2.9.1...v3.0.0-beta.1)

## [sifo-2.9.1](https://github.com/sifophp/sifo/tree/sifo-2.9.1) (2014-09-13)

[Full Changelog](https://github.com/sifophp/sifo/compare/sifo-2.9...sifo-2.9.1)

**Closed issues:**

- Add a before\_install clause in .travis.yml [\#58](https://github.com/sifophp/SIFO/issues/58)
- Move Bootstrap inside the Sifo folder [\#56](https://github.com/sifophp/SIFO/issues/56)
- Isolate autoloader inside Bootstrap to an Autoloader.php file [\#55](https://github.com/sifophp/SIFO/issues/55)
- Remove direct includes of files [\#52](https://github.com/sifophp/SIFO/issues/52)
- Remove all closing PHP tags [\#50](https://github.com/sifophp/SIFO/issues/50)
- Put all the classes in their correct namespace [\#41](https://github.com/sifophp/SIFO/issues/41)
- Integration of PHPUnit with Travis-CI [\#37](https://github.com/sifophp/SIFO/issues/37)
- Proposal of tree structure for Sifo 3, compliant with PSR-0,1,2,3,4 [\#31](https://github.com/sifophp/SIFO/issues/31)
- Split Filter classes into several files [\#11](https://github.com/sifophp/SIFO/issues/11)

**Merged pull requests:**

- trigger\_error on genurl action parameter invalid value set [\#60](https://github.com/sifophp/SIFO/pull/60) ([JavierCane](https://github.com/JavierCane))

## [sifo-2.9](https://github.com/sifophp/sifo/tree/sifo-2.9) (2014-01-09)

[Full Changelog](https://github.com/sifophp/sifo/compare/sifo-2.8...sifo-2.9)

**Closed issues:**

- Bug en js.config de la instancia common [\#17](https://github.com/sifophp/SIFO/issues/17)
- Exceptions\_XXX thrown in preDispatch invoke the autoloader when they should be in memory [\#10](https://github.com/sifophp/SIFO/issues/10)

**Merged pull requests:**

- Update README.md [\#30](https://github.com/sifophp/SIFO/pull/30) ([alombarte](https://github.com/alombarte))
- Validate email regex used in the Filter class improved [\#29](https://github.com/sifophp/SIFO/pull/29) ([JavierCane](https://github.com/JavierCane))
- Improved findi18n [\#28](https://github.com/sifophp/SIFO/pull/28) ([JavierCane](https://github.com/JavierCane))
- Added "memory" for change directory [\#26](https://github.com/sifophp/SIFO/pull/26) ([alexgt9](https://github.com/alexgt9))
- Fix problem with script, to not depends on the current directory [\#25](https://github.com/sifophp/SIFO/pull/25) ([alexgt9](https://github.com/alexgt9))
- Command line options "Did you mean". [\#24](https://github.com/sifophp/SIFO/pull/24) ([JavierCane](https://github.com/JavierCane))
- Added check before executing a class method other than build. [\#21](https://github.com/sifophp/SIFO/pull/21) ([nilportugues](https://github.com/nilportugues))
- Updating Controller class to make it 100% compatible with new router sys... [\#20](https://github.com/sifophp/SIFO/pull/20) ([nilportugues](https://github.com/nilportugues))
- Bootstrap.php : Changed $path\_parts\[0\] to implode\('/',$path\_parts\). [\#19](https://github.com/sifophp/SIFO/pull/19) ([nilportugues](https://github.com/nilportugues))
- Extended the Router class [\#18](https://github.com/sifophp/SIFO/pull/18) ([nilportugues](https://github.com/nilportugues))

## [sifo-2.8](https://github.com/sifophp/sifo/tree/sifo-2.8) (2013-04-07)

[Full Changelog](https://github.com/sifophp/sifo/compare/sifo-2.7...sifo-2.8)

**Closed issues:**

- Cache: Avoid the dogpile effect \(add cache locking\) [\#15](https://github.com/sifophp/SIFO/issues/15)

## [sifo-2.7](https://github.com/sifophp/sifo/tree/sifo-2.7) (2012-11-02)

[Full Changelog](https://github.com/sifophp/sifo/compare/sifo-2.6...sifo-2.7)

**Closed issues:**

- JSS and CSS Minify scripts [\#4](https://github.com/sifophp/SIFO/issues/4)

## [sifo-2.6](https://github.com/sifophp/sifo/tree/sifo-2.6) (2012-07-17)

[Full Changelog](https://github.com/sifophp/sifo/compare/sifo-2.5...sifo-2.6)

**Closed issues:**

- Create an instance to deploy code on servers [\#6](https://github.com/sifophp/SIFO/issues/6)

**Merged pull requests:**

- Added phpunit and php-unit-skeleton generators [\#12](https://github.com/sifophp/SIFO/pull/12) ([alombarte](https://github.com/alombarte))

## [sifo-2.5](https://github.com/sifophp/sifo/tree/sifo-2.5) (2012-05-25)

[Full Changelog](https://github.com/sifophp/sifo/compare/sifo-2.4...sifo-2.5)

## [sifo-2.4](https://github.com/sifophp/sifo/tree/sifo-2.4) (2012-05-15)

[Full Changelog](https://github.com/sifophp/sifo/compare/sifo-2.3...sifo-2.4)

## [sifo-2.3](https://github.com/sifophp/sifo/tree/sifo-2.3) (2012-04-18)

[Full Changelog](https://github.com/sifophp/sifo/compare/sifo-2.2...sifo-2.3)

## [sifo-2.2](https://github.com/sifophp/sifo/tree/sifo-2.2) (2012-02-10)

[Full Changelog](https://github.com/sifophp/sifo/compare/stable-php-5.2-last-commit...sifo-2.2)

## [stable-php-5.2-last-commit](https://github.com/sifophp/sifo/tree/stable-php-5.2-last-commit) (2011-12-07)

[Full Changelog](https://github.com/sifophp/sifo/compare/sifo-1.9...stable-php-5.2-last-commit)

## [sifo-1.9](https://github.com/sifophp/sifo/tree/sifo-1.9) (2011-12-07)

[Full Changelog](https://github.com/sifophp/sifo/compare/sifo-2.1...sifo-1.9)

**Closed issues:**

- Enable rebuild when debug is OFF and instance is in development mode [\#8](https://github.com/sifophp/SIFO/issues/8)
- SVN as read-only and mirror of Github repo [\#5](https://github.com/sifophp/SIFO/issues/5)

## [sifo-2.1](https://github.com/sifophp/sifo/tree/sifo-2.1) (2011-10-26)

[Full Changelog](https://github.com/sifophp/sifo/compare/stable-php-5.2...sifo-2.1)

**Merged pull requests:**

- PHPUnit 3.5 + VisualPHPUnit [\#3](https://github.com/sifophp/SIFO/pull/3) ([thedae](https://github.com/thedae))

## [stable-php-5.2](https://github.com/sifophp/sifo/tree/stable-php-5.2) (2011-10-06)

[Full Changelog](https://github.com/sifophp/sifo/compare/6a30ab66d5bef33d334090c58bf984fc78a86ef5...stable-php-5.2)

**Merged pull requests:**

- Added generated folder for static files in create\_instance script [\#2](https://github.com/sifophp/SIFO/pull/2) ([thedae](https://github.com/thedae))
- Added generated folder for static files in create\_instance script [\#1](https://github.com/sifophp/SIFO/pull/1) ([thedae](https://github.com/thedae))



\* *This Changelog was automatically generated by [github_changelog_generator](https://github.com/github-changelog-generator/github-changelog-generator)*
