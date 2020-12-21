### The Domain
For this tutorial, we'll work in the cafe domain. Our focus will be on the concept of a **tab**, which tracks the visit 
of an individual or group to the cafe. When people arrive to the cafe and take a table, a tab is opened. They may then 
order drinks and food. Drinks are served immediately by the table staff, however food must be cooked by a chef. 
Once the chef has prepared the food, it can then be served.

During their time at the restaurant, visitors may order extra food or drinks. 
If they realize they ordered the wrong thing, they may amend the order - but not after the food and drink has been 
served to and accepted by them.

Finally, the visitors close the tab by paying what is owed, possibly with a tip for the serving staff. 
Upon closing a tab, it must be paid for in full. A tab with unserved items cannot be closed unless the items are either 
marked as served or cancelled first.

### Screenshots
![Screenshot](screenshots/1-home.png)
![Screenshot](screenshots/2-opening-tab.png)
![Screenshot](screenshots/3-ordering.png)
![Screenshot](screenshots/4-tab-status.png)
![Screenshot](screenshots/5-chef-todo.png)
![Screenshot](screenshots/5-chef-todo.png)
![Screenshot](screenshots/7-home-some-opened.png)
![Screenshot](screenshots/8-status-2.png)
![Screenshot](screenshots/9-cant-close-tab.png)
![Screenshot](screenshots/10-closing-tab.png)

### Requirements
- Docker 

### Installation
- Clone this repository: `git clone git@github.com:yourwebmaker/cqrs.nu-php.git`
- Install the containers: `docker-compose up -d`
- Access the container: `docker exec -it cafe-fpm bash`
- Install dependencies: `composer install`
- Setup database: `vendor/bin/doctrine-migrations migrations:migrate --configuration migrations/migrations-config.php --db-configuration migrations/db-config.php --no-interaction`
- Open your browser on `http://0.0.0.0:8001/tab/open`

### Testing
- Inside the container: `vendor/bin/phpunit tests`