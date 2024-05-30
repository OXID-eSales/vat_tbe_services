# Change Log for OXID eShop eVAT Module

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [3.1.0-rc.1] - 2024-05-10

### Added
- Support for PHP 8.2
- Support for Symfony 6.3 components
- Missing argument and return type declarations

### Changed
- Upgraded PHPUnit version to 10  
- Default value of `geo_location` setting is changed to 0

### Removed
- Drop smarty support
- Trait OxidEsales\EVatModule\Traits\ServiceContainer

### Fixed
- The date and time of VatId change now reflect the latest change

## [3.0.0] - 2023-10-30

### Added
- Namespaces added
- Module works on smarty engine (Smarty related extensions in views/smarty resp. views/admin_smarty directory)
- Module works on twig engine (Twig related extensions in views/twig resp. views/admin_twig directory)
- Support for PHP 8.1
- Support for MySQL 8
- Service OxidEsales\EVatModule\Service\ModuleSettings
- Trait OxidEsales\EVatModule\Traits\ServiceContainer

### Changed
- Compatibility with OXID eShop 7.0.x
- All module core functionality moved to `src` directory
- Shop extensions moved to `Shop` directory
- All assets moved to `assets` folder to be available after module installation
- Classes have been moved to appropriate namespaces, `oevattbe` and `ox` prefixes have been dropped.
- Adapted tests to work with OXID eShop 7.0.x and without testing library
- License updated - now using OXID Module and Component License

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

[3.1.0-rc.1]: https://github.com/OXID-eSales/vat_tbe_services/compare/v3.0.0...v3.1.0-rc.1
[3.0.0]: https://github.com/OXID-eSales/vat_tbe_services/compare/v2.1.0...v3.0.0
[2.1.0]: https://github.com/OXID-eSales/vat_tbe_services/compare/v2.0.0...v2.1.0
[2.0.0]: https://github.com/OXID-eSales/vat_tbe_services/compare/v1.0.0...v2.0.0
[1.0.0]: https://github.com/OXID-eSales/vat_tbe_services/commits/v1.0.0