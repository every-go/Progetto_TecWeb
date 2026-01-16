import {inserisciMessaggioDefault, validazioneForm} from "./funzioni_comuni.js";

var dettagli_form = {
   "username": [
      "Es: FlavioPiccione",
      /^[A-Za-z]{4,24}$/,
      "Lo <span lang='en'>username</span> deve avere minimo 4 caratteri e massimo 24. Può contenere solo lettere maiuscole o minuscole."
   ],
   "password": [
      "Es: #Flavio4",
      /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{};:'",.<>\/?\\|`~])[A-Za-z\d!@#$%^&*()_\-+=\[\]{};:'",.<>\/?\\|`~]{8,24}$/,
      "Password non valida. Deve contenere almeno 1 lettera maiuscola, 1 lettera minuscola, 1 numero e almeno un carattere speciale, minimo 8, massimo 24 caratteri."
   ],
   "confirm_password": [
      "",
      null,
      "La conferma non è uguale alla <span lang='en'>password</span> inserita in precedenza"
   ]
};

// override di funzione validazione determinato dalla presenza del campo "conferma_password" che non ha una regex comune
window.validazioneCampo = function(input, dettagli_form) {
   var regex = dettagli_form[input.id][1];
   var text = input.value;
   var parent = input.parentNode;

   var existingErrors = parent.querySelectorAll('.errorSuggestion');
   existingErrors.forEach(el => el.remove());

   var valido = true;

   var messaggioErrore = dettagli_form[input.id][2];

   if (input.id === "confirm_password") {
      var passwordInput = document.getElementById("password");
      if (!passwordInput || text !== passwordInput.value) {
         valido = false;
         messaggioErrore = dettagli_form[input.id][2];
      }
   } else if (input.id === "password" && text === "#Flavio4") {
      valido = false;
      messaggioErrore = "Non puoi utilizzare la password di esempio.";
   } else if (regex instanceof RegExp && !regex.test(text)) {
      valido = false;
      messaggioErrore = dettagli_form[input.id][2];
   }

   if (!valido) {
      var node = document.createElement("strong");
      node.className = "errorSuggestion";
      node.innerHTML = messaggioErrore;
      parent.appendChild(node);
      return false;
   }

   return true;
}

// Sovrascrivi caricamento per usare validazioneCampo sovrascritta
function caricamento(dettagli_form) {
   for (var key in dettagli_form) {
      var input = document.getElementById(key);
      if (!input) continue;

      inserisciMessaggioDefault(input, dettagli_form);

      input.addEventListener("blur", function () {
         validazioneCampo(this, dettagli_form);
      });
   }

   var form = document.getElementById("login-form");
   if (form) {
      form.onsubmit = function () {
         return validazioneForm(dettagli_form);
      };
   }
}

window.onload = () => caricamento(dettagli_form);