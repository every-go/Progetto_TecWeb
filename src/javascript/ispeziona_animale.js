const img = document.getElementById('immagineCorrente');

// Al caricamento pagina
window.addEventListener('DOMContentLoaded', () => {
    if (img.src && img.src.trim() !== "") {
        img.dataset.hasCurrent = "true";
    }
});

// Al cambio immagine
const input = document.getElementById('immagine');
input.addEventListener('change', () => {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        img.src = e.target.result;
        img.dataset.hasCurrent = "true";
    };
    reader.readAsDataURL(file);
});

import { caricamento } from "./funzioni_comuni.js";

var dettagli_form = {
   "nome": [
      "Es: Flavio",
      /^[\p{L} ]{2,24}$/u,
      "Il nome deve avere tra 2 e 24 caratteri. Può contenere solo lettere (anche accentate) e spazi."
   ],
   "eta": [
      "Es: 9",
      /^(0|[1-9][0-9]{0,4})$/,
      "L'età deve essere un numero valido da 0 a 99999 senza zeri iniziali"
   ],
   "sesso": [
      "",
      null,
      "Devi selezionare il sesso dell'animale"
   ],
   "tipo_animale": [
      "",
      null,
      "Devi selezionare se l'animale è reale o fantastico"
   ],
   "immagine": [
      "",
      null,
      "Devi selezionare un'immagine"
   ],
   "alt": [
      "Es: Flavio è un piccione con delle piume bianche, ha un becco lungo e indossa delle scarpe nere con la punta bianca",
      /^[\p{L}0-9 ,:;'."()]{1,1024}$/u,
      "La descrizione fisica dell'animale può contenere lettere (anche accentate), spazi, numeri e i segni di punteggiatura : . , ; \" ' ( ). Non deve essere più lunga di 1024 caratteri"
   ],
   "luogo": [
      "Es: Sopra l'edificio in via Flavio numero 8/B",
      /^[\p{L}0-9 ,/'"]{1,100}$/u,
      "Il luogo può contenere lettere (anche accentate), spazi, numeri e i caratteri , / ' \". Non deve essere più lungo di 100 caratteri"
   ],
   "taglia": [
      "",
      null,
      "Devi selezionare la taglia dell'animale"
   ],
   "carattere": [
      "",
      null,
      "Devi selezionare la difficoltà di gestione dell'animale"
   ],
   "descrizione": [
      "Es: A Flavio piace tubare in giro e farsi dare le briciole di pane ai passanti. Tranquillo, non sporcherà la tua macchina",
      /^[\p{L}0-9 ,:;'."()?!\n]{1,}$/u,
      "La descrizione del carattere può contenere lettere (anche accentate), spazi, numeri e i segni di punteggiatura . ; , : ' \" ? ! ( )"
   ],
   "bisogni": [
      "Es: Un tetto stabile; Briciole di pane sempre a portata di becco;",
      /^[\p{L}0-9 ,:;'."()?!\n]{1,}$/u,
      "I bisogni possono contenere lettere (anche accentate), spazi, numeri e i segni di punteggiatura . ; , : ' \" ? ! ( )"
   ],
   "storia": [
      "Es: Flavio è nato su un tetto vicino alla stazione. Da piccolo adorava osservare i treni partire.",
      /^[\p{L}0-9 ,:;'."()?!\n]{0,}$/u,
      "La storia può contenere lettere (anche accentate), spazi, numeri e i segni di punteggiatura . ; , : ' \" ? ! ( )"
   ]
};

window.onload = () => caricamento(dettagli_form);