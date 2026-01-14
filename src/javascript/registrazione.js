var dettagli_form = {
   "username": [
      "Es: Flavio il Piccione",
      '/^[a-zA-Z_ ]{4,24}$/',
      "Lo <span lang='en'>username</span> deve avere minimo 4 caratteri e non contenere numeri."
   ],
   "password": [
      "Es: #Flavio4",
      /^(?=.*[a-zA-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{};:'",.<>\/?\\|`~]).{8,24}$/,
      "Password non valida. Deve contenere almeno 1 lettera maiuscola, 1 lettera minuscola, numeri e almeno un simbolo, minimo 8, massimo 24 caratteri."
   ],
   "confirm_password": [
      "",
      null,
      "La conferma non è uguale alla <span lang='en'>password</span> inserita in precedenza"
   ]
};

function caricamento() {
   for (var key in dettagli_form) {
      var input = document.getElementById(key);
      if (!input) continue;

      // messaggio di default
      inserisciMessaggioDefault(input);

      // validazione live al blur
      input.addEventListener("blur", function () {
         validazioneCampo(this);
      });
   }

   var form = document.getElementById("login-form");
   if (form) {
      form.onsubmit = function () {
         return validazioneForm();
      };
   }
}

function validazioneCampo(input) {
   var regex = dettagli_form[input.id][1];
   var text = input.value;
   var parent = input.parentNode;

   // rimuove eventuali messaggi di errore precedenti di QUESTO input
   var existingErrors = parent.querySelectorAll('.errorSuggestion');
   existingErrors.forEach(el => el.remove());

   var valido = true;

   var messaggioErrore = dettagli_form[input.id][2]; // default

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

function validazioneForm() {
   var tuttoOk = true;
   for (var key in dettagli_form) {
      var input = document.getElementById(key);
      if (!input) continue;
      var ok = validazioneCampo(input);
      tuttoOk = tuttoOk && ok;
   }
   return tuttoOk;
}

function inserisciMessaggioDefault(input) {
   var parent = input.parentNode;
   // rimuove eventuale default precedente
   var existingDefault = parent.querySelector('.default-text');
   if (existingDefault) existingDefault.remove();

   var node = document.createElement("span");
   node.className = "default-text";
   node.textContent = dettagli_form[input.id][0];
   parent.insertBefore(node, input.nextSibling);
}

// esegui caricamento al load
window.onload = caricamento;
