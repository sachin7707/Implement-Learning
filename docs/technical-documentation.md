# Teknisk dokumentation

Implement systemet består af 3 forskellige systemer her hos Konform A/S.

* ilibackend (backend til iliwordpress - samt API til nuxt site)
* iliwordpress (backend til nuxt site)
* nuxt site (selve det site, som Implements kunder ser)

Derudover er der integration til Maconomy, som ikke drives af Konform A/S, som har et custom API endpoint 
vores systemer kan tale med. 

Følgende dokument beskriver hvordan disse systemer hænger sammen, og hvor der tales med hvad.

## ilibackend

Dette system er lavet i Laravel, PHP og MySQL.

Systemet håndtere booking af kurser, samt sync af kursus information fra Maconomy.

### Booking flow

Når en kunde bestiller et kursus på Nuxt site, bliver der oprettet en ordre i ilibackend.
Dette gør at Nuxt site ikke skal håndtere logik omkring pladser på et kursus, og hvornår disse skal frigives igen.

Selve flowet er som følger (for booking af 2 pladser på et kursus):

* på Nuxt site trykkes "Vælg kursus dato"
* Nuxt site sender en "book" med maconomy id (f.eks. 25172-04) til ilibackend
* ilibackend tjekker om der er plads (vha. kald til Maconomy) og booker lokalt (på ilibackend) 1 plads.
* Kunde vælger at de har 2 deltagere.
* Nuxt site sender 2 pladser til ilibackend.
* ilibackend spørger Maconomy om der er plads og booker lokalt 2 pladser.
* Kunde udfylder resten af booking form og trykker "bestil".
* Nuxt site sender form data til ilibackend.
* ilibackend gemmer data, og sætter at orderen skal synces til Maconomy.
* "senere" sender et cronjob, på ilibackend, data til Maconomy omkring orderen.
* ilibackend sender bekræftigelses email til kunde, samt deltagerne der blev tilmeldt.

### Sync af kursusdata

ilibackend står for at hente kursusdata fra Maconomy.
Herefter bliver der (via et cronjob på ilibackend) sendt kursusdata til iliwordpress.

Nuxt site henter data fra iliwordpress, når der skal vises kursus information.

## iliwordpress

