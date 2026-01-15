import {caricamento} from './funzioni_comuni.js'

var dettagli_form = {
   "username": [
      "Es: FlavioPiccione",
      /^[A-Za-z]{8,24}$/,
      "Lo <span lang='en'>username</span> deve avere minimo 8 caratteri e massimo 24. Sono consentite solamente lettere maiuscole o minuscole senza spazi, numeri o caratteri speciali."
   ],
   "password": [
      "Es: #Flavio4",
      /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{};:'\",.<>\/?\\|`~]).{8,24}$/,
      "<span lang='en'>Password</span> non valida. Deve contenere almeno 1 lettera maiuscola, 1 lettera minuscola, numeri e almeno un simbolo, minimo 8, massimo 24 caratteri. Non può contenere spazi."
   ],
   "confirm_password": [
      "",
      null,
      "La conferma non è uguale alla <span lang='en'>password</span> inserita in precedenza"
   ]
};


window.onload = () => caricamento(dettagli_form);