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

### Requirements
- PHP 7.4 (SF5 requirements) 
- MySql


### Running
- `php bin/console doctrine:migrations:migrate` to create the tables for the aggregate root and read models
- Open your browser on `http://localhost:8000/` (ohhhh dockkkerrrr)

### Testing
- `php bin/phpunit` (yeah... I know SF testing sucks)