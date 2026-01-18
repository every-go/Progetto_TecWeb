import {caricamento} from './funzioni_comuni.js'

var dettagli_form = {
   "username": [
      "Es: FlavioPiccione",
      /^[A-Za-z]{4,24}$/,
      "Ricorda che lo <span lang='en'>username</span> deve avere minimo 4 caratteri e massimo 24. Sono consentite solamente lettere maiuscole o minuscole senza spazi, numeri o caratteri speciali"
   ],
   "password": [
      "Es: #Flavio4",
      /^(user|admin|(?=(?:.*[a-z]))(?=(?:.*[A-Z]))(?=(?:.*\d))(?=(?:.*[!@#$%^&*()_\-+=\[\]{};:'",.<>\/?\\|`~]))[A-Za-z\d!@#$%^&*()_\-+=\[\]{};:'",.<>\/?\\|`~]{8,24})$/,
      "Ricorda che la <span lang='en'>password</span> è compresa fra 8 e 24 caratteri, ha almeno un numero, una lettera maiuscola, una minuscola e un carattere speciale"
   ]
};

window.onload = () => caricamento(dettagli_form);