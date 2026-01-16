import { caricamento } from "./funzioni_comuni.js";

var dettagli_form = {
   "nome": [
      "Es: Flavio",
      /^[A-Za-z]{2,24}$/,
      "Il nome deve avere minimo 2 caratteri e massimo 24. Può contenere solo lettere maiuscole o minuscole."
   ],
   "eta": [
      "Es: 9",
      /^[0-9]{1,5}$/,
      "L'età può avere massimo 5 cifre"
   ],
   "sesso": [
      "",
      null,
      "Devi selezionare il sesso"
   ]
};

window.onload = () => caricamento(dettagli_form);