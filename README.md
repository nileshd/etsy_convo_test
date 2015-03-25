Etsy Convos Coding Homework

*********
Database
*********

I created two tables the convos and user tables.

You can see the full structure in the db folder in the initial.sql file at 

     https://github.com/nileshd/etsy_convo_test/blob/master/db/initial.sql

You can see the foreign Keys and index definitions in that file. The parent_ids are foreign keyed to the id of the same table and set to have cascade on delete to manage the hierachy when deletion happens. The sender_id and user_id are fk-ed to the user table.

************
convos table
*************
   id  int(11)

   sender_id int(11)       - FK to users.id, indexd

   recipient_id int(11)    - FK to users.id, indexed

   parent_id int(11)       - FK to convos.id, indexed

   root_parent_id int(11)  - FK to convos.id, indexed

   subject varchar(140)

   body text

   status enum('read','unread')      indexed

   date_created timestamp


I opted to do pre-emptive optimization by having the root_parent_id which the topmost level convo in each row rather than calculating the entire tree at run time each time.

The clients adding convos should have the top convo in context and know what's the root_parent_id at the time they add any comment. Therefore, it avoids the dynamic calculation of the thread in the database layer.


**************
Documentation
*************

The Documentation for the API can be found by accessing the HTML Docs page at

** http://etsyconvo.dosooye.com/apidoc/index **

*************
RestFul APIs
*************

The APIs implemented are

**CONVOS**

GET /convos/{id}

GET /convos/{id}/thread

POST /convos

PUT /convos/{id}

DELETE /convos/{id}


**USERS**

GET /users/{id}/convos

GET /users/{id}

The API uses GET http methods to get 1 convo or a convo thread, POST http method to add new convos, PUT http method to edit data of convo already in the system and the DELETE http method to delete convos.

The Delete operation does a cascade delete and deletes all it's children too from the database layer.

*******************
Live Implementation
*******************

I used the CodeIgniter Framework to do a basic implementation of the REST API for Convos.

The implemented API can be tried on with

   http://etsyconvo.dosooye.com/api/ as the base URL

e.g a full Rest API call will be http://etsyconvo.dosooye.com/api/convos/12



**********
Pagination
***********
Some APIs such as /users/{id}/convos and /users/{id}/convos take in start_row and num_items to do pagination.


*Adding Row while paginating*
While the app is paginating, if a new row is added to the database, if the pagination has not yet reached the end (right now it's sorted by table.id), the new row will have the latest auto_id and be a the bottom of the stack.

As the pagination gets to that section of the listing, the newly added row will also show up in the result set, as each call is sequential and not stored in a database cursor.

But alternatively, if we sorted by timestap showing most recent on top, in this particular case, depending on which position is pagination, the new message won't pop back in the WS.

Each API service is decoupled and therefore, does not keep state. If clients want to track the state, they can do and poll the API for new comment starting from the beginning.

*******
Caching
*******

Yes, API responses can be cached at multiple levels.

One of the ways, is that we can serialize the output and *store the processed cache data to memory* and retreive from there. Common technology to use would be Memcache or Redis for that purpose.

Another way, we can add a *proxy cache* in front of our web servers - cache server such as Varnish.

We can also leverage *http caching* and instruct the clients to cache the responses by setting Cache Control Headers and ETAGS and the max-age for content to live.

We can cache at machine layer with things like APC.

*******************
Cache Invalidation
*******************
Not every caching solution will support cache invalidation.

With proxy caches for example, Varnish has good support for invalidation. If we store cache in Memcache for example, as we have control on the hashtable that stores our data, we can delete them to invalidate them. But with client side caching, it's not possible to invalidate their cache, unless we change URL parameters. Usually, a version can be added as a get string to modify the URL and refresh the cache.


**************************************
Things not finished in Implementation
**************************************
 * All http codes are right now 200. In a perfect rest world, the http code need to be more accurate based on event.
 * Localized Messages - Messages should be extracted to a language file for internationalization
 * No Implementation for PATCH and HEAD Rest Methods
 * More validation need to be done to check each user input before passing to backend
 * Need to escape SQL to prevent SQL injections
 * When getting threaded message, get the title of first message for children's subject
 * API serializes only in standard JSON, don't offer other formats such as XML or Serialized PHP data
 * No Authentication of Owner of Asset to do operation on the assets
 * No Security or authorization on Usage of API



 Lots more need to be done for production ready, but with limited time, that's all I could actually put in the hours between my job interviews here in Austin.
