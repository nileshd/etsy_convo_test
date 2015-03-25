Etsy Convos Coding Homework

Database
I created two tables. You can see the full structure in the db folder in the initial.sql file.

convos
   id  int(11)
   sender_id int(11)       - FK to users.id, indexd
   recipient_id int(11)    - FK to users.id, indexed
   parent_id int(11)       - FK to convos.id, indexed
   root_parent_id int(11)  - FK to convos.id, indexed
   subject varchar(140)
   body text
   status enum('read','unread')      indexed
   date_created timestamp

Documentation

The Documentation for the API can be found by accessing the HTML Docs page at

/apidoc/index

The APIs implemented are

GET /convos/{id}
GET /convos/{id}/thread
POST /convos
PUT /convos/{id}
DELETE /convos/{id}

GET /users/{id}/convos
GET /users/{id}

The API uses GET http methods to get 1 convo or a convo thread, POST http method to add new convos, PUT http method to edit data of convo already in the system and the DELETE http method to delete convos.

The Delete operation does a cascade delete and deletes all it's children too from the database layer.

Pagination
Some APIs such as /users/{id}/convos and /users/{id}/convos take in start_row and num_items to do pagination.
While the app is paginating, if a new row is added to the database, if the pagination has not yet reached the end (right now it's sorted by table.id). The new row will have the latest auto_id and be a the bottom of the stack. As the pagination gets there, the newly added row will also show up in the result set, as each call is sequential and not stored in a database cursor. But alternatively, if we sorted by timestap showing most recent on top, in this particular case, depending on which position is pagination, the new message won't pop back in the WS.
Each API service is decoupled and therefore, does not keep state. If clients want to track the state, they can do and poll the API for new comment starting from the beginning.

Caching
Yes, API responses can be cached at multiple levels. One of the ways, is that we can serialize the output and store it to memory and retreive from there. Another way, we can add a proxy cache in front of our web servers - cache server such as Varnish. We can also leverage http caching and instruct the clients to cache the responses by setting Cache Control Headers and ETAGS and the max-age for content to live. We can cache at machine layer with things like APC.

Cache Invalidation
Not every caching solution will support cache invalidation. With proxy caches for example, Varnish has good support for invalidation. If we store cache in Memcache for example, as we have control on the hashtable that stores our data, we can delete them to invalidate them. But with client side caching, it's not possible to invalidate their cache, unless we change URL parameters. Usually, a version can be added as a get string to modify the URL and refresh the cache.



Things not finished in Implementation
 * Localized Messages - Messages should be extracted to a language file for internationalization
 * No Implementation for PATCH and HEAD Rest Methods
 * More validation need to be done to check each user input before passing to backend
 * Need to escape SQL to prevent SQL injections
 * When getting threaded message, get the title of first message for children's subject
 * API serializes only in standard JSON, don't offer other formats such as XML or Serialized PHP data
