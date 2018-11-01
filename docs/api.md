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
* maconomy_id: string the maconomy id from the course to signup to
* seats: int, defaults to 1, so not really needed :)

### PUT: /api/v1/orders/{id}
Updates an order, with new details about participants (names, number of, etc)

Requires the following data:
* seats: int

### POST: /api/v1/orders/{id}/close
Closes the given order, marking it as ready to be synced with maconomy.

Requires the following data:
* participants: array, a list of participant data (name, email etc) to send to maconomy
* company: array, the company information as an indexed array

## Course routes

### GET: /api/v1/sync
Starts a sync to get new information about all courses in the system \*

_NOTE: This also syncs the course types._

### GET: /api/v1/sync/{id}
Syncs single course \*

_NOTE: this DOES NOT sync the course types!_ 

\* The sync jobs run in the background and will issue a notification (using GET)
to wordpress about "something" is updated. Wordpress should then refetch the
information and update the system.

### PUT: /api/v1/course/{id}
Changes the number of participants allowed on a course.

Requires the following data:
* participants_max: int
* deadline: string, a strtotime parsable string (in GMT). OPTIONAL

### GET: /api/v1/course
Fetches the courses from our local database

### GET: /api/v1/course/{id}
Fetches a single course from our local database

### GET: /api/v1/course/{id}/cal
Fetches the calendar event for the given course. NOTE: The "id" is "maconomy_id"

### GET: /api/v1/coursetype
Fetches the course types from our local database