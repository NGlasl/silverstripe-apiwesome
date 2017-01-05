# [apiwesome](https://packagist.org/packages/nglasl/silverstripe-apiwesome)

_The current release is **2.2.5**_

> A module for SilverStripe which will automatically create customisable JSON/XML feeds for your data objects (including pages), and provides a modular security token that can be used for other applications.

## Requirement

* SilverStripe 3.0.X, 3.1.X or **3.2.X**

## Getting Started

* Place the module under your root project directory.
* Define any custom JSON/XML data object exclusions/inclusions through project configuration.
* `/dev/build`
* Select `JSON/XML` through the CMS.
* Configure attribute visibility.
* `Regenerate` the security token `x:y`
* `/apiwesome/retrieve/data-object-name/json?token=x:y`
* `/apiwesome/retrieve/data-object-name/xml?token=x:y`

## Overview

### Data Object Exclusions/Inclusions

ALL data objects are included by default (excluding some core), unless disabled or inclusions have explicitly been defined.

```php
DataObjectOutputConfiguration::customise_data_objects('exclude', array(
	'DataObjectName'
));
```

```php
DataObjectOutputConfiguration::customise_data_objects('include', array(
	'DataObjectName'
));
```

To completely disable the JSON/XML:

```php
DataObjectOutputConfiguration::customise_data_objects('disabled');
```

### Attribute Visibility Customisation

![visibility](https://raw.githubusercontent.com/nglasl/silverstripe-apiwesome/master/images/apiwesome-visibility.png)

The JSON/XML feed will only be available to data objects with attribute visibility set through the CMS. Any `has_one` relationships may be displayed, where attribute visibility is determined recursively.

#### Recursive Relationships

These are enabled by default, however will greatly impact performance if many nested relationships are visible.

To disable the recursion:

```yaml
Injector:
  APIwesomeService:
    properties:
      recursiveRelationships: false
```

### Security Token

A JSON/XML feed request will require the current security token passed through, where this may be regenerated by an administrator (invalidating the previous security token).

![token](https://raw.githubusercontent.com/nglasl/silverstripe-apiwesome/master/images/apiwesome-token.png)

The security token generation (and validation) is modular, and can still be used when the JSON/XML is completely disabled (more below):

```yaml
SecurityAdmin:
  extensions:
    - 'APIwesomeTokenExtension'
```

### Output

A JSON/XML feed request may have a number of optional filters applied, where the `&filter` will only apply to visible attributes:

* `&limit=5`
* `&sort=Attribute,ORDER`
* `&filter1=value`
* `&filter2=value`

It may also be previewed through the appropriate model admin of your data object.

![preview](https://raw.githubusercontent.com/nglasl/silverstripe-apiwesome/master/images/apiwesome-preview.png)

#### Pretty JSON

This is enabled by default, however will slightly impact performance if many nested relationships are visible.

To disable the pretty printing:

```yaml
Injector:
  APIwesomeService:
    properties:
      prettyJSON: false
```

### Developer Functionality

#### PHP

Accessing the service:

```php
$service = Singleton('APIwesomeService');
```

The methods available may be programmatically called to generate JSON, with optional filters:

```php
$JSON = $service->retrieve('DataObjectName', 'JSON');
$JSON = $service->retrieve('DataObjectName', 'JSON', 5, array(
	'Attribute',
	'ORDER'
), array(
	'Attribute1' => 'value',
	'Attribute2' => 'value'
));
```

```php
$objects = DataObjectName::get()->toNestedArray();
$JSON = $service->retrieveJSON($objects);
```

XML, with optional filters:

```php
$XML = $service->retrieve('DataObjectName', 'XML');
$XML = $service->retrieve('DataObjectName', 'XML', 5, array(
	'Attribute',
	'ORDER'
), array(
	'Attribute1' => 'value',
	'Attribute2' => 'value'
));
```

```php
$objects = DataObjectName::get()->toNestedArray();
$XML = $service->retrieveXML($objects);
```

JSON/XML for a versioned page (though the CMS may not correctly preview XML), with regard to the respective stage in `index()`:

```php
return $service->retrieveStaged($this->data()->ID, 'JSON');
```

They may also be used to parse JSON/XML from another APIwesome instance. Therefore, this module may be used as both an API and external connector between multiple projects.

```php
$objects = $service->parseJSON($JSON);
```

```php
$objects = $service->parseXML($XML);
```

The security token validation (and generation) is modular, and can be used for other applications (more above):

```php
$validation = $service->validateToken($this->getRequest()->getVar('token'));
switch($validation) {
	case APIwesomeService::VALID:

		// The token matches the current security token.

		break;
	case APIwesomeService::INVALID:

		// The token does not match a security token.

		break;
	case APIwesomeService::EXPIRED:

		// The token matches a previous security token.

		break;
}
```

#### jQuery

JSON example:

```javascript
;(function($) {
	$(function() {

		$.getJSON('//ss3.1/apiwesome/retrieve/data-object-name/json?token=' + token(), function(JSON) {

			// Iterate over each data object.

			if(JSON['APIwesome'] !== undefined) {
				$.each(JSON['APIwesome']['DataObjects'], function(index, object) {

					// The JSON feed security token is no longer valid!

					if((index === 'Expired') && (object === true)) {
						return false;
					}

					// Iterate over each visible attribute.

					$.each(object, function(type, attributes) {
						$.each(attributes, function(attribute, value) {
						});
						break;
					});
				});
			}
		})

		// The JSON feed has either not yet been configured, or no data objects were found.

		.fail(function() {
		});

	});
})(jQuery);
```

## Maintainer Contact

	Nathan Glasl, nathan@silverstripe.com.au
