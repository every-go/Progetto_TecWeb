export function caricamento(dettagli_form) {

    const form = document.querySelector("form");
    if (!form) {
        return;
    }

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
            fileInput.addEventListener("blur", function () {
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
            radio.addEventListener("blur", function () {
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

    const node = document.getElementById(dettagli_form[key][3]);

    if (!valido) {
        node.innerHTML = dettagli_form[key][2];
    } else {
        node.textContent = "";
    }


    return true;
}

export function validazioneCampoRadio(group, dettagli_form) {
    const groupName = group[0].name;
    const fieldset = group[0].closest("fieldset");
    if (!fieldset) return true;

    const node = document.getElementById(dettagli_form[groupName][3]);
    if (!node) return true;

    const almenoUnoSelezionato = group.some(r => r.checked);

    if (!almenoUnoSelezionato) {
        node.innerHTML = dettagli_form[groupName][2];
        return false;
    } else {
        node.textContent = "";
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