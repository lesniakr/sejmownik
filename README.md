# Sejmownik (Members of Parliament)

A WordPress theme and plugin combination for displaying information about Members of Parliament. This project provides a customized WordPress installation to showcase parliamentary representatives, their details, activities, and voting records.

## Features

- Custom post type for Members of Parliament (MPs)
- Dedicated templates for displaying MPs information
- Archive page listing all MPs with sorting options
- Individual MP profile pages with detailed information
- MP voting records directly from the Sejm API
- Custom fields for storing MP details (constituency, party affiliation, etc.)
- Responsive design with Tailwind CSS
- ACF integration for structured data

## Installation

1. Clone this repository to your WordPress installation directory:
   ```
   git clone https://github.com/lesniakr/sejmownik.git
   ```

2. Activate the "Sejmownik" theme in WordPress admin under Appearance > Themes

3. Activate the "Posłowie Parlamentu" plugin in WordPress admin under Plugins

4. Import MP data through the plugin's admin interface or add it manually

## Plugin Features

The "Posłowie Parlamentu" plugin includes:

- Custom post type for MPs
- ACF field groups for structured MP data
- API integration with the official Sejm API (https://api.sejm.gov.pl/)
- Import tool for fetching MPs directly from the API
- Admin interface for managing imports
- WP-CLI commands for bulk operations
- MP voting records integration

## Theme Structure

The Sejmownik theme includes:

- `archive-mp.php` - Template for displaying the list of MPs with sorting options
- `single-mp.php` - Template for displaying individual MP profiles with details and voting records
- Homepage template - Can be assigned to any page to display featured MPs
- Responsive design using Tailwind CSS

## Custom Fields

The MP post type includes the following custom fields:

- Basic identification (ID, first name, last name, active status)
- Parliamentary information (club/party, district, votes)
- Personal information (birth date, birth location, education, profession)
- Contact information (email)
- Additional information (biography, grammatical name forms)
- Voting records (fetched directly from the API)

## Creating a Homepage

1. Create a new page in WordPress
2. Select the "Strona główna z listą posłów" template
3. Set this page as your homepage in Settings > Reading

## Customization

You can customize the appearance and behavior of the theme by:

- Editing the theme options in the WordPress admin
- Modifying the Tailwind CSS styles
- Adjusting the ACF field structure
- Creating custom templates

## Development Notes

- ACF fields are registered during the `init` hook to prevent translation loading issues
- API interactions are optimized for performance and reliability
- The plugin uses WordPress transients to cache API data when appropriate
- The theme is built with performance and accessibility in mind

## Credits

- Developed by: Rafał Leśniak
- Website: [https://rafallesniak.com/](https://rafallesniak.com/)
- Sejm API: [https://api.sejm.gov.pl/](https://api.sejm.gov.pl/)

## License

This project is licensed under the GPL v2 or later.
