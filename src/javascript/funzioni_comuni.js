export function caricamento(dettagli_form) {

    const form = document.querySelector("form");
    if (!form) {
        return;
    }

    form.setAttribute("novalidate", true);

    const inputs = form.querySelectorAll('input:not([type="radio"]):not([type="file"]), textarea');
    
    inputs.forEach(input => {
        const key = input.id || input.name;

        if (dettagli_form[key]) {
            inserisciMessaggioDefault(input, dettagli_form);
        }

        input.addEventListener("blur", function () {
            if (dettagli_form[key]) {
               validazioneCampo(this, dettagli_form);
            }
        });
    });

    // AGGIUNTO: Event listener per il campo file
    const fileInput = form.querySelector('input[type="file"]');
    
    if (fileInput) {
        
        if (dettagli_form[fileInput.id]) {
            fileInput.addEventListener("blur", function() {
                const risultato = validazioneCampo(this, dettagli_form);
            });
        }
    }

    const radios = form.querySelectorAll('input[type="radio"]');
    const radioGroups = {};
    radios.forEach(radio => {
        if (!radioGroups[radio.name]) radioGroups[radio.name] = [];
        radioGroups[radio.name].push(radio);
    });

    Object.values(radioGroups).forEach(group => {
        group.forEach(radio => {
            radio.addEventListener("blur", function() {
                validazioneCampoRadio(group, dettagli_form);
            });
        });
    });

    if (form) {
      form.onsubmit = function () {
         return validazioneForm(dettagli_form);
      };
    }
}

export function inserisciMessaggioDefault(input, dettagli_form) {
    const key = input.id || input.name;
    const parent = input.parentNode;
    const existingDefault = parent.querySelector(".default-text");
    if (existingDefault) existingDefault.remove();

    const node = document.createElement("span");
    node.className = "default-text";
    node.innerHTML = dettagli_form[key][0];
    parent.insertBefore(node, input.nextSibling);
}

export function validazioneCampo(input, dettagli_form) {
   const key = input.id;
   
   const regex = dettagli_form[key][1];
   const text = input.value.trim();
   const parent = input.parentNode;

   parent.querySelectorAll(".errorSuggestion").forEach(el => el.remove());

   let valido = true;

   if (input.type === "file") {
        const imgCorrente = document.getElementById("immagineCorrente");
        const hasCurrent = imgCorrente?.dataset.hasCurrent === "true";
        
        // Se non c'è immagine corrente, il file è obbligatorio
        if (!hasCurrent && input.files.length === 0) {
            valido = false;
        }
    }

   if (regex instanceof RegExp && input.type !== "file" && !regex.test(text)) {
       valido = false;
   }

   if (!valido) {
       const node = document.createElement("div");
       node.className = "errorSuggestion";
       node.role = "alert";
       node.innerHTML = dettagli_form[key][2];
       node.id = dettagli_form[key][3];
       parent.appendChild(node);
       return false;
   }

   return true;
}

export function validazioneCampoRadio(group, dettagli_form) {
    const groupName = group[0].name;
    const fieldset = group[0].closest("fieldset");
    if (!fieldset) return true;

    fieldset.querySelectorAll(".errorSuggestion").forEach(el => el.remove());

    const almenoUnoSelezionato = group.some(r => r.checked);

    if (!almenoUnoSelezionato) {
        const node = document.createElement("div");
        node.className = "errorSuggestion";
        node.role = "alert";
        node.innerHTML = dettagli_form[groupName][2];
        node.id = dettagli_form[groupName][3];
        fieldset.appendChild(node);
        return false;
    }

    return true;
}

export function validazioneForm(dettagli_form) {
    let tuttoOk = true;

    const form = document.querySelector("form");

    const inputs = form.querySelectorAll('input:not([type="radio"]), textarea');
    inputs.forEach(input => {
        const key = input.id;
        if (dettagli_form[key]) {
            const campoValido = validazioneCampo(input, dettagli_form);
            tuttoOk = tuttoOk && campoValido;
        }
    });

    // Radio
    const radios = form.querySelectorAll('input[type="radio"]');
    const radioGroups = {};
    radios.forEach(radio => {
        if (!radioGroups[radio.name]) radioGroups[radio.name] = [];
        radioGroups[radio.name].push(radio);
    });

    Object.values(radioGroups).forEach(group => {
        const campoValido = validazioneCampoRadio(group, dettagli_form);
        tuttoOk = tuttoOk && campoValido;
    });

    return tuttoOk;
}