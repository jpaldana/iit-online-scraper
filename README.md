# iit-online-scraper

Since there's no nice way to get notifications if a new lecture video is online, why not build one?

## Disclaimer

>This script is meant to be used for personal use only. Please do not redistribute any copyrighted material.

## Usage

### Requirements

- PHP (any version should work, tested with 7.x)
- curl

1. Extract source files into a directory
2. Create a `media` subdirectory
3. Copy `vars-default.php` to `vars.php` and edit the values as needed
4. Execute the script via CLI with `php /path/to/script/crawl.php`
5. _(optional)_ Run the script automagically by adding a crontab, for example `0,15,30,45 * * * * cd /path/to/script/ && php /path/to/script/crawl.php` should run the script every 15 minutes