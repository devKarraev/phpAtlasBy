Need to change in .env:
1. `ATLAS_MONTH` 
2. `ATLAS_DAY`
3. `NEEDED_TIMES`

Need to add new cron job:
1. `* * * * * /usr/local/bin/php /Users/rasulkaraev/Documents/phpProjects/forTests/index.php >> /Users/rasulkaraev/Documents/phpProjects/forTests/cron.log 2>&1`
