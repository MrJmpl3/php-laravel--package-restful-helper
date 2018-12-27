# Changelog

## v1.0

- Initial Version

## v1.1

### Fixed

- Functions:
    - Some bugs

## v1.2

### Added

- Functions:
    - existsInFields: To use fields in API Resources

## v1.3

### Changed

- Functions:
    - Rename to prevents override another function from Laravel
	
### Added

- Paramethers: 
    - Missing model parameter in existsInApiFields

## v1.4

### Fixed

- Functions:
    - existsInApiFields when fields query is empty

## v1.5

### Fixed

- Functions: 
    - Problem in sort function

## v1.6

### Changed

- Functions:
    - Rename restful_helper.php in config to restful-helper.php
    - Rename package name from laravel_restful_helper to laravel-restful-helper

## v1.6.1

### Changed

- Core:
    - Add support to Laravel 5.7

## v1.7

### Changed

- Functions:
    - apiFieldsOnlyModel to single model

### Fixed

- Functions:
    - Bug when Filter + Sort

## v2.0

### Added

- Functions:
    - executeApiResponseFromBuilderToRC
    - executeApiResponseFromBuilderToResource
    - embed
    - apiFieldsFromArrayToResource

- Fields:
    - New Field: apiExcludeFilter

### Changed

- Fields:
    - Rename transforms -> apiTransforms
    
- Functions:
    - Rename executeApiResponse -> executeApiResponseToRC
    - Rename apiFieldsOnlyModel -> executeApiResponseToResource
    - Remove existsInApiFields

## v2.0.1

### Fix

- Core:
    - Fix the README

## v2.1.0

### Fix

- Core:
    - Fix the README

### Deleted

- Functions:
    - Function: executeApiResponseToResource because with/without Dependencies Inyection , you used a Builder always.

## v2.1.1

### Fix

- Core:
    - Fix the PHPDocs
    - Fix the CHANGELOG

## v2.1.2

### Added

- Core
    - Default params $blockFilter in function executeApiResponseToRC and function executeApiResponseFromBuilderToRC
    
## v2.1.3

### Fix

- Core
    - Fix some bugs in exists and search arrays
