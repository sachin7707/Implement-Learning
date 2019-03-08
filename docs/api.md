# API routes explained

baseurl for API is: /api/v1

## Order routes

### GET: /api/v1/orders
Fetches a list of current orders

Optional data:
* state: array of states to fetch orders for. 0 for deleted, 1 for close, 2 for synced to maconomy

### GET: /api/v1/orders/{id}
Fetches one order, to show it's information
### POST: /api/v1/orders
Creates a new order.

Optional data:
* education_id: int, the id of the education (course) we are using

### PUT: /api/v1/orders/{id}
Updates an order, with new details about participants (names, number of, etc)

Requires the following data:
* seats: int, the number of seats required

Optional data:
* courses: array with a list of maconomy_id of the courses to signup to (or "nothing")
* education: string, the maconomy id of the education (course) we are using

### POST: /api/v1/orders/{id}/close
Closes the given order, marking it as ready to be synced with maconomy.

Requires the following data:
* participants: array, a list of participant data (name, email etc) to send to maconomy
* company: array, the company information as an indexed array

### POST: /api/v1/orders/{id}/resync
Resyncs the order with the given id, with maconomy

### POST: /api/v1/orders/{id}/resendemails
Resends the emails for the given order

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

* Add _?withtrashed=1_ to the URL, to get all courses, including deleted ones.
* Add ?sku=22111,15678,XXX to filter courses, based on their parent sku

### GET: /api/v1/course/{id}
Fetches a single course from our local database.
NOTE: This now also includes a course that has been deleted.

### GET: /api/v1/course/{id}/cal
Fetches the calendar event for the given course. NOTE: The "id" is "maconomy_id"

### GET: /api/v1/coursetype
Fetches the course types from our local database

### GET: /api/v1/coursetype/{id}
Fetches information about a single course type

### PUT: /api/v1/coursetype/{id}
Changes the data on the selected coursetype

Requires the following data:
* name: string, changes the title on the backend server


### GET: /api/v1/location
Fetches the locations from our local database

### PUT: /api/v1/location/{id}
Updates or Creates the data on the selected location.

The reason we have one action for both update or create is that WP does not know if it's one or the other...

Requires the following data:
* name: string, changes the location name

### GET: /api/v1/trainer
Fetches the trainers from our local database

### PUT: /api/v1/trainer/{id}
Updates or Creates the data on the selected trainer.

The reason we have one action for both update or create is that WP does not know if it's one or the other... 

The {id} is the id from wordpress. We use this to see if we are creating or updating a trainer.

Requires the following data:
* name: string, changes the trainer name
* email: string, changes the trainer email
* phone: string, changes the trainer phone

### POST: /api/v1/newsletter/signup
Adds a person to the newsletter

Requires the following data:
* firstname: string
* lastname: string
* email: string
