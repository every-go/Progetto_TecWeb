import {caricamento} from './funzioni_comuni.js'

var dettagli_form = {
   "username": [
      "Es: FlavioPiccione",
      /^(user|admin|[A-Za-z]{8,24})$/,
      "Ricorda che lo <span lang='en'>username</span> deve avere minimo 8 caratteri e massimo 24. Sono consentite solamente lettere maiuscole o minuscole senza spazi, numeri o caratteri speciali."
   ],
   "password": [
      "Es: #Flavio4",
      /^(user|admin|(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{};:'",.<>\/?\\|`~]).{8,24})$/,
      "Ricorda che la <span lang='en'>password</span> è compresa fra 8 e 24 caratteri, ha almeno un numero, una lettera maiuscola, una minuscola e un carattere speciale."
   ]
};

window.onload = () => caricamento(dettagli_form);