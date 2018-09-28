# API routes explained

baseurl for API is: /api/v1

## Order routes

### GET: /api/v1/orders
Fetches a list of current orders
### GET: /api/v1/orders/{id}
Fetches one order, to show it's information
### POST: /api/v1/orders
Creates a new order.

Requires the following data:
* course_id: string
* number_of_participants: int

### PUT: /api/v1/orders/{id}
Updates an order, with new details about participants (names, number of, etc)

## Course routes

### GET: /api/v1/sync
Starts a sync to get new information about all courses in the system \*
### GET: /api/v1/sync/{id}
Syncs single course \* 

\* The sync jobs run in the background and will issue a notification (using GET)
to wordpress about "something" is updated. Wordpress should then refetch the
information and update the system.

### PUT: /api/v1/courses/{id}
Changes the number of participants allowed on a course.

Requires the following data:
* participants_max: int