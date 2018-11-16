# Changelog

## v1.0

- Initial Version

## v1.1

### Fixed

- Some bugs

## v1.2

### Added

- ExistsInFields function , to use fields in API Resources

## v1.3

### Fixed

- Rename some functions to prevents override another function from Laravel
	
### Added

- Missing model parameter in existsInApiFields

## v1.4

### Fixed

- ExistsInApiFields when fields query is empty

## v1.5

### Fixed

- Problem in sort function

## v1.6

### Changed

- Rename: restful_helper.php in config to restful-helper.php
- Rename: Package name from laravel_restful_helper to laravel-restful-helper

## v1.6.1

### Changed

- Add support to Laravel 5.7

## v1.7

### Changed

- To single model use apiFieldsOnlyModel

### Fixed

- Bug when Filter + Sort

## v2.0

### Added

- New Function: executeApiResponseFromBuilderToRC
- New Function: executeApiResponseFromBuilderToResource
- New Function: embed
- New Function: apiFieldsFromArrayToResource
- New Field: apiExcludeFilter

### Changed

- Rename field = transforms -> apiTransforms
- Rename Function: executeApiResponse -> executeApiResponseToRC
- Rename Function: apiFieldsOnlyModel -> executeApiResponseToResource
- Remove Function: existsInApiFields

## v2.0.1

### Fix

- Fix the README

## v2.1.0

## Fix

- Fix the README

## Deleted

- Delete Function: executeApiResponseToResource because with/without Dependencies Inyection , you used a Builder always.
