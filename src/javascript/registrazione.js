import {inserisciMessaggioDefault, validazioneForm} from "./funzioni_comuni.js";

var dettagli_form = {
   "username": [
      "<abbr title='Esempio'>Es</abbr>: FlavioPiccione",
      /^[A-Za-z ]{4,24}$/,
      'Lo <span lang="en">username</span> deve avere minimo 4 caratteri e massimo 24. Può contenere solo lettere maiuscole, minuscole e spazi senza numeri o caratteri speciali.',
      "errUsername"
   ],
   "password": [
      "<abbr title='Esempio'>Es</abbr>: #Flavio4",
      /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{};:'",.<>\/?\\|`~])[A-Za-z\d!@#$%^&*()_\-+=\[\]{};:'",.<>\/?\\|`~]{8,24}$/,
      "Password non valida. Deve contenere almeno 1 lettera maiuscola, 1 lettera minuscola, 1 numero e almeno un carattere speciale, minimo 8, massimo 24 caratteri.",
      "errPassword"
   ],
   "confirm_password": [
      "",
      null,
      "La conferma non è uguale alla <span lang='en'>password</span> inserita in precedenza.",
      "errConfirmPassword"
   ]
};

// override di funzione validazione determinato dalla presenza del campo "conferma_password" che non ha una regex comune
window.validazioneCampo = function(input, dettagli_form) {
   var regex = dettagli_form[input.id][1];
   var text = input.value;
   var parent = input.parentNode;

   var valido = true;

   var messaggioErrore = dettagli_form[input.id][2];

   if (input.id === "confirm_password") {
      var passwordInput = document.getElementById("password");
      if (!passwordInput || text !== passwordInput.value) {
         valido = false;
      }
   } else if (input.id === "password" && text === "#Flavio4") {
      valido = false;
      messaggioErrore = "Non puoi utilizzare la password di esempio.";
   } else if (regex instanceof RegExp && !regex.test(text)) {
      valido = false;
   }

   const node = document.getElementById(dettagli_form[input.id][3]);

   if (!valido) {
      node.innerHTML = messaggioErrore;
   } else {
      node.textContent = "";
   }

   return true;
}

// Sovrascrivi caricamento per usare validazioneCampo sovrascritta
function caricamento(dettagli_form) {

   const form = document.querySelector("form");
   if (!form) {
      return;
   }

   // disattiva validazione HTML5 una sola volta
   form.noValidate = true;

   // input e textarea (NO radio)
   const inputs = form.querySelectorAll("input:not([type='radio']), textarea");

   inputs.forEach(input => {
      const key = input.name || input.id;

      if (!dettagli_form[key]) {
         return;
      }

      inserisciMessaggioDefault(input, dettagli_form);

      input.addEventListener("blur", function () {
         validazioneCampo(this, dettagli_form);
      });
   });

   // submit
   form.onsubmit = function () {
      let valido = validazioneForm(dettagli_form);

      const password = document.getElementById("password");
      const confirm  = document.getElementById("confirm_password");

      if (password && confirm && password.value !== confirm.value) {
         validazioneCampo(confirm, dettagli_form);
         valido = false;
      }

      return valido;
   };
}

window.onload = () => caricamento(dettagli_form);