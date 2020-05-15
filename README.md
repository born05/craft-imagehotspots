# Image Hotspots (beta)

Image Hotspots fieldtype for adding multiple hotspots to images from a matrix or super-table in Craft CMS.

## Setup & usage

1. Create an asset field for use with images.
2. Create a "Image Hotspot" field, pointing to the asset field's handle. _When used inside a super-table or matrix, multiple hotspots can be placed on one image._
3. Make sure the hotspot field is on the same or a higher level inside an entry. _The hotspot field goes down the entry's element tree untill it finds the asset field matching the handle._
4. Fill the asset field on the entry and save.
5. Pick the hotspots on the asset using the "Hotspot" button.

## Requirements

- Craft 3.3.*

## Installation

Install from the Plugin Store or composer:

```bash
composer require born05/craft-imagehotspots
./craft install/plugin imagehotspots
```

## Screens

#### Field settings screen
![Field settings screen](https://raw.githubusercontent.com/born05/craft-imagehotspots/master/field-settings.png)

#### Field in use inside a super-table
![Field in use inside a super-table](https://raw.githubusercontent.com/born05/craft-imagehotspots/master/field-use.png)

#### Hotspot picker in use
![Hotspot picker in use](https://raw.githubusercontent.com/born05/craft-imagehotspots/master/picker-use.png)

## GraphQl support

Use the field in graphql:

```gql
hotspot {
    x
    y
}
```

## License

Copyright © [Born05](https://www.born05.com/)

See [license](https://github.com/born05/craft-imagehotspots/blob/master/LICENSE.md)