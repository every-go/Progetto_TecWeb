const img = document.getElementById('immagineCorrente');
const input = document.getElementById('immagine');
const maxSize = 2 * 1024 * 1024; // 2MB

input.addEventListener('change', () => {
    const file = input.files[0];
    const parent = input.parentElement;
    
    // Rimuovi eventuali errori precedenti
    const oldError = parent.querySelector('.errorSuggestion');
    if (oldError) {
        oldError.remove();
    }
    input.removeAttribute('aria-invalid');
    
    if (!file) return;
    
    // Validazione dimensione
    if (file.size > maxSize) {
        const node = document.createElement("div");
        node.className = "errorSuggestion";
        node.role = "alert";
        node.innerHTML = "File troppo grande (massimo 2 <abbr title=\"megabyte\">MB</abbr>)";
        parent.appendChild(node);
        input.setAttribute('aria-invalid', 'true');
        input.value = '';
        
        if (!img.dataset.hasCurrent) {
            img.src = '';
        }
        return;
    }
    
    // Validazione tipo file
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    if (!allowedTypes.includes(file.type)) {
        const node = document.createElement("div");
        node.className = "errorSuggestion";
        node.role = "alert";
        node.innerHTML = "Formato non valido (solo JPG, JPEG, PNG)";
        parent.appendChild(node);
        input.setAttribute('aria-invalid', 'true');
        input.value = '';
        return;
    }
    
    // Se tutto ok, mostra preview
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
      "<abbr title='Esempio'>Es</abbr>: Flavio",
      /^[\p{L} ]{2,24}$/u,
      "Il nome deve avere tra 2 e 24 caratteri. Può contenere solo lettere (anche accentate) e spazi.",
      "errNome"
   ],
   "eta": [
      "<abbr title='Esempio'>Es</abbr>: 9",
      /^(0|[1-9][0-9]{0,4})$/,
      "L'età deve essere un numero valido da 0 a 99999 senza zeri iniziali.",
      "errEta"
   ],
   "sesso": [
      "",
      null,
      "Devi selezionare il sesso dell'animale.",
      "errSesso"
   ],
   "tipo_animale": [
      "",
      null,
      "Devi selezionare se l'animale è reale o fantastico.",
      "errTipoAnimale"
   ],
   "immagine": [
      "",
      null,
      "Devi selezionare un'immagine valida (formati accettati: jpg, jpeg, png; dimensione massima: 2 MB).",
      "errImmagine"
   ],
   "alt": [
      "<abbr title='Esempio'>Es</abbr>: Flavio è un piccione, ha un becco lungo e indossa delle scarpe",
      /^[\p{L}0-9 ,:;'’ʼʻ`′."()]{1,1024}$/u,
      "Il testo alternativo dell'immagine può contenere lettere (anche accentate), spazi, numeri e i segni di punteggiatura: due punti, punto, virgola, punto e virgola, apici doppi, apici singoli, apostrofi, parentesi tonde. Non deve essere più lunga di 1024 caratteri e non si può andare a capo.",
      "errAlt"
   ],
   "luogo": [
      "<abbr title='Esempio'>Es</abbr>: Sopra l'edificio in via Flavio numero 8/B",
      /^[\p{L}0-9 ,/'’ʼʻ`′"]{1,100}$/u,
      "Il luogo può contenere lettere (anche accentate), spazi, numeri e i segni di punteggiatura: virgola, apice doppio, apice singolo e apostrofi. Non deve essere più lungo di 100 caratteri.",
      "errLuogo"
   ],
   "taglia": [
      "",
      null,
      "Devi selezionare la taglia dell'animale.",
      "errTaglia"
   ],
   "carattere": [
      "",
      null,
      "Devi selezionare la difficoltà di gestione dell'animale.",
      "errCarattere"
   ],
   "descrizione": [
      "<abbr title='Esempio'>Es</abbr>: A Flavio piace tubare in giro e farsi dare le briciole di pane ai passanti. Tranquillo, non sporcherà la tua macchina.",
      /^[\p{L}0-9 ,:;'’ʼʻ`′."()?!\s]{1,1024}$/u,
      "La descrizione del carattere dell'animale non può essere più lunga di 1024 caratteri e può contenere lettere (anche accentate), spazi, numeri e i segni di punteggiatura: punto, virgola, il punto e virgola, i due punti, l'apice doppio, l'apice singolo, l'apostrofo, il punto interrogativo, il punto esclamativo e le parentesi tonde",
      "errDesc"
   ],
   "bisogni": [
      "<abbr title='Esempio'>Es</abbr>: Un tetto stabile; Briciole di pane sempre a portata di becco;",
      /^[\p{L}0-9 ,:;'’ʼʻ`′."()?!\s]{1,1024}$/u,
      "I bisogni non possono essere più lunghi di 1024 caratteri e possono contenere lettere (anche accentate), spazi, numeri e i segni di punteggiatura : punto, virgola, i due punti, l'apice doppio, l'apice singolo, l'apostrofo, il punto interrogativo, il punto esclamativo e le parentesi tonde. Qualsiasi punto e virgola sarà letto come un separatore.",
      "errBisogni"
   ],
   "storia": [
      "<abbr title='Esempio'>Es</abbr>: Flavio è nato su un tetto vicino alla stazione. Da piccolo adorava osservare i treni partire.",
      /^[\p{L}0-9 ,:;'’ʼʻ`′."()?!\s]{0,1024}$/u,
      "La storia non può essere più lunga di 1024 caratteri e può contenere lettere (anche accentate), spazi, numeri e i segni di punteggiatura: punto, virgola, il punto e virgola, i due punti, l'apice doppio, l'apice singolo, l'apostrofo, il punto interrogativo, il punto esclamativo e le parentesi tonde",
      "errStoria"
   ]
};

window.onload = () => caricamento(dettagli_form);