**Read in other languages: [Українська](README.md), [English](README.en.md)**

# plugin-posts-rating

## Description

Posts Rating Widget is a WordPress plugin that adds a widget to display recent posts with the ability to rate articles.

## Installation

1. Download the plugin and unzip the archive.
2. Upload the `plugin-posts-rating` folder to the `/wp-content/plugins/` directory on your server.
3. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage

1. Go to `Appearance` > `Widgets`.
2. Find the `Posts Rating Widget` widget in the list of available widgets.
3. Drag it to the desired sidebar.
4. Configure the widget using the available options:
   - Name of the widget.
   - Number of posts to display.
   - Show rating (yes/no).
   - Initial position of the rating panel (bottom-left, bottom-center, bottom-right).

## Testing

1. Open your website where the widget was added.
2. Make sure the widget displays the latest posts.
3. Try to vote for any article by clicking on the stars.
4. Check that your vote has been taken into account and the new average rating of the article has been saved.


### Functional

- Displaying the latest posts as a list.
- Ability to rate posts from 1 to 5 stars.
- AJAX requests to save scores without reloading the page.
- Floating panel of the rating with the possibility of setting its initial position.

#### Main plugin file: `posts-rating-widget.php`