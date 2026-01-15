// funzioni_comuni.js
export function caricamento(dettagli_form) {
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

export function validazioneCampo(input, dettagli_form) {
   var regex = dettagli_form[input.id][1];
   var text = input.value;
   var parent = input.parentNode;

   parent.querySelectorAll('.errorSuggestion').forEach(el => el.remove());

   var valido = true;
   var messaggioErrore = dettagli_form[input.id][2];

   var passwordInput = document.getElementById("password");
   if (!passwordInput || text !== passwordInput.value) {
         valido = false;
   }
   if (input.id === "password" && text === "#Flavio4") {
      valido = false;
      messaggioErrore = "Non puoi utilizzare la password di esempio.";
   }
   else if (regex instanceof RegExp && !regex.test(text)) {
      valido = false;
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

export function validazioneForm(dettagli_form) {
   var tuttoOk = true;
   for (var key in dettagli_form) {
      var input = document.getElementById(key);
      if (!input) continue;
      tuttoOk = tuttoOk && validazioneCampo(input, dettagli_form);
   }
   return tuttoOk;
}

export function inserisciMessaggioDefault(input, dettagli_form) {
   var parent = input.parentNode;
   var existingDefault = parent.querySelector('.default-text');
   if (existingDefault) existingDefault.remove();

   var node = document.createElement("span");
   node.className = "default-text";
   node.textContent = dettagli_form[input.id][0];
   parent.insertBefore(node, input.nextSibling);
}
