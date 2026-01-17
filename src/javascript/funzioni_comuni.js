// funzioni_comuni.js
export function caricamento(dettagli_form) {

    const form = document.querySelector("form");
    if (!form) {
        return;
    }

    // Disabilita validazione HTML5 nativa
    form.setAttribute("novalidate", true);

    // Input text, number, file, textarea
    const inputs = form.querySelectorAll('input:not([type="radio"]), textarea');
    inputs.forEach(input => {
        const key = input.id || input.name;

        // Inserimento messaggio default
        if (dettagli_form[key]) {
            inserisciMessaggioDefault(input, dettagli_form);
        }

        // Blur listener
        input.addEventListener("blur", function () {
            console.log(`caricamento: blur su input "${key}"`);
            if (dettagli_form[key]) {
               var ok= validazioneCampo(this, dettagli_form);
            }
            if(key == "nome"){
               console.log(ok);
            }
        });
    });

    // Radio
    const radios = form.querySelectorAll('input[type="radio"]');
    const radioGroups = {};
    radios.forEach(radio => {
        if (!radioGroups[radio.name]) radioGroups[radio.name] = [];
        radioGroups[radio.name].push(radio);
    });

    Object.values(radioGroups).forEach(group => {
        const groupName = group[0].name;
    });

    // Submit listener
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
    node.textContent = dettagli_form[key][0];
    parent.insertBefore(node, input.nextSibling);
}

export function validazioneCampo(input, dettagli_form) {
   const key = input.id;
   const regex = dettagli_form[key][1];
   const text = input.value;
   const parent = input.parentNode;

   parent.querySelectorAll(".errorSuggestion").forEach(el => el.remove());

   let valido = true;
   let messaggioErrore = dettagli_form[key][2];

   // Controllo file
   if (input.type === "file" && input.files.length === 0) {
       valido = false;
   }

   // Controllo regex solo per gli altri tipi
   if (regex instanceof RegExp && input.type !== "file" && !regex.test(text)) {
       valido = false;
   }

   if (!valido) {
       const node = document.createElement("div");
       node.className = "errorSuggestion";
       node.textContent = messaggioErrore;
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
        node.innerHTML = dettagli_form[groupName][2];
        fieldset.appendChild(node);
        return false;
    }

    return true;
}

export function validazioneForm(dettagli_form) {
    let tuttoOk = true;

    const form = document.querySelector("form");

    // Text, number, file, textarea
    const inputs = form.querySelectorAll('input:not([type="radio"]), textarea');
    inputs.forEach(input => {
        const key = input.id;
        console.log(key);
        if (dettagli_form[key]) {
            const campoValido = validazioneCampo(input, dettagli_form);
            console.log("Campo valido=" + campoValido);
            tuttoOk = tuttoOk && campoValido;
            console.log("tuttoOk=" + tuttoOk);
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