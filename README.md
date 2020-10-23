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
- Docker

### Running
- Run `docker-compose up -d` on your terminal
- Open your browser on `http://localhost:8000/tab/open`


### Todo
##### Domain
- [ ] Do not allow to enter negative numbers on total to order;
#### Application
- [ ] Move code out of controllers to Handlers
- [ ] Refactor \Cafe\UserInterface\Web\Controller\TabController::order
- [ ] Handle domain exceptions
- [ ] Choose to use or not Commands on the Aggregates
##### Misc
- [ ] Replace every single array for a collection. 
- [ ] Make event store to use different types of persistence (redis, etc). Make it an interface
- [ ] Describe the project like this one: https://github.com/CodelyTV/php-ddd-example
- [ ] PHPStan
- [ ] Apply doctrine code standards
- [ ] Mutation testing
- [ ] Add to Scrutinizer
- [ ] Use yield return new DrinksServed to record events?
##### Infra
- [ ] Persistence using Doctrine
- [ ] Installation and run guide using docker
- [ ] Do not update write in case read fail.
#UI
- [ ] Add icons, better colors to buttons.
- [ ] Add footer
- [ ] Add github link
- [ ] Style tables and table headers
- [ ] Add flash messages
- [ ] Style http://localhost:8000/tab/297/order
- [ ] Add validators on DTOs
- [ ] Display total of the tab then closing it.