# Members of Parliament

A WordPress theme and plugin combination for displaying information about Members of Parliament. This project provides a customized WordPress installation to showcase parliamentary representatives, their details, and activities.

## Features

- Custom post type for Members of Parliament (MPs)
- Dedicated templates for displaying MPs information
- Archive page listing all MPs
- Individual MP profile pages with detailed information
- Custom fields for storing MP details (constituency, party affiliation, etc.)
- Responsive design

## Installation

1. Clone this repository to your WordPress installation directory:
   ```
   git clone https://github.com/lesniakr/sejmownik.git
   ```

2. Activate the "Sejmownik" theme in WordPress admin under Appearance > Themes

3. Activate the "Members of Parliament" plugin in WordPress admin under Plugins

4. Import MP data or add it manually through the WordPress admin interface

## Theme Structure

The Sejmownik theme includes:

- `archive-mp.php` - Template for displaying the list of MPs
- `single-mp.php` - Template for displaying individual MP profiles
- Custom homepage template - Can be assigned to any page to display MPs

## Custom Fields

The MP post type includes the following custom fields:

- Club/Party affiliation
- District information
- Voivodeship (province)
- Email contact
- MP ID (reference ID)
- Committee memberships and roles

## Creating a Homepage

1. Create a new page in WordPress
2. Select the "Strona główna z listą posłów" template
3. Set this page as your homepage in Settings > Reading

## Customization

You can customize the appearance and behavior of the theme by:

- Editing the theme CSS
- Adding/modifying templates
- Adjusting the custom field structure

## Credits

- Developed by: Rafał Leśniak
- Website: [https://rafallesniak.com/](https://rafallesniak.com/)
- Theme: Sejmownik

## License

This project is licensed under the GPL v2 or later.
