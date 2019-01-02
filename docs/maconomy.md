# Maconomy integration

We sync with maconoy on almost every action we do in the system.

## Talking with the service

When doing any calls to the web service, we need to have a valid token.
The tokens last around 14 days and should be refreshed automatically.

## Handling reservations

When we reserve seats, we talk with a temporary table in maconomy.
However, we should always check, their webservice for number of actual seats
available, since they can also reserve seats (or change this) directly in 
maconomy.