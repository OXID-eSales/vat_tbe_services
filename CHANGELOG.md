# Change Log for OXID eShop eVAT Module

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [3.0.0] - Unreleased

### Added
- Compatibility with OXID eShop 7.0.x
- Namespaces added
- Module works on smarty engine (Smarty related extensions in views/smarty resp. views/admin_smarty directory)
- Module works on twig engine (Twig related extensions in views/twig resp. views/admin_twig directory)
- Support for PHP 8.1
- Support for MySQL 8
- Service OxidEsales\EVatModule\Service\ModuleSettings
- Trait OxidEsales\EVatModule\Traits\ServiceContainer

### Changed
- All module core functionality moved to `src` directory
- Shop extensions moved to `Shop` directory
- Classes have been moved to appropriate namespaces, `oevattbe` and `ox` prefixes have been dropped.
- Adapted tests to work with OXID eShop 7.0.x and without testing library

## [2.1.0] - 2022-08-02

### Added
- Method `oeVATTBEOxViewConfig::isActiveThemeBasedOnFlow()`.

### Changed
- Adapt tests to work with OXID eShop compilation 6.4.x and up.
- Update License and Readme.
- Move documentation to OXID docs.

### Deprecated

### Removed

### Fixed
- Templates now detect not only flow but all flow based themes as well.

### Security

## [2.0.0] - 2017-08-04

### Changed
- Adapt module to work with OXID eShop version 6.

## [1.0.0] - 2015-03-24

[2.1.0]: https://github.com/OXID-eSales/vat_tbe_services/compare/v2.0.0...v2.1.0
[2.0.0]: https://github.com/OXID-eSales/vat_tbe_services/compare/v1.0.0...v2.0.0
[1.0.0]: https://github.com/OXID-eSales/vat_tbe_services/commits/v1.0.0